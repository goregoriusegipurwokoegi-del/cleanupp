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
        $tab = $request->input('tab', 'ringkasan'); 

        // Base query for orders
        $query = \App\Models\Order::with(['user', 'service'])
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->where([['status', '!=', 'cancelled']]);

        switch ($tab) {
            case 'ringkasan':
                return $this->ringkasanTab($query, $startDate, $endDate);
            case 'pesanan':
                return $this->pesananTab($query, $startDate, $endDate, $category, $status);
            case 'pendapatan':
                return $this->pendapatanTab($query, $startDate, $endDate);
            case 'laba-rugi':
                return $this->labaRugiTab($query, $startDate, $endDate);
            case 'terlaris':
                return $this->terlarisTab($query, $startDate, $endDate);
            case 'pinjaman':
                return $this->pinjamanTab($startDate, $endDate);
            case 'export':
            default:
                return view('admin.reports.index', compact('tab', 'startDate', 'endDate'));
        }
    }

    private function ringkasanTab($query, $startDate, $endDate)
    {
        $tab = 'ringkasan';
        $totalOrders = $query->count();
        $totalRevenue = (clone $query)->where('status', '!=', 'pending')->sum('total_price');
        $activeServices = \App\Models\Service::count();
        
        $chartData = ['labels' => [], 'revenue' => []];
        // For Ringkasan Chart
        for ($i = 6; $i >= 0; $i--) {
            $d = now()->subDays($i)->format('Y-m-d');
            $chartData['labels'][] = now()->subDays($i)->format('d M');
            $chartData['revenue'][] = \App\Models\Order::where([['status', '!=', 'cancelled']])
                ->where([['status', '!=', 'pending']])
                ->whereDate('created_at', $d)
                ->sum('total_price');
        }

        return view('admin.reports.index', compact('tab', 'startDate', 'endDate', 'totalOrders', 'totalRevenue', 'activeServices', 'chartData'));
    }

    private function pesananTab($query, $startDate, $endDate, $category, $status)
    {
        $tab = 'pesanan';
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
        
        return view('admin.reports.index', compact('tab', 'startDate', 'endDate', 'orders', 'services'));
    }

    private function pendapatanTab($query, $startDate, $endDate)
    {
        $tab = 'pendapatan';
        $orders = $query->where('status', '!=', 'pending')->latest()->get();
        $totalRevenue = $orders->sum('total_price');
        
        return view('admin.reports.index', compact('tab', 'startDate', 'endDate', 'orders', 'totalRevenue'));
    }

    private function labaRugiTab($query, $startDate, $endDate)
    {
        $tab = 'laba-rugi';
        $totalIncome = (clone $query)->where([['status', '!=', 'pending']])->sum('total_price') + 
                       \App\Models\Finance::where(['type' => 'income'])->whereBetween('date', [$startDate, $endDate])->sum('amount');
        $totalExpense = \App\Models\Finance::where(['type' => 'expense'])->whereBetween('date', [$startDate, $endDate])->sum('amount');
        $netBalance = $totalIncome - $totalExpense;
        
        return view('admin.reports.index', compact('tab', 'startDate', 'endDate', 'totalIncome', 'totalExpense', 'netBalance'));
    }

    private function terlarisTab($query, $startDate, $endDate)
    {
        $tab = 'terlaris';
        $completedOrders = $query->where('status', '!=', 'pending')->get();
        $popularServices = $completedOrders->groupBy('service_id')->map(function ($group) {
            return [
                'service' => $group->first()->service->name,
                'category' => $group->first()->service->category,
                'count' => $group->count(),
                'revenue' => $group->sum('total_price'  )
            ];
        })->sortByDesc('count')->values();
        
        return view('admin.reports.index', compact('tab', 'startDate', 'endDate', 'popularServices'));
    }

    private function pinjamanTab($startDate, $endDate)
    {
        $tab = 'pinjaman';
        $loans = Loan::with('user')->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])->latest()->get();
        $totalLoansApproved = $loans->where(fn($l) => $l->status === 'approved')->sum(fn($l) => $l->amount);
        $totalLoansPending = $loans->where(fn($l) => $l->status === 'pending')->sum(fn($l) => $l->amount);
        $totalLoansRejected = $loans->where(fn($l) => $l->status === 'rejected')->count();
        
        return view('admin.reports.index', compact('tab', 'startDate', 'endDate', 'loans', 'totalLoansApproved', 'totalLoansPending', 'totalLoansRejected'));
    }

    public function exportExcel(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());
        $category = $request->input('category');
        $status = $request->input('status');

        $query = \App\Models\Order::with(['user', 'service'])
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->where([['status', '!=', 'cancelled']]);

        if ($category) {
            $query->whereHas('service', function($q) use($category) {
                $q->where('category', $category);
            });
        }

        if ($status) {
            $query->where(['status' => $status]);
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

    public function exportRevenueExcel(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());

        $orders = \App\Models\Order::with(['user', 'service'])
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->where([['status', '!=', 'pending']])
            ->where([['status', '!=', 'cancelled']])
            ->get();

        $fileName = 'Laporan_Keuangan_' . $startDate . '_to_' . $endDate . '.csv';

        $headers = array(
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = array('Tanggal', 'No. Order', 'Pelanggan', 'Layanan', 'Metode Pembayaran', 'Nominal Pendapatan');

        $callback = function() use($orders, $columns) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($file, $columns);
            
            $total = 0;
            foreach ($orders as $order) {
                fputcsv($file, array(
                    $order->created_at->format('d/m/Y'),
                    '#' . $order->order_number,
                    $order->user->name,
                    $order->service->name,
                    strtoupper($order->payment_method),
                    $order->total_price
                ));
                $total += $order->total_price;
            }
            
            fputcsv($file, array('', '', '', '', 'TOTAL PENDAPATAN', $total));
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportRevenuePdf(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());

        $orders = \App\Models\Order::with(['user', 'service'])
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->where([['status', '!=', 'pending']])
            ->where([['status', '!=', 'cancelled']])
            ->get();

        $totalRevenue = $orders->sum(fn($o) => $o->total_price);

        return view('admin.reports.revenue-print', compact('orders', 'startDate', 'endDate', 'totalRevenue'));
    }

    public function employeeIndex(Request $request)
    {
        $user = auth()->user();
        
        $isFiltered = $request->filled('start_date') || $request->filled('end_date') || $request->filled('search');

        if ($isFiltered) {
            $startDate = $request->input('start_date', now()->toDateString());
            $endDate = $request->input('end_date', now()->toDateString());
        } else {
            // Default: Show current month's data
            $startDate = now()->startOfMonth()->toDateString();
            $endDate = now()->toDateString();
        }

        $search = $request->input('search');

        // Base query for current employee's tasks
        $query = \App\Models\Order::with(['user', 'service'])
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->where([['status', '!=', 'cancelled']]);

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
        $attendances = \App\Models\Attendance::where(['user_id' => $user->id])
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date', 'desc')
            ->get();

        // Performance Statistics
        $stats = [
            'total_completed' => $allOrders->where(fn($o) => $o->status === 'completed')->count(),
            'cleaning_done' => $allOrders->where(fn($o) => $o->status === 'completed')->filter(function($o) {
                return str_contains(strtolower($o->service->category ?? ''), 'clean');
            })->count(),
            'repair_done' => $allOrders->where(fn($o) => $o->status === 'completed')->filter(function($o) {
                return str_contains(strtolower($o->service->category ?? ''), 'repair');
            })->count(),
            'processing' => $allOrders->filter(fn($o) => in_array($o->status, ['processing', 'washing', 'drying', 'finishing']))->count(),
            'avg_rating' => round($allOrders->filter(fn($o) => $o->rating !== null)->avg(fn($o) => $o->rating) ?? 0, 1),
            'total_delivery' => $allOrders->where(fn($o) => $o->is_delivery)->count(),
        ];

        // Chart Data (Last 7 Days)
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $chartData['labels'][] = now()->subDays($i)->format('D');
            $chartData['counts'][] = \App\Models\Order::whereDate('created_at', $date)
                ->where(['status' => 'completed'])
                ->count();
        }

        $myTasks = $allOrders->filter(fn($o) => in_array($o->status, ['processing', 'washing', 'drying', 'finishing', 'ready', 'uncollected']));
        $historyTasks = $allOrders->where(fn($o) => $o->status === 'completed');
        $customerRatings = $allOrders->filter(fn($o) => $o->rating !== null);
        $deliveryTasks = $allOrders->where(fn($o) => $o->is_delivery);

        return view('employee.reports.index', compact(
            'allOrders', 'stats', 'chartData', 'startDate', 'endDate', 
            'attendances', 'myTasks', 'historyTasks', 'customerRatings', 'deliveryTasks'
        ));
    }

    public function exportAttendanceExcel(Request $request)
    {
        $user = auth()->user();
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());

        $attendances = \App\Models\Attendance::where(['user_id' => $user->id])
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

        $attendances = \App\Models\Attendance::where(['user_id' => $user->id])
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date', 'desc')
            ->get();

        return view('employee.reports.attendance-print', compact('user', 'attendances', 'startDate', 'endDate'));
    }
}
