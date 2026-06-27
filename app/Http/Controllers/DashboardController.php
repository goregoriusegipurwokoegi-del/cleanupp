<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $role = auth()->user()->role;

        return match($role) {
            'admin' => redirect()->route('admin.dashboard'),
            'employee' => redirect()->route('employee.dashboard'),
            default => redirect()->route('customer.dashboard'),
        };
    }

    public function admin()
    {
        // Total Order Revenue (KPI Cards) - Count all accepted/processed orders as projected/current revenue
        $orderToday = Order::where(['payment_status' => 'paid'])
            ->where([['status', '!=', 'cancelled']])
            ->whereBetween('updated_at', [now()->startOfDay(), now()->endOfDay()])
            ->sum('total_price');
            
        $orderMonthly = Order::where(['payment_status' => 'paid'])
            ->where([['status', '!=', 'cancelled']])
            ->whereMonth('updated_at', now()->month)
            ->whereYear('updated_at', now()->year)
            ->sum('total_price');

        // Manual Finance Entries
        $manualToday = \App\Models\Finance::where(['type' => 'income'])->where('date', now()->toDateString())->sum('amount');
        $manualMonthly = \App\Models\Finance::where(['type' => 'income'])
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->sum('amount');
            
        $orderTotal = Order::where(['payment_status' => 'paid'])
            ->where([['status', '!=', 'cancelled']])
            ->sum('total_price');
        $manualTotal = \App\Models\Finance::where(['type' => 'income'])->sum('amount');

        $todayRevenue = $orderToday + $manualToday;
        $monthlyRevenue = $orderMonthly + $manualMonthly;
        $totalRevenue = $orderTotal + $manualTotal;
        $totalOrdersCount = Order::where([['status', '!=', 'cancelled']])->count();
        $totalCustomers = User::where(['role' => 'customer'])->count();
        $totalEmployees = User::where(['role' => 'employee'])->count();

        $statusCounts = [
            'pending' => Order::where(['status' => 'pending'])->count(),
            'processing' => Order::whereIn('status', ['processing', 'washing', 'drying', 'finishing'])->count(),
            'ready' => Order::where(['status' => 'ready'])->count(),
            'completed' => Order::whereIn('status', ['completed', 'picked_up'])->count(),
        ];

        // Grafik Pendapatan & Transaksi dengan Filter
        $filter = request('filter', 'week');
        $limit = 7;
        $dateFormat = 'DATE(updated_at)';
        $dateCreatedFormat = 'DATE(created_at)';
        
        $revenueQuery = Order::where(['payment_status' => 'paid'])->where([['status', '!=', 'cancelled']]);
        $transactionQuery = Order::where([['status', '!=', 'cancelled']]);

        if ($filter === 'day') {
            $limit = 24;
            $dateFormat = 'DATE_FORMAT(updated_at, "%H:00")';
            $dateCreatedFormat = 'DATE_FORMAT(created_at, "%H:00")';
            $revenueQuery->whereBetween('updated_at', [now()->startOfDay(), now()->endOfDay()]);
            $transactionQuery->whereBetween('created_at', [now()->startOfDay(), now()->endOfDay()]);
        } elseif ($filter === 'month') {
            $limit = 30;
        } elseif ($filter === 'year') {
            $limit = 12;
            $dateFormat = 'DATE_FORMAT(updated_at, "%Y-%m")';
            $dateCreatedFormat = 'DATE_FORMAT(created_at, "%Y-%m")';
        }

        $revenueTrends = (clone $revenueQuery)
            ->selectRaw($dateFormat . ' as date, SUM(total_price) as total')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->limit($limit)
            ->get()
            ->reverse();

        $transactionTrends = (clone $transactionQuery)
            ->selectRaw($dateCreatedFormat . ' as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->limit($limit)
            ->get()
            ->reverse();

        // Layanan Terpopuler (Hanya yang memiliki pesanan)
        $popularServices = Order::selectRaw('service_id, COUNT(*) as count')
            ->groupBy('service_id')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->with('service')
            ->get()
            ->filter(function($item) {
                return $item->service !== null;
            });

        // Aktivitas Terbaru (Hanya Hari Ini)
        $recentOrders = Order::with(['user', 'service'])
            ->whereBetween('created_at', [now()->startOfDay(), now()->endOfDay()])
            ->where([['status', '!=', 'cancelled']])
            ->latest()
            ->limit(10)
            ->get();

        // Staff Aktif (Absensi hari ini yang belum pulang)
        $activeStaff = \App\Models\Attendance::where('date', now()->toDateString())
            ->whereNull('clock_out')
            ->with('user')
            ->get();

        return view('dashboards.admin', compact(
            'totalRevenue', 'todayRevenue', 'monthlyRevenue', 'totalOrdersCount', 'totalCustomers', 'totalEmployees',
            'statusCounts', 'revenueTrends', 'transactionTrends', 'popularServices', 'recentOrders', 'activeStaff'
        ));
    }

    public function employee()
    {
        // Fetch incoming orders (Pending)
        $incomingOrders = Order::with(['user', 'service'])
            ->where(['status' => 'pending'])
            ->latest()
            ->limit(5)
            ->get();

        // Fetch tasks for the logged-in employee (In progress)
        $tasks = Order::with(['user', 'service'])
            ->whereIn('status', ['processing', 'finishing', 'ready'])
            ->latest()
            ->limit(5)
            ->get();
            
        $pendingOrdersCount = Order::where(['status' => 'pending'])->count();
        $weeklyCompletedCount = Order::where(['status' => 'completed'])
            ->whereBetween('updated_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->count();

        // Specific counts for Cleaning
        $cleaningCounts = [
            'queue' => Order::whereHas('service', fn($q) => $q->where(['category' => 'cleaning']))
                ->where(['status' => 'processing'])->count(),
            'washing' => Order::whereHas('service', fn($q) => $q->where(['category' => 'cleaning']))
                ->where(['status' => 'washing'])->count(),
            'drying' => Order::whereHas('service', fn($q) => $q->where(['category' => 'cleaning']))
                ->where(['status' => 'finishing'])->count(),
            'ready' => Order::whereHas('service', fn($q) => $q->where(['category' => 'cleaning']))
                ->where(['status' => 'ready'])->count(),
            'uncollected' => Order::whereHas('service', fn($q) => $q->where(['category' => 'cleaning']))
                ->where(['status' => 'uncollected'])->count(),
        ];

        // Specific counts for Repair
        $repairCounts = [
            'queue' => Order::whereHas('service', fn($q) => $q->where(['category' => 'repair']))
                ->where(['status' => 'processing'])->count(),
            'processing' => Order::whereHas('service', fn($q) => $q->where(['category' => 'repair']))
                ->where(['status' => 'washing'])->count(),
            'finishing' => Order::whereHas('service', fn($q) => $q->where(['category' => 'repair']))
                ->where(['status' => 'finishing'])->count(),
            'ready' => Order::whereHas('service', fn($q) => $q->where(['category' => 'repair']))
                ->where(['status' => 'ready'])->count(),
            'uncollected' => Order::whereHas('service', fn($q) => $q->where(['category' => 'repair']))
                ->where(['status' => 'uncollected'])->count(),
        ];

        $todayAttendance = \App\Models\Attendance::where([
            'user_id' => auth()->id(),
            'date' => now()->toDateString()
        ])->first();

        $isClockedIn = !is_null($todayAttendance);
        $isClockedOut = $todayAttendance && !is_null($todayAttendance->clock_out);

        return view('dashboards.employee', compact(
            'incomingOrders', 'tasks', 'pendingOrdersCount', 'weeklyCompletedCount', 'cleaningCounts', 'repairCounts',
            'todayAttendance', 'isClockedIn', 'isClockedOut'
        ));
    }

    public function customer()
    {
        $orders = auth()->user()->orders()->with('service')->latest()->get();
        return view('dashboards.customer', compact('orders'));
    }
}
