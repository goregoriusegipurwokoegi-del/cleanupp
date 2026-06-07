@extends('layouts.premium-dashboard')

@section('page_title', 'Manajemen Kasbon Karyawan')

@section('nav_items')
    <li class="nav-item"><a href="{{ route('admin.dashboard') }}" class="nav-link {{ Route::is('admin.dashboard') ? 'active' : '' }}">Dashboard</a></li>
    <li class="nav-item"><a href="{{ route('admin.orders.index') }}" class="nav-link {{ Route::is('admin.orders.index') ? 'active' : '' }}">Kelola Pesanan</a></li>
    <li class="nav-item"><a href="{{ route('admin.services.index') }}" class="nav-link {{ Route::is('admin.services.index') ? 'active' : '' }}">Kelola Layanan</a></li>
    <li class="nav-item"><a href="{{ route('admin.finances.index') }}" class="nav-link {{ Route::is('admin.finances.index') ? 'active' : '' }}">Keuangan</a></li>
    <li class="nav-item"><a href="{{ route('admin.employees.index') }}" class="nav-link {{ Route::is('admin.employees.index') ? 'active' : '' }}">Manajemen Staff</a></li>
    <li class="nav-item"><a href="{{ route('admin.reports.index') }}" class="nav-link {{ Route::is('admin.reports.index') ? 'active' : '' }}">Laporan</a></li>
@endsection

@section('content')


@if(session('success'))
    <div style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.2); color: #10b981; padding: 1rem; border-radius: 12px; margin-bottom: 2rem;">
        {{ session('success') }}
    </div>
@endif

<div class="glass-card" style="border-radius: 24px; overflow: hidden;">
    <table style="width: 100%; border-collapse: collapse; text-align: left;">
        <thead>
            <tr style="background: rgba(255,255,255,0.03); border-bottom: 1px solid rgba(255,255,255,0.05);">
                <th style="padding: 1.5rem; font-size: 0.85rem; text-transform: uppercase; opacity: 0.6;">Karyawan</th>
                <th style="padding: 1.5rem; font-size: 0.85rem; text-transform: uppercase; opacity: 0.6;">Nominal</th>
                <th style="padding: 1.5rem; font-size: 0.85rem; text-transform: uppercase; opacity: 0.6;">Alasan</th>
                <th style="padding: 1.5rem; font-size: 0.85rem; text-transform: uppercase; opacity: 0.6;">Status</th>
                <th style="padding: 1.5rem; font-size: 0.85rem; text-transform: uppercase; opacity: 0.6; text-align: center;">Tindakan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($loans as $loan)
            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02); transition: 0.3s;" onmouseover="this.style.background='rgba(255,255,255,0.01)'" onmouseout="this.style.background='transparent'">
                <td style="padding: 1.5rem; font-weight: 700;">{{ $loan->user->name }}</td>
                <td style="padding: 1.5rem; font-weight: 700; color: var(--primary);">Rp {{ number_format($loan->amount, 0, ',', '.') }}</td>
                <td style="padding: 1.5rem; opacity: 0.8;">{{ $loan->reason }}</td>
                <td style="padding: 1.5rem;">
                    @if($loan->status == 'pending')
                        <span style="background: rgba(245, 158, 11, 0.1); color: #f59e0b; padding: 0.4rem 0.8rem; border-radius: 8px; font-size: 0.75rem; font-weight: 700;">MENUNGGU</span>
                    @elseif($loan->status == 'approved')
                        <span style="background: rgba(16, 185, 129, 0.1); color: #10b981; padding: 0.4rem 0.8rem; border-radius: 8px; font-size: 0.75rem; font-weight: 700;">DISETUJUI</span>
                    @else
                        <span style="background: rgba(244, 63, 94, 0.1); color: #f43f5e; padding: 0.4rem 0.8rem; border-radius: 8px; font-size: 0.75rem; font-weight: 700;">DITOLAK</span>
                    @endif
                </td>
                <td style="padding: 1.5rem; text-align: center;">
                    @if($loan->status == 'pending')
                    <div style="display: flex; justify-content: center; gap: 0.5rem;">
                        <form action="{{ route('admin.loans.update', $loan) }}" method="POST">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="approved">
                            <button type="submit" style="background: #10b981; color: #fff; border: none; padding: 0.5rem 0.8rem; border-radius: 8px; font-size: 0.75rem; font-weight: 700; cursor: pointer;">Setujui</button>
                        </form>
                        <form action="{{ route('admin.loans.update', $loan) }}" method="POST">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="rejected">
                            <button type="submit" style="background: #f43f5e; color: #fff; border: none; padding: 0.5rem 0.8rem; border-radius: 8px; font-size: 0.75rem; font-weight: 700; cursor: pointer;">Tolak</button>
                        </form>
                    </div>
                    @else
                    <span style="opacity: 0.4; font-size: 0.8rem;">Selesai</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @if($loans->isEmpty())
        <div style="padding: 5rem; text-align: center; opacity: 0.3;">
            <p>Tidak ada permintaan kasbon.</p>
        </div>
    @endif
</div>
@endsection
