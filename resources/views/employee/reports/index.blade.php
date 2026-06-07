@extends('layouts.premium-dashboard')

@section('page_title', 'Laporan Karyawan 📊')

@section('content')
<style>
    .reports-layout {
        display: flex;
        gap: 2rem;
        align-items: flex-start;
    }
    
    .reports-sidebar {
        width: 250px;
        flex-shrink: 0;
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid rgba(255, 255, 255, 0.05);
        border-radius: 20px;
        padding: 1rem;
        position: sticky;
        top: 2rem;
    }
    
    .reports-content {
        flex-grow: 1;
        min-width: 0;
    }
    
    .tab-btn {
        width: 100%;
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 1rem 1.2rem;
        background: transparent;
        border: none;
        color: rgba(255, 255, 255, 0.6);
        font-size: 0.95rem;
        font-weight: 600;
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.3s ease;
        text-align: left;
        margin-bottom: 0.5rem;
    }
    
    .tab-btn:hover {
        background: rgba(255, 255, 255, 0.05);
        color: #fff;
    }
    
    .tab-btn.active {
        background: var(--primary);
        color: #0f172a !important;
        box-shadow: 0 4px 15px rgba(249, 115, 22, 0.25);
    }
    
    .tab-btn.active svg {
        stroke: #0f172a;
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
    
    .achievement-badge {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        padding: 2rem;
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.05);
        border-radius: 24px;
        gap: 1rem;
        transition: all 0.3s ease;
    }
    
    .achievement-badge.locked {
        opacity: 0.4;
        filter: grayscale(100%);
    }
    
    .achievement-icon {
        width: 80px;
        height: 80px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.05);
        font-size: 2.5rem;
    }

    .badge-gold { background: linear-gradient(135deg, rgba(250, 204, 21, 0.2), rgba(234, 179, 8, 0.1)); border: 1px solid rgba(250, 204, 21, 0.3); }
    .badge-blue { background: linear-gradient(135deg, rgba(56, 189, 248, 0.2), rgba(14, 165, 233, 0.1)); border: 1px solid rgba(56, 189, 248, 0.3); }
    .badge-purple { background: linear-gradient(135deg, rgba(168, 85, 247, 0.2), rgba(139, 92, 246, 0.1)); border: 1px solid rgba(168, 85, 247, 0.3); }

    .responsive-table {
        width: 100%;
        border-collapse: collapse;
    }

    .responsive-table th {
        padding: 1.2rem;
        font-size: 0.85rem;
        opacity: 0.5;
        font-weight: 600;
        text-transform: uppercase;
        text-align: left;
        border-bottom: 1px solid rgba(255,255,255,0.05);
    }

    .responsive-table td {
        padding: 1.2rem;
        font-size: 0.9rem;
        color: #fff;
        border-bottom: 1px solid rgba(255,255,255,0.02);
    }

    .mobile-cards { display: none; }

    @media (max-width: 991px) {
        .reports-layout {
            flex-direction: column;
        }
        
        .reports-sidebar {
            width: 100%;
            position: relative;
            top: 0;
            display: flex;
            overflow-x: auto;
            padding: 0.5rem;
            gap: 0.5rem;
            border-radius: 16px;
        }
        
        .tab-btn {
            width: auto;
            white-space: nowrap;
            margin-bottom: 0;
            padding: 0.8rem 1.2rem;
        }

        .reports-sidebar::-webkit-scrollbar {
            height: 4px;
        }
        
        .reports-sidebar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 4px;
        }
    }

    @media (max-width: 768px) {
        .desktop-table { display: none; }
        .mobile-cards { display: grid; gap: 1rem; grid-template-columns: 1fr; }
        
        .mobile-card {
            background: rgba(255,255,255,0.03); 
            border: 1px solid rgba(255,255,255,0.05); 
            border-radius: 16px; 
            padding: 1.2rem; 
        }

        .grid-3 { grid-template-columns: 1fr !important; }
        .grid-2 { grid-template-columns: 1fr !important; }
    }
</style>

<div class="reports-layout">
    <!-- Main Content Area -->
    <div class="reports-content" style="width: 100%;">
        <!-- Fitur Filter Global -->
        <div class="glass-card" style="margin-bottom: 2rem; padding: 1.5rem !important;">
            <form action="{{ route('employee.reports.index') }}" method="GET" style="display: flex; gap: 1rem; align-items: flex-end; flex-wrap: wrap;">
                <div>
                    <label style="display: block; font-size: 0.8rem; opacity: 0.7; margin-bottom: 0.4rem; color: #fff;">Tanggal Mulai</label>
                    <input type="date" name="start_date" value="{{ $startDate }}" style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); padding: 0.7rem 1rem; border-radius: 10px; color: #fff; outline: none;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.8rem; opacity: 0.7; margin-bottom: 0.4rem; color: #fff;">Tanggal Selesai</label>
                    <input type="date" name="end_date" value="{{ $endDate }}" style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); padding: 0.7rem 1rem; border-radius: 10px; color: #fff; outline: none;">
                </div>
                <div style="flex-grow: 1; min-width: 200px;">
                    <label style="display: block; font-size: 0.8rem; opacity: 0.7; margin-bottom: 0.4rem; color: #fff;">Pencarian (Opsional)</label>
                    <div style="position: relative;">
                        <i data-lucide="search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); opacity: 0.4; width: 16px;"></i>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari ID Pesanan..." style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); padding: 0.7rem 1rem 0.7rem 2.5rem; border-radius: 10px; color: #fff; outline: none;">
                    </div>
                </div>
                <button type="submit" style="background: var(--primary); color: #0f172a; border: none; padding: 0.7rem 1.5rem; border-radius: 10px; font-weight: 700; cursor: pointer;">Terapkan</button>
            </form>
        </div>

        <!-- TAB 1: RINGKASAN KINERJA -->
        <div id="ringkasan-content" class="tab-content active">
            <h2 style="font-size: 1.5rem; font-weight: 800; margin-bottom: 1.5rem; color: #fff;">Ringkasan Kinerja Bulan Ini</h2>
            
            <div class="grid-3" style="margin-bottom: 2rem;">
                <div class="glass-card" style="border-left: 4px solid var(--primary);">
                    <p style="font-size: 0.85rem; opacity: 0.6; text-transform: uppercase; margin-bottom: 0.5rem;">Selesai Dikerjakan</p>
                    <h3 style="font-size: 2.5rem; font-weight: 800; color: #fff;">{{ $stats['total_completed'] }} <span style="font-size: 1rem; opacity: 0.5;">Sepatu</span></h3>
                </div>
                <div class="glass-card" style="border-left: 4px solid #3b82f6;">
                    <p style="font-size: 0.85rem; opacity: 0.6; text-transform: uppercase; margin-bottom: 0.5rem;">Sedang Diproses</p>
                    <h3 style="font-size: 2.5rem; font-weight: 800; color: #fff;">{{ $stats['processing'] }} <span style="font-size: 1rem; opacity: 0.5;">Sepatu</span></h3>
                </div>
                <div class="glass-card" style="border-left: 4px solid #a855f7;">
                    <p style="font-size: 0.85rem; opacity: 0.6; text-transform: uppercase; margin-bottom: 0.5rem;">Rata-rata Rating</p>
                    <h3 style="font-size: 2.5rem; font-weight: 800; color: #fff;">{{ $stats['avg_rating'] }} <i data-lucide="star" style="fill: #f59e0b; stroke: #f59e0b; width: 24px;"></i></h3>
                </div>
            </div>

            <div class="grid-2" style="gap: 1.5rem;">
                <div class="glass-card">
                    <h4 style="margin-bottom: 1rem; color: #fff;">Distribusi Pekerjaan Selesai</h4>
                    <div style="display: flex; flex-direction: column; gap: 1rem;">
                        <div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                                <span style="opacity: 0.8;">Cleaning (Cuci)</span>
                                <span>{{ $stats['cleaning_done'] }}</span>
                            </div>
                            <div style="width: 100%; height: 8px; background: rgba(255,255,255,0.05); border-radius: 4px;">
                                <div style="height: 100%; width: {{ $stats['total_completed'] > 0 ? ($stats['cleaning_done'] / $stats['total_completed']) * 100 : 0 }}%; background: #3b82f6; border-radius: 4px;"></div>
                            </div>
                        </div>
                        <div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                                <span style="opacity: 0.8;">Reparasi</span>
                                <span>{{ $stats['repair_done'] }}</span>
                            </div>
                            <div style="width: 100%; height: 8px; background: rgba(255,255,255,0.05); border-radius: 4px;">
                                <div style="height: 100%; width: {{ $stats['total_completed'] > 0 ? ($stats['repair_done'] / $stats['total_completed']) * 100 : 0 }}%; background: var(--primary); border-radius: 4px;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 2: TUGAS SAYA -->
        <div id="tugas-content" class="tab-content">
            <h2 style="font-size: 1.5rem; font-weight: 800; margin-bottom: 1.5rem; color: #fff;">Tugas Aktif Saya</h2>
            <div class="glass-card" style="padding: 0 !important; overflow: hidden;">
                <div class="desktop-table" style="overflow-x: auto;">
                    <table class="responsive-table">
                        <thead>
                            <tr>
                                <th>ID Pesanan</th>
                                <th>Pelanggan</th>
                                <th>Layanan</th>
                                <th>Status</th>
                                <th>Masuk Pada</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($myTasks as $task)
                            <tr>
                                <td style="font-weight: 700; color: var(--primary);">#{{ $task->queue_number }}</td>
                                <td>{{ $task->user->name }}</td>
                                <td>{{ $task->service->name }}</td>
                                <td>
                                    <span style="padding: 4px 8px; border-radius: 6px; font-size: 0.75rem; background: rgba(59, 130, 246, 0.1); color: #3b82f6; text-transform: uppercase;">{{ $task->status }}</span>
                                </td>
                                <td>{{ $task->created_at->format('d M Y') }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="5" style="text-align: center; padding: 3rem; opacity: 0.5;">Tidak ada tugas aktif.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mobile-cards" style="padding: 1rem;">
                    @forelse($myTasks as $task)
                    <div class="mobile-card">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                            <strong style="color: var(--primary);">#{{ $task->queue_number }}</strong>
                            <span style="font-size: 0.8rem; color: #3b82f6;">{{ strtoupper($task->status) }}</span>
                        </div>
                        <p style="font-size: 0.9rem; margin-bottom: 0.2rem;">{{ $task->service->name }}</p>
                        <p style="font-size: 0.8rem; opacity: 0.6;">{{ $task->user->name }}</p>
                    </div>
                    @empty
                    <p style="text-align: center; opacity: 0.5;">Tidak ada tugas aktif.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- TAB 3: RIWAYAT PEKERJAAN -->
        <div id="riwayat-content" class="tab-content">
            <h2 style="font-size: 1.5rem; font-weight: 800; margin-bottom: 1.5rem; color: #fff;">Riwayat Pekerjaan Selesai</h2>
            <div class="glass-card" style="padding: 0 !important; overflow: hidden;">
                <div class="desktop-table" style="overflow-x: auto;">
                    <table class="responsive-table">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>ID Pesanan</th>
                                <th>Layanan</th>
                                <th>Harga Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($historyTasks as $task)
                            <tr>
                                <td>{{ $task->updated_at->format('d M Y H:i') }}</td>
                                <td style="font-weight: 700; color: var(--primary);">#{{ $task->queue_number }}</td>
                                <td>{{ $task->service->name }}</td>
                                <td>Rp {{ number_format($task->total_price, 0, ',', '.') }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="4" style="text-align: center; padding: 3rem; opacity: 0.5;">Tidak ada riwayat pekerjaan.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mobile-cards" style="padding: 1rem;">
                    @forelse($historyTasks as $task)
                    <div class="mobile-card">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                            <strong style="color: var(--primary);">#{{ $task->queue_number }}</strong>
                            <span style="font-size: 0.8rem; opacity: 0.6;">{{ $task->updated_at->format('d/m/Y') }}</span>
                        </div>
                        <p style="font-size: 0.9rem;">{{ $task->service->name }}</p>
                        <p style="font-size: 0.9rem; font-weight: 600; margin-top: 0.5rem;">Rp {{ number_format($task->total_price, 0, ',', '.') }}</p>
                    </div>
                    @empty
                    <p style="text-align: center; opacity: 0.5;">Tidak ada riwayat pekerjaan.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- TAB 4: REKAP ABSENSI -->
        <div id="absensi-content" class="tab-content">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
                <h2 style="font-size: 1.5rem; font-weight: 800; color: #fff;">Rekap Absensi</h2>
                <div style="display: flex; gap: 0.5rem;">
                    <a href="{{ route('employee.reports.attendance.pdf', ['start_date' => $startDate, 'end_date' => $endDate]) }}" target="_blank" class="tab-btn" style="width: auto; background: rgba(255,255,255,0.1); padding: 0.5rem 1rem;">PDF</a>
                    <a href="{{ route('employee.reports.attendance.excel', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="tab-btn" style="width: auto; background: var(--primary); color: #0f172a; padding: 0.5rem 1rem;">Excel</a>
                </div>
            </div>
            
            <div class="glass-card" style="padding: 0 !important; overflow: hidden;">
                <div class="desktop-table" style="overflow-x: auto;">
                    <table class="responsive-table">
                        <thead>
                            <tr>
                                <th>Hari, Tanggal</th>
                                <th>Jam Masuk</th>
                                <th>Jam Keluar</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($attendances as $att)
                            @php
                                $status = ($att->clock_in && \Carbon\Carbon::parse($att->clock_in)->format('H:i') > '09:00') ? 'Terlambat' : 'Tepat Waktu';
                                $color = $status == 'Tepat Waktu' ? '#10b981' : '#f43f5e';
                                $bg = $status == 'Tepat Waktu' ? 'rgba(16, 185, 129, 0.1)' : 'rgba(244, 63, 94, 0.1)';
                            @endphp
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($att->date)->translatedFormat('l, d M Y') }}</td>
                                <td style="color: #10b981; font-weight: 600;">{{ $att->clock_in ? \Carbon\Carbon::parse($att->clock_in)->format('H:i') : '-' }}</td>
                                <td style="color: #f43f5e; font-weight: 600;">{{ $att->clock_out ? \Carbon\Carbon::parse($att->clock_out)->format('H:i') : '-' }}</td>
                                <td><span style="background: {{ $bg }}; color: {{ $color }}; padding: 4px 8px; border-radius: 6px; font-size: 0.75rem; text-transform: uppercase;">{{ $status }}</span></td>
                            </tr>
                            @empty
                            <tr><td colspan="4" style="text-align: center; padding: 3rem; opacity: 0.5;">Belum ada data absensi.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mobile-cards" style="padding: 1rem;">
                    @forelse($attendances as $att)
                    @php
                        $status = ($att->clock_in && \Carbon\Carbon::parse($att->clock_in)->format('H:i') > '09:00') ? 'Terlambat' : 'Tepat Waktu';
                        $color = $status == 'Tepat Waktu' ? '#10b981' : '#f43f5e';
                    @endphp
                    <div class="mobile-card">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                            <strong>{{ \Carbon\Carbon::parse($att->date)->translatedFormat('d M Y') }}</strong>
                            <span style="color: {{ $color }}; font-size: 0.8rem;">{{ $status }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; font-size: 0.9rem;">
                            <span>Masuk: <span style="color: #10b981;">{{ $att->clock_in ? \Carbon\Carbon::parse($att->clock_in)->format('H:i') : '-' }}</span></span>
                            <span>Keluar: <span style="color: #f43f5e;">{{ $att->clock_out ? \Carbon\Carbon::parse($att->clock_out)->format('H:i') : '-' }}</span></span>
                        </div>
                    </div>
                    @empty
                    <p style="text-align: center; opacity: 0.5;">Belum ada data absensi.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- TAB 5: RATING PELANGGAN -->
        <div id="rating-content" class="tab-content">
            <h2 style="font-size: 1.5rem; font-weight: 800; margin-bottom: 1.5rem; color: #fff;">Penilaian & Feedback</h2>
            <div class="glass-card" style="padding: 0 !important; overflow: hidden;">
                <div class="desktop-table" style="overflow-x: auto;">
                    <table class="responsive-table">
                        <thead>
                            <tr>
                                <th>Pesanan</th>
                                <th>Rating</th>
                                <th>Komentar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($customerRatings as $task)
                            <tr>
                                <td style="font-weight: 700; color: var(--primary);">#{{ $task->queue_number }}</td>
                                <td>
                                    <div style="display: flex; gap: 2px;">
                                        @for($i=0; $i<$task->rating; $i++)
                                            <i data-lucide="star" style="fill: #f59e0b; stroke: #f59e0b; width: 14px;"></i>
                                        @endfor
                                    </div>
                                </td>
                                <td>{{ $task->review ?? '-' }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="3" style="text-align: center; padding: 3rem; opacity: 0.5;">Belum ada rating.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mobile-cards" style="padding: 1rem;">
                    @forelse($customerRatings as $task)
                    <div class="mobile-card">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                            <strong style="color: var(--primary);">#{{ $task->queue_number }}</strong>
                            <div style="display: flex; gap: 2px;">
                                @for($i=0; $i<$task->rating; $i++)
                                    <i data-lucide="star" style="fill: #f59e0b; stroke: #f59e0b; width: 12px;"></i>
                                @endfor
                            </div>
                        </div>
                        <p style="font-size: 0.9rem; font-style: italic; opacity: 0.8;">"{{ $task->review ?? 'Tanpa komentar' }}"</p>
                    </div>
                    @empty
                    <p style="text-align: center; opacity: 0.5;">Belum ada rating.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- TAB 6: STATISTIK KERJA -->
        <div id="statistik-content" class="tab-content">
            <h2 style="font-size: 1.5rem; font-weight: 800; margin-bottom: 1.5rem; color: #fff;">Statistik Penyelesaian Pekerjaan</h2>
            <div class="glass-card">
                <div style="height: 300px; position: relative; width: 100%;">
                    <canvas id="performanceChart"></canvas>
                </div>
            </div>
        </div>

        <!-- TAB 7: LAPORAN ANTAR JEMPUT -->
        <div id="antar_jemput-content" class="tab-content">
            <h2 style="font-size: 1.5rem; font-weight: 800; margin-bottom: 1.5rem; color: #fff;">Daftar Layanan Antar Jemput</h2>
            <div class="glass-card" style="padding: 0 !important; overflow: hidden;">
                <div class="desktop-table" style="overflow-x: auto;">
                    <table class="responsive-table">
                        <thead>
                            <tr>
                                <th>Pesanan</th>
                                <th>Status</th>
                                <th>Alamat Pengiriman</th>
                                <th>Biaya Antar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($deliveryTasks as $task)
                            <tr>
                                <td style="font-weight: 700; color: var(--primary);">#{{ $task->queue_number }}</td>
                                <td><span style="background: rgba(255,255,255,0.1); padding: 4px 8px; border-radius: 6px; font-size: 0.75rem; text-transform: uppercase;">{{ $task->status }}</span></td>
                                <td>{{ Str::limit($task->delivery_address, 50) }}</td>
                                <td>Rp {{ number_format($task->delivery_fee, 0, ',', '.') }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="4" style="text-align: center; padding: 3rem; opacity: 0.5;">Tidak ada pesanan antar jemput.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mobile-cards" style="padding: 1rem;">
                    @forelse($deliveryTasks as $task)
                    <div class="mobile-card">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                            <strong style="color: var(--primary);">#{{ $task->queue_number }}</strong>
                            <span style="font-size: 0.8rem; opacity: 0.6;">{{ strtoupper($task->status) }}</span>
                        </div>
                        <p style="font-size: 0.85rem; opacity: 0.8; margin-bottom: 0.5rem;">{{ $task->delivery_address }}</p>
                        <p style="font-size: 0.9rem; font-weight: 600;">Ongkir: Rp {{ number_format($task->delivery_fee, 0, ',', '.') }}</p>
                    </div>
                    @empty
                    <p style="text-align: center; opacity: 0.5;">Tidak ada pesanan antar jemput.</p>
                    @endforelse
                </div>
            </div>
        </div>

    </div>
</div>

<script src="https://unpkg.com/lucide@latest"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    function switchTab(tabId) {
        document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
        const target = document.getElementById(tabId + '-content');
        if (target) {
            target.classList.add('active');
        }
        localStorage.setItem('employee_reports_active_tab', tabId);
    }

    function handleHashChange() {
        let hash = window.location.hash.substring(1);
        if (!hash) {
            hash = localStorage.getItem('employee_reports_active_tab') || 'ringkasan';
        }
        // Pastikan div dengan ID tersebut ada
        if (document.getElementById(hash + '-content')) {
            switchTab(hash);
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Init Icons
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }

        handleHashChange();
        window.addEventListener('hashchange', handleHashChange);

        // Chart
        const ctx = document.getElementById('performanceChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($chartData['labels'] ?? []) !!},
                datasets: [{
                    label: 'Selesai',
                    data: {!! json_encode($chartData['counts'] ?? []) !!},
                    borderColor: '#f97316',
                    backgroundColor: 'rgba(249, 115, 22, 0.1)',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { color: 'rgba(255,255,255,0.05)' }, ticks: { color: 'rgba(255,255,255,0.4)' } },
                    x: { grid: { display: false }, ticks: { color: 'rgba(255,255,255,0.4)' } }
                }
            }
        });
    });
</script>
@endsection
