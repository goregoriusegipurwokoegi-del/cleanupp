@extends('layouts.premium-dashboard')

@section('page_title', 'Log Absensi Karyawan')

@section('nav_items')
    <li class="nav-item"><a href="{{ route('admin.dashboard') }}" class="nav-link {{ Route::is('admin.dashboard') ? 'active' : '' }}">Dashboard</a></li>
    <li class="nav-item"><a href="{{ route('admin.orders.index') }}" class="nav-link {{ Route::is('admin.orders.index') ? 'active' : '' }}">Kelola Pesanan</a></li>
    <li class="nav-item"><a href="{{ route('admin.services.index') }}" class="nav-link {{ Route::is('admin.services.index') ? 'active' : '' }}">Kelola Layanan</a></li>
    <li class="nav-item"><a href="{{ route('admin.finances.index') }}" class="nav-link {{ Route::is('admin.finances.index') ? 'active' : '' }}">Keuangan</a></li>
    <li class="nav-item"><a href="{{ route('admin.employees.index') }}" class="nav-link {{ Route::is('admin.employees.index') ? 'active' : '' }}">Manajemen Staff</a></li>
    <li class="nav-item"><a href="{{ route('admin.reports.index') }}" class="nav-link {{ Route::is('admin.reports.index') ? 'active' : '' }}">Laporan</a></li>
@endsection

@section('content')


<div class="glass-card" style="border-radius: 24px; overflow: hidden;">
    <table style="width: 100%; border-collapse: collapse; text-align: left;">
        <thead>
            <tr style="background: rgba(255,255,255,0.03); border-bottom: 1px solid rgba(255,255,255,0.05);">
                <th style="padding: 1.5rem; font-size: 0.85rem; text-transform: uppercase; opacity: 0.6;">Tanggal</th>
                <th style="padding: 1.5rem; font-size: 0.85rem; text-transform: uppercase; opacity: 0.6;">Nama Karyawan</th>
                <th style="padding: 1.5rem; font-size: 0.85rem; text-transform: uppercase; opacity: 0.6;">Jam Masuk</th>
                <th style="padding: 1.5rem; font-size: 0.85rem; text-transform: uppercase; opacity: 0.6;">Jam Keluar</th>
                <th style="padding: 1.5rem; font-size: 0.85rem; text-transform: uppercase; opacity: 0.6;">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($attendances as $attendance)
            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02); transition: 0.3s;" onmouseover="this.style.background='rgba(255,255,255,0.01)'" onmouseout="this.style.background='transparent'">
                <td style="padding: 1.5rem; font-weight: 600;">{{ \Carbon\Carbon::parse($attendance->date)->format('d M Y') }}</td>
                <td style="padding: 1.5rem; font-weight: 700;">{{ $attendance->user->name }}</td>
                <td style="padding: 1.5rem; color: var(--success); font-weight: 700;">{{ $attendance->clock_in ?? '--:--' }}</td>
                <td style="padding: 1.5rem; color: #f43f5e; font-weight: 700;">{{ $attendance->clock_out ?? '--:--' }}</td>
                <td style="padding: 1.5rem;">
                    @if($attendance->clock_in && $attendance->clock_out)
                        <span style="background: rgba(16, 185, 129, 0.1); color: #10b981; padding: 0.4rem 0.8rem; border-radius: 8px; font-size: 0.75rem; font-weight: 700;">HADIR</span>
                    @elseif($attendance->clock_in)
                        <span style="background: rgba(245, 158, 11, 0.1); color: #f59e0b; padding: 0.4rem 0.8rem; border-radius: 8px; font-size: 0.75rem; font-weight: 700;">AKTIF Bekerja</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @if($attendances->isEmpty())
        <div style="padding: 5rem; text-align: center; opacity: 0.3;">
            <p>Belum ada log absensi.</p>
        </div>
    @endif
</div>
@endsection
