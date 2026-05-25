@extends('layouts.premium-dashboard')

@section('page_title', 'Rekap Absensi Karyawan')

@section('nav_items')
    <li class="nav-item"><a href="{{ route('admin.dashboard') }}" class="nav-link {{ Route::is('admin.dashboard') ? 'active' : '' }}">Dashboard</a></li>
    <li class="nav-item"><a href="{{ route('admin.orders.index') }}" class="nav-link {{ Route::is('admin.orders.index') ? 'active' : '' }}">Kelola Pesanan</a></li>
    <li class="nav-item"><a href="{{ route('admin.services.index') }}" class="nav-link {{ Route::is('admin.services.index') ? 'active' : '' }}">Kelola Layanan</a></li>
    <li class="nav-item"><a href="{{ route('admin.finances.index') }}" class="nav-link {{ Route::is('admin.finances.index') ? 'active' : '' }}">Keuangan</a></li>
    <li class="nav-item"><a href="{{ route('admin.employees.index') }}" class="nav-link {{ Route::is('admin.employees.index') ? 'active' : '' }}">Manajemen Staff</a></li>
    <li class="nav-item"><a href="{{ route('admin.reports.index') }}" class="nav-link {{ Route::is('admin.reports.index') ? 'active' : '' }}">Laporan</a></li>
@endsection

@section('content')
<div class="header-actions" style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h2 style="font-size: 1.8rem; font-weight: 800; margin-bottom: 0.5rem;">Rekap Absensi</h2>
        <p style="opacity: 0.6;">Riwayat kehadiran seluruh staf berdasarkan jam kerja yang ditentukan.</p>
    </div>
    <a href="{{ route('admin.employees.index') }}" style="background: rgba(255,255,255,0.05); color: #fff; text-decoration: none; padding: 0.8rem 1.5rem; border-radius: 12px; font-weight: 700; border: 1px solid rgba(255,255,255,0.1); display: flex; align-items: center; gap: 0.5rem;">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
        Kembali ke Manajemen
    </a>
</div>

<!-- Filters -->
<div class="glass-card" style="margin-bottom: 2rem; padding: 1.5rem;">
    <form action="{{ route('admin.employees.attendance') }}" method="GET" style="display: flex; gap: 1.5rem; align-items: flex-end; flex-wrap: wrap;">
        <div style="flex: 1; min-width: 200px;">
            <label style="display: block; font-size: 0.8rem; opacity: 0.6; margin-bottom: 0.5rem;">Dari Tanggal</label>
            <input type="date" name="start_date" value="{{ $startDate }}" style="width: 100%; padding: 0.8rem; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; color: #fff; outline: none;">
        </div>
        <div style="flex: 1; min-width: 200px;">
            <label style="display: block; font-size: 0.8rem; opacity: 0.6; margin-bottom: 0.5rem;">Sampai Tanggal</label>
            <input type="date" name="end_date" value="{{ $endDate }}" style="width: 100%; padding: 0.8rem; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; color: #fff; outline: none;">
        </div>
        <button type="submit" style="background: var(--primary); color: #0f172a; border: none; padding: 0.8rem 2rem; border-radius: 12px; font-weight: 700; cursor: pointer;">Tampilkan</button>
    </form>
</div>

<div class="table-container">
    <table style="width: 100%; border-collapse: collapse; text-align: left; min-width: 800px;">
        <thead>
            <tr style="background: rgba(255,255,255,0.03); border-bottom: 1px solid rgba(255,255,255,0.05);">
                <th style="padding: 1.5rem; font-size: 0.85rem; text-transform: uppercase; opacity: 0.6;">Karyawan</th>
                <th style="padding: 1.5rem; font-size: 0.85rem; text-transform: uppercase; opacity: 0.6;">Tanggal</th>
                <th style="padding: 1.5rem; font-size: 0.85rem; text-transform: uppercase; opacity: 0.6;">Jam Masuk</th>
                <th style="padding: 1.5rem; font-size: 0.85rem; text-transform: uppercase; opacity: 0.6;">Jam Pulang</th>
                <th style="padding: 1.5rem; font-size: 0.85rem; text-transform: uppercase; opacity: 0.6;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($attendances as $att)
            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02); transition: 0.3s;" onmouseover="this.style.background='rgba(255,255,255,0.01)'" onmouseout="this.style.background='transparent'">
                <td style="padding: 1.5rem;">
                    <div style="font-weight: 700;">{{ $att->user->name }}</div>
                    <div style="font-size: 0.75rem; opacity: 0.5;">Target: {{ \Carbon\Carbon::parse($att->user->work_start)->format('H:i') }}</div>
                </td>
                <td style="padding: 1.5rem; opacity: 0.8;">{{ \Carbon\Carbon::parse($att->date)->format('d F Y') }}</td>
                <td style="padding: 1.5rem;">
                    <span style="font-weight: 600;">{{ $att->clock_in ? \Carbon\Carbon::parse($att->clock_in)->format('H:i:s') : '--:--' }}</span>
                </td>
                <td style="padding: 1.5rem;">
                    <span style="font-weight: 600;">{{ $att->clock_out ? \Carbon\Carbon::parse($att->clock_out)->format('H:i:s') : '--:--' }}</span>
                </td>
                <td style="padding: 1.5rem;">
                    @php
                        $isLate = false;
                        if($att->clock_in) {
                            $in = \Carbon\Carbon::parse($att->clock_in)->format('H:i');
                            $target = \Carbon\Carbon::parse($att->user->work_start)->format('H:i');
                            $isLate = $in > $target;
                        }
                    @endphp
                    @if($isLate)
                        <span style="background: rgba(244, 63, 94, 0.1); color: #f43f5e; padding: 0.4rem 0.8rem; border-radius: 8px; font-size: 0.75rem; font-weight: 700;">TERLAMBAT</span>
                    @else
                        <span style="background: rgba(16, 185, 129, 0.1); color: #10b981; padding: 0.4rem 0.8rem; border-radius: 8px; font-size: 0.75rem; font-weight: 700;">TEPAT WAKTU</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="padding: 4rem; text-align: center; opacity: 0.3;">Belum ada data absensi untuk periode ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
