@extends('layouts.premium-dashboard')

@section('page_title', 'Dashboard Ultra Admin')

@section('nav_items')
    <li class="nav-item"><a href="{{ route('admin.dashboard') }}" class="nav-link {{ Route::is('admin.dashboard') ? 'active' : '' }}">Dashboard</a></li>
    <li class="nav-item"><a href="{{ route('admin.orders.index') }}" class="nav-link {{ Route::is('admin.orders.index') ? 'active' : '' }}">Kelola Pesanan</a></li>
    <li class="nav-item"><a href="{{ route('admin.services.index') }}" class="nav-link {{ Route::is('admin.services.index') ? 'active' : '' }}">Kelola Layanan</a></li>
    <li class="nav-item"><a href="{{ route('admin.finances.index') }}" class="nav-link {{ Route::is('admin.finances.index') ? 'active' : '' }}">Keuangan</a></li>
    <li class="nav-item"><a href="{{ route('admin.employees.index') }}" class="nav-link {{ Route::is('admin.employees.index') ? 'active' : '' }}">Manajemen Staff</a></li>
    <li class="nav-item"><a href="{{ route('admin.reports.index') }}" class="nav-link {{ Route::is('admin.reports.index') ? 'active' : '' }}">Laporan</a></li>
@endsection

@section('content')
<style>
    .stat-card {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.05);
        border-radius: 16px;
        padding: 15px;
        transition: 0.3s;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .stat-card:hover {
        transform: translateY(-2px);
        background: rgba(255, 255, 255, 0.05);
    }
    .table-container {
        overflow-x: auto;
    }
    .staff-badge {
        width: 35px;
        height: 35px;
        border-radius: 10px;
        background: rgba(59, 130, 246, 0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        color: var(--primary);
        border: 1px solid rgba(59, 130, 246, 0.3);
    }
    .active-dot {
        width: 8px;
        height: 8px;
        background: var(--success);
        border-radius: 50%;
        display: inline-block;
        box-shadow: 0 0 8px rgba(16, 185, 129, 0.5);
    }
    
    .section-title {
        font-size: 1.1rem;
        font-weight: 700;
        margin-bottom: 1.2rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .section-title-line {
        width: 4px;
        height: 18px;
        background: var(--primary);
        border-radius: 4px;
    }

    /* Mobile specific hiding */
    .mobile-cards { display: none; }
    .desktop-table { display: block; }
    
    /* Layout Grids */
    .main-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 20px;
    }

    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 15px;
        margin-bottom: 25px;
    }
    
    @media (max-width: 1024px) {
        .kpi-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }
        .main-grid {
            grid-template-columns: 1.5fr 1fr;
            gap: 15px;
        }
    }
    
    @media (max-width: 768px) {
        .header-section { display: none !important; }
        .stat-card {
            padding: 8px 10px;
            border-radius: 10px;
            border-left-width: 3px !important;
            min-height: auto;
        }
        .stat-card p {
            font-size: 0.6rem !important;
            margin-bottom: 2px !important;
        }
        .stat-card h3 {
            font-size: 1.1rem !important;
            margin-top: 0 !important;
        }
        .desktop-table { display: none; }
        .mobile-cards { display: flex; flex-direction: column; gap: 10px; }
        .main-grid {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        .glass-card {
            padding: 15px !important;
            border-radius: 20px !important;
        }
    }
    @media (max-width: 576px) {
        .kpi-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
        }
    }
</style>



<!-- KPI Cards -->
<div class="kpi-grid">
    <div class="stat-card" style="border-left: 4px solid var(--success); background: rgba(16, 185, 129, 0.05);">
        <p style="color: var(--success); font-size: 0.7rem; font-weight: 800; text-transform: uppercase;">Total Pendapatan</p>
        <h3 style="font-size: 1.3rem; font-weight: 900; color: #fff;">Rp{{ number_format($totalRevenue, 0, ',', '.') }}</h3>
    </div>
    <div class="stat-card" style="border-left: 4px solid var(--warning); background: rgba(245, 158, 11, 0.05);">
        <p style="color: var(--warning); font-size: 0.7rem; font-weight: 800; text-transform: uppercase;">Pesanan Aktif</p>
        <h3 style="font-size: 1.5rem; font-weight: 900; color: #fff;">{{ array_sum($statusCounts) - ($statusCounts['completed'] ?? 0) }}</h3>
    </div>
    <div class="stat-card" style="border-left: 4px solid #a855f7; background: rgba(168, 85, 247, 0.05);">
        <p style="color: #a855f7; font-size: 0.7rem; font-weight: 800; text-transform: uppercase;">Pelanggan</p>
        <h3 style="font-size: 1.5rem; font-weight: 900; color: #fff;">{{ $totalCustomers }}</h3>
    </div>
    <div class="stat-card" style="border-left: 4px solid #3b82f6; background: rgba(59, 130, 246, 0.05);">
        <p style="color: #3b82f6; font-size: 0.7rem; font-weight: 800; text-transform: uppercase;">Staf Aktif</p>
        <h3 style="font-size: 1.5rem; font-weight: 900; color: #fff;">{{ $activeStaff->count() }} / {{ $totalEmployees }}</h3>
    </div>
</div>

<div class="main-grid">
    <!-- LEFT COLUMN -->
    <div>
        <!-- Revenue Chart -->
        <div class="glass-card" style="margin-bottom: 20px; padding: 25px; border-radius: 28px;">
            <div class="section-title" style="display: flex; justify-content: space-between; align-items: center;">
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <div class="section-title-line"></div>
                    Trend Pendapatan
                </div>
                <select onchange="window.location.href='?filter=' + this.value" style="background: rgba(255,255,255,0.05); color: #fff; border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; padding: 5px 10px; font-size: 0.8rem; outline: none; cursor: pointer;">
                    <option value="day" {{ request('filter') == 'day' ? 'selected' : '' }}>Hari Ini (Per Jam)</option>
                    <option value="week" {{ request('filter', 'week') == 'week' ? 'selected' : '' }}>7 Hari Terakhir</option>
                    <option value="month" {{ request('filter') == 'month' ? 'selected' : '' }}>1 Bulan Terakhir</option>
                    <option value="year" {{ request('filter') == 'year' ? 'selected' : '' }}>1 Tahun Terakhir</option>
                </select>
            </div>
            <div style="height: 250px;">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <!-- Recent Orders -->
        <div>
            <div class="section-title" style="justify-content: space-between; margin-bottom: 15px;">
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <div class="section-title-line" style="background: var(--warning);"></div>
                    Pesanan Terbaru
                </div>
                <a href="{{ route('admin.orders.index') }}" style="color: var(--primary); font-size: 0.8rem; font-weight: 700; text-decoration: none;">Lihat Semua</a>
            </div>

            <!-- Desktop Table -->
            <div class="glass-card desktop-table" style="padding: 25px; border-radius: 28px;">
                <table class="table-custom" style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th style="padding: 15px; font-size: 0.75rem; text-transform: uppercase; color: #64748b; border-bottom: 1px solid rgba(255,255,255,0.05); text-align: left;">No Order</th>
                            <th style="padding: 15px; font-size: 0.75rem; text-transform: uppercase; color: #64748b; border-bottom: 1px solid rgba(255,255,255,0.05); text-align: left;">Pelanggan</th>
                            <th style="padding: 15px; font-size: 0.75rem; text-transform: uppercase; color: #64748b; border-bottom: 1px solid rgba(255,255,255,0.05); text-align: left;">Layanan</th>
                            <th style="padding: 15px; font-size: 0.75rem; text-transform: uppercase; color: #64748b; border-bottom: 1px solid rgba(255,255,255,0.05); text-align: left;">Status</th>
                            <th style="padding: 15px; font-size: 0.75rem; text-transform: uppercase; color: #64748b; border-bottom: 1px solid rgba(255,255,255,0.05); text-align: left;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentOrders as $order)
                        <tr>
                            <td style="padding: 15px; border-bottom: 1px solid rgba(255,255,255,0.02); font-weight: 800; color: var(--primary);">#{{ $order->order_number }}</td>
                            <td style="padding: 15px; border-bottom: 1px solid rgba(255,255,255,0.02);">{{ $order->user->name }}</td>
                            <td style="padding: 15px; border-bottom: 1px solid rgba(255,255,255,0.02); font-weight: 600;">{{ $order->service->name }}</td>
                            <td style="padding: 15px; border-bottom: 1px solid rgba(255,255,255,0.02);">
                                <span style="background: rgba(16, 185, 129, 0.1); color: #10b981; padding: 4px 10px; border-radius: 6px; font-size: 0.75rem; font-weight: 800;">
                                    {{ strtoupper($order->status) }}
                                </span>
                            </td>
                            <td style="padding: 15px; border-bottom: 1px solid rgba(255,255,255,0.02); font-weight: 700;">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Mobile Cards (Android Look) -->
            <div class="mobile-cards">
                @foreach($recentOrders as $order)
                <div class="glass-card" style="padding: 1rem; border-left: 3px solid var(--warning); display: flex; justify-content: space-between; align-items: center; background: rgba(255, 255, 255, 0.02);">
                    <div style="display: flex; flex-direction: column; gap: 4px;">
                        <span style="font-weight: 900; color: var(--warning); font-size: 0.85rem;">#{{ $order->order_number }}</span>
                        <span style="font-weight: 700; color: #fff; font-size: 0.9rem;">{{ Str::limit($order->user->name, 15) }}</span>
                        <span style="font-size: 0.7rem; opacity: 0.6;">{{ $order->service->name }}</span>
                    </div>
                    <div style="text-align: right; display: flex; flex-direction: column; align-items: flex-end; gap: 5px;">
                        <span style="background: rgba(16, 185, 129, 0.1); color: #10b981; padding: 3px 8px; border-radius: 6px; font-size: 0.65rem; font-weight: 800;">{{ strtoupper($order->status) }}</span>
                        <span style="font-size: 0.8rem; font-weight: 800;">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- RIGHT COLUMN -->
    <div>
        <!-- Active Staff -->
        <div class="glass-card" style="margin-bottom: 20px; padding: 25px; border-radius: 28px;">
            <div class="section-title">
                <div class="section-title-line" style="background: var(--success);"></div>
                Staf Aktif <span class="active-dot" style="margin-left: 5px;"></span>
            </div>
            <div style="display: flex; flex-direction: column; gap: 10px;">
                @forelse($activeStaff as $attendance)
                <div style="display: flex; align-items: center; justify-content: space-between; background: rgba(255,255,255,0.02); padding: 10px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.05);">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <div class="staff-badge">{{ substr($attendance->user->name, 0, 1) }}</div>
                        <div>
                            <div style="font-weight: 700; font-size: 0.85rem;">{{ $attendance->user->name }}</div>
                            <div style="font-size: 0.7rem; color: var(--success); font-weight: 600;">Online</div>
                        </div>
                    </div>
                    <div style="font-size: 0.75rem; opacity: 0.5;">{{ date('H:i', strtotime($attendance->clock_in)) }}</div>
                </div>
                @empty
                <div style="padding: 1.5rem; text-align: center; border-radius: 12px; border: 1px dashed rgba(255,255,255,0.05);">
                    <p style="opacity: 0.3; font-size: 0.8rem;">Tidak ada staf bertugas.</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Status Workload (Android Look Grid) -->
        <div>
            <div class="section-title">
                <div class="section-title-line" style="background: #3b82f6;"></div>
                Status Antrian
            </div>
            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px;">
                @foreach([
                    ['status' => 'pending', 'label' => 'Menunggu', 'count' => $statusCounts['pending'] ?? 0, 'color' => 'var(--warning)', 'bg' => 'rgba(245, 158, 11, 0.1)'],
                    ['status' => 'processing', 'label' => 'Proses', 'count' => $statusCounts['processing'] ?? 0, 'color' => '#3b82f6', 'bg' => 'rgba(59, 130, 246, 0.1)'],
                    ['status' => 'ready', 'label' => 'Siap', 'count' => $statusCounts['ready'] ?? 0, 'color' => 'var(--success)', 'bg' => 'rgba(16, 185, 129, 0.1)'],
                    ['status' => 'completed', 'label' => 'Selesai', 'count' => $statusCounts['completed'] ?? 0, 'color' => '#a855f7', 'bg' => 'rgba(168, 85, 247, 0.1)']
                ] as $st)
                <a href="{{ route('admin.orders.index', ['queue' => 1, 'status' => $st['status']]) }}" style="text-decoration: none; background: {{ $st['bg'] }}; border: 1px solid rgba(255,255,255,0.05); padding: 1rem; border-radius: 16px; transition: transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                    <p style="font-size: 0.65rem; opacity: 0.8; text-transform: uppercase; font-weight: 700; margin-bottom: 5px; color: #fff;">{{ $st['label'] }}</p>
                    <div style="font-size: 1.5rem; font-weight: 900; color: {{ $st['color'] }};">{{ $st['count'] }}</div>
                </a>
                @endforeach
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('revenueChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($revenueTrends->pluck('date')),
            datasets: [{
                label: 'Pendapatan',
                data: @json($revenueTrends->pluck('total')),
                borderColor: '#f97316',
                backgroundColor: 'rgba(249, 115, 22, 0.1)',
                fill: true,
                tension: 0.4,
                borderWidth: 3,
                pointRadius: 3,
                pointBackgroundColor: '#f97316'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { color: 'rgba(255,255,255,0.05)' }, ticks: { color: '#64748b', font: { size: 10 } } },
                x: { grid: { display: false }, ticks: { color: '#64748b', font: { size: 10 } } }
            }
        }
    });

</script>
@endsection
