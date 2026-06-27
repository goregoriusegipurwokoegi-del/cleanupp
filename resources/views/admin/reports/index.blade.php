@extends('layouts.premium-dashboard')

@section('page_title', 'Laporan & Analisis')

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
    @media print {
        .no-print { display: none !important; }
        .sidebar { display: none !important; }
        .main-content { margin-left: 0 !important; width: 100% !important; padding: 0 !important; background: #fff !important; color: #000 !important; }
        .glass-card { background: #fff !important; border: 1px solid #ddd !important; box-shadow: none !important; color: #000 !important; }
        body { background: #fff !important; }
    }
    .filter-card {
        background: var(--surface-variant);
        border: 1px solid var(--border-color);
        border-radius: 20px;
        padding: 1.2rem;
        margin-bottom: 1.5rem;
    }
    .tab-bar {
        display: flex;
        gap: 0.5rem;
        margin-bottom: 1.5rem;
        background: var(--surface-variant);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        padding: 0.4rem;
        overflow-x: auto;
    }
    .tab-btn {
        flex: 1;
        text-align: center;
        padding: 0.7rem 1rem;
        border-radius: 12px;
        font-weight: 700;
        font-size: 0.85rem;
        cursor: pointer;
        text-decoration: none;
        color: var(--text-secondary);
        transition: all 0.3s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.4rem;
        white-space: nowrap;
    }
    .tab-btn.active {
        background: var(--primary);
        color: #fff;
    }
    .tab-btn:not(.active):hover {
        background: var(--primary-glow);
        color: var(--primary);
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .tab-content { animation: fadeIn 0.4s ease; }
    .form-input {
        width: 100%;
        background: var(--surface);
        border: 1px solid var(--border-color);
        padding: 0.8rem;
        border-radius: 12px;
        color: var(--text);
        outline: none;
    }
    .form-input:focus { border-color: var(--primary); }
</style>



{{-- Tab Bar --}}
<div class="tab-bar no-print">
    <a href="{{ route('admin.reports.index', ['tab' => 'ringkasan']) }}" class="tab-btn {{ $tab == 'ringkasan' ? 'active' : '' }}">Ringkasan</a>
    <a href="{{ route('admin.reports.index', ['tab' => 'pesanan']) }}" class="tab-btn {{ $tab == 'pesanan' ? 'active' : '' }}">Pesanan</a>
    <a href="{{ route('admin.reports.index', ['tab' => 'pendapatan']) }}" class="tab-btn {{ $tab == 'pendapatan' ? 'active' : '' }}">Pendapatan</a>
    <a href="{{ route('admin.reports.index', ['tab' => 'laba-rugi']) }}" class="tab-btn {{ $tab == 'laba-rugi' ? 'active' : '' }}">Laba Rugi</a>
    <a href="{{ route('admin.reports.index', ['tab' => 'terlaris']) }}" class="tab-btn {{ $tab == 'terlaris' ? 'active' : '' }}">Layanan Terlaris</a>
    <a href="{{ route('admin.reports.index', ['tab' => 'pinjaman']) }}" class="tab-btn {{ $tab == 'pinjaman' ? 'active' : '' }}">Pinjaman / Kasbon</a>
    <a href="{{ route('admin.reports.index', ['tab' => 'export']) }}" class="tab-btn {{ $tab == 'export' ? 'active' : '' }}">Export Data</a>
</div>

{{-- Date Filter (Only show for tabs that need it) --}}
@if($tab != 'export')
<div class="filter-card no-print">
    <form action="{{ route('admin.reports.index') }}" method="GET" style="display: flex; gap: 1rem; align-items: flex-end; flex-wrap: wrap;">
        <input type="hidden" name="tab" value="{{ $tab }}">
        <div style="flex: 1; min-width: 150px;">
            <label style="display: block; font-size: 0.75rem; font-weight: 700; margin-bottom: 0.5rem; opacity: 0.6; text-transform: uppercase; letter-spacing: 1px;">Dari Tanggal</label>
            <input type="date" name="start_date" value="{{ $startDate }}" class="form-input">
        </div>
        <div style="flex: 1; min-width: 150px;">
            <label style="display: block; font-size: 0.75rem; font-weight: 700; margin-bottom: 0.5rem; opacity: 0.6; text-transform: uppercase; letter-spacing: 1px;">Sampai Tanggal</label>
            <input type="date" name="end_date" value="{{ $endDate }}" class="form-input">
        </div>
        
        @if($tab == 'pesanan')
        <div style="flex: 1; min-width: 150px;">
            <label style="display: block; font-size: 0.75rem; font-weight: 700; margin-bottom: 0.5rem; opacity: 0.6; text-transform: uppercase; letter-spacing: 1px;">Kategori</label>
            <select name="category" class="form-input">
                <option value="" style="color: #000;">Semua Kategori</option>
                <option value="cleaning" {{ request('category') == 'cleaning' ? 'selected' : '' }} style="color: #000;">Cleaning</option>
                <option value="repair" {{ request('category') == 'repair' ? 'selected' : '' }} style="color: #000;">Repair</option>
            </select>
        </div>
        @endif

        <div>
            <button type="submit" style="background: var(--primary); color: #fff; border: none; padding: 0.85rem 1.5rem; border-radius: 12px; font-weight: 800; cursor: pointer; transition: 0.3s;">Filter Laporan</button>
        </div>
    </form>
</div>
@endif

{{-- ================= TAB: RINGKASAN ================= --}}
@if($tab == 'ringkasan')
<div class="tab-content">
    <div class="grid-3" style="margin-bottom: 2rem;">
        <div class="glass-card" style="border-top: 4px solid var(--primary);">
            <p style="opacity: 0.6; font-size: 0.8rem; margin-bottom: 0.5rem; text-transform: uppercase;">Total Pesanan Valid</p>
            <h3 style="font-size: 2rem; font-weight: 800;">{{ $totalOrders }} <span style="font-size: 1rem; font-weight: 500; opacity: 0.6;">Pesanan</span></h3>
        </div>
        <div class="glass-card" style="border-top: 4px solid var(--success);">
            <p style="opacity: 0.6; font-size: 0.8rem; margin-bottom: 0.5rem; text-transform: uppercase;">Total Pendapatan Pesanan</p>
            <h3 style="font-size: 2rem; font-weight: 800; color: var(--success);">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h3>
        </div>
        <div class="glass-card" style="border-top: 4px solid #3b82f6;">
            <p style="opacity: 0.6; font-size: 0.8rem; margin-bottom: 0.5rem; text-transform: uppercase;">Layanan Tersedia</p>
            <h3 style="font-size: 2rem; font-weight: 800;">{{ $activeServices }} <span style="font-size: 1rem; font-weight: 500; opacity: 0.6;">Layanan</span></h3>
        </div>
    </div>

    <div class="glass-card">
        <h4 style="font-size: 1.2rem; font-weight: 800; margin-bottom: 1.5rem;">Tren Pendapatan (7 Hari Terakhir)</h4>
        <canvas id="revenueChart" height="100"></canvas>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('revenueChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($chartData['labels']) !!},
                datasets: [{
                    label: 'Pendapatan Pesanan (Rp)',
                    data: {!! json_encode($chartData['revenue']) !!},
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true
                }]
            },
            options: { responsive: true, scales: { y: { beginAtZero: true, grid: { color: 'rgba(255,255,255,0.1)' }, ticks: { color: '#aaa' } }, x: { grid: { display: false }, ticks: { color: '#aaa' } } }, plugins: { legend: { labels: { color: '#fff' } } } }
        });
    });
</script>
@endif

{{-- ================= TAB: PESANAN ================= --}}
@if($tab == 'pesanan')
<div class="tab-content">
    <div class="table-container">
        <table style="width: 100%; border-collapse: collapse; text-align: left; min-width: 800px;">
            <thead>
                <tr style="background: rgba(255,255,255,0.03); border-bottom: 1px solid rgba(255,255,255,0.05);">
                    <th style="padding: 1.5rem; font-size: 0.85rem; text-transform: uppercase; opacity: 0.6;">Tanggal</th>
                    <th style="padding: 1.5rem; font-size: 0.85rem; text-transform: uppercase; opacity: 0.6;">ID Pesanan</th>
                    <th style="padding: 1.5rem; font-size: 0.85rem; text-transform: uppercase; opacity: 0.6;">Pelanggan</th>
                    <th style="padding: 1.5rem; font-size: 0.85rem; text-transform: uppercase; opacity: 0.6;">Layanan</th>
                    <th style="padding: 1.5rem; font-size: 0.85rem; text-transform: uppercase; opacity: 0.6;">Status</th>
                    <th style="padding: 1.5rem; font-size: 0.85rem; text-transform: uppercase; opacity: 0.6; text-align: right;">Total Harga</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $order)
                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);">
                    <td style="padding: 1.5rem;">{{ $order->created_at->format('d/m/Y') }}</td>
                    <td style="padding: 1.5rem; font-weight: 700; color: var(--primary);">#{{ $order->queue_number }}</td>
                    <td style="padding: 1.5rem;">{{ $order->user->name }}</td>
                    <td style="padding: 1.5rem;">{{ $order->service->name }}</td>
                    <td style="padding: 1.5rem;">
                        <span style="background: rgba(255,255,255,0.1); padding: 0.4rem 0.8rem; border-radius: 8px; font-size: 0.75rem; font-weight: 700;">
                            {{ strtoupper($order->status) }}
                        </span>
                    </td>
                    <td style="padding: 1.5rem; text-align: right; font-weight: 700;">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @if($orders->isEmpty())
            <div style="padding: 4rem; text-align: center; opacity: 0.4;">Belum ada data pesanan.</div>
        @endif
    </div>
</div>
@endif

{{-- ================= TAB: PENDAPATAN ================= --}}
@if($tab == 'pendapatan')
<div class="tab-content">
    <div class="glass-card" style="margin-bottom: 2rem; border-left: 4px solid var(--success);">
        <p style="opacity: 0.6; font-size: 0.9rem; margin-bottom: 0.5rem;">Total Pendapatan Pesanan (Periode {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }})</p>
        <h3 style="font-size: 2.5rem; font-weight: 900; color: var(--success);">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h3>
    </div>

    <div class="table-container">
        <table style="width: 100%; border-collapse: collapse; text-align: left; min-width: 600px;">
            <thead>
                <tr style="background: rgba(255,255,255,0.03); border-bottom: 1px solid rgba(255,255,255,0.05);">
                    <th style="padding: 1.5rem; font-size: 0.85rem; text-transform: uppercase; opacity: 0.6;">Tanggal</th>
                    <th style="padding: 1.5rem; font-size: 0.85rem; text-transform: uppercase; opacity: 0.6;">No. Order</th>
                    <th style="padding: 1.5rem; font-size: 0.85rem; text-transform: uppercase; opacity: 0.6;">Sumber Layanan</th>
                    <th style="padding: 1.5rem; font-size: 0.85rem; text-transform: uppercase; opacity: 0.6; text-align: right;">Pendapatan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $order)
                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);">
                    <td style="padding: 1.5rem;">{{ $order->created_at->format('d/m/Y') }}</td>
                    <td style="padding: 1.5rem;">#{{ $order->order_number }}</td>
                    <td style="padding: 1.5rem;">{{ $order->service->name }}</td>
                    <td style="padding: 1.5rem; text-align: right; font-weight: 700; color: var(--success);">+ Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @if($orders->isEmpty())
            <div style="padding: 4rem; text-align: center; opacity: 0.4;">Belum ada pendapatan.</div>
        @endif
    </div>
</div>
@endif

{{-- ================= TAB: LABA RUGI ================= --}}
@if($tab == 'laba-rugi')
<div class="tab-content">
    <div style="text-align: center; margin-bottom: 2rem;">
        <h3 style="font-weight: 800; font-size: 1.5rem;">Laporan Laba Rugi</h3>
        <p style="opacity: 0.6;">Periode: {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</p>
    </div>

    <div class="grid-2" style="margin-bottom: 1.5rem;">
        <div class="glass-card" style="text-align: center;">
            <p style="opacity: 0.6; font-size: 0.9rem; margin-bottom: 0.5rem;">Total Pendapatan (Pesanan + Manual)</p>
            <h3 style="font-size: 2rem; font-weight: 800; color: var(--success);">Rp {{ number_format($totalIncome, 0, ',', '.') }}</h3>
        </div>
        <div class="glass-card" style="text-align: center;">
            <p style="opacity: 0.6; font-size: 0.9rem; margin-bottom: 0.5rem;">Total Biaya (Pengeluaran)</p>
            <h3 style="font-size: 2rem; font-weight: 800; color: #f43f5e;">Rp {{ number_format($totalExpense, 0, ',', '.') }}</h3>
        </div>
    </div>
    
    <div class="glass-card" style="text-align: center; background: rgba(255,255,255,0.05);">
        <p style="opacity: 0.6; font-size: 1rem; margin-bottom: 0.5rem;">Laba/Rugi Bersih</p>
        <h3 style="font-size: 3rem; font-weight: 900; color: {{ $netBalance >= 0 ? 'var(--primary)' : '#f43f5e' }};">
            Rp {{ number_format($netBalance, 0, ',', '.') }}
        </h3>
        @if($netBalance > 0)
            <p style="color: var(--success); font-weight: 700; margin-top: 10px;">▲ Profit / Untung</p>
        @elseif($netBalance < 0)
            <p style="color: #f43f5e; font-weight: 700; margin-top: 10px;">▼ Rugi</p>
        @else
            <p style="opacity: 0.6; font-weight: 700; margin-top: 10px;">Break Even (Impas)</p>
        @endif
    </div>
</div>
@endif

{{-- ================= TAB: TERLARIS ================= --}}
@if($tab == 'terlaris')
<div class="tab-content">
    <div class="table-container">
        <table style="width: 100%; border-collapse: collapse; text-align: left; min-width: 600px;">
            <thead>
                <tr style="background: rgba(255,255,255,0.03); border-bottom: 1px solid rgba(255,255,255,0.05);">
                    <th style="padding: 1.5rem; font-size: 0.85rem; text-transform: uppercase; opacity: 0.6;">Peringkat</th>
                    <th style="padding: 1.5rem; font-size: 0.85rem; text-transform: uppercase; opacity: 0.6;">Nama Layanan</th>
                    <th style="padding: 1.5rem; font-size: 0.85rem; text-transform: uppercase; opacity: 0.6;">Kategori</th>
                    <th style="padding: 1.5rem; font-size: 0.85rem; text-transform: uppercase; opacity: 0.6; text-align: center;">Jumlah Dipesan</th>
                    <th style="padding: 1.5rem; font-size: 0.85rem; text-transform: uppercase; opacity: 0.6; text-align: right;">Total Nilai</th>
                </tr>
            </thead>
            <tbody>
                @foreach($popularServices as $index => $item)
                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);">
                    <td style="padding: 1.5rem; font-weight: 800; font-size: 1.2rem; color: {{ $index == 0 ? 'var(--primary)' : '#fff' }};">#{{ $index + 1 }}</td>
                    <td style="padding: 1.5rem; font-weight: 700;">{{ $item['service'] }}</td>
                    <td style="padding: 1.5rem; opacity: 0.8;">{{ $item['category'] }}</td>
                    <td style="padding: 1.5rem; text-align: center; font-weight: 800;">{{ $item['count'] }}</td>
                    <td style="padding: 1.5rem; text-align: right; color: var(--success); font-weight: 700;">Rp {{ number_format($item['revenue'], 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @if($popularServices->isEmpty())
            <div style="padding: 4rem; text-align: center; opacity: 0.4;">Belum ada pesanan selesai.</div>
        @endif
    </div>
</div>
@endif

{{-- ================= TAB: PINJAMAN ================= --}}
@if($tab == 'pinjaman')
<div class="tab-content">
    <div class="grid-3" style="margin-bottom: 2rem;">
        <div class="glass-card" style="border-left: 4px solid #f59e0b;">
            <p style="opacity: 0.6; font-size: 0.8rem; margin-bottom: 0.5rem; text-transform: uppercase;">Menunggu Persetujuan</p>
            <h3 style="font-size: 1.5rem; font-weight: 800; color: #f59e0b;">Rp {{ number_format($totalLoansPending, 0, ',', '.') }}</h3>
        </div>
        <div class="glass-card" style="border-left: 4px solid var(--success);">
            <p style="opacity: 0.6; font-size: 0.8rem; margin-bottom: 0.5rem; text-transform: uppercase;">Disetujui (Periode Ini)</p>
            <h3 style="font-size: 1.5rem; font-weight: 800; color: var(--success);">Rp {{ number_format($totalLoansApproved, 0, ',', '.') }}</h3>
        </div>
        <div class="glass-card" style="border-left: 4px solid #f43f5e;">
            <p style="opacity: 0.6; font-size: 0.8rem; margin-bottom: 0.5rem; text-transform: uppercase;">Ditolak</p>
            <h3 style="font-size: 1.5rem; font-weight: 800; color: #f43f5e;">{{ $totalLoansRejected }} <span style="font-size: 1rem; font-weight: 500; opacity: 0.6;">Pengajuan</span></h3>
        </div>
    </div>
    
    <div class="table-container">
        <table style="width: 100%; border-collapse: collapse; text-align: left; min-width: 600px;">
            <thead>
                <tr style="background: rgba(255,255,255,0.03); border-bottom: 1px solid rgba(255,255,255,0.05);">
                    <th style="padding: 1.5rem; font-size: 0.85rem; text-transform: uppercase; opacity: 0.6;">Tanggal</th>
                    <th style="padding: 1.5rem; font-size: 0.85rem; text-transform: uppercase; opacity: 0.6;">Karyawan</th>
                    <th style="padding: 1.5rem; font-size: 0.85rem; text-transform: uppercase; opacity: 0.6;">Keterangan</th>
                    <th style="padding: 1.5rem; font-size: 0.85rem; text-transform: uppercase; opacity: 0.6;">Status</th>
                    <th style="padding: 1.5rem; font-size: 0.85rem; text-transform: uppercase; opacity: 0.6; text-align: right;">Nominal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($loans as $loan)
                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02); transition: 0.3s;" onmouseover="this.style.background='rgba(255,255,255,0.01)'" onmouseout="this.style.background='transparent'">
                    <td style="padding: 1.5rem;">{{ $loan->created_at->format('d/m/Y') }}</td>
                    <td style="padding: 1.5rem; font-weight: 700;">{{ $loan->user->name }}</td>
                    <td style="padding: 1.5rem; opacity: 0.9;">{{ $loan->reason }}</td>
                    <td style="padding: 1.5rem;">
                        @if($loan->status == 'approved')
                            <span style="background: rgba(16, 185, 129, 0.1); color: #10b981; padding: 0.4rem 0.8rem; border-radius: 8px; font-size: 0.75rem; font-weight: 700;">DISETUJUI</span>
                        @elseif($loan->status == 'rejected')
                            <span style="background: rgba(244, 63, 94, 0.1); color: #f43f5e; padding: 0.4rem 0.8rem; border-radius: 8px; font-size: 0.75rem; font-weight: 700;">DITOLAK</span>
                        @else
                            <span style="background: rgba(245, 158, 11, 0.1); color: #f59e0b; padding: 0.4rem 0.8rem; border-radius: 8px; font-size: 0.75rem; font-weight: 700;">PENDING</span>
                        @endif
                    </td>
                    <td style="padding: 1.5rem; text-align: right; font-weight: 700; color: {{ $loan->status == 'approved' ? 'var(--success)' : '#fff' }};">
                        Rp {{ number_format($loan->amount, 0, ',', '.') }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @if($loans->isEmpty())
            <div style="padding: 4rem; text-align: center; opacity: 0.4;">Belum ada pengajuan pinjaman/kasbon.</div>
        @endif
    </div>
</div>
@endif

{{-- ================= TAB: EXPORT ================= --}}
@if($tab == 'export')
<div class="tab-content">
    <div class="glass-card" style="max-width: 700px; margin: 0 auto; text-align: center;">
        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2" style="margin-bottom: 1rem;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
        <h3 style="font-size: 1.5rem; font-weight: 800; margin-bottom: 0.5rem;">Cetak & Unduh Laporan</h3>
        <p style="opacity: 0.6; margin-bottom: 2rem;">Pilih rentang tanggal dan jenis laporan yang ingin Anda ekspor.</p>
        
        <div style="display: flex; gap: 1rem; margin-bottom: 2rem; text-align: left;">
            <div style="flex: 1;">
                <label style="display: block; font-size: 0.85rem; margin-bottom: 0.5rem; opacity: 0.7;">Dari Tanggal</label>
                <input type="date" id="exp_start" value="{{ $startDate }}" style="width: 100%; padding: 0.8rem; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; color: #fff; outline: none;">
            </div>
            <div style="flex: 1;">
                <label style="display: block; font-size: 0.85rem; margin-bottom: 0.5rem; opacity: 0.7;">Sampai Tanggal</label>
                <input type="date" id="exp_end" value="{{ $endDate }}" style="width: 100%; padding: 0.8rem; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; color: #fff; outline: none;">
            </div>
        </div>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
            <button type="button" onclick="exportData('{{ route('admin.reports.export.excel') }}')" style="background: rgba(16,185,129,0.1); border: 1px solid rgba(16,185,129,0.3); color: #10b981; padding: 1rem; border-radius: 12px; font-weight: 800; cursor: pointer; transition: 0.3s;" onmouseover="this.style.background='rgba(16,185,129,0.2)'" onmouseout="this.style.background='rgba(16,185,129,0.1)'">
                Unduh Data Pesanan (Excel)
            </button>
            <button type="button" onclick="exportData('{{ route('admin.reports.export.revenue.excel') }}')" style="background: rgba(59,130,246,0.1); border: 1px solid rgba(59,130,246,0.3); color: #3b82f6; padding: 1rem; border-radius: 12px; font-weight: 800; cursor: pointer; transition: 0.3s;" onmouseover="this.style.background='rgba(59,130,246,0.2)'" onmouseout="this.style.background='rgba(59,130,246,0.1)'">
                Unduh Rekap Pendapatan (Excel)
            </button>
            <button type="button" onclick="exportData('{{ route('admin.reports.export.revenue.pdf') }}')" style="grid-column: span 2; background: rgba(244,63,94,0.1); border: 1px solid rgba(244,63,94,0.3); color: #f43f5e; padding: 1rem; border-radius: 12px; font-weight: 800; cursor: pointer; transition: 0.3s;" onmouseover="this.style.background='rgba(244,63,94,0.2)'" onmouseout="this.style.background='rgba(244,63,94,0.1)'">
                Cetak Laporan Pendapatan (PDF)
            </button>
        </div>
    </div>
</div>
<script>
    function exportData(baseUrl) {
        const start = document.getElementById('exp_start').value;
        const end = document.getElementById('exp_end').value;
        window.location.href = baseUrl + '?start_date=' + start + '&end_date=' + end;
    }
</script>
@endif

@endsection
