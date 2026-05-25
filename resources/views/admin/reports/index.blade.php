@extends('layouts.premium-dashboard')

@section('page_title', 'Laporan Operasional')

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
        background: rgba(255,255,255,0.03);
        border: 1px solid rgba(255,255,255,0.05);
        border-radius: 20px;
        padding: 1.2rem;
        margin-bottom: 1rem;
    }
    .report-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    .tab-bar {
        display: flex;
        gap: 0.5rem;
        margin-bottom: 1.5rem;
        background: rgba(255,255,255,0.03);
        border: 1px solid rgba(255,255,255,0.06);
        border-radius: 16px;
        padding: 0.4rem;
    }
    .tab-btn {
        flex: 1;
        text-align: center;
        padding: 0.7rem 1.2rem;
        border-radius: 12px;
        font-weight: 700;
        font-size: 0.88rem;
        cursor: pointer;
        text-decoration: none;
        color: rgba(255,255,255,0.5);
        transition: all 0.3s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.4rem;
    }
    .tab-btn.active {
        background: var(--primary);
        color: #0f172a;
    }
    .tab-btn:not(.active):hover {
        background: rgba(255,255,255,0.05);
        color: #fff;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .tab-content { animation: fadeIn 0.4s ease; }
</style>

<div class="no-print" style="margin-bottom: 1.2rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h2 style="font-size: 1.5rem; font-weight: 800; margin-bottom: 0.2rem;">Laporan &amp; Analisis</h2>
        <p style="opacity: 0.6; font-size: 0.85rem;">Filter dan unduh data transaksi &amp; pinjaman CleanUP Shoes.</p>
    </div>
    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
        @if($tab == 'orders')
        <a href="{{ route('admin.reports.export.excel', request()->all()) }}" style="background: #10b981; color: #fff; text-decoration: none; padding: 0.8rem 1.5rem; border-radius: 12px; font-weight: 700; display: flex; align-items: center; gap: 0.5rem; transition: 0.3s; white-space: nowrap;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
            Export Excel
        </a>
        @endif
    </div>
</div>

{{-- Filter --}}
<div class="filter-card no-print">
    <form action="{{ route('admin.reports.index') }}" method="GET" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1.2rem; align-items: flex-end;">
        <input type="hidden" name="tab" value="{{ $tab }}">
        <div>
            <label style="display: block; font-size: 0.75rem; font-weight: 700; margin-bottom: 0.6rem; opacity: 0.6; text-transform: uppercase; letter-spacing: 1px;">Dari Tanggal</label>
            <input type="date" name="start_date" value="{{ $startDate }}" style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); padding: 0.8rem; border-radius: 12px; color: #fff; outline: none;">
        </div>
        <div>
            <label style="display: block; font-size: 0.75rem; font-weight: 700; margin-bottom: 0.6rem; opacity: 0.6; text-transform: uppercase; letter-spacing: 1px;">Sampai Tanggal</label>
            <input type="date" name="end_date" value="{{ $endDate }}" style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); padding: 0.8rem; border-radius: 12px; color: #fff; outline: none;">
        </div>
        @if($tab == 'orders')
        <div>
            <label style="display: block; font-size: 0.75rem; font-weight: 700; margin-bottom: 0.6rem; opacity: 0.6; text-transform: uppercase; letter-spacing: 1px;">Kategori Layanan</label>
            <select name="category" style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); padding: 0.8rem; border-radius: 12px; color: #fff; outline: none; cursor: pointer;">
                <option value="" style="color: #000;">Semua Layanan</option>
                <option value="cleaning" {{ request('category') == 'cleaning' ? 'selected' : '' }} style="color: #000;">Cuci Sepatu</option>
                <option value="repair" {{ request('category') == 'repair' ? 'selected' : '' }} style="color: #000;">Reparasi</option>
            </select>
        </div>
        @endif
        <div>
            <button type="submit" style="width: 100%; background: var(--primary); color: #0f172a; border: none; padding: 0.85rem; border-radius: 12px; font-weight: 800; cursor: pointer; transition: 0.3s; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                Cari Data
            </button>
        </div>
    </form>
</div>

{{-- Tab Bar --}}
<div class="tab-bar no-print">
    <a href="{{ route('admin.reports.index', array_merge(request()->all(), ['tab' => 'orders'])) }}" class="tab-btn {{ $tab == 'orders' ? 'active' : '' }}">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
        Transaksi Order
    </a>
    <a href="{{ route('admin.reports.index', array_merge(request()->all(), ['tab' => 'loans'])) }}" class="tab-btn {{ $tab == 'loans' ? 'active' : '' }}">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
        Pinjaman / Kasbon
        @if($loans->where('status','pending')->count() > 0)
            <span style="background: #f59e0b; color: #000; border-radius: 50%; width: 18px; height: 18px; font-size: 0.65rem; display: inline-flex; align-items: center; justify-content: center; font-weight: 900;">{{ $loans->where('status','pending')->count() }}</span>
        @endif
    </a>
</div>

{{-- ===================== TAB: ORDERS ===================== --}}
@if($tab == 'orders')
<div class="tab-content">
    {{-- Summary Cards --}}
    <div class="report-stats">
        <div class="glass-card" id="total-transactions-card"
            onclick="toggleDetails('transaction-details', 'total-transactions-card')"
            style="text-align: center; cursor: pointer; transition: 0.3s; border: 1px solid rgba(255,255,255,0.05);"
            onmouseover="this.style.transform='translateY(-5px)'; this.style.borderColor='var(--primary)'"
            onmouseout="this.style.transform='translateY(0)'; if(document.getElementById('transaction-details').style.display === 'none') this.style.borderColor='rgba(255,255,255,0.05)'">
            <p style="opacity: 0.6; font-size: 0.8rem; margin-bottom: 0.5rem;">Total Transaksi <br><small style="color:var(--primary)">(Klik Lihat Detail)</small></p>
            <h3 style="font-size: 1.8rem; font-weight: 800; color: var(--primary);">{{ $totalOrders }}</h3>
        </div>
        <div class="glass-card" style="text-align: center; border-bottom: 4px solid var(--success);">
            <p style="opacity: 0.6; font-size: 0.8rem; margin-bottom: 0.5rem;">Total Pendapatan</p>
            <h3 style="font-size: 1.8rem; font-weight: 800; color: var(--success);">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h3>
        </div>
        <div class="glass-card" style="text-align: center;">
            <p style="opacity: 0.6; font-size: 0.8rem; margin-bottom: 0.5rem;">Periode</p>
            <h3 style="font-size: 1rem; font-weight: 700;">{{ \Carbon\Carbon::parse($startDate)->format('d M') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</h3>
        </div>
    </div>

    {{-- Table --}}
    <div class="table-container" id="transaction-details" style="margin-bottom: 3rem; display: none; animation: fadeIn 0.5s ease-in-out;">
        <div style="padding: 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.05); background: rgba(255,255,255,0.02);">
            <h4 style="font-weight: 800; opacity: 0.8;">Data Detail Transaksi</h4>
        </div>
        <table style="width: 100%; border-collapse: collapse; text-align: left; min-width: 800px;">
            <thead>
                <tr style="background: rgba(255,255,255,0.03); border-bottom: 1px solid rgba(255,255,255,0.05);">
                    <th style="padding: 1.2rem; font-size: 0.75rem; text-transform: uppercase; opacity: 0.5;">Tanggal</th>
                    <th style="padding: 1.2rem; font-size: 0.75rem; text-transform: uppercase; opacity: 0.5;">No. Antrian</th>
                    <th style="padding: 1.2rem; font-size: 0.75rem; text-transform: uppercase; opacity: 0.5;">Pelanggan</th>
                    <th style="padding: 1.2rem; font-size: 0.75rem; text-transform: uppercase; opacity: 0.5;">Layanan</th>
                    <th style="padding: 1.2rem; font-size: 0.75rem; text-transform: uppercase; opacity: 0.5;">Status</th>
                    <th style="padding: 1.2rem; font-size: 0.75rem; text-transform: uppercase; opacity: 0.5; text-align: right;">Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);">
                    <td style="padding: 1.2rem; opacity: 0.8;">{{ $order->created_at->format('d/m/Y') }}</td>
                    <td style="padding: 1.2rem; font-weight: 800; color: var(--primary);">#{{ $order->queue_number }}</td>
                    <td style="padding: 1.2rem; font-weight: 700;">{{ $order->user->name }}</td>
                    <td style="padding: 1.2rem; opacity: 0.8;">{{ $order->service->name }}</td>
                    <td style="padding: 1.2rem;">
                        @php
                            $statusLabel = match($order->status) {
                                'pending' => ['text' => 'MENUNGGU', 'bg' => 'rgba(100, 116, 139, 0.1)', 'color' => '#64748b'],
                                'washing', 'drying', 'finishing' => ['text' => 'DIPROSES', 'bg' => 'rgba(245, 158, 11, 0.1)', 'color' => '#f59e0b'],
                                'ready' => ['text' => 'SELESAI', 'bg' => 'rgba(0, 210, 255, 0.1)', 'color' => '#00d2ff'],
                                'uncollected' => ['text' => 'BELUM DIAMBIL', 'bg' => 'rgba(168, 85, 247, 0.1)', 'color' => '#a855f7'],
                                'picked_up' => ['text' => 'DIAMBIL', 'bg' => 'rgba(16, 185, 129, 0.1)', 'color' => '#10b981'],
                                default => ['text' => strtoupper($order->status), 'bg' => 'rgba(255,255,255,0.05)', 'color' => '#fff'],
                            };
                        @endphp
                        <span style="font-size: 0.7rem; font-weight: 800; padding: 0.3rem 0.6rem; border-radius: 6px; background: {{ $statusLabel['bg'] }}; color: {{ $statusLabel['color'] }};">
                            {{ $statusLabel['text'] }}
                        </span>
                    </td>
                    <td style="padding: 1.2rem; text-align: right; font-weight: 800;">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="padding: 5rem; text-align: center; opacity: 0.3;">Tidak ada data ditemukan untuk periode ini.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endif

{{-- ===================== TAB: LOANS ===================== --}}
@if($tab == 'loans')
<div class="tab-content">
    {{-- Loan Summary Cards --}}
    <div class="report-stats">
        <div class="glass-card" style="text-align: center; border-bottom: 4px solid #10b981;">
            <p style="opacity: 0.6; font-size: 0.8rem; margin-bottom: 0.5rem;">Total Disetujui</p>
            <h3 style="font-size: 1.6rem; font-weight: 800; color: #10b981;">Rp {{ number_format($totalLoansApproved, 0, ',', '.') }}</h3>
        </div>
        <div class="glass-card" style="text-align: center; border-bottom: 4px solid #f59e0b;">
            <p style="opacity: 0.6; font-size: 0.8rem; margin-bottom: 0.5rem;">Menunggu Konfirmasi</p>
            <h3 style="font-size: 1.6rem; font-weight: 800; color: #f59e0b;">Rp {{ number_format($totalLoansPending, 0, ',', '.') }}</h3>
        </div>
        <div class="glass-card" style="text-align: center; border-bottom: 4px solid #f43f5e;">
            <p style="opacity: 0.6; font-size: 0.8rem; margin-bottom: 0.5rem;">Ditolak</p>
            <h3 style="font-size: 1.6rem; font-weight: 800; color: #f43f5e;">{{ $totalLoansRejected }} Pengajuan</h3>
        </div>
        <div class="glass-card" style="text-align: center;">
            <p style="opacity: 0.6; font-size: 0.8rem; margin-bottom: 0.5rem;">Total Pengajuan</p>
            <h3 style="font-size: 1.6rem; font-weight: 800; color: var(--primary);">{{ $loans->count() }}</h3>
        </div>
    </div>

    {{-- Loan Table --}}
    <div class="table-container" style="margin-bottom: 3rem;">
        <div style="padding: 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.05); background: rgba(255,255,255,0.02); display: flex; justify-content: space-between; align-items: center;">
            <h4 style="font-weight: 800; opacity: 0.8;">Riwayat Pinjaman / Kasbon</h4>
            <span style="font-size: 0.78rem; opacity: 0.5;">{{ \Carbon\Carbon::parse($startDate)->format('d M') }} – {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</span>
        </div>
        <table style="width: 100%; border-collapse: collapse; text-align: left; min-width: 700px;">
            <thead>
                <tr style="background: rgba(255,255,255,0.03); border-bottom: 1px solid rgba(255,255,255,0.05);">
                    <th style="padding: 1.2rem; font-size: 0.75rem; text-transform: uppercase; opacity: 0.5;">Tanggal</th>
                    <th style="padding: 1.2rem; font-size: 0.75rem; text-transform: uppercase; opacity: 0.5;">Karyawan</th>
                    <th style="padding: 1.2rem; font-size: 0.75rem; text-transform: uppercase; opacity: 0.5;">Alasan</th>
                    <th style="padding: 1.2rem; font-size: 0.75rem; text-transform: uppercase; opacity: 0.5;">Status</th>
                    <th style="padding: 1.2rem; font-size: 0.75rem; text-transform: uppercase; opacity: 0.5;">Catatan Admin</th>
                    <th style="padding: 1.2rem; font-size: 0.75rem; text-transform: uppercase; opacity: 0.5; text-align: right;">Nominal</th>
                </tr>
            </thead>
            <tbody>
                @forelse($loans as $loan)
                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02); transition: 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.01)'" onmouseout="this.style.background='transparent'">
                    <td style="padding: 1.2rem; opacity: 0.8; white-space: nowrap;">{{ $loan->created_at->format('d/m/Y') }}</td>
                    <td style="padding: 1.2rem; font-weight: 700;">{{ $loan->user->name }}</td>
                    <td style="padding: 1.2rem; opacity: 0.8; max-width: 200px;">{{ $loan->reason }}</td>
                    <td style="padding: 1.2rem;">
                        @if($loan->status == 'pending')
                            <span style="background: rgba(245,158,11,0.1); color: #f59e0b; padding: 0.3rem 0.7rem; border-radius: 8px; font-size: 0.72rem; font-weight: 800;">MENUNGGU</span>
                        @elseif($loan->status == 'approved')
                            <span style="background: rgba(16,185,129,0.1); color: #10b981; padding: 0.3rem 0.7rem; border-radius: 8px; font-size: 0.72rem; font-weight: 800;">DISETUJUI</span>
                        @else
                            <span style="background: rgba(244,63,94,0.1); color: #f43f5e; padding: 0.3rem 0.7rem; border-radius: 8px; font-size: 0.72rem; font-weight: 800;">DITOLAK</span>
                        @endif
                    </td>
                    <td style="padding: 1.2rem; opacity: 0.6; font-size: 0.85rem;">{{ $loan->admin_note ?? '-' }}</td>
                    <td style="padding: 1.2rem; text-align: right; font-weight: 800; color: {{ $loan->status == 'approved' ? '#10b981' : ($loan->status == 'rejected' ? '#f43f5e' : '#f59e0b') }};">
                        Rp {{ number_format($loan->amount, 0, ',', '.') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="padding: 5rem; text-align: center; opacity: 0.3;">Tidak ada data pinjaman untuk periode ini.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endif

<script>
    function toggleDetails(detailId, cardId) {
        const details = document.getElementById(detailId);
        const card = document.getElementById(cardId);
        if (!details) return;
        if (details.style.display === 'none' || details.style.display === '') {
            details.style.display = 'block';
            if (card) card.style.borderColor = 'var(--primary)';
            details.scrollIntoView({ behavior: 'smooth' });
        } else {
            details.style.display = 'none';
            if (card) card.style.borderColor = 'rgba(255,255,255,0.05)';
        }
    }
</script>
@endsection
