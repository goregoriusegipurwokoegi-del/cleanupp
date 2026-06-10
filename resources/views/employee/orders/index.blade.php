@extends('layouts.premium-dashboard')

@section('page_title', 'Antrian Pesanan')

@section('nav_items')
    <li class="nav-item"><a href="{{ route('employee.dashboard') }}" class="nav-link">Dashboard</a></li>
    <li class="nav-item"><a href="{{ route('employee.orders.index') }}" class="nav-link active">Antrian Pesanan</a></li>
    <li class="nav-item"><a href="{{ route('employee.inventories.index') }}" class="nav-link">Stok Barang</a></li>
@endsection

@section('content')
<style>
    .header { margin-bottom: 0.5rem !important; }
</style>
<div style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
    <div>
        <h2 style="font-size: 1.8rem; font-weight: 900; margin-bottom: 5px;">
            @if(request('queue')) Monitor <span style="color: var(--primary);">Antrian</span> @elseif(request('delivery')) Antar <span style="color: var(--primary);">Jemput</span> @else Tugas <span style="color: var(--primary);">Saya</span> @endif
        </h2>
        <p style="opacity: 0.6;">
            @if(request('queue')) Pantau pesanan aktif. Pesanan selesai otomatis disembunyikan. @else Kelola dan perbarui status pengerjaan sepatu pelanggan. @endif
        </p>
    </div>
    @if(!request('queue'))
    <button onclick="openCreateModal()" style="background: var(--primary); color: #000; border: none; padding: 10px 20px; border-radius: 12px; font-weight: 800; cursor: pointer; display: flex; align-items: center; gap: 8px;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
        Tambah Pesanan
    </button>
    @endif
</div>

@if(session('success'))
    <div style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.2); color: #10b981; padding: 1rem; border-radius: 12px; margin-bottom: 2rem;">
        {{ session('success') }}
    </div>
@endif

<style>
    .order-card { display: none; }
    @media (max-width: 768px) {
        .table-desktop { display: none; }
        .order-card { 
            display: block; 
            background: rgba(255,255,255,0.03); 
            border: 1px solid rgba(255,255,255,0.05); 
            border-radius: 12px; 
            padding: 1rem; 
            margin-bottom: 0.8rem;
        }
        .order-card-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.8rem; }
        .order-card-body { margin-bottom: 0.8rem; }
        .order-card-footer { border-top: 1px solid rgba(255,255,255,0.05); pt: 0.8rem; padding-top: 0.8rem; }
        select { width: 100%; padding: 0.8rem !important; }
    }
    
    /* Modal Styles */
    .modal-backdrop {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.7);
        backdrop-filter: blur(8px);
        z-index: 1100;
        align-items: center;
        justify-content: center;
        padding: 1.5rem;
    }
    .modal-backdrop.active {
        display: flex;
    }
    .modal-box {
        background: #111114;
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 24px;
        width: 100%;
        max-width: 650px;
        max-height: 90vh;
        overflow-y: auto;
        padding: 2rem;
        position: relative;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5);
        color: #fff;
    }
    .modal-close {
        position: absolute;
        top: 1.5rem;
        right: 1.5rem;
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        color: #fff;
        font-size: 1.2rem;
        width: 35px;
        height: 35px;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: 0.3s;
    }
    .modal-close:hover {
        background: rgba(255, 255, 255, 0.1);
        color: var(--primary);
    }
    .modal-grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }
    @media (max-width: 600px) {
        .modal-grid-2 {
            grid-template-columns: 1fr;
            gap: 1rem;
        }
        .modal-box {
            padding: 1.2rem;
        }
    }
    .filter-input {
        background: rgba(255,255,255,0.03);
        border: 1px solid rgba(255,255,255,0.05);
        color: #fff;
        padding: 10px 15px;
        border-radius: 12px;
        font-size: 0.9rem;
        outline: none;
        transition: 0.3s;
    }
    .filter-input:focus {
        border-color: var(--primary);
    }
    .custom-scroll::-webkit-scrollbar {
        width: 4px;
    }
    .custom-scroll::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.2);
        border-radius: 4px;
    }
</style>

<div class="glass-card" style="border-radius: 24px; overflow: hidden; background: transparent; border: none; padding: 0;">
    <!-- Desktop Table -->
    <div class="table-desktop table-container">
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="background: rgba(255,255,255,0.03); border-bottom: 1px solid rgba(255,255,255,0.05);">
                    <th style="padding: 1.5rem; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px; opacity: 0.6;">Antrian</th>
                    <th style="padding: 1.5rem; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px; opacity: 0.6;">Pelanggan</th>
                    <th style="padding: 1.5rem; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px; opacity: 0.6;">Sepatu</th>
                    <th style="padding: 1.5rem; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px; opacity: 0.6;">Layanan</th>
                    <th style="padding: 1.5rem; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px; opacity: 0.6;">Status Pengerjaan</th>
                <th style="padding: 1.5rem; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px; opacity: 0.6;">Tanggal Masuk</th>
                    <th style="padding: 1.5rem; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px; opacity: 0.6;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $order)
                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02); transition: 0.3s;" onmouseover="this.style.background='rgba(255,255,255,0.01)'" onmouseout="this.style.background='transparent'">
                    <td style="padding: 1.5rem;">
                        <div style="background: var(--primary); color: #0f172a; padding: 0.3rem 0.8rem; border-radius: 8px; font-weight: 800; width: fit-content; font-size: 1rem;">{{ $order->queue_number ?? '-' }}</div>
                    </td>
                    <td style="padding: 1.5rem;">
                        <div style="font-size: 0.95rem; font-weight: 700; color: #fff; margin-bottom: 0.2rem; display: flex; align-items: center; gap: 6px; flex-wrap: wrap;">
                            {{ $order->user->name }}
                            @if($order->is_delivery)
                                <span style="background: rgba(249, 115, 22, 0.15); color: var(--primary); padding: 2px 6px; border-radius: 4px; font-size: 0.6rem; font-weight: 800; text-transform: uppercase; border: 1px solid rgba(249, 115, 22, 0.3);" title="Layanan Antar Jemput">🚚 Antar</span>
                            @endif
                        </div>
                        <a href="{{ route('orders.show', $order->id) }}" style="text-decoration: none; display: inline-flex; align-items: center; gap: 4px; font-size: 0.75rem; opacity: 0.6; color: #fff; transition: 0.2s;" onmouseover="this.style.color='var(--primary)'; this.style.opacity='1'" onmouseout="this.style.color='#fff'; this.style.opacity='0.6'">
                            Klik Detail Pesanan <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                        </a>
                    </td>
                    <td style="padding: 1.5rem;">
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <a href="{{ route('orders.show', $order->id) }}" style="display: block; position: relative;">
                                <div style="width: 50px; height: 50px; border-radius: 10px; overflow: hidden; border: 1px solid rgba(255,255,255,0.1); background: #0c0c0e;">
                                    <img src="{{ asset('storage/' . $order->photo_before) }}" style="width: 100%; height: 100%; object-fit: cover;">
                                </div>
                                @if($order->service->image)
                                    <div style="position: absolute; bottom: -4px; right: -4px; width: 20px; height: 20px; border-radius: 50%; border: 1.5px solid #0c0c0e; overflow: hidden; background: #1e293b; z-index: 2;">
                                        <img src="{{ asset('storage/' . $order->service->image) }}" style="width: 100%; height: 100%; object-fit: cover;">
                                    </div>
                                @endif
                            </a>
                            <div>
                                <div style="font-weight: 700; font-size: 0.9rem; text-transform: capitalize;">{{ $order->shoe_name }}</div>
                                <div style="font-size: 0.75rem; color: var(--primary); font-weight: 600;">Size: {{ $order->shoe_size }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="padding: 1.5rem;">
                        <div style="font-weight: 600;">{{ $order->service->name }}</div>
                        <span class="badge {{ $order->service->category == 'cleaning' ? 'badge-success' : 'badge-warning' }}" style="font-size: 0.7rem;">
                            {{ $order->service->category == 'cleaning' ? 'Cuci' : 'Reparasi' }}
                        </span>
                    </td>
                    <td style="padding: 1.5rem;">
                        @php
                            $statusLabel = 'Diterima';
                            if($order->status == 'processing') $statusLabel = ($order->service->category == 'cleaning' ? 'Sedang di cuci' : 'Sedang dikerjakan');
                            if($order->status == 'finishing') $statusLabel = ($order->service->category == 'cleaning' ? 'Di jemur' : 'Proses finishing');
                            if($order->status == 'ready') $statusLabel = 'Siap Diambil/Dikirim';
                            if($order->status == 'dikirim') $statusLabel = 'Sedang Dikirim';
                            if($order->status == 'uncollected') $statusLabel = 'Belum diambil';
                            if($order->status == 'completed') $statusLabel = 'Selesai';
                        @endphp
                        <span style="padding: 0.5rem 1rem; border-radius: 20px; background: rgba(0,210,255,0.1); color: var(--primary); font-size: 0.85rem; font-weight: 600;">
                            {{ $statusLabel }}
                        </span>
                    </td>
                    <td style="padding: 1.5rem; font-size: 0.9rem; opacity: 0.8;">
                        {{ $order->created_at->format('d M Y H:i') }}
                    </td>
                    <td style="padding: 1.5rem;">
                        @if($order->status == 'pending')
                            <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                                <div style="display: flex; gap: 0.5rem;">
                                    <form action="{{ route('orders.status.update', $order) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="processing">
                                        <button type="submit" style="background: #10b981; color: #fff; border: none; padding: 0.5rem 1rem; border-radius: 8px; font-size: 0.8rem; font-weight: 700; cursor: pointer; transition: 0.3s;" onmouseover="this.style.opacity='0.8'" onmouseout="this.style.opacity='1'">Terima</button>
                                    </form>
                                    <form action="{{ route('orders.status.update', $order) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="cancelled">
                                        <button type="submit" onclick="return confirm('Apakah Anda yakin ingin menolak pesanan ini?')" style="background: #f43f5e; color: #fff; border: none; padding: 0.5rem 1rem; border-radius: 8px; font-size: 0.8rem; font-weight: 700; cursor: pointer; transition: 0.3s;" onmouseover="this.style.opacity='0.8'" onmouseout="this.style.opacity='1'">Tolak</button>
                                    </form>
                                </div>
                                @if($order->payment_status == 'unpaid')
                                    <form action="{{ route('orders.payment.remind', $order) }}" method="POST">
                                        @csrf
                                        <button type="submit" title="Kirim Pengingat WA" style="width: 100%; background: rgba(16, 185, 129, 0.1); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.2); padding: 0.5rem; border-radius: 8px; font-size: 0.75rem; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 4px;">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 1 1-7.6-13.3 8.38 8.38 0 0 1 3.9.9L22 4l-1.5 6.5z"></path></svg>
                                            REKAYASA WA
                                        </button>
                                    </form>
                                @endif
                                <button onclick='openEditModal(@json($order))' style="width: 100%; background: #3b82f6; color: #fff; border: none; padding: 0.5rem; border-radius: 8px; font-size: 0.75rem; font-weight: 700; cursor: pointer; transition: 0.3s; margin-top: 0.5rem;" onmouseover="this.style.opacity='0.8'" onmouseout="this.style.opacity='1'">EDIT PESANAN</button>
                            </div>
                        @else
                            <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                                @php
                                    $nextStatus = null;
                                    $nextLabel = '';
                                    $btnColor = '#3b82f6';

                                    if ($order->service->category == 'cleaning') {
                                        if ($order->status == 'processing') { $nextStatus = 'finishing'; $nextLabel = 'Selesai Cuci → Jemur'; }
                                        elseif ($order->status == 'finishing') { $nextStatus = 'ready'; $nextLabel = 'Selesai Jemur → Siap'; }
                                        elseif ($order->status == 'ready') { 
                                            if ($order->is_delivery) { $nextStatus = 'dikirim'; $nextLabel = 'Kirim ke Pelanggan'; $btnColor = '#f59e0b'; }
                                            else { $nextStatus = 'completed'; $nextLabel = 'Selesai & Diambil'; $btnColor = '#10b981'; }
                                        }
                                        elseif ($order->status == 'dikirim') { $nextStatus = 'completed'; $nextLabel = 'Selesai & Diterima'; $btnColor = '#10b981'; }
                                    } else {
                                        if ($order->status == 'processing') { $nextStatus = 'finishing'; $nextLabel = 'Selesai Kerja → Finishing'; }
                                        elseif ($order->status == 'finishing') { $nextStatus = 'ready'; $nextLabel = 'Finishing → Siap'; }
                                        elseif ($order->status == 'ready') { 
                                            if ($order->is_delivery) { $nextStatus = 'dikirim'; $nextLabel = 'Kirim ke Pelanggan'; $btnColor = '#f59e0b'; }
                                            else { $nextStatus = 'completed'; $nextLabel = 'Selesai & Diambil'; $btnColor = '#10b981'; }
                                        }
                                        elseif ($order->status == 'dikirim') { $nextStatus = 'completed'; $nextLabel = 'Selesai & Diterima'; $btnColor = '#10b981'; }
                                    }
                                @endphp

                                @if($nextStatus)
                                    <form action="{{ route('orders.status.update', $order) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="{{ $nextStatus }}">
                                        @if($nextStatus == 'ready')
                                            <input type="text" name="storage_location" placeholder="Lokasi Rak (Cth: A1)" required style="width: 100%; padding: 0.5rem; margin-bottom: 0.5rem; border-radius: 8px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: #fff; font-size: 0.8rem;">
                                        @endif
                                        <button type="submit" style="width: 100%; background: {{ $btnColor }}; color: #fff; border: none; padding: 0.8rem; border-radius: 12px; font-size: 0.85rem; font-weight: 800; cursor: pointer; transition: 0.3s; display: flex; align-items: center; justify-content: center; gap: 0.5rem; box-shadow: 0 4px 15px rgba(0,0,0,0.1);" onmouseover="this.style.transform='translateY(-2px)'; this.style.opacity='0.9'" onmouseout="this.style.transform='translateY(0)'; this.style.opacity='1'">
                                            <span>{{ $nextLabel }}</span>
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                                        </button>
                                    </form>
                                @endif
                                
                                @if($order->payment_status == 'unpaid')
                                    <form action="{{ route('orders.payment.remind', $order) }}" method="POST">
                                        @csrf
                                        <button type="submit" title="Kirim Pengingat WA" style="width: 100%; background: rgba(16, 185, 129, 0.1); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.2); padding: 0.5rem; border-radius: 8px; font-size: 0.75rem; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 4px;">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 1 1-7.6-13.3 8.38 8.38 0 0 1 3.9.9L22 4l-1.5 6.5z"></path></svg>
                                            REKAYASA WA
                                        </button>
                                    </form>
                                @endif
                                <button onclick='openEditModal(@json($order))' style="width: 100%; background: #3b82f6; color: #fff; border: none; padding: 0.5rem; border-radius: 8px; font-size: 0.75rem; font-weight: 700; cursor: pointer; transition: 0.3s; margin-top: 0.5rem;" onmouseover="this.style.opacity='0.8'" onmouseout="this.style.opacity='1'">EDIT PESANAN</button>
                            </div>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Mobile Cards -->
    <div class="mobile-cards">
        @foreach($orders as $order)
        <div class="order-card">
            <div class="order-card-header">
                <a href="{{ route('orders.show', $order->id) }}" style="text-decoration: none; color: inherit; display: flex; align-items: center; gap: 0.5rem;">
                    <div style="background: var(--primary); color: #0f172a; padding: 0.2rem 0.6rem; border-radius: 6px; font-weight: 800; cursor: pointer; transition: 0.2s;" onmouseover="this.style.opacity='0.8'" onmouseout="this.style.opacity='1'">#{{ $order->queue_number ?? '-' }} 🔍</div>
                </a>
                <span style="font-size: 0.85rem; opacity: 0.6;">{{ $order->created_at->format('d M Y H:i') }}</span>
            </div>
            @if($order->is_delivery && $order->latitude && $order->longitude)
                <div style="margin-bottom: 0.6rem; padding: 0;">
                    <a href="https://www.google.com/maps/search/?api=1&query={{ $order->latitude }},{{ $order->longitude }}" target="_blank" style="display: inline-flex; align-items: center; gap: 4px; background: rgba(16, 185, 129, 0.1); color: #10b981; padding: 0.3rem 0.6rem; border-radius: 6px; font-size: 0.7rem; font-weight: 700; text-decoration: none; border: 1px solid rgba(16, 185, 129, 0.2);">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                        Buka Peta Pelanggan
                    </a>
                </div>
            @endif
            <div class="order-card-body">
                <div style="font-weight: 700; font-size: 1rem; margin-bottom: 0.2rem;">{{ $order->service->name }}</div>
                <div style="font-size: 0.8rem; opacity: 0.6; margin-bottom: 0.4rem;">Pelanggan: {{ $order->user->name }}</div>
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span class="badge {{ $order->service->category == 'cleaning' ? 'badge-success' : 'badge-warning' }}" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
                        {{ $order->service->category == 'cleaning' ? 'Cuci' : 'Reparasi' }}
                    </span>
                    <span style="color: var(--primary); font-size: 0.8rem; font-weight: 600;">{{ strtoupper($order->status) }}</span>
                </div>
            </div>
            <div class="order-card-footer">
                @php
                    $nextStatus = null;
                    $nextLabel = '';
                    $btnColor = '#3b82f6';

                    if ($order->service->category == 'cleaning') {
                        if ($order->status == 'processing') { $nextStatus = 'finishing'; $nextLabel = 'Selesai Cuci → Jemur'; }
                        elseif ($order->status == 'finishing') { $nextStatus = 'ready'; $nextLabel = 'Selesai Jemur → Siap'; }
                        elseif ($order->status == 'ready') { 
                            if ($order->is_delivery) { $nextStatus = 'dikirim'; $nextLabel = 'Kirim ke Pelanggan'; $btnColor = '#f59e0b'; }
                            else { $nextStatus = 'completed'; $nextLabel = 'Selesai & Diambil'; $btnColor = '#10b981'; }
                        }
                        elseif ($order->status == 'dikirim') { $nextStatus = 'completed'; $nextLabel = 'Selesai & Diterima'; $btnColor = '#10b981'; }
                    } else {
                        if ($order->status == 'processing') { $nextStatus = 'finishing'; $nextLabel = 'Selesai Kerja → Finishing'; }
                        elseif ($order->status == 'finishing') { $nextStatus = 'ready'; $nextLabel = 'Finishing → Siap'; }
                        elseif ($order->status == 'ready') { 
                            if ($order->is_delivery) { $nextStatus = 'dikirim'; $nextLabel = 'Kirim ke Pelanggan'; $btnColor = '#f59e0b'; }
                            else { $nextStatus = 'completed'; $nextLabel = 'Selesai & Diambil'; $btnColor = '#10b981'; }
                        }
                        elseif ($order->status == 'dikirim') { $nextStatus = 'completed'; $nextLabel = 'Selesai & Diterima'; $btnColor = '#10b981'; }
                    }
                @endphp

                @if($order->status == 'pending')
                    <div style="display: flex; gap: 0.5rem;">
                        <form action="{{ route('orders.status.update', $order) }}" method="POST" style="flex: 1;">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="processing">
                            <button type="submit" style="width: 100%; background: #10b981; color: #fff; border: none; padding: 0.6rem; border-radius: 8px; font-size: 0.8rem; font-weight: 700; cursor: pointer;">Terima</button>
                        </form>
                        <form action="{{ route('orders.status.update', $order) }}" method="POST" style="flex: 1;">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="cancelled">
                            <button type="submit" onclick="return confirm('Apakah Anda yakin ingin menolak pesanan ini?')" style="width: 100%; background: #f43f5e; color: #fff; border: none; padding: 0.6rem; border-radius: 8px; font-size: 0.8rem; font-weight: 700; cursor: pointer;">Tolak</button>
                        </form>
                    </div>
                @else
                    @if($nextStatus)
                        <form action="{{ route('orders.status.update', $order) }}" method="POST" style="margin-bottom: 0.4rem;">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="{{ $nextStatus }}">
                            @if($nextStatus == 'ready')
                                <input type="text" name="storage_location" placeholder="Lokasi Rak (Cth: A1)" required style="width: 100%; padding: 0.6rem; margin-bottom: 0.4rem; border-radius: 6px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: #fff; font-size: 0.8rem;">
                            @endif
                            <button type="submit" style="width: 100%; background: {{ $btnColor }}; color: #fff; border: none; padding: 0.6rem; border-radius: 8px; font-size: 0.8rem; font-weight: 800; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                                <span>{{ $nextLabel }}</span>
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                            </button>
                        </form>
                    @endif
                @endif
                @if($order->payment_status == 'unpaid')
                    <form action="{{ route('orders.payment.remind', $order) }}" method="POST" style="margin-top: 0.4rem;">
                        @csrf
                        <button type="submit" style="width: 100%; background: rgba(16, 185, 129, 0.1); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.2); padding: 0.5rem; border-radius: 8px; font-size: 0.75rem; font-weight: 700; cursor: pointer;">PENGINGAT WA PEMBAYARAN</button>
                    </form>
                @endif
                <button onclick='openEditModal(@json($order))' style="width: 100%; background: #3b82f6; color: #fff; border: none; padding: 0.6rem; border-radius: 8px; font-size: 0.8rem; font-weight: 700; cursor: pointer; margin-top: 0.4rem;">EDIT PESANAN</button>
                @if($order->payment_status == 'paid')
                <a href="{{ route('orders.receipt', $order->id) }}" target="_blank" style="width: 100%; background: rgba(255,255,255,0.05); color: #fff; border: 1px solid rgba(255,255,255,0.1); padding: 0.6rem; border-radius: 8px; font-size: 0.8rem; font-weight: 700; cursor: pointer; margin-top: 0.4rem; text-decoration: none; text-align: center; display: block;">CETAK NOTA</a>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    @if($orders->isEmpty())
        <div style="padding: 5rem; text-align: center; opacity: 0.3;">
            <p>Belum ada pesanan masuk.</p>
        </div>
    @endif
</div>

<!-- Modal Tambah Pesanan -->
<div id="createOrderModal" class="modal-backdrop" onclick="closeModal('createOrderModal')">
    <div class="modal-box" onclick="event.stopPropagation()">
        <button class="modal-close" onclick="closeModal('createOrderModal')">&times;</button>
        <h3 style="font-size: 1.5rem; font-weight: 900; margin-bottom: 20px; border-bottom: 1px solid rgba(255,255,255,0.08); padding-bottom: 15px;">Tambah <span style="color: var(--primary);">Pesanan Baru</span></h3>
        
        <form action="{{ route('employee.orders.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div style="margin-bottom: 1.2rem;">
                <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--text-secondary); margin-bottom: 5px; text-transform: uppercase;">Pelanggan</label>
                <select name="user_id" required class="filter-input" style="width: 100%; background: #1e1e24;">
                    <option value="">Pilih Pelanggan</option>
                    @foreach($customers as $cust)
                        <option value="{{ $cust->id }}">{{ $cust->name }} ({{ $cust->phone }})</option>
                    @endforeach
                </select>
            </div>
            <div class="modal-grid-2">
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--text-secondary); margin-bottom: 5px; text-transform: uppercase;">Layanan (Bisa Pilih Lebih dari Satu)</label>
                    <div style="background: #1e1e24; border: 1px solid rgba(255,255,255,0.05); padding: 12px; border-radius: 12px; max-height: 150px; overflow-y: auto; display: flex; flex-direction: column; gap: 8px;" class="custom-scroll">
                        @foreach($services as $serv)
                            <label style="display: flex; align-items: center; gap: 10px; color: #fff; font-size: 0.85rem; cursor: pointer; user-select: none;">
                                <input type="checkbox" name="service_ids[]" value="{{ $serv->id }}" style="width: 18px; height: 18px; accent-color: var(--primary);">
                                <span>{{ $serv->name }} <span style="color: var(--primary); font-weight: 700;">(Rp{{ number_format($serv->price, 0, ',', '.') }})</span></span>
                            </label>
                        @endforeach
                    </div>
                </div>
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--text-secondary); margin-bottom: 5px; text-transform: uppercase;">Kecepatan</label>
                    <select name="processing_speed" required class="filter-input" style="width: 100%; background: #1e1e24;">
                        <option value="regular">Regular (+Rp0)</option>
                        <option value="express">Express (+Rp25.000 / Sepatu)</option>
                    </select>
                </div>
            </div>
            <div class="modal-grid-2">
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--text-secondary); margin-bottom: 5px; text-transform: uppercase;">Nama Sepatu</label>
                    <input type="text" name="shoe_name" placeholder="Contoh: Nike Air Jordan" required class="filter-input" style="width: 100%; background: #1e1e24;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--text-secondary); margin-bottom: 5px; text-transform: uppercase;">Ukuran Sepatu</label>
                    <input type="text" name="shoe_size" placeholder="Contoh: 42" required class="filter-input" style="width: 100%; background: #1e1e24;">
                </div>
            </div>
            <div class="modal-grid-2">
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--text-secondary); margin-bottom: 5px; text-transform: uppercase;">Jumlah Sepatu</label>
                    <input type="number" name="shoe_quantity" value="1" min="1" required class="filter-input" style="width: 100%; background: #1e1e24;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--text-secondary); margin-bottom: 5px; text-transform: uppercase;">Metode Pembayaran</label>
                    <select name="payment_method" required class="filter-input" style="width: 100%; background: #1e1e24;">
                        <option value="cash">Tunai (Cash)</option>
                        <option value="qris">QRIS</option>
                        <option value="transfer">Transfer Bank</option>
                    </select>
                </div>
            </div>
            <div class="modal-grid-2">
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--text-secondary); margin-bottom: 5px; text-transform: uppercase;">Status Pembayaran</label>
                    <select name="payment_status" required class="filter-input" style="width: 100%; background: #1e1e24;">
                        <option value="unpaid">Belum Lunas (Unpaid)</option>
                        <option value="paid">Lunas (Paid)</option>
                    </select>
                </div>
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--text-secondary); margin-bottom: 5px; text-transform: uppercase;">Status Pesanan</label>
                    <select name="status" required class="filter-input" style="width: 100%; background: #1e1e24;">
                        <option value="pending">Menunggu (Pending)</option>
                        <option value="processing">Proses (Processing)</option>
                        <option value="washing">Dicuci (Washing)</option>
                        <option value="drying">Dikeringkan (Drying)</option>
                        <option value="finishing">Finishing</option>
                        <option value="ready">Siap Diambil (Ready)</option>
                        <option value="completed">Diambil (Completed)</option>
                    </select>
                </div>
            </div>
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--text-secondary); margin-bottom: 5px; text-transform: uppercase;">Foto Sepatu (Sebelum)</label>
                <input type="file" name="shoe_photo" class="filter-input" style="width: 100%; background: #1e1e24;">
            </div>
            <button type="submit" style="background: var(--primary); color: #000; border: none; width: 100%; padding: 12px; border-radius: 12px; font-weight: 800; cursor: pointer;">Buat Pesanan</button>
        </form>
    </div>
</div>

<!-- Modal Edit Pesanan -->
<div id="editOrderModal" class="modal-backdrop" onclick="closeModal('editOrderModal')">
    <div class="modal-box" onclick="event.stopPropagation()">
        <button class="modal-close" onclick="closeModal('editOrderModal')">&times;</button>
        <h3 style="font-size: 1.5rem; font-weight: 900; margin-bottom: 20px; border-bottom: 1px solid rgba(255,255,255,0.08); padding-bottom: 15px;">Edit <span style="color: var(--primary);">Pesanan</span></h3>
        
        <form id="editOrderForm" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="modal-grid-2">
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--text-secondary); margin-bottom: 5px; text-transform: uppercase;">Layanan</label>
                    <select name="service_id" id="edit_service_id" required class="filter-input" style="width: 100%; background: #1e1e24;">
                        @foreach($services as $serv)
                            <option value="{{ $serv->id }}">{{ $serv->name }} (Rp{{ number_format($serv->price, 0, ',', '.') }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--text-secondary); margin-bottom: 5px; text-transform: uppercase;">Nomor Antrian</label>
                    <input type="text" name="queue_number" id="edit_queue_number" required class="filter-input" style="width: 100%; background: #1e1e24;">
                </div>
            </div>
            <div class="modal-grid-2">
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--text-secondary); margin-bottom: 5px; text-transform: uppercase;">Kecepatan</label>
                    <select name="processing_speed" id="edit_processing_speed" required class="filter-input" style="width: 100%; background: #1e1e24;">
                        <option value="regular">Regular</option>
                        <option value="express">Express</option>
                    </select>
                </div>
            </div>
            <div class="modal-grid-2">
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--text-secondary); margin-bottom: 5px; text-transform: uppercase;">Nama Sepatu</label>
                    <input type="text" name="shoe_name" id="edit_shoe_name" required class="filter-input" style="width: 100%; background: #1e1e24;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--text-secondary); margin-bottom: 5px; text-transform: uppercase;">Ukuran Sepatu</label>
                    <input type="text" name="shoe_size" id="edit_shoe_size" required class="filter-input" style="width: 100%; background: #1e1e24;">
                </div>
            </div>
            <div class="modal-grid-2">
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--text-secondary); margin-bottom: 5px; text-transform: uppercase;">Jumlah Sepatu</label>
                    <input type="number" name="shoe_quantity" id="edit_shoe_quantity" min="1" required class="filter-input" style="width: 100%; background: #1e1e24;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--text-secondary); margin-bottom: 5px; text-transform: uppercase;">Metode Pembayaran</label>
                    <select name="payment_method" id="edit_payment_method" required class="filter-input" style="width: 100%; background: #1e1e24;">
                        <option value="cash">Tunai (Cash)</option>
                        <option value="qris">QRIS</option>
                        <option value="transfer">Transfer Bank</option>
                    </select>
                </div>
            </div>
            <div class="modal-grid-2">
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--text-secondary); margin-bottom: 5px; text-transform: uppercase;">Status Pembayaran</label>
                    <select name="payment_status" id="edit_payment_status" required class="filter-input" style="width: 100%; background: #1e1e24;">
                        <option value="unpaid">Belum Lunas (Unpaid)</option>
                        <option value="paid">Lunas (Paid)</option>
                    </select>
                    <div id="edit_payment_proof_container" style="display: none; margin-top: 8px;">
                        <a id="edit_payment_proof_link" href="#" target="_blank" style="font-size: 0.75rem; color: #60a5fa; text-decoration: underline;">Lihat Bukti Pembayaran (Validasi)</a>
                    </div>
                </div>
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--text-secondary); margin-bottom: 5px; text-transform: uppercase;">Status Pesanan</label>
                    <select name="status" id="edit_status" required class="filter-input" style="width: 100%; background: #1e1e24;">
                        <option value="pending">Menunggu (Pending)</option>
                        <option value="processing">Proses (Processing)</option>
                        <option value="washing">Dicuci (Washing)</option>
                        <option value="drying">Dikeringkan (Drying)</option>
                        <option value="finishing">Finishing</option>
                        <option value="ready">Siap Diambil (Ready)</option>
                        <option value="completed">Diambil (Completed)</option>
                        <option value="cancelled">Dibatalkan (Cancelled)</option>
                    </select>
                </div>
            </div>
            <div class="modal-grid-2">
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--text-secondary); margin-bottom: 5px; text-transform: uppercase;">Ubah Foto Sebelum</label>
                    <input type="file" name="shoe_photo" class="filter-input" style="width: 100%; background: #1e1e24;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--text-secondary); margin-bottom: 5px; text-transform: uppercase;">Upload Foto Sesudah</label>
                    <input type="file" name="photo_after" class="filter-input" style="width: 100%; background: #1e1e24;">
                </div>
            </div>
            <button type="submit" style="background: var(--primary); color: #000; border: none; width: 100%; padding: 12px; border-radius: 12px; font-weight: 800; cursor: pointer;">Simpan Perubahan</button>
        </form>
    </div>
</div>
@endsection

<script>
    function openCreateModal() {
        document.getElementById('createOrderModal').classList.add('active');
    }

    function openEditModal(order) {
        document.getElementById('editOrderForm').action = `/employee/orders/${order.id}`;
        
        document.getElementById('edit_queue_number').value = order.queue_number;
        document.getElementById('edit_service_id').value = order.service_id;
        document.getElementById('edit_processing_speed').value = order.processing_speed;
        document.getElementById('edit_shoe_name').value = order.shoe_name || '';
        document.getElementById('edit_shoe_size').value = order.shoe_size || '';
        document.getElementById('edit_shoe_quantity').value = order.shoe_quantity || 1;
        document.getElementById('edit_payment_method').value = order.payment_method;
        document.getElementById('edit_payment_status').value = order.payment_status;
        document.getElementById('edit_status').value = order.status;
        
        const proofContainer = document.getElementById('edit_payment_proof_container');
        const proofLink = document.getElementById('edit_payment_proof_link');
        if (order.payment_proof) {
            proofLink.href = '/storage/' + order.payment_proof;
            proofContainer.style.display = 'block';
        } else {
            proofContainer.style.display = 'none';
        }
        
        document.getElementById('editOrderModal').classList.add('active');
    }

    function closeModal(modalId) {
        document.getElementById(modalId).classList.remove('active');
    }

    // Real-time Auto Refresh every 5 seconds for instant queue updates
    let isInteracting = false;

    // Detect if user is interacting with dropdowns or modals to avoid refreshing mid-selection
    document.addEventListener('focusin', (e) => {
        if (e.target.tagName === 'SELECT' || e.target.tagName === 'INPUT' || e.target.closest('.modal-backdrop')) isInteracting = true;
    });
    document.addEventListener('focusout', (e) => {
        if (e.target.tagName === 'SELECT' || e.target.tagName === 'INPUT' || e.target.closest('.modal-backdrop')) isInteracting = false;
    });
    
    // Also disable refresh when modal is open
    setInterval(() => {
        const isModalOpen = document.getElementById('createOrderModal').classList.contains('active');
        if (!isInteracting && !isModalOpen) {
            window.location.reload();
        }
    }, 5000); // 5 seconds
</script>
