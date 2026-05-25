@extends('layouts.premium-dashboard')

@section('page_title', 'Panel Karyawan')

@section('nav_items')
    <li class="nav-item"><a href="{{ route('employee.dashboard') }}" class="nav-link active">Dashboard</a></li>
    <li class="nav-item"><a href="{{ route('employee.orders.index') }}" class="nav-link">Tugas Saya</a></li>
    <li class="nav-item"><a href="#" class="nav-link">Inventaris</a></li>
@endsection

@section('content')
@php
    $todayAttendance = \App\Models\Attendance::where('user_id', Auth::id())
        ->whereDate('date', \Carbon\Carbon::today())
        ->first();
    $isClockedIn = $todayAttendance ? true : false;
    $isClockedOut = ($todayAttendance && $todayAttendance->clock_out) ? true : false;
@endphp

<style>
    .accent-card {
        position: relative;
        overflow: hidden;
    }
    .accent-card::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 4px;
    }
    .accent-warning::before {
        background: var(--warning);
    }
    .stat-label {
        opacity: 0.6;
        font-size: 0.72rem;
        margin-bottom: 0.5rem;
        text-transform: uppercase;
        font-weight: 700;
        letter-spacing: 0.5px;
    }

    @media (max-width: 768px) {
        .header-welcome { text-align: left !important; padding: 1.5rem !important; }
        .attendance-btns { grid-template-columns: 1fr 1fr !important; }
        .grid-monitoring { grid-template-columns: 1fr 1fr !important; gap: 0.8rem !important; }
        .grid-2 { grid-template-columns: 1fr 1fr !important; gap: 1rem !important; }
        .stat-card-compact { padding: 1.2rem !important; }
        .stat-card-compact div { font-size: 1.8rem !important; }
    }

    .premium-action-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
        margin-bottom: 2rem;
    }
    
    .action-widget {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .action-widget:hover {
        transform: translateY(-4px);
    }
</style>

<!-- Quick Actions Grid -->
<!-- Quick Actions Grid -->
<div class="premium-action-grid">
    <!-- Absensi Compact -->
    <div class="glass-card action-widget" style="padding: 1.2rem; border-radius: 20px; background: rgba(30, 41, 59, 0.4); border: 1px solid rgba(255,255,255,0.05); display: flex; flex-direction: column; justify-content: space-between; position: relative; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.2);">
        <!-- Background Glow -->
        @if($isClockedOut)
            <div style="position: absolute; right: -20%; top: -20%; width: 100px; height: 100px; background: rgba(239, 68, 68, 0.15); filter: blur(30px); border-radius: 50%;"></div>
        @elseif($isClockedIn)
            <div style="position: absolute; right: -20%; top: -20%; width: 100px; height: 100px; background: rgba(16, 185, 129, 0.15); filter: blur(30px); border-radius: 50%;"></div>
        @else
            <div style="position: absolute; right: -20%; top: -20%; width: 100px; height: 100px; background: rgba(245, 158, 11, 0.15); filter: blur(30px); border-radius: 50%;"></div>
        @endif
        
        <div style="margin-bottom: 1.2rem; position: relative; z-index: 1;">
            <p style="opacity: 0.6; font-size: 0.65rem; text-transform: uppercase; font-weight: 800; letter-spacing: 1px; margin-bottom: 4px;">Kehadiran</p>
            @if($isClockedOut)
                <div style="color: #f43f5e; font-size: 1rem; font-weight: 900;">Selesai</div>
            @elseif($isClockedIn)
                <div style="color: #10b981; font-size: 1rem; font-weight: 900;">Aktif</div>
            @else
                <div style="color: #f59e0b; font-size: 1rem; font-weight: 900;">Belum Absen</div>
            @endif
        </div>
        <div style="position: relative; z-index: 1;">
            @if(!$isClockedIn)
                <form action="{{ route('employee.attendance.clock-in') }}" method="POST" style="margin: 0;">
                    @csrf
                    <button type="submit" style="width: 100%; height: 42px; background: var(--success); color: #000; border: none; border-radius: 12px; font-weight: 900; font-size: 0.8rem; cursor: pointer; letter-spacing: 1px; box-shadow: 0 4px 15px rgba(16, 185, 129, 0.25);">MASUK</button>
                </form>
            @elseif(!$isClockedOut)
                <form action="{{ route('employee.attendance.clock-out') }}" method="POST" style="margin: 0;">
                    @csrf
                    <button type="submit" style="width: 100%; height: 42px; background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3); color: #ef4444; border-radius: 12px; font-weight: 900; font-size: 0.8rem; cursor: pointer; letter-spacing: 1px;">KELUAR</button>
                </form>
            @endif
        </div>
    </div>

    <!-- Scan Button Box -->
    <a href="{{ route('employee.orders.scan') }}" style="text-decoration: none;">
        <div class="glass-card action-widget" style="padding: 1.2rem; border-radius: 20px; background: rgba(30, 41, 59, 0.4); border: 1px solid rgba(255,255,255,0.05); display: flex; flex-direction: column; justify-content: space-between; height: 100%; box-shadow: 0 5px 15px rgba(0,0,0,0.2); position: relative; overflow: hidden;">
            <!-- Background Glow -->
            <div style="position: absolute; right: -20%; top: -20%; width: 100px; height: 100px; background: rgba(249, 115, 22, 0.15); filter: blur(30px); border-radius: 50%;"></div>
            
            <div style="margin-bottom: 1.2rem; position: relative; z-index: 1;">
                <p style="opacity: 0.6; font-size: 0.65rem; text-transform: uppercase; font-weight: 800; letter-spacing: 1px; color: #fff; margin-bottom: 4px;">Aksi Cepat</p>
                <div style="color: #fff; font-size: 1rem; font-weight: 900;">Cari Data Sepatu</div>
            </div>
            <div style="position: relative; z-index: 1;">
                <div style="width: 100%; height: 42px; background: var(--primary); color: #000; border-radius: 12px; font-weight: 900; font-size: 0.8rem; display: flex; align-items: center; justify-content: center; letter-spacing: 1px; box-shadow: 0 4px 15px rgba(249, 115, 22, 0.25);">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#000" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 6px;"><path d="M4 3h16a2 2 0 0 1 2 2v6h-2V5H4v14h6v2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z"/><polyline points="14 14 18 18 22 14"/><line x1="18" y1="22" x2="18" y2="14"/></svg>
                    BUKA
                </div>
            </div>
        </div>
    </a>
</div>

<!-- Incoming Orders Section Small -->
<div style="margin-bottom: 2rem;">
    <h3 style="font-size: 1rem; font-weight: 700; margin-bottom: 1rem; display: flex; align-items: center; justify-content: space-between;">
        <span style="display: flex; align-items: center; gap: 0.5rem;">
            <span style="width: 4px; height: 16px; background: var(--warning); border-radius: 4px;"></span>
            Baru Masuk
        </span>
        @if($incomingOrders->count() > 0)
            <span style="background: var(--warning); color: #000; font-size: 0.65rem; padding: 2px 8px; border-radius: 10px; font-weight: 900;">{{ $incomingOrders->count() }}</span>
        @endif
    </h3>
    <div style="display: grid; gap: 0.8rem;">
        @forelse($incomingOrders as $order)
        <div class="glass-card accent-card accent-warning" style="padding: 1rem 1.2rem; display: flex; justify-content: space-between; align-items: center; background: rgba(245, 158, 11, 0.03); border: 1px solid rgba(255,255,255,0.05); border-radius: 16px;">
            <div style="display: flex; gap: 12px; align-items: center;">
                <div style="font-weight: 900; color: var(--warning); font-size: 1rem;">{{ $order->queue_number }}</div>
                <div>
                    <div style="font-weight: 700; color: #fff; font-size: 0.9rem; margin-bottom: 3px;">{{ Str::limit($order->service->name, 20) }}</div>
                    <p style="font-size: 0.75rem; opacity: 0.5; font-weight: 500;">{{ $order->created_at->diffForHumans() }}</p>
                </div>
            </div>
            <a href="{{ route('employee.orders.index') }}" style="background: rgba(255,255,255,0.08); color: #fff; padding: 6px 14px; border-radius: 8px; text-decoration: none; font-size: 0.8rem; font-weight: 700; border: 1px solid rgba(255,255,255,0.1);">Detil</a>
        </div>
        @empty
        <div style="padding: 1.5rem; text-align: center; background: rgba(255,255,255,0.01); border-radius: 12px; border: 1px dashed rgba(255,255,255,0.05);">
            <p style="opacity: 0.3; font-size: 0.8rem;">Belum ada pesanan.</p>
        </div>
        @endforelse
    </div>
</div>


<div class="grid-2" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(130px, 1fr)); gap: 1.2rem; margin-bottom: 3rem;">
    <a href="{{ route('employee.orders.index', ['status' => 'pending']) }}" style="text-decoration: none; color: inherit;">
        <div class="glass-card stat-card-compact accent-card accent-warning" style="background: rgba(245, 158, 11, 0.05); transition: 0.3s; border-radius: 16px; border: 1px solid rgba(255,255,255,0.05);" onmouseover="this.style.background='rgba(245, 158, 11, 0.1)'" onmouseout="this.style.background='rgba(245, 158, 11, 0.05)'">
            <p class="stat-label" style="color: var(--warning);">Butuh Validasi</p>
            <div style="font-size: 2rem; font-weight: 900; color: #fff;">{{ $pendingOrdersCount }}</div>
        </div>
    </a>
    <a href="{{ route('employee.orders.index', ['status' => 'completed']) }}" style="text-decoration: none; color: inherit;">
        <div class="glass-card stat-card-compact" style="transition: 0.3s; border-radius: 16px; border: 1px solid rgba(255,255,255,0.05);" onmouseover="this.style.background='rgba(255,255,255,0.05)'" onmouseout="this.style.background='transparent'">
            <p class="stat-label">Selesai (Minggu Ini)</p>
            <div style="font-size: 2rem; font-weight: 900; color: #fff;">{{ $weeklyCompletedCount }}</div>
        </div>
    </a>
</div>

<!-- Cleaning Monitoring -->
<div style="margin-bottom: 2.5rem;">
    <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 1.2rem; display: flex; align-items: center; gap: 0.5rem;">
        <span style="width: 4px; height: 18px; background: var(--primary); border-radius: 4px;"></span>
        Monitoring Cuci Sepatu
    </h3>
    <div class="grid-monitoring" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 1rem;">
        <a href="{{ route('employee.orders.index', ['status' => 'processing', 'category' => 'cleaning']) }}" style="text-decoration: none;">
            <div style="background: rgba(59, 130, 246, 0.08); border: 1px solid rgba(59, 130, 246, 0.15); padding: 1.2rem; border-radius: 18px; transition: 0.3s;" onmouseover="this.style.background='rgba(59, 130, 246, 0.15)'" onmouseout="this.style.background='rgba(59, 130, 246, 0.08)'">
                <p class="stat-label" style="color: #fff;">Dicuci</p>
                <div style="font-size: 1.8rem; font-weight: 900; color: #3b82f6;">{{ $cleaningCounts['washing'] }}</div>
            </div>
        </a>
        <a href="{{ route('employee.orders.index', ['status' => 'finishing', 'category' => 'cleaning']) }}" style="text-decoration: none;">
            <div style="background: rgba(168, 85, 247, 0.08); border: 1px solid rgba(168, 85, 247, 0.15); padding: 1.2rem; border-radius: 18px; transition: 0.3s;" onmouseover="this.style.background='rgba(168, 85, 247, 0.15)'" onmouseout="this.style.background='rgba(168, 85, 247, 0.08)'">
                <p class="stat-label" style="color: #fff;">Pengeringan</p>
                <div style="font-size: 1.8rem; font-weight: 900; color: #a855f7;">{{ $cleaningCounts['drying'] }}</div>
            </div>
        </a>
        <a href="{{ route('employee.orders.index', ['status' => 'ready', 'category' => 'cleaning']) }}" style="text-decoration: none;">
            <div style="background: rgba(16, 185, 129, 0.08); border: 1px solid rgba(16, 185, 129, 0.15); padding: 1.2rem; border-radius: 18px; transition: 0.3s;" onmouseover="this.style.background='rgba(16, 185, 129, 0.15)'" onmouseout="this.style.background='rgba(16, 185, 129, 0.08)'">
                <p class="stat-label" style="color: #fff;">Siap Diambil</p>
                <div style="font-size: 1.8rem; font-weight: 900; color: #10b981;">{{ $cleaningCounts['ready'] }}</div>
            </div>
        </a>
        <a href="{{ route('employee.orders.index', ['status' => 'uncollected', 'category' => 'cleaning']) }}" style="text-decoration: none;">
            <div style="background: rgba(245, 158, 11, 0.08); border: 1px solid rgba(245, 158, 11, 0.15); padding: 1.2rem; border-radius: 18px; transition: 0.3s;" onmouseover="this.style.background='rgba(245, 158, 11, 0.15)'" onmouseout="this.style.background='rgba(245, 158, 11, 0.08)'">
                <p class="stat-label" style="color: #fff;">Belum Diambil</p>
                <div style="font-size: 1.8rem; font-weight: 900; color: #f59e0b;">{{ $cleaningCounts['uncollected'] }}</div>
            </div>
        </a>
    </div>
</div>

<!-- Repair Monitoring -->
<div style="margin-bottom: 6rem;">
    <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 1.2rem; display: flex; align-items: center; gap: 0.5rem;">
        <span style="width: 4px; height: 18px; background: #f59e0b; border-radius: 4px;"></span>
        Monitoring Reparasi Sepatu
    </h3>
    <div class="grid-monitoring" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 1rem;">
        <a href="{{ route('employee.orders.index', ['status' => 'processing', 'category' => 'repair']) }}" style="text-decoration: none;">
            <div style="background: rgba(59, 130, 246, 0.08); border: 1px solid rgba(59, 130, 246, 0.15); padding: 1.2rem; border-radius: 18px; transition: 0.3s;" onmouseover="this.style.background='rgba(59, 130, 246, 0.15)'" onmouseout="this.style.background='rgba(59, 130, 246, 0.08)'">
                <p class="stat-label" style="color: #fff;">Dikerjakan</p>
                <div style="font-size: 1.8rem; font-weight: 900; color: #3b82f6;">{{ $repairCounts['processing'] }}</div>
            </div>
        </a>
        <a href="{{ route('employee.orders.index', ['status' => 'finishing', 'category' => 'repair']) }}" style="text-decoration: none;">
            <div style="background: rgba(168, 85, 247, 0.08); border: 1px solid rgba(168, 85, 247, 0.15); padding: 1.2rem; border-radius: 18px; transition: 0.3s;" onmouseover="this.style.background='rgba(168, 85, 247, 0.15)'" onmouseout="this.style.background='rgba(168, 85, 247, 0.08)'">
                <p class="stat-label" style="color: #fff;">Proses Finishing</p>
                <div style="font-size: 1.8rem; font-weight: 900; color: #a855f7;">{{ $repairCounts['finishing'] }}</div>
            </div>
        </a>
        <a href="{{ route('employee.orders.index', ['status' => 'ready', 'category' => 'repair']) }}" style="text-decoration: none;">
            <div style="background: rgba(16, 185, 129, 0.08); border: 1px solid rgba(16, 185, 129, 0.15); padding: 1.2rem; border-radius: 18px; transition: 0.3s;" onmouseover="this.style.background='rgba(16, 185, 129, 0.15)'" onmouseout="this.style.background='rgba(16, 185, 129, 0.08)'">
                <p class="stat-label" style="color: #fff;">Siap Diambil</p>
                <div style="font-size: 1.8rem; font-weight: 900; color: #10b981;">{{ $repairCounts['ready'] }}</div>
            </div>
        </a>
        <a href="{{ route('employee.orders.index', ['status' => 'uncollected', 'category' => 'repair']) }}" style="text-decoration: none;">
            <div style="background: rgba(245, 158, 11, 0.08); border: 1px solid rgba(245, 158, 11, 0.15); padding: 1.2rem; border-radius: 18px; transition: 0.3s;" onmouseover="this.style.background='rgba(245, 158, 11, 0.15)'" onmouseout="this.style.background='rgba(245, 158, 11, 0.08)'">
                <p class="stat-label" style="color: #fff;">Belum Diambil</p>
                <div style="font-size: 1.8rem; font-weight: 900; color: #f59e0b;">{{ $repairCounts['uncollected'] }}</div>
            </div>
        </a>
    </div>
</div>

    <script>
        // Auto refresh every 5 seconds for instant dashboard updates
        setTimeout(() => { window.location.reload(); }, 5000);
    </script>
@endsection
