@extends('layouts.premium-dashboard')

@section('page_title', 'Scan / Cari Pesanan')

@section('content')
<div class="glass-card" style="max-width: 600px; margin: 0 auto; padding: 2rem;">
    <h2 style="font-weight: 800; margin-bottom: 1rem; color: var(--primary);">Pengambilan Barang</h2>
    <p style="opacity: 0.7; margin-bottom: 2rem; font-size: 0.9rem;">Masukkan nomor resi (Order Number) atau nomor antrean (Queue Number) pelanggan untuk mencari pesanan yang akan diambil.</p>

    @if(session('success'))
        <div style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.2); color: #10b981; padding: 1rem; border-radius: 12px; margin-bottom: 2rem;">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('employee.orders.scan') }}" method="GET" style="display: flex; gap: 1rem; margin-bottom: 2rem; flex-wrap: wrap;">
        <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Contoh: ORD-12345 atau Q001" autofocus required style="flex-grow: 1; padding: 1rem; border-radius: 12px; background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); color: #fff; outline: none; font-size: 1rem; min-width: 250px;">
        <button type="submit" style="background: var(--primary); color: #000; font-weight: 800; padding: 1rem 2rem; border-radius: 12px; border: none; cursor: pointer; transition: 0.3s; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
            Cari
        </button>
    </form>

    @if(isset($search) && !$order)
        <div style="text-align: center; padding: 2rem; background: rgba(244, 63, 94, 0.05); border-radius: 12px; border: 1px dashed rgba(244, 63, 94, 0.2);">
            <p style="color: #f43f5e; font-weight: 600;">Pesanan tidak ditemukan.</p>
            <p style="font-size: 0.8rem; opacity: 0.6; margin-top: 0.5rem;">Pastikan nomor resi atau antrean benar.</p>
        </div>
    @endif

    @if(isset($order))
        <div style="background: rgba(255, 255, 255, 0.02); border-radius: 16px; padding: 1.5rem; border: 1px solid rgba(255, 255, 255, 0.05);">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.5rem;">
                <div>
                    <span style="background: var(--primary); color: #000; font-size: 0.75rem; font-weight: 800; padding: 0.2rem 0.6rem; border-radius: 6px; text-transform: uppercase;">#{{ $order->queue_number }}</span>
                    <h3 style="font-size: 1.2rem; font-weight: 700; margin-top: 0.5rem;">{{ $order->shoe_name }}</h3>
                    <p style="opacity: 0.6; font-size: 0.85rem; margin-top: 0.2rem;">{{ $order->user->name }} • {{ $order->user->phone ?? '-' }}</p>
                </div>
                <div style="text-align: right;">
                    <p style="font-size: 0.8rem; opacity: 0.6;">Status Saat Ini</p>
                    <p style="font-weight: 800; color: {{ $order->status == 'completed' ? '#10b981' : ($order->status == 'ready' ? '#3b82f6' : '#f59e0b') }}; text-transform: uppercase;">
                        @if($order->status == 'completed') Sudah Diambil
                        @elseif($order->status == 'ready') Siap Diambil
                        @elseif($order->status == 'cancelled') Dibatalkan
                        @elseif($order->status == 'pending') Menunggu
                        @else Sedang Diproses @endif
                    </p>
                </div>
            </div>

            <div style="background: rgba(0,0,0,0.2); padding: 1rem; border-radius: 12px; margin-bottom: 1.5rem;">
                <p style="font-size: 0.85rem; opacity: 0.7; margin-bottom: 0.3rem;">Letak / Lokasi Barang (Rak):</p>
                @if($order->storage_location)
                    <p style="font-size: 1.2rem; font-weight: 800; color: #fff;">{{ $order->storage_location }}</p>
                @else
                    <p style="font-size: 1rem; font-weight: 600; color: #f59e0b; font-style: italic;">Lokasi belum diatur</p>
                @endif
            </div>

            @if($order->status != 'completed')
            <form action="{{ route('employee.orders.scan.process') }}" method="POST">
                @csrf
                <input type="hidden" name="order_id" value="{{ $order->id }}">
                
                @if(!$order->storage_location)
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; font-size: 0.85rem; margin-bottom: 0.5rem; opacity: 0.8;">Atur Lokasi (Opsional)</label>
                    <input type="text" name="storage_location" placeholder="Contoh: Rak A1" style="width: 100%; padding: 0.8rem; border-radius: 8px; background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); color: #fff; outline: none; font-size: 0.9rem;">
                </div>
                @endif

                <button type="submit" style="width: 100%; background: var(--success); color: #000; font-weight: 800; padding: 1rem; border-radius: 12px; border: none; cursor: pointer; transition: 0.3s; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                    Tandai Sudah Diambil
                </button>
            </form>
            @endif
        </div>
    @endif
</div>
@endsection
