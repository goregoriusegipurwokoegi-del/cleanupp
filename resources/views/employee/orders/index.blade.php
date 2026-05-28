@extends('layouts.premium-dashboard')

@section('page_title', 'Daftar Tugas Karyawan')

@section('nav_items')
    <li class="nav-item"><a href="{{ route('employee.dashboard') }}" class="nav-link">Dashboard</a></li>
    <li class="nav-item"><a href="{{ route('employee.orders.index') }}" class="nav-link active">Antrian Pesanan</a></li>
    <li class="nav-item"><a href="#" class="nav-link">Inventaris</a></li>
@endsection

@section('content')
<div style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center;">
    <div>
        <h2 style="font-size: 1.8rem; font-weight: 800; margin-bottom: 0.5rem;">Antrian Pesanan</h2>
        <p style="opacity: 0.6;">Kelola dan perbarui status pengerjaan sepatu pelanggan.</p>
    </div>
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
            border-radius: 16px; 
            padding: 1.2rem; 
            margin-bottom: 1rem;
        }
        .order-card-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; }
        .order-card-body { margin-bottom: 1rem; }
        .order-card-footer { border-top: 1px solid rgba(255,255,255,0.05); pt: 1rem; padding-top: 1rem; }
        select { width: 100%; padding: 0.8rem !important; }
    }
</style>

<div class="glass-card" style="border-radius: 24px; overflow: hidden; background: transparent; border: none; padding: 0;">
    <!-- Desktop Table -->
    <div class="table-desktop table-container">
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="background: rgba(255,255,255,0.03); border-bottom: 1px solid rgba(255,255,255,0.05);">
                    <th style="padding: 1.5rem; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px; opacity: 0.6;">Antrian</th>
                    <th style="padding: 1.5rem; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px; opacity: 0.6;">Sepatu</th>
                    <th style="padding: 1.5rem; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px; opacity: 0.6;">Layanan</th>
                    <th style="padding: 1.5rem; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px; opacity: 0.6;">Status Saat Ini</th>
                <th style="padding: 1.5rem; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px; opacity: 0.6;">Tanggal Masuk</th>
                    <th style="padding: 1.5rem; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px; opacity: 0.6;">Perbarui Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $order)
                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02); transition: 0.3s;" onmouseover="this.style.background='rgba(255,255,255,0.01)'" onmouseout="this.style.background='transparent'">
                    <td style="padding: 1.5rem;">
                        <a href="{{ route('orders.show', $order->id) }}" style="text-decoration: none; color: inherit; display: block;">
                            <div style="background: var(--primary); color: #0f172a; padding: 0.2rem 0.6rem; border-radius: 6px; font-weight: 800; width: fit-content; margin-bottom: 0.3rem; cursor: pointer; transition: 0.2s;" onmouseover="this.style.opacity='0.8'" onmouseout="this.style.opacity='1'">{{ $order->queue_number ?? '-' }}</div>
                            <div style="font-size: 0.75rem; font-weight: 600; color: #fff; transition: 0.2s;" onmouseover="this.style.color='var(--primary)'" onmouseout="this.style.color='#fff'">{{ $order->user->name }}</div>
                            <div style="font-size: 0.65rem; opacity: 0.4; margin-top: 0.2rem;">Klik Detail Pesanan 🔍</div>
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
            <div class="order-card-body">
                <div style="font-weight: 700; font-size: 1.1rem; margin-bottom: 0.3rem;">{{ $order->service->name }}</div>
                <div style="font-size: 0.85rem; opacity: 0.6; margin-bottom: 0.5rem;">Pelanggan: {{ $order->user->name }}</div>
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span class="badge {{ $order->service->category == 'cleaning' ? 'badge-success' : 'badge-warning' }}">
                        {{ $order->service->category == 'cleaning' ? 'Cuci' : 'Reparasi' }}
                    </span>
                    <span style="color: var(--primary); font-size: 0.85rem; font-weight: 600;">{{ strtoupper($order->status) }}</span>
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
                            <button type="submit" style="width: 100%; background: #10b981; color: #fff; border: none; padding: 0.8rem; border-radius: 12px; font-weight: 700; cursor: pointer;">Terima</button>
                        </form>
                        <form action="{{ route('orders.status.update', $order) }}" method="POST" style="flex: 1;">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="cancelled">
                            <button type="submit" onclick="return confirm('Apakah Anda yakin ingin menolak pesanan ini?')" style="width: 100%; background: #f43f5e; color: #fff; border: none; padding: 0.8rem; border-radius: 12px; font-weight: 700; cursor: pointer;">Tolak</button>
                        </form>
                    </div>
                @else
                    @if($nextStatus)
                        <form action="{{ route('orders.status.update', $order) }}" method="POST" style="margin-bottom: 0.5rem;">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="{{ $nextStatus }}">
                            @if($nextStatus == 'ready')
                                <input type="text" name="storage_location" placeholder="Lokasi Rak (Cth: A1)" required style="width: 100%; padding: 0.8rem; margin-bottom: 0.5rem; border-radius: 8px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: #fff; font-size: 0.9rem;">
                            @endif
                            <button type="submit" style="width: 100%; background: {{ $btnColor }}; color: #fff; border: none; padding: 0.8rem; border-radius: 12px; font-size: 0.9rem; font-weight: 800; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                                <span>{{ $nextLabel }}</span>
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                            </button>
                        </form>
                    @endif
                @endif
                @if($order->payment_status == 'unpaid')
                    <form action="{{ route('orders.payment.remind', $order) }}" method="POST" style="margin-top: 0.5rem;">
                        @csrf
                        <button type="submit" style="width: 100%; background: rgba(16, 185, 129, 0.1); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.2); padding: 0.8rem; border-radius: 12px; font-size: 0.85rem; font-weight: 700; cursor: pointer;">PENGINGAT WA PEMBAYARAN</button>
                    </form>
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
@endsection

<script>
    // Real-time Auto Refresh every 5 seconds for instant queue updates
    let isInteracting = false;

    // Detect if user is interacting with dropdowns to avoid refreshing mid-selection
    document.addEventListener('focusin', (e) => {
        if (e.target.tagName === 'SELECT') isInteracting = true;
    });
    document.addEventListener('focusout', (e) => {
        if (e.target.tagName === 'SELECT') isInteracting = false;
    });

    setInterval(() => {
        if (!isInteracting) {
            window.location.reload();
        }
    }, 5000); // 5 seconds
</script>
