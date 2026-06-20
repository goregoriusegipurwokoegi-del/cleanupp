<?php

namespace App\Http\Controllers;

use App\Models\Finance;
use App\Models\Order;
use Illuminate\Http\Request;

class FinanceController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'buku-kas');
        $filter = $request->query('filter', 'monthly');
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());

        $dateOrdersQuery = Order::where(['payment_status' => 'paid'])->whereBetween('updated_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        $dateManualQuery = Finance::whereBetween('date', [$startDate, $endDate]);

        $ordersQuery = Order::where(['payment_status' => 'paid']);
        $manualFinanceQuery = Finance::query();

        if ($filter == 'daily') {
            $ordersQuery->whereDate('updated_at', now());
            $manualFinanceQuery->where('date', now()->toDateString());
        } elseif ($filter == 'monthly') {
            $ordersQuery->whereMonth('updated_at', now()->month)->whereYear('updated_at', now()->year);
            $manualFinanceQuery->whereMonth('date', now()->month)->whereYear('date', now()->year);
        } elseif ($filter == 'yearly') {
            $ordersQuery->whereYear('updated_at', now()->year);
            $manualFinanceQuery->whereYear('date', now()->year);
        }

        $finances = collect();
        $totalIncome = 0;
        $totalExpense = 0;
        $netBalance = 0;
        $chartData = ['labels' => [], 'income' => [], 'expense' => []];

        if ($tab == 'buku-kas' || $tab == 'pemasukan' || $tab == 'pengeluaran') {
            $orders = $ordersQuery->get()->map(function($order) {
                return (object) ['id' => 'ord_'.$order->id, 'date' => $order->updated_at->format('Y-m-d'), 'description' => 'Pesanan #' . $order->order_number . ' (' . $order->user->name . ')', 'type' => 'income', 'amount' => $order->total_price, 'is_manual' => false];
            });
            $manuals = $manualFinanceQuery->get()->map(function($fin) {
                return (object) ['id' => $fin->id, 'date' => $fin->date, 'description' => $fin->description, 'type' => $fin->type, 'amount' => $fin->amount, 'is_manual' => true, 'model' => $fin];
            });
            
            if ($tab == 'pemasukan') {
                $finances = $orders->merge($manuals->where(fn($item) => $item->type === 'income'))->sortByDesc('date');
            } elseif ($tab == 'pengeluaran') {
                $finances = $manuals->where(fn($item) => $item->type === 'expense')->sortByDesc('date');
            } else {
                $finances = $orders->merge($manuals)->sortByDesc('date');
            }

            $totalIncome = $ordersQuery->sum('total_price') + (clone $manualFinanceQuery)->where(['type' => 'income'])->sum('amount');
            $totalExpense = (clone $manualFinanceQuery)->where(['type' => 'expense'])->sum('amount');
            $netBalance = $totalIncome - $totalExpense;

        } elseif ($tab == 'laba-rugi') {
            $totalIncome = $dateOrdersQuery->sum('total_price') + (clone $dateManualQuery)->where(['type' => 'income'])->sum('amount');
            $totalExpense = (clone $dateManualQuery)->where(['type' => 'expense'])->sum('amount');
            $netBalance = $totalIncome - $totalExpense;

        } elseif ($tab == 'grafik') {
            for ($i = 6; $i >= 0; $i--) {
                $d = now()->subDays($i)->format('Y-m-d');
                $chartData['labels'][] = now()->subDays($i)->format('d M');
                
                $incOrders = Order::where(['payment_status' => 'paid'])->whereDate('updated_at', $d)->sum('total_price');
                $incManual = Finance::where(['type' => 'income'])->where('date', $d)->sum('amount');
                $expManual = Finance::where(['type' => 'expense'])->where('date', $d)->sum('amount');
                
                $chartData['income'][] = $incOrders + $incManual;
                $chartData['expense'][] = $expManual;
            }
        }

        return view('admin.finances.index', compact(
            'finances', 'totalIncome', 'totalExpense', 'netBalance', 'filter', 'tab', 'startDate', 'endDate', 'chartData'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required|integer|min:1',
            'category' => 'nullable|string|max:100',
            'description' => 'required|string|max:255',
            'date' => 'required|date',
            'type' => 'required|in:income,expense'
        ]);

        Finance::create($request->all());

        return back()->with('success', 'Data keuangan berhasil ditambahkan.');
    }

    public function destroy(Finance $finance)
    {
        $finance->delete();
        return back()->with('success', 'Data keuangan berhasil dihapus.');
    }

    public function exportCashbookExcel(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());

        $orders = Order::where(['payment_status' => 'paid'])
            ->whereBetween('updated_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->get()->map(function($order) {
                return (object) ['date' => $order->updated_at->format('Y-m-d'), 'description' => 'Pesanan #' . $order->order_number, 'type' => 'income', 'amount' => $order->total_price];
            });
        
        $manuals = Finance::whereBetween('date', [$startDate, $endDate])->get()->map(function($fin) {
            return (object) ['date' => $fin->date, 'description' => $fin->description, 'type' => $fin->type, 'amount' => $fin->amount];
        });

        $finances = $orders->merge($manuals)->sortBy('date'); // ascending for reports usually

        $fileName = 'Buku_Kas_' . $startDate . '_to_' . $endDate . '.csv';

        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use($finances) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($file, ['Tanggal', 'Kategori', 'Deskripsi', 'Pemasukan (Rp)', 'Pengeluaran (Rp)', 'Saldo (Rp)']);
            
            $saldo = 0;
            foreach ($finances as $fin) {
                $masuk = $fin->type == 'income' ? $fin->amount : 0;
                $keluar = $fin->type == 'expense' ? $fin->amount : 0;
                $saldo += ($masuk - $keluar);
                
                fputcsv($file, [
                    \Carbon\Carbon::parse($fin->date)->format('d/m/Y'),
                    isset($fin->category) ? $fin->category : '-',
                    $fin->description,
                    $masuk,
                    $keluar,
                    $saldo
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
