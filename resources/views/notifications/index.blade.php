@extends('layouts.premium-dashboard')

@section('page_title', 'Notifikasi')

@section('content')
<div style="max-width: 600px; margin: 0 auto; padding: 10px;">
    <!-- Header Ringkas -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
        <div>
            <h2 style="color: #fff; margin: 0; font-size: 16px; font-weight: 800;">Pusat Pemberitahuan</h2>
            <p style="color: #64748b; font-size: 11px; margin: 2px 0 0 0;">Update aktivitas akun Anda</p>
        </div>
        <form action="{{ route('notifications.markAllAsRead') }}" method="POST">
            @csrf
            <button type="submit" style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: var(--primary); padding: 6px 12px; border-radius: 8px; font-size: 11px; font-weight: bold; cursor: pointer;">Tandai Semua Dibaca</button>
        </form>
    </div>

    <!-- Filter Tab -->
    <div style="display: flex; gap: 8px; margin-bottom: 15px;">
        <a href="{{ route('notifications.index', ['filter' => 'all']) }}" style="text-decoration: none; padding: 6px 15px; border-radius: 20px; font-size: 11px; font-weight: bold; {{ $filter == 'all' ? 'background: var(--primary); color: #000;' : 'background: #1e293b; color: #94a3b8;' }}">Semua</a>
        <a href="{{ route('notifications.index', ['filter' => 'unread']) }}" style="text-decoration: none; padding: 6px 15px; border-radius: 20px; font-size: 11px; font-weight: bold; {{ $filter == 'unread' ? 'background: var(--primary); color: #000;' : 'background: #1e293b; color: #94a3b8;' }}">Belum Dibaca</a>
    </div>

    <!-- Daftar Notifikasi -->
    <div style="display: flex; flex-direction: column; gap: 8px;">
        @forelse($notifications as $notification)
            <div style="background: rgba(255,255,255,0.03); border: 1px solid {{ !$notification->read_at ? 'rgba(0, 210, 255, 0.2)' : 'rgba(255,255,255,0.05)' }}; padding: 12px; border-radius: 15px; position: relative;">
                <div style="display: flex; gap: 12px; align-items: flex-start;">
                    <!-- Icon Bulat Kecil -->
                    <div style="width: 32px; height: 32px; border-radius: 10px; background: rgba(255,255,255,0.05); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        @php
                            $icon = $notification->data['icon'] ?? 'bell';
                            $color = $notification->data['color'] ?? 'blue';
                            $hexColor = ['blue' => '#3b82f6', 'green' => '#10b981', 'red' => '#ef4444', 'yellow' => '#f59e0b', 'indigo' => '#6366f1'][$color] ?? '#3b82f6';
                        @endphp
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="{{ $hexColor }}" stroke-width="2.5"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
                    </div>

                    <div style="flex-grow: 1;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2px;">
                            <h4 style="color: #fff; margin: 0; font-size: 13px; font-weight: 700;">{{ $notification->data['title'] ?? 'Notifikasi' }}</h4>
                            <span style="font-size: 10px; color: #475569;">{{ $notification->created_at->diffForHumans() }}</span>
                        </div>
                        <p style="color: #94a3b8; font-size: 12px; line-height: 1.4; margin: 0 0 8px 0;">{{ $notification->data['message'] }}</p>
                        
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <a href="{{ $notification->data['url'] ?? '#' }}" style="color: var(--primary); text-decoration: none; font-size: 11px; font-weight: bold;">Lihat Detail &rarr;</a>
                            
                            <div style="display: flex; gap: 5px;">
                                @if(!$notification->read_at)
                                    <form action="{{ route('notifications.markAsRead', $notification->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" style="background: none; border: none; color: #10b981; cursor: pointer; padding: 2px;"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg></button>
                                    </form>
                                @endif
                                <form action="{{ route('notifications.destroy', $notification->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="background: none; border: none; color: #ef4444; cursor: pointer; padding: 2px;"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M3 6h18m-2 0v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path></svg></button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @if(!$notification->read_at)
                    <div style="position: absolute; top: 12px; right: 12px; width: 6px; height: 6px; background: #3b82f6; border-radius: 50%; box-shadow: 0 0 10px #3b82f6;"></div>
                @endif
            </div>
        @empty
            <div style="text-align: center; padding: 60px 20px; color: #475569;">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" style="margin-bottom: 10px; opacity: 0.3;"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
                <p style="font-size: 13px;">Tidak ada notifikasi baru.</p>
                <a href="{{ route('dashboard') }}" style="color: var(--primary); text-decoration: none; font-size: 12px; font-weight: bold; display: block; margin-top: 10px;">Kembali ke Beranda</a>
            </div>
        @endforelse
    </div>

    <div style="margin-top: 20px;">
        {{ $notifications->links() }}
    </div>
</div>
@endsection
