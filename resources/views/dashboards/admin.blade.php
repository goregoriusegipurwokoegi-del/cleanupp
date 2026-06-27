@extends('layouts.premium-dashboard')

@section('page_title', 'Dashboard Admin')

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
/* ================================================
   ADMINLTE 3 V2 DARK THEME REDESIGN
================================================ */

/* Main layout background overrides removed to rely on layout variables */

/* Info Box Component */
.info-box-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
    margin-bottom: 24px;
}
.info-box-v2 {
    display: flex;
    background: var(--card-bg);
    border-radius: 4px;
    min-height: 85px;
    border: 1px solid var(--border-color);
    box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
    overflow: hidden;
}
.info-box-icon-v2 {
    width: 70px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 1.8rem;
    flex-shrink: 0;
}
.info-box-content-v2 {
    flex: 1;
    padding: 10px 15px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    color: var(--text);
    min-width: 0;
}
.info-box-text-v2 {
    font-size: 0.78rem;
    text-transform: uppercase;
    color: var(--text-secondary);
    margin-bottom: 2px;
    font-weight: 600;
    letter-spacing: 0.5px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.info-box-number-v2 {
    font-size: 1.3rem;
    font-weight: 700;
    line-height: 1.2;
}
.info-box-sub-v2 {
    font-size: 0.72rem;
    color: var(--text-secondary);
    margin-top: 4px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Card v2 Component (AdminLTE 3 Dynamic Card) */
.card-v2 {
    background: var(--card-bg);
    border: 1px solid var(--border-color);
    border-radius: 4px;
    margin-bottom: 24px;
    box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
    color: var(--text);
}
.card-v2-header {
    background: transparent;
    border-bottom: 1px solid var(--border-color);
    padding: 12px 18px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.card-v2-title {
    font-size: 0.95rem;
    font-weight: 600;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 8px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.card-v2-body {
    padding: 20px;
}

/* Monthly Recap Layout */
.recap-grid {
    display: grid;
    grid-template-columns: 2.2fr 1fr;
    gap: 24px;
}

/* Goal Completion / Progress bar styling */
.goal-title-v2 {
    font-size: 0.8rem;
    font-weight: 700;
    text-align: center;
    margin-bottom: 18px;
    color: var(--text);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.progress-group-v2 {
    margin-bottom: 16px;
}
.progress-group-v2:last-child {
    margin-bottom: 0;
}
.progress-header-v2 {
    display: flex;
    justify-content: space-between;
    font-size: 0.78rem;
    font-weight: 600;
    color: var(--text-secondary);
    margin-bottom: 5px;
}
.progress-val-v2 {
    font-weight: 700;
    color: var(--text);
}
.progress-track-v2 {
    height: 8px;
    background: var(--surface-variant);
    border-radius: 4px;
    overflow: hidden;
}
.progress-fill-v2 {
    height: 100%;
    border-radius: 4px;
    transition: width 1.4s cubic-bezier(0.4,0,0.2,1);
}

/* Table styling for dark mode */
.table-v2 {
    width: 100%;
    margin-bottom: 0;
    color: var(--text);
    border-collapse: collapse;
}
.table-v2 th {
    border-bottom: 1.5px solid var(--border-color);
    font-size: 0.75rem;
    text-transform: uppercase;
    font-weight: 700;
    color: var(--text-secondary);
    padding: 12px 15px;
    text-align: left;
}
.table-v2 td {
    padding: 12px 15px;
    border-bottom: 1px solid var(--border-color);
    font-size: 0.85rem;
    vertical-align: middle;
}
.table-v2 tr:last-child td {
    border-bottom: none;
}
.table-v2 tr:hover {
    background: var(--surface-variant);
}

/* Status Pill in Table */
.status-badge-v2 {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 3px 8px;
    border-radius: 4px;
    font-size: 0.68rem;
    font-weight: 700;
    text-transform: uppercase;
}
.dot-v2 {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    display: inline-block;
}

/* Status colors */
.sb-pending  { color:#ffc107; background:rgba(255, 193, 7, 0.15); border: 1px solid rgba(255, 193, 7, 0.25); }
.sb-process  { color:#17a2b8; background:rgba(23, 162, 184, 0.15); border: 1px solid rgba(23, 162, 184, 0.25); }
.sb-ready    { color:#28a745; background:rgba(40, 167, 70, 0.15); border: 1px solid rgba(40, 167, 70, 0.25); }
.sb-done     { color:#6f42c1; background:rgba(111, 66, 193, 0.15); border: 1px solid rgba(111, 66, 193, 0.25); }
.sb-cancel   { color:#dc3545; background:rgba(220, 53, 69, 0.15); border: 1px solid rgba(220, 53, 69, 0.25); }

/* Right Column Cards */
.donut-legend-v2 {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 8px;
    margin-top: 15px;
}
.legend-item-v2 {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 10px;
    border-radius: 4px;
    border: 1px solid var(--border-color);
    background: var(--surface-variant);
    color: var(--text-secondary);
}
.legend-dot-v2 { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
.legend-val-v2 { font-weight: 700; font-size: 0.9rem; margin-left: auto; color: var(--text); }

/* Staff Items Dynamic */
.staff-item-v2 {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 12px;
    border-radius: 4px;
    background: var(--surface-variant);
    border: 1px solid var(--border-color);
    margin-bottom: 8px;
    transition: 0.2s;
}
.staff-item-v2:last-child {
    margin-bottom: 0;
}
.staff-item-v2:hover {
    background: var(--surface-variant);
    opacity: 0.85;
}
.staff-avatar-v2 {
    width: 35px;
    height: 35px;
    border-radius: 50%;
    background: linear-gradient(135deg, #17a2b8, #117a8b);
    color: #fff;
    font-weight: 700;
    font-size: 0.85rem;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.live-dot-v2 {
    width: 8px;
    height: 8px;
    background: #28a745;
    border-radius: 50%;
    box-shadow: 0 0 0 3px rgba(40, 167, 70, 0.2);
    animation: live-pulse-v2 2s infinite;
    flex-shrink: 0;
    margin-left: auto;
}
@keyframes live-pulse-v2 {
    0%, 100% { box-shadow: 0 0 0 3px rgba(40, 167, 70, 0.2); }
    50% { box-shadow: 0 0 0 6px rgba(40, 167, 70, 0.05); }
}

/* Filter drop-down styling */
.select-v2 {
    background: var(--surface-variant);
    color: var(--text);
    border: 1px solid var(--border-color);
    border-radius: 4px;
    padding: 4px 10px;
    font-size: 0.8rem;
    outline: none;
    cursor: pointer;
    font-weight: 600;
}
.select-v2:focus {
    border-color: #17a2b8;
}

/* Link Button styling */
.btn-v2-link {
    font-size: 0.78rem;
    color: #17a2b8;
    text-decoration: none;
    font-weight: 600;
}
.btn-v2-link:hover {
    color: #138496;
    text-decoration: underline;
}

/* Quick Action Buttons AdminLTE style */
.quick-btn-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 14px;
    margin-bottom: 24px;
}
.quick-btn-v2 {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    padding: 14px;
    border-radius: 4px;
    background: var(--card-bg);
    color: var(--text);
    border: 1px solid var(--border-color);
    text-decoration: none;
    font-weight: 600;
    font-size: 0.85rem;
    transition: 0.2s;
    box-shadow: 0 1px 3px rgba(0,0,0,0.12);
}
.quick-btn-v2:hover {
    background: var(--surface-variant);
    color: var(--text);
    transform: translateY(-1px);
    box-shadow: 0 3px 6px rgba(0,0,0,0.2);
}

/* Responsive adjustments */
.main-grid-v2 {
    display: grid;
    grid-template-columns: 1.7fr 1fr;
    gap: 20px;
    margin-bottom: 24px;
}
.mobile-order-cards { display: none; }
.desktop-orders-table { display: block; }
.mobile-order-card {
    background: var(--card-bg);
    border: 1px solid var(--border-color);
    border-radius: 4px;
    padding: 14px 16px;
    margin-bottom: 9px;
    border-left: 4px solid #17a2b8;
}

@media (max-width: 1200px) {
    .info-box-grid { grid-template-columns: repeat(2, 1fr); }
    .quick-btn-grid { grid-template-columns: repeat(2, 1fr); }
    .main-grid-v2 { grid-template-columns: 1fr; }
}
@media (max-width: 768px) {
    .recap-grid { grid-template-columns: 1fr; }
    /* Info boxes: 2-per-row on mobile for compact display */
    .info-box-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
        margin-bottom: 14px;
    }
    /* Shrink the colored icon block on mobile */
    .info-box-icon-v2 {
        width: 52px;
        font-size: 1.3rem;
    }
    .info-box-content-v2 {
        padding: 8px 10px;
    }
    .info-box-number-v2 {
        font-size: 1.1rem;
    }
    .info-box-text-v2 {
        font-size: 0.7rem;
    }
    .info-box-sub-v2 {
        font-size: 0.65rem;
    }
    /* info-box card: rounder corners on mobile */
    .info-box-v2 {
        border-radius: 12px;
        min-height: 70px;
    }
    /* card-v2: rounder, compact padding on mobile */
    .card-v2 {
        border-radius: 16px;
        margin-bottom: 14px;
    }
    .card-v2-header {
        padding: 10px 14px;
    }
    .card-v2-title {
        font-size: 0.82rem;
    }
    .card-v2-body {
        padding: 14px;
    }
    /* Quick actions: keep 2 per row, not 1 */
    .quick-btn-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
        margin-bottom: 14px;
    }
    .quick-btn-v2 {
        padding: 12px 8px;
        font-size: 0.8rem;
        gap: 6px;
        flex-direction: column;
        text-align: center;
        border-radius: 12px;
    }
    .quick-btn-v2 i {
        font-size: 1.4rem !important;
    }
    .desktop-orders-table { display: none; }
    .mobile-order-cards { 
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
    }
    .mobile-order-card {
        margin-bottom: 0 !important;
        padding: 10px;
        border: 1px solid var(--border-color);
        border-radius: 8px;
        background: var(--surface-variant);
    }
    /* Chart height cap on mobile */
    .recap-chart-container {
        height: 180px !important;
    }
    /* Status donut chart: smaller on mobile */
    .status-donut-container {
        height: 150px !important;
    }
    /* Target Bulanan Stats grid on mobile */
    .target-stats-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
    }
    .progress-group-v2 {
        margin-bottom: 0;
        background: rgba(255,255,255,0.05);
        padding: 10px;
        border-radius: 8px;
    }
    .goal-title-v2 {
        margin-bottom: 12px;
        font-size: 0.75rem;
    }
    /* main-grid gap */
    .main-grid-v2 {
        gap: 0;
    }
    /* Legend grid 2x2 stays */
    .donut-legend-v2 {
        grid-template-columns: 1fr 1fr;
        gap: 6px;
        margin-top: 10px;
    }
    .legend-item-v2 {
        padding: 6px 8px;
        font-size: 0.78rem;
    }
    /* Staff items */
    .staff-item-v2 {
        padding: 8px 10px;
    }
    /* Mobile order card styling */
    .mobile-order-card {
        border-radius: 10px;
        padding: 10px 12px;
        margin-bottom: 7px;
    }
}
</style>

{{-- ===== ADMINLTE 3 HEADER BLOCK REMOVED ===== --}}

{{-- ===== ADMINLTE 3 INFO BOXES ===== --}}
<div class="info-box-grid">
    {{-- Total Revenue Box (Teal bg) --}}
    <div class="info-box-v2">
        <span class="info-box-icon-v2" style="background-color: #17a2b8;">
            <i class="bi bi-wallet2"></i>
        </span>
        <div class="info-box-content-v2">
            <span class="info-box-text-v2">Total Pendapatan</span>
            <span class="info-box-number-v2" id="kpi-revenue">Rp 0</span>
            <span class="info-box-sub-v2">Bulan ini: Rp {{ number_format($monthlyRevenue, 0, ',', '.') }}</span>
        </div>
    </div>

    {{-- Active Orders Box (Red bg) --}}
    @php $activeOrdersCount = array_sum($statusCounts) - ($statusCounts['completed'] ?? 0); @endphp
    <div class="info-box-v2">
        <span class="info-box-icon-v2" style="background-color: #dc3545;">
            <i class="bi bi-cart3"></i>
        </span>
        <div class="info-box-content-v2">
            <span class="info-box-text-v2">Pesanan Aktif</span>
            <span class="info-box-number-v2" id="kpi-active">0</span>
            <span class="info-box-sub-v2">Total: {{ $totalOrdersCount }} Order</span>
        </div>
    </div>

    {{-- Total Customers Box (Green bg) --}}
    <div class="info-box-v2">
        <span class="info-box-icon-v2" style="background-color: #28a745;">
            <i class="bi bi-people"></i>
        </span>
        <div class="info-box-content-v2">
            <span class="info-box-text-v2">Total Pelanggan</span>
            <span class="info-box-number-v2" id="kpi-customers">0</span>
            <span class="info-box-sub-v2">Terdaftar aktif</span>
        </div>
    </div>

    {{-- Active Staff Box (Yellow bg) --}}
    <div class="info-box-v2">
        <span class="info-box-icon-v2 text-dark" style="background-color: #ffc107;">
            <i class="bi bi-shield-check"></i>
        </span>
        <div class="info-box-content-v2">
            <span class="info-box-text-v2">Staf Bertugas</span>
            <span class="info-box-number-v2">
                <span id="kpi-staff">0</span>
                <span style="font-size: 0.9rem; font-weight: 500; opacity: 0.7;"> / {{ $totalEmployees }}</span>
            </span>
            <span class="info-box-sub-v2">Online Hari Ini</span>
        </div>
    </div>
</div>

{{-- ===== ADMINLTE 3 QUICK ACTIONS ===== --}}
<div class="quick-btn-grid">
    <a href="{{ route('admin.orders.index') }}" class="quick-btn-v2">
        <i class="bi bi-list-task" style="color: #ff6b35; font-size: 1.1rem;"></i>
        <span>Semua Pesanan</span>
    </a>
    <a href="{{ route('admin.employees.index') }}" class="quick-btn-v2">
        <i class="bi bi-people" style="color: #28a745; font-size: 1.1rem;"></i>
        <span>Manajemen Staff</span>
    </a>
    <a href="{{ route('admin.finances.index') }}" class="quick-btn-v2">
        <i class="bi bi-cash-stack" style="color: #8b5cf6; font-size: 1.1rem;"></i>
        <span>Keuangan</span>
    </a>
    <a href="{{ route('admin.reports.index') }}" class="quick-btn-v2">
        <i class="bi bi-bar-chart-line" style="color: #17a2b8; font-size: 1.1rem;"></i>
        <span>Laporan</span>
    </a>
</div>

{{-- ===== MAIN GRID LAYOUT ===== --}}
<div class="main-grid-v2">

    {{-- LEFT COLUMN --}}
    <div>
        {{-- Monthly Recap Card --}}
        <div class="card-v2">
            <div class="card-v2-header">
                <h5 class="card-v2-title">
                    <i class="bi bi-activity text-info"></i>
                    Monthly Recap Report
                </h5>
                <select class="select-v2" onchange="window.location.href='?filter='+this.value">
                    <option value="day" {{ request('filter')=='day'?'selected':'' }}>Hari Ini</option>
                    <option value="week" {{ request('filter','week')=='week'?'selected':'' }}>7 Hari</option>
                    <option value="month" {{ request('filter')=='month'?'selected':'' }}>1 Bulan</option>
                    <option value="year" {{ request('filter')=='year'?'selected':'' }}>1 Tahun</option>
                </select>
            </div>
            <div class="card-v2-body">
                <div class="recap-grid">
                    <div class="recap-chart-container" style="height:250px; position: relative;">
                        <div style="font-size:0.8rem; color: var(--text-secondary); font-weight:700; margin-bottom:10px; text-align: center;">
                            Sales: {{ now()->locale('id')->isoFormat('D MMM Y') }}
                        </div>
                        <canvas id="revenueChart"></canvas>
                    </div>
                    <div>
                        <div class="goal-title-v2">Target Bulanan</div>
                        @php
                            $targetRevenue = 50000000;
                            $revPct = min(100, round(($monthlyRevenue / $targetRevenue) * 100));
                            $targetCust = 100;
                            $custPct = min(100, round(($totalCustomers / $targetCust) * 100));
                            $presencePct = $totalEmployees > 0 ? round(($activeStaff->count() / $totalEmployees) * 100) : 0;
                        @endphp
                        <div class="target-stats-grid">
                            <div class="progress-group-v2">
                                <div class="progress-header-v2">
                                    <span>💰 Pendapatan</span>
                                    <span class="progress-val-v2">{{ $revPct }}%</span>
                                </div>
                                <div class="progress-track-v2">
                                    <div class="progress-fill-v2" style="width:0%; background:#17a2b8;" data-w="{{ $revPct }}"></div>
                                </div>
                                <div style="font-size:0.65rem; color: var(--text-secondary); margin-top:4px;">Rp {{ number_format($monthlyRevenue,0,',','.') }} / 50jt</div>
                            </div>
                            <div class="progress-group-v2">
                                <div class="progress-header-v2">
                                    <span>👥 Plg. Baru</span>
                                    <span class="progress-val-v2">{{ $custPct }}%</span>
                                </div>
                                <div class="progress-track-v2">
                                    <div class="progress-fill-v2" style="width:0%; background:#28a745;" data-w="{{ $custPct }}"></div>
                                </div>
                                <div style="font-size:0.65rem; color: var(--text-secondary); margin-top:4px;">{{ $totalCustomers }} / {{ $targetCust }} org</div>
                            </div>
                            <div class="progress-group-v2" style="grid-column: span 2;">
                                <div class="progress-header-v2">
                                    <span>🛡️ Staf Hadir</span>
                                    <span class="progress-val-v2">{{ $presencePct }}%</span>
                                </div>
                                <div class="progress-track-v2">
                                    <div class="progress-fill-v2" style="width:0%; background:#dc3545;" data-w="{{ $presencePct }}"></div>
                                </div>
                                <div style="font-size:0.65rem; color: var(--text-secondary); margin-top:4px;">{{ $activeStaff->count() }} / {{ $totalEmployees }} staf</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Table Card --}}
        <div class="card-v2">
            <div class="card-v2-header">
                <h5 class="card-v2-title">
                    <i class="bi bi-cart-check text-success"></i>
                    Pesanan Hari Ini
                </h5>
                <a href="{{ route('admin.orders.index') }}" class="btn-v2-link">Lihat Semua →</a>
            </div>
            <div class="card-v2-body p-0">
                <div class="desktop-orders-table">
                    <table class="table-v2">
                        <thead>
                            <tr>
                                <th>No. Order</th>
                                <th>Pelanggan</th>
                                <th>Layanan</th>
                                <th>Status</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentOrders as $order)
                            @php
                                $sc = match(true) {
                                    $order->status === 'pending'    => ['cls'=>'sb-pending',  'dot'=>'#ffc107', 'label'=>'Menunggu'],
                                    in_array($order->status,['processing','washing','drying','finishing']) => ['cls'=>'sb-process','dot'=>'#17a2b8','label'=>'Diproses'],
                                    $order->status === 'ready'      => ['cls'=>'sb-ready',    'dot'=>'#28a745', 'label'=>'Siap'],
                                    in_array($order->status,['completed','picked_up']) => ['cls'=>'sb-done','dot'=>'#6f42c1','label'=>'Selesai'],
                                    $order->status === 'cancelled'  => ['cls'=>'sb-cancel',   'dot'=>'#dc3545', 'label'=>'Dibatalkan'],
                                    default => ['cls'=>'sb-process','dot'=>'#c2c7d0','label'=>ucfirst($order->status)],
                                };
                            @endphp
                            <tr>
                                <td><span style="font-weight:700; color:#17a2b8; font-family:monospace; font-size:0.85rem;">#{{ $order->order_number }}</span></td>
                                <td>
                                    <div style="display:flex; align-items:center; gap:10px;">
                                        <div style="width:30px; height:30px; border-radius:50%; background: var(--surface-variant); color: var(--text); border: 1px solid var(--border-color); font-weight:700; font-size:0.8rem; display:flex; align-items:center; justify-content:center; flex-shrink:0;">{{ strtoupper(substr($order->user->name,0,1)) }}</div>
                                        <span style="font-weight:600; color: var(--text);">{{ Str::limit($order->user->name,18) }}</span>
                                    </div>
                                </td>
                                <td style="color: var(--text-secondary); font-size:0.83rem;">{{ Str::limit($order->service->name,22) }}</td>
                                <td><span class="status-badge-v2 {{ $sc['cls'] }}"><span class="dot-v2" style="background:{{ $sc['dot'] }}; margin-right: 5px;"></span>{{ $sc['label'] }}</span></td>
                                <td style="font-weight:700; color: var(--text);">Rp {{ number_format($order->total_price,0,',','.') }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="5" style="text-align:center; padding:32px; color: var(--text-secondary); font-size:0.85rem;">Belum ada pesanan hari ini</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Mobile view for orders --}}
                <div class="mobile-order-cards p-3">
                    @forelse($recentOrders as $order)
                    <div class="mobile-order-card">
                        <div style="display:flex; flex-direction:column; justify-content:space-between; align-items:flex-start; gap:4px; height: 100%;">
                            <div>
                                <span style="font-weight:700; color:#17a2b8; font-size:0.75rem;">#{{ $order->order_number }}</span>
                                <div style="font-weight:600; color: var(--text); margin:2px 0; font-size:0.8rem;">{{ Str::limit($order->user->name,15) }}</div>
                                <div style="font-size:0.65rem; color: var(--text-secondary);">{{ Str::limit($order->service->name,18) }}</div>
                            </div>
                            <div style="text-align:left; margin-top: auto; padding-top: 6px; border-top: 1px solid var(--border-color); width: 100%;">
                                <div style="font-weight:700; font-size:0.75rem; color: var(--text);">Rp {{ number_format($order->total_price,0,',','.') }}</div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div style="text-align:center; padding:24px; color: var(--text-secondary); border:1px dashed var(--border-color); border-radius:4px; font-size:0.85rem;">Belum ada pesanan</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- RIGHT COLUMN --}}
    <div>
        {{-- Doughnut Status Card --}}
        <div class="card-v2">
            <div class="card-v2-header">
                <h5 class="card-v2-title">
                    <i class="bi bi-pie-chart text-warning"></i>
                    Status Pesanan
                </h5>
                <span style="font-size:0.72rem; font-weight:700; color:#17a2b8; background:rgba(23, 162, 184, 0.15); padding:3px 9px; border-radius:4px; border:1px solid rgba(23, 162, 184, 0.2);">{{ array_sum($statusCounts) }} Total</span>
            </div>
            <div class="card-v2-body">
                <div class="status-donut-container" style="position:relative; height:175px; display:flex; justify-content:center; align-items:center;">
                    <canvas id="statusDoughnutChart"></canvas>
                    <div style="position:absolute; text-align:center; pointer-events:none;">
                        <div style="font-size:0.65rem; color: var(--text-secondary); font-weight:700; text-transform:uppercase; letter-spacing:0.5px;">Aktif</div>
                        <div style="font-size:2rem; font-weight:700; color: var(--text); line-height:1;">{{ $activeOrdersCount }}</div>
                    </div>
                </div>
                <div class="donut-legend-v2">
                    <div class="legend-item-v2"><span class="legend-dot-v2" style="background:#ffc107;"></span><span>Menunggu</span><span class="legend-val-v2">{{ $statusCounts['pending'] ?? 0 }}</span></div>
                    <div class="legend-item-v2"><span class="legend-dot-v2" style="background:#17a2b8;"></span><span>Proses</span><span class="legend-val-v2">{{ $statusCounts['processing'] ?? 0 }}</span></div>
                    <div class="legend-item-v2"><span class="legend-dot-v2" style="background:#28a745;"></span><span>Siap</span><span class="legend-val-v2">{{ $statusCounts['ready'] ?? 0 }}</span></div>
                    <div class="legend-item-v2"><span class="legend-dot-v2" style="background:#6f42c1;"></span><span>Selesai</span><span class="legend-val-v2">{{ $statusCounts['completed'] ?? 0 }}</span></div>
                </div>
            </div>
        </div>

        {{-- Active Staff Card --}}
        <div class="card-v2">
            <div class="card-v2-header">
                <h5 class="card-v2-title">
                    <i class="bi bi-people text-success"></i>
                    Staf Aktif Hari Ini
                </h5>
                <span style="font-size:0.72rem; font-weight:700; color:#28a745; background:rgba(40, 167, 70, 0.15); padding:3px 9px; border-radius:4px; border:1px solid rgba(40, 167, 70, 0.2);">{{ $activeStaff->count() }} Online</span>
            </div>
            <div class="card-v2-body">
                @forelse($activeStaff as $att)
                <div class="staff-item-v2">
                    <div class="staff-avatar-v2">{{ strtoupper(substr($att->user->name,0,1)) }}</div>
                    <div style="min-width:0; flex-grow:1;">
                        <div style="font-weight:600; font-size:0.85rem; color: var(--text); white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $att->user->name }}</div>
                        <div style="font-size:0.72rem; color:#28a745; font-weight:600;">Masuk: {{ date('H:i', strtotime($att->clock_in)) }}</div>
                    </div>
                    <div class="live-dot-v2"></div>
                </div>
                @empty
                <div style="text-align:center; padding:22px; border:1px dashed var(--border-color); border-radius:4px;">
                    <p style="color: var(--text-secondary); font-size:0.82rem; margin-bottom:0;">Belum ada staf bertugas hari ini</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {

    // Counter animation
    function animateNum(el, target, isCurrency) {
        if (!el) return;
        const dur = 1300; const start = performance.now();
        function step(now) {
            const p = Math.min((now-start)/dur,1);
            const e = 1-Math.pow(1-p,4);
            const v = Math.round(e*target);
            el.textContent = isCurrency ? 'Rp '+v.toLocaleString('id-ID') : v.toLocaleString('id-ID');
            if (p<1) requestAnimationFrame(step);
        }
        requestAnimationFrame(step);
    }
    animateNum(document.getElementById('kpi-revenue'), {{ $totalRevenue }}, true);
    animateNum(document.getElementById('kpi-active'), {{ $activeOrdersCount }}, false);
    animateNum(document.getElementById('kpi-customers'), {{ $totalCustomers }}, false);
    animateNum(document.getElementById('kpi-staff'), {{ $activeStaff->count() }}, false);

    // Theme-specific colors for charts
    function getChartColors(theme) {
        if (theme === 'dark') {
            return {
                gridColor: 'rgba(255, 255, 255, 0.06)',
                tickColor: '#c2c7d0',
                tooltipBg: '#343a40',
                tooltipTitle: '#ffffff',
                tooltipBody: '#c2c7d0',
                tooltipBorder: 'rgba(255, 255, 255, 0.1)',
                chartBorderColor: '#343a40'
            };
        } else {
            return {
                gridColor: 'rgba(0, 0, 0, 0.06)',
                tickColor: '#64748b',
                tooltipBg: '#ffffff',
                tooltipTitle: '#0f172a',
                tooltipBody: '#64748b',
                tooltipBorder: 'rgba(0, 0, 0, 0.1)',
                chartBorderColor: '#ffffff'
            };
        }
    }

    let revenueChart = null;
    let statusDoughnutChart = null;

    function initCharts(theme) {
        const colors = getChartColors(theme);
        
        if (revenueChart) revenueChart.destroy();
        if (statusDoughnutChart) statusDoughnutChart.destroy();

        // Revenue Chart
        const ctx = document.getElementById('revenueChart').getContext('2d');
        const g = ctx.createLinearGradient(0,0,0,220);
        g.addColorStop(0,'rgba(23,162,184,0.3)');
        g.addColorStop(1,'rgba(23,162,184,0)');
        
        revenueChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($revenueTrends->pluck('date')),
                datasets: [{
                    data: @json($revenueTrends->pluck('total')),
                    borderColor: '#17a2b8', backgroundColor: g,
                    fill: true, tension: 0.45, borderWidth: 2.5,
                    pointRadius: 4, pointBackgroundColor: '#17a2b8',
                    pointBorderColor: colors.chartBorderColor, pointBorderWidth: 2,
                    pointHoverRadius: 7
                }]
            },
            options: {
                responsive:true, maintainAspectRatio:false,
                interaction:{mode:'index',intersect:false},
                plugins:{
                    legend:{display:false},
                    tooltip:{
                        backgroundColor: colors.tooltipBg, titleColor: colors.tooltipTitle,
                        bodyColor: colors.tooltipBody, borderColor: colors.tooltipBorder,
                        borderWidth:1, padding:12, cornerRadius:4, displayColors:false,
                        callbacks:{label:c=>'Rp '+Number(c.parsed.y).toLocaleString('id-ID')}
                    }
                },
                scales:{
                    y:{beginAtZero:true,grid:{color: colors.gridColor},border:{display:false},
                       ticks:{color: colors.tickColor,font:{family:'Outfit',size:10},
                              callback:v=>'Rp '+(v>=1000000?(v/1000000).toFixed(1)+'jt':v.toLocaleString('id-ID'))}},
                    x:{grid:{display:false},border:{display:false},
                       ticks:{color: colors.tickColor,font:{family:'Outfit',size:10}}}
                }
            }
        });

        // Donut Chart
        statusDoughnutChart = new Chart(document.getElementById('statusDoughnutChart').getContext('2d'), {
            type:'doughnut',
            data:{
                labels:['Menunggu','Proses','Siap','Selesai'],
                datasets:[{
                    data:[{{ $statusCounts['pending']??0 }},{{ $statusCounts['processing']??0 }},{{ $statusCounts['ready']??0 }},{{ $statusCounts['completed']??0 }}],
                    backgroundColor:['#ffc107','#17a2b8','#28a745','#6f42c1'],
                    hoverBackgroundColor:['#e0a800','#138496','#218838','#5a32a3'],
                    borderWidth:3, borderColor: colors.chartBorderColor, hoverOffset:6
                }]
            },
            options:{responsive:true,maintainAspectRatio:false,cutout:'74%',
                plugins:{legend:{display:false},
                tooltip:{backgroundColor: colors.tooltipBg, titleColor: colors.tooltipTitle, bodyColor: colors.tooltipBody, borderColor: colors.tooltipBorder, borderWidth:1, padding:10, cornerRadius:4}}
            }
        });
    }

    // Initialize with active theme
    const activeTheme = document.documentElement.getAttribute('data-theme') || 'light';
    initCharts(activeTheme);

    // Dynamic Chart color shift on theme toggled
    window.addEventListener('theme-changed', function(e) {
        initCharts(e.detail.theme);
    });

    // Progress bars animate
    setTimeout(()=>{
        document.querySelectorAll('.progress-fill-v2').forEach(el=>{
            el.style.width = el.dataset.w+'%';
        });
    }, 400);
});
</script>
@endsection
