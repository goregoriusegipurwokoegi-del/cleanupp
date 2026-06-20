@extends('layouts.premium-dashboard')

@section('page_title', 'Panel Karyawan')

@section('nav_items')
    <li class="nav-item"><a href="{{ route('employee.dashboard') }}" class="nav-link active">Dashboard</a></li>
    <li class="nav-item"><a href="{{ route('employee.orders.index') }}" class="nav-link">Orderan Masuk</a></li>
    <li class="nav-item"><a href="{{ route('employee.inventories.index') }}" class="nav-link">Stok Barang</a></li>
@endsection

@section('content')
@php
    $todayAttendance = \App\Models\Attendance::where(['user_id' => Auth::id()])
        ->where('date', \Carbon\Carbon::today()->toDateString())
        ->first();
    $isClockedIn = $todayAttendance ? true : false;
    $isClockedOut = ($todayAttendance && $todayAttendance->clock_out) ? true : false;
@endphp

<style>
    /* Responsive Spacing & Design System */
    .dashboard-container {
        display: flex;
        flex-direction: column;
        gap: 1.8rem;
        padding-bottom: 2rem;
    }
    
    .quick-stats-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1.25rem;
    }

    .monitoring-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        gap: 1.25rem;
    }

    /* Premium Modern Glassmorphic Card */
    .premium-card {
        background: rgba(30, 41, 59, 0.45); /* Soft premium slate */
        border: 1px solid rgba(255, 255, 255, 0.08);
        backdrop-filter: blur(24px) saturate(120%);
        border-radius: 24px;
        padding: 1.5rem;
        position: relative;
        overflow: hidden;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.5);
    }
    
    .premium-card:hover {
        transform: translateY(-4px);
        border-color: rgba(249, 115, 22, 0.3);
        background: rgba(30, 41, 59, 0.65);
        box-shadow: 0 15px 35px -5px rgba(0, 0, 0, 0.6), 0 0 15px rgba(249, 115, 22, 0.1);
    }

    /* Status Ambient Glow Backgrounds */
    .status-glow {
        position: absolute;
        top: -50px;
        right: -50px;
        width: 150px;
        height: 150px;
        border-radius: 50%;
        filter: blur(50px);
        opacity: 0.22;
        z-index: 0;
        pointer-events: none;
    }

    /* Pulse Dots for Realtime Feel */
    .pulse-dot {
        display: inline-block;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        position: relative;
    }
    .pulse-dot::after {
        content: '';
        position: absolute;
        inset: -4px;
        border-radius: 50%;
        border: 2px solid currentColor;
        opacity: 0.8;
        animation: pulse-ring 1.8s cubic-bezier(0.215, 0.610, 0.355, 1) infinite;
    }

    @keyframes pulse-ring {
        0% { transform: scale(0.6); opacity: 1; }
        100% { transform: scale(1.6); opacity: 0; }
    }

    /* Action Buttons */
    .action-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        width: 100%;
        height: 48px;
        border-radius: 14px;
        font-weight: 800;
        font-size: 0.9rem;
        letter-spacing: 0.5px;
        border: none;
        cursor: pointer;
        transition: all 0.25s ease;
        position: relative;
        z-index: 1;
        text-decoration: none;
        text-transform: uppercase;
    }

    .action-button:active {
        transform: scale(0.97);
    }

    /* Accent indicator bars for list rows */
    .accent-bar {
        position: relative;
        overflow: hidden;
    }
    .accent-bar::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 5px;
        border-radius: 5px 0 0 5px;
    }
    .accent-bar-warning::before { background: linear-gradient(to bottom, #f59e0b, #d97706); }
    .accent-bar-primary::before { background: linear-gradient(to bottom, var(--primary), #ea580c); }

    /* Custom List Card styled beautifully */
    .list-item-card {
        background: rgba(30, 41, 59, 0.35);
        border: 1px solid rgba(255, 255, 255, 0.05);
        border-radius: 16px;
        transition: all 0.25s ease;
    }
    .list-item-card:hover {
        background: rgba(30, 41, 59, 0.55);
        border-color: rgba(255, 255, 255, 0.1);
        transform: translateX(3px);
    }

    /* Horizontal Scrolling Container for New Orders */
    .horizontal-scroll-container {
        display: flex;
        overflow-x: auto;
        gap: 1rem;
        padding-bottom: 0.8rem;
        scroll-snap-type: x mandatory;
        -webkit-overflow-scrolling: touch;
        scroll-padding: 0 1rem;
    }
    .horizontal-scroll-container::-webkit-scrollbar {
        height: 6px;
    }
    .horizontal-scroll-container::-webkit-scrollbar-track {
        background: rgba(255, 255, 255, 0.02);
        border-radius: 10px;
    }
    .horizontal-scroll-container::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.12);
        border-radius: 10px;
    }
    .scroll-card {
        flex: 0 0 280px;
        scroll-snap-align: start;
        background: rgba(30, 41, 59, 0.4);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 20px;
        padding: 1.25rem;
        transition: all 0.25s ease;
        position: relative;
    }
    .scroll-card:hover {
        background: rgba(30, 41, 59, 0.6);
        border-color: rgba(249, 115, 22, 0.25);
        transform: translateY(-2px);
    }
    @media (max-width: 768px) {
        .scroll-card {
            flex: 0 0 250px;
            padding: 1.1rem;
            border-radius: 18px;
        }
    }

    /* Colorized Dashboard Widgets (Optimized for readability) */
    .widget-blue {
        background: linear-gradient(135deg, rgba(14, 165, 233, 0.08) 0%, rgba(30, 41, 59, 0.45) 100%);
        border-color: rgba(14, 165, 233, 0.2);
    }
    .widget-blue:hover { border-color: rgba(14, 165, 233, 0.45); }
    
    .widget-purple {
        background: linear-gradient(135deg, rgba(168, 85, 247, 0.08) 0%, rgba(30, 41, 59, 0.45) 100%);
        border-color: rgba(168, 85, 247, 0.2);
    }
    .widget-purple:hover { border-color: rgba(168, 85, 247, 0.45); }

    .widget-emerald {
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.08) 0%, rgba(30, 41, 59, 0.45) 100%);
        border-color: rgba(16, 185, 129, 0.2);
    }
    .widget-emerald:hover { border-color: rgba(16, 185, 129, 0.45); }

    .widget-amber {
        background: linear-gradient(135deg, rgba(245, 158, 11, 0.08) 0%, rgba(30, 41, 59, 0.45) 100%);
        border-color: rgba(245, 158, 11, 0.2);
    }
    .widget-amber:hover { border-color: rgba(245, 158, 11, 0.45); }

    /* Interactive scanning radar line effect */
    .scanner-card::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 2px;
        background: linear-gradient(to right, transparent, var(--primary), transparent);
        animation: scan-radar-line 3s linear infinite;
        opacity: 0.6;
        z-index: 1;
        pointer-events: none;
    }
    @keyframes scan-radar-line {
        0% { top: 0%; }
        50% { top: 100%; }
        100% { top: 0%; }
    }

    /* Media query optimizations for phones (HP view) */
    @media (max-width: 768px) {
        .dashboard-container { gap: 1.25rem; }
        .quick-stats-grid { gap: 0.9rem; }
        .monitoring-grid { grid-template-columns: repeat(2, 1fr); gap: 0.9rem; }
        .premium-card { padding: 1.15rem; border-radius: 20px; }
        
        .stat-label { font-size: 0.65rem !important; letter-spacing: 0.3px; }
        .stat-number { font-size: 1.45rem !important; }
        .action-button { height: 42px; font-size: 0.8rem; border-radius: 12px; }
        
        .section-header { font-size: 0.95rem !important; }
        .pulse-dot { width: 8px; height: 8px; }
    }
</style>

<div class="dashboard-container">
    
    <!-- Top Action Cards -->
    <div class="quick-stats-grid">
        <!-- Kehadiran Widget -->
        <div class="premium-card">
            @if($isClockedOut)
                <div class="status-glow" style="background: #f43f5e;"></div>
            @elseif($isClockedIn)
                <div class="status-glow" style="background: #10b981;"></div>
            @else
                <div class="status-glow" style="background: #fb923c;"></div>
            @endif

            <div style="position: relative; z-index: 2; display: flex; flex-direction: column; height: 100%; justify-content: space-between; min-height: 108px;">
                <div>
                    <span class="stat-label" style="display: block; opacity: 0.55; font-size: 0.7rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 4px;">Presensi Hari Ini</span>
                    @if($isClockedOut)
                        <h4 style="color: #f43f5e; font-size: 0.95rem; font-weight: 900; display: flex; align-items: center; gap: 8px;">
                            <span class="pulse-dot" style="color: #f43f5e;"></span> SELESAI BEKERJA
                        </h4>
                    @elseif($isClockedIn)
                        <h4 style="color: #34d399; font-size: 0.95rem; font-weight: 900; display: flex; align-items: center; gap: 8px;">
                            <span class="pulse-dot" style="color: #34d399;"></span> SEDANG BEKERJA
                        </h4>
                    @else
                        <h4 style="color: #fb923c; font-size: 0.95rem; font-weight: 900; display: flex; align-items: center; gap: 8px;">
                            <span class="pulse-dot" style="color: #fb923c;"></span> BELUM ABSEN MASUK
                        </h4>
                    @endif
                </div>

                <div style="margin-top: 1.2rem;">
                    @if(!$isClockedIn)
                        <form action="{{ route('employee.attendance.clock-in') }}" method="POST" style="margin: 0;">
                            @csrf
                            <button type="submit" class="action-button" style="background: linear-gradient(135deg, #10b981, #059669); color: #ffffff; box-shadow: 0 4px 15px rgba(16, 185, 129, 0.25); text-shadow: 0 1px 2px rgba(0,0,0,0.25);">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4M10 17l5-5-5-5M15 12H3"/></svg>
                                Absen Masuk
                            </button>
                        </form>
                    @elseif(!$isClockedOut)
                        <form action="{{ route('employee.attendance.clock-out') }}" method="POST" style="margin: 0;">
                            @csrf
                            <button type="submit" class="action-button" style="background: rgba(244, 63, 94, 0.08); border: 1.5px solid rgba(244, 63, 94, 0.35); color: #f43f5e; box-shadow: 0 4px 12px rgba(244, 63, 94, 0.05);">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/></svg>
                                Absen Pulang
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <!-- Scan Widget -->
        <div class="premium-card scanner-card">
            <div class="status-glow" style="background: var(--primary);"></div>
            <div style="position: relative; z-index: 2; display: flex; flex-direction: column; height: 100%; justify-content: space-between; min-height: 108px;">
                <div>
                    <span class="stat-label" style="display: block; opacity: 0.55; font-size: 0.7rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 4px;">Aksi Cepat</span>
                    <h4 style="color: #fff; font-size: 0.95rem; font-weight: 900; display: flex; align-items: center; gap: 8px;">
                        🔍 PENCARIAN SEPATU
                    </h4>
                </div>

                <div style="margin-top: 1.2rem;">
                    <a href="{{ route('employee.orders.scan') }}" class="action-button" style="background: linear-gradient(135deg, var(--primary), #ea580c); color: #ffffff; box-shadow: 0 4px 15px rgba(249, 115, 22, 0.25); text-shadow: 0 1px 2px rgba(0,0,0,0.25);">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                        Buka Scanner
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Feed Baru Masuk (Compact Auto-sliding Banner) -->
    @if($incomingOrders->isNotEmpty())
    <div>
        <div class="carousel-wrapper" style="position: relative; overflow: hidden; border-radius: 16px; border: 1.5px solid rgba(251, 146, 60, 0.25); background: linear-gradient(135deg, rgba(251, 146, 60, 0.12) 0%, rgba(30, 41, 59, 0.6) 100%); backdrop-filter: blur(24px); box-shadow: 0 8px 30px rgba(0,0,0,0.35); margin-bottom: 0.5rem;">
            <div id="carousel-track" style="display: flex; transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1); width: 100%;">
                @foreach($incomingOrders as $order)
                <div class="carousel-slide" style="flex: 0 0 100%; display: flex; justify-content: space-between; align-items: center; padding: 0.75rem 1.1rem; min-width: 100%; box-sizing: border-box;">
                    <div style="display: flex; align-items: center; gap: 10px; min-width: 0; flex-grow: 1; padding-right: 8px;">
                        <span class="pulse-dot" style="color: #fb923c; flex-shrink: 0;"></span>
                        <span style="font-weight: 900; color: #fb923c; font-size: 0.85rem; font-family: monospace; letter-spacing: -0.5px; background: rgba(251, 146, 60, 0.15); padding: 2px 6px; border-radius: 6px; flex-shrink: 0;">{{ $order->queue_number }}</span>
                        <div style="min-width: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: flex; align-items: center; gap: 6px; font-size: 0.8rem;">
                            <span style="font-weight: 800; color: #fff; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 110px;" title="{{ $order->service->name }}">{{ $order->service->name }}</span>
                            <span style="font-size: 0.68rem; opacity: 0.5; font-weight: 600; white-space: nowrap; flex-shrink: 0;">({{ $order->created_at->diffForHumans() }})</span>
                        </div>
                    </div>
                    <a href="{{ route('employee.orders.index') }}" style="background: #fb923c; color: #000; padding: 4px 11px; border-radius: 6px; text-decoration: none; font-size: 0.7rem; font-weight: 900; transition: 0.25s; flex-shrink: 0; letter-spacing: 0.5px;" onmouseover="this.style.background='#ea580c'" onmouseout="this.style.background='#fb923c'">PROSES</a>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Summary Stats Row -->
    <div class="quick-stats-grid">
        <a href="{{ route('employee.orders.index', ['status' => 'pending']) }}" style="text-decoration: none; color: inherit; display: block;">
            <div class="premium-card accent-bar accent-bar-warning" style="background: linear-gradient(135deg, rgba(239, 68, 68, 0.06) 0%, rgba(30, 41, 59, 0.45) 100%); border-color: rgba(239, 68, 68, 0.18); height: 100%;">
                <p class="stat-label" style="color: #f87171; font-size: 0.72rem; font-weight: 800; margin-bottom: 6px; letter-spacing: 0.8px;">BUTUH VALIDASI</p>
                <div class="stat-number" style="font-size: 1.9rem; font-weight: 900; color: #fff; line-height: 1;">{{ $pendingOrdersCount }}</div>
            </div>
        </a>
        <a href="{{ route('employee.orders.index', ['status' => 'completed']) }}" style="text-decoration: none; color: inherit; display: block;">
            <div class="premium-card accent-bar accent-bar-primary" style="background: linear-gradient(135deg, rgba(16, 185, 129, 0.06) 0%, rgba(30, 41, 59, 0.45) 100%); border-color: rgba(16, 185, 129, 0.18); height: 100%;">
                <p class="stat-label" style="color: #34d399; font-size: 0.72rem; font-weight: 800; margin-bottom: 6px; letter-spacing: 0.8px;">SELESAI (MINGGU INI)</p>
                <div class="stat-number" style="font-size: 1.9rem; font-weight: 900; color: #fff; line-height: 1;">{{ $weeklyCompletedCount }}</div>
            </div>
        </a>
    </div>

    <!-- Cleaning Monitoring Grid -->
    <div>
        <h3 class="section-header" style="font-size: 1.05rem; font-weight: 800; margin-bottom: 0.9rem; display: flex; align-items: center; gap: 8px; color: #fff;">
            <span style="width: 4px; height: 18px; background: #38bdf8; border-radius: 4px;"></span>
            Monitoring Cuci Sepatu (Cleaning)
        </h3>
        <div class="monitoring-grid">
            <a href="{{ route('employee.orders.index', ['status' => 'processing', 'category' => 'cleaning']) }}" style="text-decoration: none;">
                <div class="premium-card widget-blue" style="height: 100%; display: flex; flex-direction: column; justify-content: space-between; min-height: 86px;">
                    <span class="stat-label" style="color: #94a3b8; font-weight: 800; font-size: 0.68rem; letter-spacing: 0.5px;">DICUCI</span>
                    <div class="stat-number" style="font-size: 1.7rem; font-weight: 900; color: #38bdf8; line-height: 1;">{{ $cleaningCounts['washing'] }}</div>
                </div>
            </a>
            <a href="{{ route('employee.orders.index', ['status' => 'finishing', 'category' => 'cleaning']) }}" style="text-decoration: none;">
                <div class="premium-card widget-purple" style="height: 100%; display: flex; flex-direction: column; justify-content: space-between; min-height: 86px;">
                    <span class="stat-label" style="color: #94a3b8; font-weight: 800; font-size: 0.68rem; letter-spacing: 0.5px;">PENGERINGAN</span>
                    <div class="stat-number" style="font-size: 1.7rem; font-weight: 900; color: #c084fc; line-height: 1;">{{ $cleaningCounts['drying'] }}</div>
                </div>
            </a>
            <a href="{{ route('employee.orders.index', ['status' => 'ready', 'category' => 'cleaning']) }}" style="text-decoration: none;">
                <div class="premium-card widget-emerald" style="height: 100%; display: flex; flex-direction: column; justify-content: space-between; min-height: 86px;">
                    <span class="stat-label" style="color: #94a3b8; font-weight: 800; font-size: 0.68rem; letter-spacing: 0.5px;">SIAP DIAMBIL</span>
                    <div class="stat-number" style="font-size: 1.7rem; font-weight: 900; color: #34d399; line-height: 1;">{{ $cleaningCounts['ready'] }}</div>
                </div>
            </a>
            <a href="{{ route('employee.orders.index', ['status' => 'uncollected', 'category' => 'cleaning']) }}" style="text-decoration: none;">
                <div class="premium-card widget-amber" style="height: 100%; display: flex; flex-direction: column; justify-content: space-between; min-height: 86px;">
                    <span class="stat-label" style="color: #94a3b8; font-weight: 800; font-size: 0.68rem; letter-spacing: 0.5px;">BELUM DIAMBIL</span>
                    <div class="stat-number" style="font-size: 1.7rem; font-weight: 900; color: #fbbf24; line-height: 1;">{{ $cleaningCounts['uncollected'] }}</div>
                </div>
            </a>
        </div>
    </div>

    <!-- Repair Monitoring Grid -->
    <div style="margin-bottom: 2rem;">
        <h3 class="section-header" style="font-size: 1.05rem; font-weight: 800; margin-bottom: 0.9rem; display: flex; align-items: center; gap: 8px; color: #fff;">
            <span style="width: 4px; height: 18px; background: #fbbf24; border-radius: 4px;"></span>
            Monitoring Reparasi Sepatu (Repair)
        </h3>
        <div class="monitoring-grid">
            <a href="{{ route('employee.orders.index', ['status' => 'processing', 'category' => 'repair']) }}" style="text-decoration: none;">
                <div class="premium-card widget-blue" style="height: 100%; display: flex; flex-direction: column; justify-content: space-between; min-height: 86px;">
                    <span class="stat-label" style="color: #94a3b8; font-weight: 800; font-size: 0.68rem; letter-spacing: 0.5px;">DIKERJAKAN</span>
                    <div class="stat-number" style="font-size: 1.7rem; font-weight: 900; color: #38bdf8; line-height: 1;">{{ $repairCounts['processing'] }}</div>
                </div>
            </a>
            <a href="{{ route('employee.orders.index', ['status' => 'finishing', 'category' => 'repair']) }}" style="text-decoration: none;">
                <div class="premium-card widget-purple" style="height: 100%; display: flex; flex-direction: column; justify-content: space-between; min-height: 86px;">
                    <span class="stat-label" style="color: #94a3b8; font-weight: 800; font-size: 0.68rem; letter-spacing: 0.5px;">FINISHING</span>
                    <div class="stat-number" style="font-size: 1.7rem; font-weight: 900; color: #c084fc; line-height: 1;">{{ $repairCounts['finishing'] }}</div>
                </div>
            </a>
            <a href="{{ route('employee.orders.index', ['status' => 'ready', 'category' => 'repair']) }}" style="text-decoration: none;">
                <div class="premium-card widget-emerald" style="height: 100%; display: flex; flex-direction: column; justify-content: space-between; min-height: 86px;">
                    <span class="stat-label" style="color: #94a3b8; font-weight: 800; font-size: 0.68rem; letter-spacing: 0.5px;">SIAP DIAMBIL</span>
                    <div class="stat-number" style="font-size: 1.7rem; font-weight: 900; color: #34d399; line-height: 1;">{{ $repairCounts['ready'] }}</div>
                </div>
            </a>
            <a href="{{ route('employee.orders.index', ['status' => 'uncollected', 'category' => 'repair']) }}" style="text-decoration: none;">
                <div class="premium-card widget-amber" style="height: 100%; display: flex; flex-direction: column; justify-content: space-between; min-height: 86px;">
                    <span class="stat-label" style="color: #94a3b8; font-weight: 800; font-size: 0.68rem; letter-spacing: 0.5px;">BELUM DIAMBIL</span>
                    <div class="stat-number" style="font-size: 1.7rem; font-weight: 900; color: #fbbf24; line-height: 1;">{{ $repairCounts['uncollected'] }}</div>
                </div>
            </a>
        </div>
    </div>

</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const track = document.getElementById('carousel-track');
        if (track) {
            const slides = track.querySelectorAll('.carousel-slide');
            const totalSlides = slides.length;
            if (totalSlides > 1) {
                let currentIndex = 0;
                setInterval(() => {
                    currentIndex = (currentIndex + 1) % totalSlides;
                    track.style.transform = `translateX(-${currentIndex * 100}%)`;
                }, 3000);
            }
        }
    });
</script>
@endpush

@endsection
