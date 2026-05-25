<?php

namespace App\Http\Controllers;

use App\Models\Finance;
use App\Models\Order;
use Illuminate\Http\Request;

class FinanceController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->query('filter', 'monthly');

        $ordersQuery = Order::where('payment_status', 'paid');
        $manualFinanceQuery = Finance::query();

        if ($filter == 'daily') {
            $ordersQuery->whereDate('created_at', now());
            $manualFinanceQuery->whereDate('date', now());
        } elseif ($filter == 'monthly') {
            $ordersQuery->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year);
            $manualFinanceQuery->whereMonth('date', now()->month)->whereYear('date', now()->year);
        } elseif ($filter == 'yearly') {
            $ordersQuery->whereYear('created_at', now()->year);
            $manualFinanceQuery->whereYear('date', now()->year);
        }

        // Calculate totals
        $orderIncome = $ordersQuery->sum('total_price');
        $manualIncome = (clone $manualFinanceQuery)->where('type', 'income')->sum('amount');
        $totalIncome = $orderIncome + $manualIncome;
        
        $totalExpense = (clone $manualFinanceQuery)->where('type', 'expense')->sum('amount');
        $netBalance = $totalIncome - $totalExpense;

        // Combine for table view
        $orders = $ordersQuery->get()->toBase()->map(function($order) {
            return (object) [
                'date' => $order->created_at->format('Y-m-d'),
                'description' => 'Pesanan #' . $order->order_number . ' (' . $order->user->name . ')',
                'type' => 'income',
                'amount' => $order->total_price,
                'is_manual' => false
            ];
        });

        $manuals = $manualFinanceQuery->get()->toBase()->map(function($fin) {
            return (object) [
                'id' => $fin->id,
                'date' => $fin->date,
                'description' => $fin->description,
                'type' => $fin->type,
                'amount' => $fin->amount,
                'is_manual' => true
            ];
        });

        $finances = $orders->merge($manuals)->sortByDesc('date');

        return view('admin.finances.index', compact(
            'finances', 'totalIncome', 'totalExpense', 'netBalance', 'filter'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required|integer|min:1',
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
}
