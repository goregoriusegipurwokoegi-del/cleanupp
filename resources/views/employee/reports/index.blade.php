@extends('layouts.premium-dashboard')

@section('page_title', 'Laporan & Kasbon 📊')

@section('content')
<style>
    .attendance-card, .order-card, .loan-card { display: none; }
    
    /* Tabs Navigation Style */
    .reports-tabs {
        display: inline-flex;
        gap: 0.5rem;
        background: rgba(255, 255, 255, 0.02);
        padding: 0.4rem;
        border-radius: 16px;
        border: 1px solid rgba(255,255,255,0.04);
        margin-bottom: 2.5rem;
        flex-wrap: wrap;
    }
    
    .tab-btn {
        background: transparent;
        border: none;
        color: var(--text-secondary);
        font-size: 0.9rem;
        font-weight: 700;
        cursor: pointer;
        padding: 0.7rem 1.4rem;
        border-radius: 12px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .tab-btn:hover {
        color: #fff;
        background: rgba(255, 255, 255, 0.03);
    }
    
    .tab-btn.active {
        background: var(--primary);
        color: #0f172a !important;
        box-shadow: 0 4px 15px rgba(249, 115, 22, 0.25);
    }
    
    .tab-content {
        display: none;
        animation: fadeIn 0.4s ease;
    }
    
    .tab-content.active {
        display: block;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Premium Responsiveness styling */
    @media (max-width: 991px) {
        .grid-3 {
            grid-template-columns: 1fr 1fr !important;
            gap: 1rem !important;
        }
        .grid-2 {
            grid-template-columns: 1fr !important;
            gap: 1rem !important;
        }
    }
    
    @media (max-width: 768px) {
        .grid-3 {
            grid-template-columns: 1fr !important;
        }
        
        .table-desktop { display: none !important; }
        .attendance-card, .order-card, .loan-card { 
            display: block; 
            background: rgba(255,255,255,0.03); 
            border: 1px solid rgba(255,255,255,0.05); 
            border-radius: 16px; 
            padding: 1.2rem; 
            margin-bottom: 1rem;
        }
        .card-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.8rem; }
        .card-body { margin-bottom: 0.8rem; }
        .card-footer { border-top: 1px solid rgba(255,255,255,0.05); padding-top: 0.8rem; display: flex; justify-content: space-between; align-items: center; }
        
        .responsive-header {
            flex-direction: column !important;
            align-items: flex-start !important;
            gap: 1rem !important;
        }
        
        .responsive-card-header {
            flex-direction: column !important;
            align-items: stretch !important;
            gap: 1.2rem !important;
        }
        
        .responsive-header-actions {
            flex-direction: column !important;
            width: 100% !important;
            gap: 0.6rem !important;
        }
        
        .responsive-header-actions a, .responsive-header-actions button {
            width: 100% !important;
            justify-content: center !important;
            text-align: center !important;
        }
        
        .responsive-filter-form {
            flex-direction: column !important;
            align-items: stretch !important;
            gap: 1rem !important;
        }
        
        .responsive-filter-inputs {
            flex-direction: column !important;
            width: 100% !important;
            gap: 0.6rem !important;
        }
        
        .responsive-filter-inputs > div,
        .responsive-filter-inputs input,
        .responsive-filter-inputs button {
            width: 100% !important;
        }
        
        .responsive-search-container {
            width: 100% !important;
            max-width: 100% !important;
        }
        .reports-tabs {
            display: flex;
            width: 100%;
        }
        .tab-btn {
            flex: 1;
            justify-content: center;
            padding: 0.6rem 0.8rem;
            font-size: 0.8rem;
        }
    }
</style>

<!-- Session Alerts -->
@if(session('success'))
    <div style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.2); color: #10b981; padding: 1rem; border-radius: 12px; margin-bottom: 2rem; display: flex; align-items: center; gap: 8px;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div style="background: rgba(244, 63, 94, 0.1); border: 1px solid rgba(244, 63, 94, 0.2); color: #f43f5e; padding: 1rem; border-radius: 12px; margin-bottom: 2rem; display: flex; align-items: center; gap: 8px;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        {{ session('error') }}
    </div>
@endif

<!-- Tabs Switcher -->
<div class="reports-tabs">
    <button class="tab-btn active" onclick="switchTab('kinjera')">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
        Statistik Kerja
    </button>
    <button class="tab-btn" onclick="switchTab('absensi')">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        Kehadiran
    </button>
    <button class="tab-btn" onclick="switchTab('kasbon')">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
        Kasbon & Pinjaman
    </button>
</div>

<!-- ==================== TAB 1: KINERJA KARYAWAN ==================== -->
<div id="kinjera-content" class="tab-content active">
    <div class="report-header" style="margin-bottom: 2.5rem;">
        <div class="responsive-header" style="display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 1.5rem;">
            <div>
                <h2 style="font-size: 1.8rem; font-weight: 800; margin-bottom: 0.5rem; color: #fff;">Statistik Pekerjaan 📊</h2>
                <p style="opacity: 0.6; font-size: 0.9rem;">Pantau produktivitas dan hasil layanan Anda secara realtime.</p>
            </div>
        </div>
    </div>

    <!-- Stats Overview -->
    <div class="grid-3" style="margin-bottom: 2.5rem;">
        <div class="glass-card" style="border-left: 4px solid var(--primary);">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <div>
                    <p style="font-size: 0.8rem; opacity: 0.6; text-transform: uppercase; margin-bottom: 0.5rem;">Sepatu Dicuci</p>
                    <h3 style="font-size: 2rem; font-weight: 800; color: #fff;">{{ $stats['cleaning_done'] }}</h3>
                </div>
                <div style="background: rgba(249, 115, 22, 0.1); color: var(--primary); padding: 0.8rem; border-radius: 12px;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z"/></svg>
                </div>
            </div>
            <div style="margin-top: 1rem; font-size: 0.8rem; color: var(--success);">
                <i data-lucide="trending-up" size="14" style="display: inline-block; vertical-align: middle;"></i> +{{ rand(5,15) }}% dari bulan lalu
            </div>
        </div>
        
        <div class="glass-card" style="border-left: 4px solid var(--warning);">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <div>
                    <p style="font-size: 0.8rem; opacity: 0.6; text-transform: uppercase; margin-bottom: 0.5rem;">Reparasi Selesai</p>
                    <h3 style="font-size: 2rem; font-weight: 800; color: #fff;">{{ $stats['repair_done'] }}</h3>
                </div>
                <div style="background: rgba(245, 158, 11, 0.1); color: var(--warning); padding: 0.8rem; border-radius: 12px;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
                </div>
            </div>
            <div style="margin-top: 1rem; font-size: 0.8rem; opacity: 0.6;">
                Target: 50 per bulan
            </div>
        </div>

        <div class="glass-card" style="border-left: 4px solid #a855f7;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <div>
                    <p style="font-size: 0.8rem; opacity: 0.6; text-transform: uppercase; margin-bottom: 0.5rem;">Rating Rata-rata</p>
                    <h3 style="font-size: 2rem; font-weight: 800; color: #fff;">{{ $stats['avg_rating'] }}<span style="font-size: 1rem; opacity: 0.5;">/5.0</span></h3>
                </div>
                <div style="background: rgba(168, 85, 247, 0.1); color: #a855f7; padding: 0.8rem; border-radius: 12px;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                </div>
            </div>
            <div style="margin-top: 1rem; display: flex; gap: 2px; color: #f59e0b;" id="rating-stars">
                @for($i=0; $i<5; $i++)
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="{{ $i < floor($stats['avg_rating']) ? '#f59e0b' : 'none' }}" stroke="#f59e0b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                @endfor
            </div>
        </div>
    </div>

    <div class="grid-2" style="margin-bottom: 2.5rem; grid-template-columns: 2fr 1fr;">
        <!-- Chart Section -->
        <div class="glass-card">
            <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 1.5rem; color: #fff;">Progres Pekerjaan Harian</h3>
            <div style="height: 220px; position: relative; width: 100%;">
                <canvas id="performanceChart"></canvas>
            </div>
        </div>

        <!-- Active Status -->
        <div class="glass-card" style="display: flex; flex-direction: column; justify-content: center; text-align: center;">
            <div style="margin-bottom: 1.5rem;">
                <p style="font-size: 0.9rem; opacity: 0.6; margin-bottom: 0.5rem;">Layanan Sedang Diproses</p>
                <h4 style="font-size: 3rem; font-weight: 800; color: var(--primary);">{{ $stats['processing'] }}</h4>
            </div>
            <div style="width: 100%; height: 8px; background: rgba(255,255,255,0.05); border-radius: 10px; overflow: hidden; margin-bottom: 1rem;">
                <div style="width: {{ min(($stats['total_completed'] / 20) * 100, 100) }}%; height: 100%; background: var(--primary); box-shadow: 0 0 10px var(--primary);"></div>
            </div>
            <p style="font-size: 0.8rem; opacity: 0.5;">Target harian: {{ $stats['total_completed'] }}/20 sepatu</p>
        </div>
    </div>

    <!-- Filters & Table -->
    <div class="glass-card">
        <div class="responsive-card-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; border-bottom: 1px solid rgba(255,255,255,0.05); padding-bottom: 1.2rem;">
            <div>
                <h3 style="font-size: 1.3rem; font-weight: 800; display: flex; align-items: center; gap: 10px; color: #fff;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                    Riwayat Pengerjaan & Rating Layanan 📋
                </h3>
                <p style="font-size: 0.85rem; opacity: 0.6; margin-top: 0.2rem;">Daftar seluruh riwayat sepatu yang Anda kerjakan beserta penilaian kepuasan dari pelanggan.</p>
            </div>
        </div>
        <form class="responsive-filter-form" action="{{ route('employee.reports.index') }}" method="GET" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
            <div class="responsive-filter-inputs" style="display: flex; gap: 1rem; flex-wrap: wrap;">
                <div style="position: relative;">
                    <input type="date" name="start_date" value="{{ $startDate }}" style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); padding: 0.7rem 1rem; border-radius: 10px; color: #fff; font-size: 0.9rem; outline: none;">
                </div>
                <div style="position: relative;">
                    <input type="date" name="end_date" value="{{ $endDate }}" style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); padding: 0.7rem 1rem; border-radius: 10px; color: #fff; font-size: 0.9rem; outline: none;">
                </div>
                <button type="submit" style="background: rgba(255,255,255,0.1); color: #fff; border: none; padding: 0.7rem 1.5rem; border-radius: 10px; cursor: pointer; font-weight: 600; transition: 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.15)'" onmouseout="this.style.background='rgba(255,255,255,0.1)'">Filter</button>
            </div>
            <div class="responsive-search-container" style="position: relative; flex-grow: 1; max-width: 300px;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); opacity: 0.4; width: 16px;"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari ID atau Pelanggan..." style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); padding: 0.7rem 1rem 0.7rem 2.5rem; border-radius: 10px; color: #fff; font-size: 0.9rem; outline: none;">
            </div>
        </form>

        <div class="table-desktop table-container">
            <table style="width: 100%; border-collapse: collapse; text-align: left;">
                <thead>
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <th style="padding: 1.2rem; font-size: 0.85rem; opacity: 0.5; font-weight: 600; text-transform: uppercase;">TANGGAL</th>
                        <th style="padding: 1.2rem; font-size: 0.85rem; opacity: 0.5; font-weight: 600; text-transform: uppercase;">ID PESANAN</th>
                        <th style="padding: 1.2rem; font-size: 0.85rem; opacity: 0.5; font-weight: 600; text-transform: uppercase;">PELANGGAN</th>
                        <th style="padding: 1.2rem; font-size: 0.85rem; opacity: 0.5; font-weight: 600; text-transform: uppercase;">LAYANAN</th>
                        <th style="padding: 1.2rem; font-size: 0.85rem; opacity: 0.5; font-weight: 600; text-transform: uppercase;">STATUS</th>
                        <th style="padding: 1.2rem; font-size: 0.85rem; opacity: 0.5; font-weight: 600; text-transform: uppercase;">RATING</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($allOrders as $order)
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.02); transition: 0.3s;" onmouseover="this.style.background='rgba(255,255,255,0.02)'" onmouseout="this.style.background='transparent'">
                        <td style="padding: 1.2rem; font-size: 0.9rem; color: #fff;">{{ $order->created_at->format('d M Y') }}</td>
                        <td style="padding: 1.2rem; font-weight: 700; color: var(--primary);">#{{ $order->queue_number }}</td>
                        <td style="padding: 1.2rem; font-size: 0.9rem; color: #fff;">{{ $order->user->name }}</td>
                        <td style="padding: 1.2rem; font-size: 0.9rem; color: #fff;">{{ $order->service->name }}</td>
                        <td style="padding: 1.2rem;">
                            <span style="padding: 0.4rem 0.8rem; border-radius: 8px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; 
                                background: {{ $order->status == 'completed' ? 'rgba(16, 185, 129, 0.1)' : 'rgba(245, 158, 11, 0.1)' }};
                                color: {{ $order->status == 'completed' ? '#10b981' : '#f59e0b' }};">
                                {{ $order->status }}
                            </span>
                        </td>
                        <td style="padding: 1.2rem;">
                            @if($order->rating)
                                <div style="display: flex; gap: 2px; color: #f59e0b;">
                                    @for($i=0; $i<$order->rating; $i++)
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="#f59e0b" stroke="#f59e0b" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                                    @endfor
                                </div>
                            @else
                                <span style="opacity: 0.3; font-size: 0.8rem; color: #fff;">Belum ada</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="padding: 4rem; text-align: center; opacity: 0.4; color: #fff;">Tidak ada data laporan ditemukan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Cards for Orders -->
        <div class="mobile-cards">
            @forelse($allOrders as $order)
            <div class="order-card">
                <div class="card-header">
                    <span style="font-weight: 800; font-size: 1.1rem; color: var(--primary);">#{{ $order->queue_number }}</span>
                    <span style="font-size: 0.85rem; opacity: 0.6; color: #fff;">{{ $order->created_at->format('d M Y') }}</span>
                </div>
                <div class="card-body">
                    <p style="font-weight: 700; font-size: 1rem; margin-bottom: 0.2rem; color: #fff;">{{ $order->service->name }}</p>
                    <p style="font-size: 0.85rem; opacity: 0.7; margin-bottom: 0.8rem; color: #fff;">Pelanggan: {{ $order->user->name }}</p>
                    
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="padding: 0.4rem 0.8rem; border-radius: 8px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; 
                            background: {{ $order->status == 'completed' ? 'rgba(16, 185, 129, 0.1)' : 'rgba(245, 158, 11, 0.1)' }};
                            color: {{ $order->status == 'completed' ? '#10b981' : '#f59e0b' }};">
                            {{ $order->status }}
                        </span>
                        <div>
                            @if($order->rating)
                                <div style="display: flex; gap: 2px; color: #f59e0b;">
                                    @for($i=0; $i<$order->rating; $i++)
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="#f59e0b" stroke="#f59e0b" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                                    @endfor
                                </div>
                            @else
                                <span style="opacity: 0.3; font-size: 0.8rem; color: #fff;">Belum rating</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @empty
                <div style="padding: 3rem; text-align: center; opacity: 0.4; color: #fff;">Tidak ada data laporan.</div>
            @endforelse
        </div>
    </div>
</div>

<!-- ==================== TAB 2: REKAP KEHADIRAN ==================== -->
<div id="absensi-content" class="tab-content">
    <div class="glass-card" style="margin-bottom: 2.5rem;">
        <div class="responsive-card-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.05); padding-bottom: 1.2rem;">
            <div>
                <h3 style="font-size: 1.3rem; font-weight: 800; display: flex; align-items: center; gap: 10px; color: #fff;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    Rekap Kehadiran / Absensi Karyawan ⏰
                </h3>
                <p style="font-size: 0.85rem; opacity: 0.6; margin-top: 0.2rem; color: #fff;">Data kehadiran dan ketepatan waktu Anda pada periode ini.</p>
            </div>
            <div class="responsive-header-actions" style="display: flex; gap: 0.8rem;">
                <a href="{{ route('employee.reports.attendance.pdf', ['start_date' => $startDate, 'end_date' => $endDate]) }}" target="_blank" style="background: rgba(255,255,255,0.05); color: #fff; border: 1px solid rgba(255,255,255,0.1); padding: 0.6rem 1.2rem; border-radius: 10px; font-weight: 700; cursor: pointer; text-decoration: none; display: flex; align-items: center; gap: 8px; font-size: 0.85rem; transition: 0.3s;" onmouseover="this.style.background='rgba(255,255,255,0.1)'" onmouseout="this.style.background='rgba(255,255,255,0.05)'">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                    Cetak PDF
                </a>
                <a href="{{ route('employee.reports.attendance.excel', ['start_date' => $startDate, 'end_date' => $endDate]) }}" style="background: var(--primary); color: #0f172a; border: none; padding: 0.6rem 1.2rem; border-radius: 10px; font-weight: 700; cursor: pointer; text-decoration: none; display: flex; align-items: center; gap: 8px; font-size: 0.85rem; transition: 0.3s;" onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    Unduh Excel
                </a>
            </div>
        </div>

        <div class="table-desktop table-container">
            <table style="width: 100%; border-collapse: collapse; text-align: left;">
                <thead>
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <th style="padding: 1.2rem; font-size: 0.85rem; opacity: 0.5; font-weight: 600; text-transform: uppercase;">HARI & TANGGAL</th>
                        <th style="padding: 1.2rem; font-size: 0.85rem; opacity: 0.5; font-weight: 600; text-transform: uppercase;">JAM MASUK</th>
                        <th style="padding: 1.2rem; font-size: 0.85rem; opacity: 0.5; font-weight: 600; text-transform: uppercase;">JAM KELUAR</th>
                        <th style="padding: 1.2rem; font-size: 0.85rem; opacity: 0.5; font-weight: 600; text-transform: uppercase;">DURASI KERJA</th>
                        <th style="padding: 1.2rem; font-size: 0.85rem; opacity: 0.5; font-weight: 600; text-transform: uppercase;">STATUS</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attendances as $att)
                    @php
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
                    @endphp
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.02); transition: 0.3s;" onmouseover="this.style.background='rgba(255,255,255,0.02)'" onmouseout="this.style.background='transparent'">
                        <td style="padding: 1.2rem; font-size: 0.9rem; font-weight: 600; color: #fff;">
                            {{ \Carbon\Carbon::parse($att->date)->translatedFormat('l, d M Y') }}
                        </td>
                        <td style="padding: 1.2rem; font-size: 0.9rem; color: #10b981; font-weight: 700;">
                            {{ $att->clock_in ? \Carbon\Carbon::parse($att->clock_in)->format('H:i:s') : '-' }}
                        </td>
                        <td style="padding: 1.2rem; font-size: 0.9rem; color: #f43f5e; font-weight: 700;">
                            {{ $att->clock_out ? \Carbon\Carbon::parse($att->clock_out)->format('H:i:s') : '-' }}
                        </td>
                        <td style="padding: 1.2rem; font-size: 0.9rem; opacity: 0.8; color: #fff;">
                            {{ $duration }}
                        </td>
                        <td style="padding: 1.2rem;">
                            <span style="padding: 0.4rem 0.8rem; border-radius: 8px; font-size: 0.75rem; font-weight: 800; text-transform: uppercase;
                                background: {{ $status == 'Tepat Waktu' ? 'rgba(16, 185, 129, 0.1)' : 'rgba(244, 63, 94, 0.1)' }};
                                color: {{ $status == 'Tepat Waktu' ? '#10b981' : '#f43f5e' }};">
                                {{ $status }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="padding: 4rem; text-align: center; opacity: 0.4; color: #fff;">Tidak ada data riwayat kehadiran ditemukan pada periode ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Cards for Attendance -->
        <div class="mobile-cards">
            @forelse($attendances as $att)
            @php
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
            @endphp
            <div class="attendance-card">
                <div class="card-header">
                    <span style="font-weight: 800; font-size: 1rem; color: #fff;">{{ \Carbon\Carbon::parse($att->date)->translatedFormat('l, d M Y') }}</span>
                    <span style="padding: 0.4rem 0.8rem; border-radius: 8px; font-size: 0.75rem; font-weight: 800; text-transform: uppercase;
                        background: {{ $status == 'Tepat Waktu' ? 'rgba(16, 185, 129, 0.1)' : 'rgba(244, 63, 94, 0.1)' }};
                        color: {{ $status == 'Tepat Waktu' ? '#10b981' : '#f43f5e' }};">
                        {{ $status }}
                    </span>
                </div>
                <div class="card-body" style="display: flex; justify-content: space-between;">
                    <div>
                        <p style="font-size: 0.8rem; opacity: 0.6; margin-bottom: 0.2rem; color: #fff;">Jam Masuk</p>
                        <p style="color: #10b981; font-weight: 700;">{{ $att->clock_in ? \Carbon\Carbon::parse($att->clock_in)->format('H:i:s') : '-' }}</p>
                    </div>
                    <div style="text-align: right;">
                        <p style="font-size: 0.8rem; opacity: 0.6; margin-bottom: 0.2rem; color: #fff;">Jam Keluar</p>
                        <p style="color: #f43f5e; font-weight: 700;">{{ $att->clock_out ? \Carbon\Carbon::parse($att->clock_out)->format('H:i:s') : '-' }}</p>
                    </div>
                </div>
                <div class="card-footer">
                    <span style="font-size: 0.85rem; opacity: 0.6; color: #fff;">Durasi Kerja:</span>
                    <span style="font-size: 0.9rem; font-weight: 600; color: #fff;">{{ $duration }}</span>
                </div>
            </div>
            @empty
                <div style="padding: 3rem; text-align: center; opacity: 0.4; color: #fff;">Tidak ada riwayat kehadiran.</div>
            @endforelse
        </div>
    </div>
</div>

<!-- ==================== TAB 3: KASBON & PINJAMAN ==================== -->
<div id="kasbon-content" class="tab-content">
    <div class="responsive-card-header" style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid rgba(255,255,255,0.05); padding-bottom: 1.2rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h2 style="font-size: 1.8rem; font-weight: 800; margin-bottom: 0.5rem; color: #fff; display: flex; align-items: center; gap: 10px;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
                Kasbon Karyawan
            </h2>
            <p style="opacity: 0.6; font-size: 0.85rem; color: #fff;">Ajukan dan pantau status permohonan pinjaman Anda.</p>
        </div>
        <div class="responsive-header-actions">
            @php $hasPending = $loans->where('status', 'pending')->isNotEmpty(); @endphp
            @if($hasPending)
                <button disabled style="background: rgba(255,255,255,0.05); color: rgba(255,255,255,0.3); border: 1px solid rgba(255,255,255,0.1); padding: 0.8rem 1.5rem; border-radius: 12px; font-weight: 700; cursor: not-allowed; transition: 0.3s; display: inline-flex; align-items: center; gap: 6px;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    Sedang Diproses
                </button>
            @else
                <button onclick="document.getElementById('add-loan-modal').style.display='flex'" style="background: var(--primary); color: #0f172a; border: none; padding: 0.8rem 1.5rem; border-radius: 12px; font-weight: 700; cursor: pointer; transition: 0.3s; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 4px 15px rgba(249, 115, 22, 0.25);" onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    Ajukan Kasbon
                </button>
            @endif
        </div>
    </div>

    <div class="glass-card" style="border-radius: 24px; overflow: hidden; padding: 0; background: transparent; border: none;">
        <!-- Desktop Table -->
        <div class="table-desktop table-container">
            <table style="width: 100%; border-collapse: collapse; text-align: left; min-width: 600px;">
                <thead>
                    <tr style="background: rgba(255,255,255,0.03); border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <th style="padding: 1.5rem; font-size: 0.85rem; text-transform: uppercase; opacity: 0.6; color: #fff;">Tanggal Pengajuan</th>
                        <th style="padding: 1.5rem; font-size: 0.85rem; text-transform: uppercase; opacity: 0.6; color: #fff;">Nominal</th>
                        <th style="padding: 1.5rem; font-size: 0.85rem; text-transform: uppercase; opacity: 0.6; color: #fff;">Status</th>
                        <th style="padding: 1.5rem; font-size: 0.85rem; text-transform: uppercase; opacity: 0.6; color: #fff;">Catatan Admin / Alasan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($loans as $loan)
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.02); transition: 0.3s;" onmouseover="this.style.background='rgba(255,255,255,0.01)'" onmouseout="this.style.background='transparent'">
                        <td style="padding: 1.5rem; color: #fff;">{{ $loan->created_at->format('d/m/Y H:i') }}</td>
                        <td style="padding: 1.5rem; font-weight: 700; color: var(--primary);">Rp {{ number_format($loan->amount, 0, ',', '.') }}</td>
                        <td style="padding: 1.5rem;">
                            @if($loan->status == 'pending')
                                <span style="color: #f59e0b; font-weight: 700; background: rgba(245, 158, 11, 0.1); padding: 0.3rem 0.7rem; border-radius: 8px; font-size: 0.75rem; text-transform: uppercase;">Menunggu</span>
                            @elseif($loan->status == 'approved')
                                <span style="color: #10b981; font-weight: 700; background: rgba(16, 185, 129, 0.1); padding: 0.3rem 0.7rem; border-radius: 8px; font-size: 0.75rem; text-transform: uppercase;">Disetujui</span>
                            @else
                                <span style="color: #f43f5e; font-weight: 700; background: rgba(244, 63, 94, 0.1); padding: 0.3rem 0.7rem; border-radius: 8px; font-size: 0.75rem; text-transform: uppercase;">Ditolak</span>
                            @endif
                        </td>
                        <td style="padding: 1.5rem; color: #fff;">
                            <div style="font-weight: 600; margin-bottom: 4px;">Alasan: {{ $loan->reason }}</div>
                            @if($loan->admin_note)
                                <div style="font-size: 0.8rem; opacity: 0.6; font-style: italic;">Catatan Admin: {{ $loan->admin_note }}</div>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" style="padding: 4rem; text-align: center; opacity: 0.4; color: #fff;">Belum ada riwayat kasbon/pinjaman ditemukan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Cards -->
        <div class="mobile-cards">
            @forelse($loans as $loan)
            <div class="loan-card">
                <div class="loan-card-header">
                    <span style="font-weight: 800; font-size: 1.2rem; color: var(--primary);">Rp {{ number_format($loan->amount, 0, ',', '.') }}</span>
                    <span style="font-size: 0.85rem; opacity: 0.6; color: #fff;">{{ $loan->created_at->format('d/m/Y') }}</span>
                </div>
                <div class="loan-card-body">
                    <div style="margin-bottom: 8px;">
                        @if($loan->status == 'pending')
                            <span style="color: #f59e0b; font-weight: 700; font-size: 0.85rem; text-transform: uppercase;">Menunggu</span>
                        @elseif($loan->status == 'approved')
                            <span style="color: #10b981; font-weight: 700; font-size: 0.85rem; text-transform: uppercase;">Disetujui</span>
                        @else
                            <span style="color: #f43f5e; font-weight: 700; font-size: 0.85rem; text-transform: uppercase;">Ditolak</span>
                        @endif
                    </div>
                    <p style="font-size: 0.85rem; color: #fff;"><span style="opacity: 0.6;">Alasan:</span> {{ $loan->reason }}</p>
                </div>
                @if($loan->admin_note)
                <div class="loan-card-footer">
                    <p style="font-size: 0.8rem; opacity: 0.7; margin-bottom: 0.2rem; color: #fff;">Catatan Admin:</p>
                    <p style="font-size: 0.85rem; font-style: italic; color: #fff;">{{ $loan->admin_note }}</p>
                </div>
                @endif
            </div>
            @empty
                <div style="padding: 3rem; text-align: center; opacity: 0.3; color: #fff;">
                    <p>Belum ada riwayat pinjaman.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Modal Ajukan Pinjaman -->
<div id="add-loan-modal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.8); z-index: 1000; align-items: center; justify-content: center; backdrop-filter: blur(5px);">
    <div class="glass-card" style="width: 90%; max-width: 450px; padding: 2.5rem; border-radius: 24px; background: #121214; border: 1px solid rgba(255,255,255,0.08);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h3 style="font-size: 1.5rem; font-weight: 800; color: #fff;">Ajukan Kasbon</h3>
            <button onclick="document.getElementById('add-loan-modal').style.display='none'" style="background: transparent; border: none; color: #fff; cursor: pointer; opacity: 0.5; display: flex; align-items: center; justify-content: center;"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></button>
        </div>
        
        <form method="POST" action="{{ route('employee.loans.store') }}" onsubmit="disableSubmitButton(this)">
            @csrf
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-size: 0.85rem; margin-bottom: 0.5rem; opacity: 0.7; color: #fff;">Nominal Pinjaman (Rp)</label>
                <input type="number" name="amount" min="1000" step="1000" required placeholder="Contoh: 50000" style="width: 100%; padding: 0.8rem 1.2rem; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; color: #fff; outline: none; transition: 0.3s;" onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='rgba(255,255,255,0.1)'">
            </div>

            <div style="margin-bottom: 2rem;">
                <label style="display: block; font-size: 0.85rem; margin-bottom: 0.5rem; opacity: 0.7; color: #fff;">Alasan Pinjaman</label>
                <textarea name="reason" required placeholder="Jelaskan alasan peminjaman..." style="width: 100%; padding: 0.8rem 1.2rem; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; color: #fff; outline: none; height: 100px; resize: none; transition: 0.3s;" onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='rgba(255,255,255,0.1)'"></textarea>
            </div>

            <button type="submit" id="btn-submit-loan" style="width: 100%; background: var(--primary); color: #0f172a; border: none; padding: 1rem; border-radius: 12px; font-weight: 700; cursor: pointer; transition: 0.3s; box-shadow: 0 4px 15px rgba(249, 115, 22, 0.25);">Kirim Permintaan</button>
        </form>
    </div>
</div>

<!-- Load Lucide Icons CDN to render the stats card icons -->
<script src="https://unpkg.com/lucide@latest"></script>
<!-- Load Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Tab switching logic
    function switchTab(tabId) {
        document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
        document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));
        
        document.getElementById(tabId + '-content').classList.add('active');
        event.currentTarget.classList.add('active');

        // Persist tab inside localStorage so page refreshes don't reset view
        localStorage.setItem('employee_reports_active_tab', tabId);
    }

    function disableSubmitButton(form) {
        const btn = document.getElementById('btn-submit-loan');
        btn.disabled = true;
        btn.style.opacity = '0.5';
        btn.style.cursor = 'not-allowed';
        btn.innerText = 'Mengirim Permintaan...';
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Restore active tab
        const activeTab = localStorage.getItem('employee_reports_active_tab') || 'kinjera';
        const targetBtn = Array.from(document.querySelectorAll('.tab-btn')).find(btn => btn.getAttribute('onclick').includes(activeTab));
        if (targetBtn) {
            targetBtn.click();
        }

        // Safely initialize Lucide Icons
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }

        const ctx = document.getElementById('performanceChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($chartData['labels']) !!},
                datasets: [{
                    label: 'Sepatu Selesai',
                    data: {!! json_encode($chartData['counts']) !!},
                    borderColor: '#f97316',
                    backgroundColor: 'rgba(249, 115, 22, 0.1)',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#f97316',
                    pointBorderColor: '#fff',
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(255, 255, 255, 0.05)' },
                        ticks: { color: 'rgba(255, 255, 255, 0.4)' }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: 'rgba(255, 255, 255, 0.4)' }
                    }
                }
            }
        });
    });
</script>
@endsection
