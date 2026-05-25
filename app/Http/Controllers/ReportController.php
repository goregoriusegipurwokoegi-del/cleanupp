<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());
        $category = $request->input('category');
        $status = $request->input('status');
        $tab = $request->input('tab', 'orders'); // 'orders' or 'loans'

        $query = \App\Models\Order::with(['user', 'service'])
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->where('status', '!=', 'cancelled');

        if ($category) {
            $query->whereHas('service', function($q) use($category) {
                $q->where('category', $category);
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        $orders = $query->latest()->get();
        $services = \App\Models\Service::all();

        // Calculate Revenue from filtered orders (Sum all that have been accepted/confirmed)
        $totalRevenue = $orders->where('status', '!=', 'pending')
                               ->where('status', '!=', 'cancelled')
                               ->sum('total_price');
        $totalOrders = $orders->count();

        // Loans data for the period
        $loansQuery = Loan::with('user')
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        $loans = $loansQuery->latest()->get();
        $totalLoansApproved = $loans->where('status', 'approved')->sum('amount');
        $totalLoansPending = $loans->where('status', 'pending')->sum('amount');
        $totalLoansRejected = $loans->where('status', 'rejected')->count();

        return view('admin.reports.index', compact(
            'orders', 'services', 'totalRevenue', 'totalOrders',
            'startDate', 'endDate', 'loans',
            'totalLoansApproved', 'totalLoansPending', 'totalLoansRejected', 'tab'
        ));
    }

    public function exportExcel(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());
        $category = $request->input('category');
        $status = $request->input('status');

        $query = \App\Models\Order::with(['user', 'service'])
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->where('status', '!=', 'cancelled');

        if ($category) {
            $query->whereHas('service', function($q) use($category) {
                $q->where('category', $category);
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        $orders = $query->get();

        $fileName = 'Laporan_CleanUP_Shoes_' . $startDate . '_to_' . $endDate . '.csv';

        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = array('Tanggal', 'ID Pesanan', 'Pelanggan', 'Layanan', 'Status', 'Metode Pembayaran', 'Total Harga');

        $callback = function() use($orders, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($orders as $order) {
                fputcsv($file, array(
                    $order->created_at->format('d/m/Y'),
                    '#' . $order->queue_number,
                    $order->user->name,
                    $order->service->name,
                    strtoupper($order->status),
                    strtoupper($order->payment_method),
                    $order->total_price
                ));
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function employeeIndex(Request $request)
    {
        $user = auth()->user();
        
        // Check if explicitly filtered by user (query parameters exist in URL)
        $isFiltered = $request->filled('start_date') || $request->filled('end_date') || $request->filled('search');

        if ($isFiltered) {
            $startDate = $request->input('start_date', now()->toDateString());
            $endDate = $request->input('end_date', now()->toDateString());
        } else {
            // Default: Show only today's data (newest date)
            $startDate = now()->toDateString();
            $endDate = now()->toDateString();
        }

        $search = $request->input('search');

        // Base query for current employee's tasks
        $query = \App\Models\Order::with(['user', 'service'])
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->where('status', '!=', 'cancelled');

        if ($search) {
            $query->where(function($q) use($search) {
                $q->where('queue_number', 'like', "%$search%")
                  ->orWhereHas('user', function($uq) use($search) {
                      $uq->where('name', 'like', "%$search%");
                  });
            });
        }

        $allOrders = $query->latest()->get();

        // Fetch current employee's daily attendance records
        $attendances = \App\Models\Attendance::where('user_id', $user->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date', 'desc')
            ->get();

        // Performance Statistics
        $stats = [
            'total_completed' => $allOrders->where('status', 'completed')->count(),
            'cleaning_done' => $allOrders->where('status', 'completed')->filter(function($o) {
                return str_contains(strtolower($o->service->category ?? ''), 'clean');
            })->count(),
            'repair_done' => $allOrders->where('status', 'completed')->filter(function($o) {
                return str_contains(strtolower($o->service->category ?? ''), 'repair');
            })->count(),
            'processing' => $allOrders->whereIn('status', ['processing', 'washing', 'drying', 'finishing'])->count(),
            'avg_rating' => round($allOrders->whereNotNull('rating')->avg('rating') ?? 0, 1)
        ];

        // Chart Data (Last 7 Days)
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $chartData['labels'][] = now()->subDays($i)->format('D');
            $chartData['counts'][] = \App\Models\Order::whereDate('created_at', $date)
                ->where('status', 'completed')
                ->count();
        }

        // By default (if not filtered), show only the completed orders (data terbaru yang sudah selesai)
        // If filtered, show all statuses including processing etc.
        if (!$isFiltered) {
            $allOrders = $allOrders->where('status', 'completed');
        }

        // Fetch current employee's loans history
        $loans = Loan::where('user_id', $user->id)->latest()->get();

        return view('employee.reports.index', compact('allOrders', 'stats', 'chartData', 'startDate', 'endDate', 'attendances', 'loans'));
    }

    public function exportAttendanceExcel(Request $request)
    {
        $user = auth()->user();
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());

        $attendances = \App\Models\Attendance::where('user_id', $user->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date', 'asc')
            ->get();

        $fileName = 'Rekap_Absensi_' . str_replace(' ', '_', $user->name) . '_' . $startDate . '_to_' . $endDate . '.csv';

        $headers = array(
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = array('Tanggal', 'Jam Masuk', 'Jam Keluar', 'Durasi Kerja', 'Status');

        $callback = function() use($attendances, $columns) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for UTF-8 compatibility with MS Excel
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            fputcsv($file, $columns);

            foreach ($attendances as $att) {
                $clockIn = $att->clock_in ? \Carbon\Carbon::parse($att->clock_in)->format('H:i:s') : '-';
                $clockOut = $att->clock_out ? \Carbon\Carbon::parse($att->clock_out)->format('H:i:s') : '-';
                
                // Calculate duration
                $duration = '-';
                if ($att->clock_in && $att->clock_out) {
                    $in = \Carbon\Carbon::parse($att->clock_in);
                    $out = \Carbon\Carbon::parse($att->clock_out);
                    $diff = $in->diff($out);
                    $duration = $diff->format('%h Jam %i Menit');
                }

                $status = 'Tepat Waktu';
                if ($att->clock_in && \Carbon\Carbon::parse($att->clock_in)->format('H:i') > '09:00') {
                    $status = 'Terlambat';
                }

                fputcsv($file, array(
                    \Carbon\Carbon::parse($att->date)->format('d/m/Y'),
                    $clockIn,
                    $clockOut,
                    $duration,
                    $status
                ));
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportAttendancePdf(Request $request)
    {
        $user = auth()->user();
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());

        $attendances = \App\Models\Attendance::where('user_id', $user->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date', 'desc')
            ->get();

        return view('employee.reports.attendance-print', compact('user', 'attendances', 'startDate', 'endDate'));
    }
}
