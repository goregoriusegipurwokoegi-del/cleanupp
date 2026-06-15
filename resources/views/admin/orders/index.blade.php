@extends('layouts.premium-dashboard')

@section('page_title', request('queue') ? 'Monitor Antrian' : (request('delivery') ? 'Antar Jemput' : 'Kelola Pesanan'))

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
    .filter-bar {
        display: flex;
        gap: 15px;
        margin-bottom: 25px;
        flex-wrap: wrap;
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
    
    .order-table-container {
        background: rgba(255,255,255,0.02);
        border: 1px solid rgba(255,255,255,0.05);
        border-radius: 20px;
        overflow: hidden;
    }
    
    .order-card-mobile {
        display: none;
    }

    @media (max-width: 1024px) {
        .order-table-desktop { display: none; }
        .order-card-mobile { 
            display: block; 
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.05);
            border-radius: 16px;
            padding: 15px;
            margin-bottom: 15px;
        }
        .card-header-mobile { display: flex; justify-content: space-between; margin-bottom: 10px; }
        .card-body-mobile { margin-bottom: 15px; }
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
    .modal-photo {
        width: 100%;
        height: 150px;
        border-radius: 12px;
        object-fit: cover;
        background: #000;
        border: 1px solid rgba(255, 255, 255, 0.05);
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
</style>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; flex-wrap: wrap; gap: 15px;">

    @if(!request('queue'))
    <button onclick="openCreateModal()" style="background: var(--primary); color: #000; border: none; padding: 10px 20px; border-radius: 12px; font-weight: 800; cursor: pointer; display: flex; align-items: center; gap: 8px;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
        Tambah Pesanan
    </button>
    @endif
</div>

<!-- Search & Filter Bar -->
<form action="{{ route('admin.orders.index') }}" method="GET" class="filter-bar">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, No. Order, No. Antrian..." class="filter-input" style="flex: 2; min-width: 250px;">
    <select name="status" class="filter-input" style="flex: 1; min-width: 150px;" onchange="this.form.submit()">
        <option value="">Semua Status</option>
        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu</option>
        <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Proses</option>
        <option value="finishing" {{ request('status') == 'finishing' ? 'selected' : '' }}>Finishing</option>
        <option value="ready" {{ request('status') == 'ready' ? 'selected' : '' }}>Selesai</option>
        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Diambil</option>
    </select>
</form>

@if(session('success'))
    <div style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.2); color: #10b981; padding: 12px; border-radius: 12px; margin-bottom: 20px; font-weight: 600;">
        {{ session('success') }}
    </div>
@endif

<!-- Desktop View -->
<div class="order-table-desktop order-table-container">
    <table style="width: 100%; border-collapse: collapse; text-align: left;">
        <thead>
            <tr style="background: rgba(255,255,255,0.03); border-bottom: 1px solid rgba(255,255,255,0.05);">
                <th style="padding: 15px; font-size: 0.8rem; opacity: 0.5;">ANTRIAN</th>
                <th style="padding: 15px; font-size: 0.8rem; opacity: 0.5;">PELANGGAN</th>
                <th style="padding: 15px; font-size: 0.8rem; opacity: 0.5;">SEPATU</th>
                <th style="padding: 15px; font-size: 0.8rem; opacity: 0.5;">LAYANAN</th>
                <th style="padding: 15px; font-size: 0.8rem; opacity: 0.5;">STATUS</th>
                <th style="padding: 15px; font-size: 0.8rem; opacity: 0.5;">AKSI</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $group)
                @php
                    $order = $group->first();
                @endphp
            <tr class="clickable-row" 
                data-order="{{ json_encode($order) }}" 
                data-user="{{ json_encode($order->user) }}" 
                data-service="{{ json_encode($order->service) }}"
                data-services="{{ json_encode($group->map(fn($o) => ['name' => $o->service->name, 'price' => $o->total_price - $o->delivery_fee])) }}"
                data-group-total="{{ $group->sum('total_price') }}"
                style="border-bottom: 1px solid rgba(255,255,255,0.02); cursor: pointer; transition: background 0.2s;"
                onmouseover="this.style.background='rgba(255,255,255,0.02)'"
                onmouseout="this.style.background='transparent'">
                <td style="padding: 15px;">
                    <div style="background: var(--primary); color: #000; padding: 2px 8px; border-radius: 6px; font-weight: 800; width: fit-content; margin-bottom: 3px; white-space: nowrap;">
                        @foreach($group->pluck('queue_number')->unique() as $qNum)
                            {{ $qNum }}{{ !$loop->last ? ',' : '' }}
                        @endforeach
                    </div>
                    <div style="font-size: 0.75rem; font-weight: 700; color: var(--primary);">#{{ $order->group_id ?: $order->order_number }}</div>
                    <div style="font-size: 0.65rem; opacity: 0.4;">{{ $order->created_at->format('d/m H:i') }}</div>
                </td>
                <td style="padding: 15px;">
                    <div style="font-weight: 700;">{{ $order->user->name }}</div>
                    <div style="font-size: 0.75rem; opacity: 0.5;">{{ $order->user->phone }}</div>
                </td>
                <td style="padding: 15px;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <img src="{{ asset('storage/' . $order->photo_before) }}" style="width: 45px; height: 45px; border-radius: 8px; object-fit: cover; background: #000;">
                        <div>
                            <div style="font-weight: 700; font-size: 0.85rem;">{{ $order->shoe_name }}</div>
                            <div style="font-size: 0.75rem; color: var(--primary); font-weight: 700;">Size: {{ $order->shoe_size }}</div>
                        </div>
                    </div>
                </td>
                <td style="padding: 15px;">
                    @foreach($group as $grpItem)
                        <div style="font-weight: 700; font-size: 0.85rem; margin-bottom: 2px;">{{ $grpItem->service->name }}</div>
                    @endforeach
                    <div style="font-size: 0.75rem; opacity: 0.5; font-weight: bold; margin-top: 5px; color: var(--primary);">Rp {{ number_format($group->sum('total_price'), 0, ',', '.') }}</div>
                </td>
                <td style="padding: 15px;">
                    @php
                        $statusLabels = [
                            'pending' => 'MENUNGGU',
                            'processing' => 'PROSES',
                            'washing' => 'DICUCI',
                            'drying' => 'DIKERINGKAN',
                            'finishing' => 'FINISHING',
                            'ready' => 'SIAP DIAMBIL',
                            'completed' => 'DIAMBIL',
                            'cancelled' => 'DIBATALKAN'
                        ];
                        $colors = [
                            'pending' => '#f59e0b',
                            'processing' => '#3b82f6',
                            'washing' => '#0ea5e9',
                            'drying' => '#6366f1',
                            'finishing' => '#a855f7',
                            'ready' => '#10b981',
                            'completed' => '#2563eb',
                            'cancelled' => '#ef4444'
                        ];
                        $currentColor = $colors[$order->status] ?? '#64748b';
                        $currentLabel = $statusLabels[$order->status] ?? strtoupper($order->status);
                    @endphp
                    <span style="background: {{ $currentColor }}20; color: {{ $currentColor }}; padding: 4px 10px; border-radius: 8px; font-size: 0.75rem; font-weight: 800; border: 1px solid {{ $currentColor }}30; white-space: nowrap;">
                        {{ $currentLabel }}
                    </span>
                    <div style="margin-top: 5px;">
                        @if($order->payment_status == 'paid')
                            <span style="background: rgba(16, 185, 129, 0.15); color: #10b981; padding: 2px 6px; border-radius: 6px; font-size: 0.65rem; font-weight: 800; border: 1px solid rgba(16, 185, 129, 0.3); white-space: nowrap;">LUNAS</span>
                        @else
                            <span style="background: rgba(239, 68, 68, 0.15); color: #ef4444; padding: 2px 6px; border-radius: 6px; font-size: 0.65rem; font-weight: 800; border: 1px solid rgba(239, 68, 68, 0.3); white-space: nowrap;">BELUM BAYAR</span>
                        @endif
                    </div>
                </td>
                <td style="padding: 15px;">
                    <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                        @if($order->status == 'pending')
                            <form action="{{ route('admin.orders.status.update', $order) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="processing">
                                <button type="submit" style="background: #10b981; color: #fff; border: none; padding: 6px 12px; border-radius: 8px; font-size: 0.75rem; font-weight: 800; cursor: pointer;">Terima</button>
                            </form>
                            <form action="{{ route('admin.orders.status.update', $order) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="cancelled">
                                <button type="submit" onclick="return confirm('Tolak pesanan?')" style="background: #ef4444; color: #fff; border: none; padding: 6px 12px; border-radius: 8px; font-size: 0.75rem; font-weight: 800; cursor: pointer;">Tolak</button>
                            </form>
                        @else
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
                                    if ($order->status == 'processing') { $nextStatus = 'ready'; $nextLabel = 'Selesai Repaint/Reparasi → Siap'; }
                                    elseif ($order->status == 'ready') { 
                                        if ($order->is_delivery) { $nextStatus = 'dikirim'; $nextLabel = 'Kirim ke Pelanggan'; $btnColor = '#f59e0b'; }
                                        else { $nextStatus = 'completed'; $nextLabel = 'Selesai & Diambil'; $btnColor = '#10b981'; }
                                    }
                                    elseif ($order->status == 'dikirim') { $nextStatus = 'completed'; $nextLabel = 'Selesai & Diterima'; $btnColor = '#10b981'; }
                                }
                            @endphp
                            
                            @if($nextStatus)
                                <form action="{{ route('admin.orders.status.update', $order) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="{{ $nextStatus }}">
                                    <button type="submit" style="background: {{ $btnColor }}; color: #fff; border: none; padding: 6px 12px; border-radius: 8px; font-size: 0.75rem; font-weight: 800; cursor: pointer;">{{ $nextLabel }}</button>
                                </form>
                            @endif
                        @endif
                        <button class="view-detail-btn" style="background: rgba(255,255,255,0.05); color: #fff; border: 1px solid rgba(255,255,255,0.1); padding: 6px 12px; border-radius: 8px; font-size: 0.75rem; font-weight: 800; cursor: pointer;">Detail</button>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Mobile View -->
<div class="order-card-mobile">
    @foreach($orders as $group)
        @php
            $order = $group->first();
        @endphp
    <div class="order-card-mobile clickable-row" 
         data-order="{{ json_encode($order) }}" 
         data-user="{{ json_encode($order->user) }}" 
         data-service="{{ json_encode($order->service) }}"
         data-services="{{ json_encode($group->map(fn($o) => ['name' => $o->service->name, 'price' => $o->total_price - $o->delivery_fee])) }}"
         data-group-total="{{ $group->sum('total_price') }}"
         style="margin-bottom: 20px; cursor: pointer;">
        <div class="card-header-mobile">
            <div style="display: flex; gap: 10px; align-items: center;">
                <div style="background: var(--primary); color: #000; padding: 2px 8px; border-radius: 6px; font-weight: 900; white-space: nowrap;">
                    @foreach($group->pluck('queue_number')->unique() as $qNum)
                        {{ $qNum }}{{ !$loop->last ? ',' : '' }}
                    @endforeach
                </div>
                <div style="font-weight: 800; color: var(--primary);">#{{ $order->group_id ?: $order->order_number }}</div>
            </div>
            <div style="font-size: 0.7rem; opacity: 0.5;">{{ $order->created_at->format('d/m H:i') }}</div>
        </div>
        <div class="card-body-mobile">
            <div style="font-weight: 700; margin-bottom: 5px;">{{ $order->user->name }} ({{ $order->shoe_name }})</div>
            <div style="font-size: 0.85rem; opacity: 0.6;">
                @foreach($group as $grpItem)
                    {{ $grpItem->service->name }}{{ !$loop->last ? ', ' : '' }}
                @endforeach
                 - Rp {{ number_format($group->sum('total_price'), 0, ',', '.') }}</div>
            <div style="margin-top: 8px;">
                @php
                    $labelIndo = [
                        'pending' => 'MENUNGGU',
                        'processing' => 'PROSES',
                        'washing' => 'DICUCI',
                        'drying' => 'DIKERINGKAN',
                        'finishing' => 'FINISHING',
                        'ready' => 'SIAP DIAMBIL',
                        'completed' => 'DIAMBIL',
                        'cancelled' => 'DIBATALKAN'
                    ][$order->status] ?? strtoupper($order->status);
                @endphp
                <span style="font-size: 0.7rem; background: rgba(249,115,22,0.1); color: var(--primary); padding: 3px 8px; border-radius: 6px; font-weight: 800;">{{ $labelIndo }}</span>
                <span style="font-size: 0.7rem; margin-left: 5px; {{ $order->payment_status == 'paid' ? 'background: rgba(16, 185, 129, 0.15); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.3);' : 'background: rgba(239, 68, 68, 0.15); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.3);' }} padding: 3px 8px; border-radius: 6px; font-weight: 800;">
                    {{ $order->payment_status == 'paid' ? 'LUNAS' : 'BELUM BAYAR' }}
                </span>
            </div>
        </div>
        @if($order->status == 'pending')
        <div style="display: flex; gap: 8px; margin-top: 10px;">
            <form action="{{ route('admin.orders.status.update', $order) }}" method="POST" style="flex: 1;">
                @csrf
                @method('PATCH')
                <input type="hidden" name="status" value="processing">
                <button type="submit" style="width: 100%; background: #10b981; color: #fff; border: none; padding: 8px; border-radius: 10px; font-weight: 800; font-size: 0.8rem;">Terima</button>
            </form>
            <form action="{{ route('admin.orders.status.update', $order) }}" method="POST" style="flex: 1;">
                @csrf
                @method('PATCH')
                <input type="hidden" name="status" value="cancelled">
                <button type="submit" onclick="return confirm('Tolak pesanan?')" style="width: 100%; background: #ef4444; color: #fff; border: none; padding: 8px; border-radius: 10px; font-weight: 800; font-size: 0.8rem;">Tolak</button>
            </form>
        </div>
        @else
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
                    if ($order->status == 'processing') { $nextStatus = 'ready'; $nextLabel = 'Selesai Repaint/Reparasi → Siap'; }
                    elseif ($order->status == 'ready') { 
                        if ($order->is_delivery) { $nextStatus = 'dikirim'; $nextLabel = 'Kirim ke Pelanggan'; $btnColor = '#f59e0b'; }
                        else { $nextStatus = 'completed'; $nextLabel = 'Selesai & Diambil'; $btnColor = '#10b981'; }
                    }
                    elseif ($order->status == 'dikirim') { $nextStatus = 'completed'; $nextLabel = 'Selesai & Diterima'; $btnColor = '#10b981'; }
                }
            @endphp
            
            @if($nextStatus)
            <div style="margin-top: 10px;">
                <form action="{{ route('admin.orders.status.update', $order) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="{{ $nextStatus }}">
                    <button type="submit" style="width: 100%; background: {{ $btnColor }}; color: #fff; border: none; padding: 8px; border-radius: 10px; font-weight: 800; font-size: 0.8rem;">{{ $nextLabel }}</button>
                </form>
            </div>
            @endif
        @endif
    </div>
    @endforeach
</div>

@if($orders->isEmpty())
    <div style="text-align: center; padding: 50px; opacity: 0.3;">
        <p>Tidak ada pesanan yang sesuai dengan filter.</p>
    </div>
@endif

<!-- Modal Detail Pesanan -->
<div id="orderDetailModal" class="modal-backdrop" onclick="closeModal('orderDetailModal')">
    <div class="modal-box" onclick="event.stopPropagation()">
        <button class="modal-close" onclick="closeModal('orderDetailModal')">&times;</button>
        
        <div style="border-bottom: 1px solid rgba(255,255,255,0.08); padding-bottom: 15px; margin-bottom: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <span id="detail_queue_number" style="background: var(--primary); color: #000; padding: 4px 12px; border-radius: 8px; font-weight: 900; font-size: 1.1rem;">-</span>
                    <h3 id="detail_order_number" style="font-size: 1.3rem; font-weight: 800; color: #fff; margin: 0;">-</h3>
                </div>
                <span id="detail_reception_date" style="font-size: 0.8rem; opacity: 0.5;">-</span>
            </div>
        </div>

        <div class="modal-grid-2">
            <!-- Left: Shoe & Service -->
            <div>
                <h4 style="font-size: 0.85rem; text-transform: uppercase; color: var(--primary); font-weight: 800; margin-bottom: 10px; letter-spacing: 0.5px;">👟 Detail Sepatu</h4>
                <div style="background: rgba(255,255,255,0.02); padding: 12px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.05); margin-bottom: 15px;">
                    <p style="font-size: 0.8rem; color: var(--text-secondary); margin-bottom: 3px;">Nama Sepatu</p>
                    <p id="detail_shoe_name" style="font-weight: 700; font-size: 0.95rem; margin-bottom: 10px;">-</p>
                    
                    <div style="display: flex; gap: 15px;">
                        <div>
                            <p style="font-size: 0.75rem; color: var(--text-secondary); margin-bottom: 3px;">Ukuran</p>
                            <p id="detail_shoe_size" style="font-weight: 700; font-size: 0.9rem;">-</p>
                        </div>
                        <div>
                            <p style="font-size: 0.75rem; color: var(--text-secondary); margin-bottom: 3px;">Jumlah</p>
                            <p id="detail_shoe_quantity" style="font-weight: 700; font-size: 0.9rem;">-</p>
                        </div>
                    </div>

                    <div style="margin-top: 10px;">
                        <p style="font-size: 0.75rem; color: var(--text-secondary); margin-bottom: 3px;">Lokasi Penyimpanan</p>
                        <p id="detail_storage_location" style="font-weight: 700; font-size: 0.9rem; color: var(--warning);">-</p>
                    </div>
                </div>

                <h4 style="font-size: 0.85rem; text-transform: uppercase; color: var(--primary); font-weight: 800; margin-bottom: 10px; letter-spacing: 0.5px;">💼 Layanan & Biaya</h4>
                <div style="background: rgba(255,255,255,0.02); padding: 12px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.05);">
                    <p style="font-size: 0.8rem; color: var(--text-secondary); margin-bottom: 5px;">Daftar Layanan</p>
                    <div id="detail_services_container" style="margin-bottom: 10px; display: flex; flex-direction: column; gap: 8px;"></div>
                    
                    <div style="display: flex; gap: 15px; margin-bottom: 10px;">
                        <div>
                            <p style="font-size: 0.75rem; color: var(--text-secondary); margin-bottom: 3px;">Kecepatan</p>
                            <p id="detail_processing_speed" style="font-weight: 700; font-size: 0.9rem; text-transform: capitalize;">-</p>
                        </div>
                        <div>
                            <p style="font-size: 0.75rem; color: var(--text-secondary); margin-bottom: 3px;">Biaya Antar</p>
                            <p id="detail_delivery_fee" style="font-weight: 700; font-size: 0.9rem;">-</p>
                        </div>
                    </div>
                    
                    <div style="border-top: 1px dashed rgba(255,255,255,0.1); padding-top: 8px; display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-weight: 800; font-size: 0.9rem; color: #fff;">Total Bayar:</span>
                        <span id="detail_total_price" style="font-weight: 900; font-size: 1.1rem; color: var(--primary);">-</span>
                    </div>
                </div>
            </div>

            <!-- Right: Photo & Payment / Customer -->
            <div>
                <h4 style="font-size: 0.85rem; text-transform: uppercase; color: var(--primary); font-weight: 800; margin-bottom: 10px; letter-spacing: 0.5px;">👤 Pelanggan</h4>
                <div style="background: rgba(255,255,255,0.02); padding: 12px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.05); margin-bottom: 15px;">
                    <p id="detail_customer_name" style="font-weight: 700; font-size: 0.95rem; margin-bottom: 3px;">-</p>
                    <p id="detail_customer_phone" style="font-size: 0.8rem; opacity: 0.6; margin-bottom: 5px;">-</p>
                    <p id="detail_delivery_address" style="font-size: 0.75rem; opacity: 0.5; display: none; line-height: 1.3;"></p>
                </div>

                <h4 style="font-size: 0.85rem; text-transform: uppercase; color: var(--primary); font-weight: 800; margin-bottom: 10px; letter-spacing: 0.5px;">💳 Status Pembayaran</h4>
                <div style="background: rgba(255,255,255,0.02); padding: 12px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.05); margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <p id="detail_payment_method" style="font-weight: 800; font-size: 0.9rem; margin-bottom: 3px; text-transform: uppercase;">-</p>
                        <span id="detail_payment_status" style="font-size: 0.7rem; font-weight: 800; padding: 2px 8px; border-radius: 6px;">-</span>
                        <div id="detail_payment_proof_container" style="display: none; margin-top: 8px;">
                            <a id="detail_payment_proof_link" href="#" target="_blank" style="font-size: 0.75rem; color: #60a5fa; text-decoration: underline;">Lihat Bukti Transfer</a>
                        </div>
                    </div>
                    
                    <form id="detail_confirm_payment_form" method="POST" style="display: none;">
                        @csrf
                        <button type="submit" style="background: #10b981; color: #fff; border: none; padding: 6px 12px; border-radius: 8px; font-size: 0.75rem; font-weight: 800; cursor: pointer;">Konfirmasi Lunas</button>
                    </form>
                </div>

                <h4 style="font-size: 0.85rem; text-transform: uppercase; color: var(--primary); font-weight: 800; margin-bottom: 10px; letter-spacing: 0.5px;">📸 Foto Sepatu</h4>
                <div class="modal-grid-2" style="gap: 10px; margin-bottom: 0;">
                    <div>
                        <p style="font-size: 0.7rem; opacity: 0.5; margin-bottom: 3px; text-align: center;">Sebelum</p>
                        <img id="detail_photo_before" class="modal-photo" src="" alt="Sebelum">
                    </div>
                    <div>
                        <p style="font-size: 0.7rem; opacity: 0.5; margin-bottom: 3px; text-align: center;">Sesudah</p>
                        <img id="detail_photo_after" class="modal-photo" src="" alt="Sesudah">
                    </div>
                </div>
            </div>
        </div>

        <!-- Ubah Status Section -->
        <div style="background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05); padding: 15px; border-radius: 16px; margin-bottom: 20px;">
            <label style="display: block; font-size: 0.8rem; font-weight: 800; color: var(--text-secondary); margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px;">⚙️ Ubah Status Pesanan</label>
            <form id="detail_status_update_form" method="POST" style="display: flex; gap: 10px;">
                @csrf
                @method('PATCH')
                <select name="status" id="detail_status_select" class="filter-input" style="flex: 1; margin: 0; background: #1e1e24;">
                    <option value="pending">MENUNGGU (PENDING)</option>
                    <option value="processing">PROSES (PROCESSING)</option>
                    <option value="washing">DICUCI (WASHING)</option>
                    <option value="drying">DIKERINGKAN (DRYING)</option>
                    <option value="finishing">FINISHING</option>
                    <option value="ready">SIAP DIAMBIL (READY)</option>
                    <option value="completed">DIAMBIL (COMPLETED)</option>
                </select>
                <button type="submit" style="background: var(--primary); color: #000; border: none; padding: 0 20px; border-radius: 12px; font-weight: 800; cursor: pointer;">Perbarui</button>
            </form>
        </div>

        <!-- Action Buttons Footer -->
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; border-top: 1px solid rgba(255,255,255,0.08); padding-top: 20px;">
            <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                <button id="detail_edit_btn" class="btn" style="background: #3b82f6; color: #fff; border: none; padding: 10px 18px; border-radius: 12px; font-weight: 800; cursor: pointer;">Edit Pesanan</button>
                <a id="detail_print_receipt_btn" href="#" target="_blank" class="btn" style="background: rgba(255,255,255,0.05); color: #fff; text-decoration: none; border: 1px solid rgba(255,255,255,0.1); padding: 10px 18px; border-radius: 12px; font-weight: 800; display: inline-flex; align-items: center; justify-content: center;">Cetak Nota</a>
                <a id="detail_view_detail_btn" href="#" target="_blank" class="btn" style="background: rgba(255,255,255,0.05); color: #fff; text-decoration: none; border: 1px solid rgba(255,255,255,0.1); padding: 10px 18px; border-radius: 12px; font-weight: 800; display: inline-flex; align-items: center; justify-content: center;">Lihat Detail</a>
            </div>
            
            <form id="detail_delete_form" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pesanan ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" style="background: #ef4444; color: #fff; border: none; padding: 10px 18px; border-radius: 12px; font-weight: 800; cursor: pointer;">Hapus Pesanan</button>
            </form>
        </div>
    </div>
</div>

<!-- Modal Tambah Pesanan -->
<div id="createOrderModal" class="modal-backdrop" onclick="closeModal('createOrderModal')">
    <div class="modal-box" onclick="event.stopPropagation()">
        <button class="modal-close" onclick="closeModal('createOrderModal')">&times;</button>
        <h3 style="font-size: 1.5rem; font-weight: 900; margin-bottom: 20px; border-bottom: 1px solid rgba(255,255,255,0.08); padding-bottom: 15px;">Tambah <span style="color: var(--primary);">Pesanan Baru</span></h3>
        
        <form action="{{ route('admin.orders.store') }}" method="POST" enctype="multipart/form-data">
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
            <div style="margin-bottom: 1.2rem;">
                <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--text-secondary); margin-bottom: 5px; text-transform: uppercase;">Uang Diterima (Khusus Tunai)</label>
                <input type="number" name="cash_amount" placeholder="Contoh: 100000" class="filter-input" style="width: 100%; background: #1e1e24;">
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
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--text-secondary); margin-bottom: 5px; text-transform: uppercase;">Nama Sepatu</label>
                    <input type="text" name="shoe_name" id="edit_shoe_name" required class="filter-input" style="width: 100%; background: #1e1e24;">
                </div>
            </div>
            <div class="modal-grid-2">
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
            <div style="margin-bottom: 1.2rem;">
                <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--text-secondary); margin-bottom: 5px; text-transform: uppercase;">Uang Diterima (Khusus Tunai)</label>
                <input type="number" name="cash_amount" id="edit_cash_amount" placeholder="Contoh: 100000" class="filter-input" style="width: 100%; background: #1e1e24;">
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

<script>
    // Modal operations
    function openCreateModal() {
        document.getElementById('createOrderModal').classList.add('active');
    }

    function openEditModal(order, user, service) {
        document.getElementById('editOrderForm').action = `/admin/orders/${order.id}`;
        
        document.getElementById('edit_queue_number').value = order.queue_number;
        document.getElementById('edit_service_id').value = order.service_id;
        document.getElementById('edit_processing_speed').value = order.processing_speed;
        document.getElementById('edit_shoe_name').value = order.shoe_name || '';
        document.getElementById('edit_shoe_size').value = order.shoe_size || '';
        document.getElementById('edit_shoe_quantity').value = order.shoe_quantity || 1;
        document.getElementById('edit_payment_method').value = order.payment_method;
        document.getElementById('edit_payment_status').value = order.payment_status;
        document.getElementById('edit_status').value = order.status;
        document.getElementById('edit_cash_amount').value = order.cash_amount || '';
        
        document.getElementById('editOrderModal').classList.add('active');
    }

    function closeModal(modalId) {
        document.getElementById(modalId).classList.remove('active');
    }

    function openDetailModal(order, user, service, services, groupTotal) {
        document.getElementById('detail_queue_number').innerText = order.queue_number;
        document.getElementById('detail_order_number').innerText = '#' + (order.group_id || order.order_number);
        
        const date = new Date(order.created_at);
        document.getElementById('detail_reception_date').innerText = date.toLocaleDateString('id-ID') + ' ' + date.toLocaleTimeString('id-ID', {hour: '2-digit', minute:'2-digit'});
        
        document.getElementById('detail_shoe_name').innerText = order.shoe_name || 'Tidak ada';
        document.getElementById('detail_shoe_size').innerText = order.shoe_size || '-';
        document.getElementById('detail_shoe_quantity').innerText = order.shoe_quantity || '1';
        document.getElementById('detail_storage_location').innerText = order.storage_location || 'Belum diatur';
        
        const servicesContainer = document.getElementById('detail_services_container');
        servicesContainer.innerHTML = '';
        if (services && services.length > 0) {
            services.forEach(s => {
                const itemDiv = document.createElement('div');
                itemDiv.style.display = 'flex';
                itemDiv.style.justifyContent = 'space-between';
                itemDiv.style.alignItems = 'center';
                itemDiv.style.marginBottom = '4px';
                itemDiv.innerHTML = `
                    <span style="font-weight: 700; font-size: 0.85rem;">${s.name}</span>
                    <span style="font-weight: 700; font-size: 0.85rem; opacity: 0.8;">Rp ${(parseInt(s.price) || 0).toLocaleString('id-ID')}</span>
                `;
                servicesContainer.appendChild(itemDiv);
            });
        } else {
            const itemDiv = document.createElement('div');
            itemDiv.style.display = 'flex';
            itemDiv.style.justifyContent = 'space-between';
            itemDiv.style.alignItems = 'center';
            itemDiv.innerHTML = `
                <span style="font-weight: 700; font-size: 0.85rem;">${service.name}</span>
                <span style="font-weight: 700; font-size: 0.85rem; opacity: 0.8;">Rp ${(parseInt(order.total_price) - (parseInt(order.delivery_fee) || 0)).toLocaleString('id-ID')}</span>
            `;
            servicesContainer.appendChild(itemDiv);
        }
        
        document.getElementById('detail_processing_speed').innerText = order.processing_speed;
        
        const deliveryFee = parseInt(order.delivery_fee) || 0;
        document.getElementById('detail_delivery_fee').innerText = deliveryFee > 0 ? 'Rp ' + deliveryFee.toLocaleString('id-ID') : 'Rp 0';
        
        const totalPrice = groupTotal ? parseInt(groupTotal) : (parseInt(order.total_price) || 0);
        document.getElementById('detail_total_price').innerText = 'Rp ' + totalPrice.toLocaleString('id-ID');
        
        document.getElementById('detail_customer_name').innerText = user.name;
        document.getElementById('detail_customer_phone').innerText = user.phone || 'Tidak ada no WhatsApp';
        
        const deliveryAddressEl = document.getElementById('detail_delivery_address');
        if (order.is_delivery == 1 && order.delivery_address) {
            deliveryAddressEl.innerText = '📍 Alamat Kirim: ' + order.delivery_address;
            deliveryAddressEl.style.display = 'block';
        } else {
            deliveryAddressEl.style.display = 'none';
        }
        
        document.getElementById('detail_payment_method').innerText = order.payment_method;
        
        const paymentStatusEl = document.getElementById('detail_payment_status');
        paymentStatusEl.innerText = order.payment_status === 'paid' ? 'LUNAS' : 'BELUM BAYAR';
        if (order.payment_status === 'paid') {
            paymentStatusEl.style.background = 'rgba(16, 185, 129, 0.15)';
            paymentStatusEl.style.color = '#10b981';
            paymentStatusEl.style.border = '1px solid rgba(16, 185, 129, 0.3)';
        } else {
            paymentStatusEl.style.background = 'rgba(239, 68, 68, 0.15)';
            paymentStatusEl.style.color = '#ef4444';
            paymentStatusEl.style.border = '1px solid rgba(239, 68, 68, 0.3)';
        }
        
        const proofContainer = document.getElementById('detail_payment_proof_container');
        const proofLink = document.getElementById('detail_payment_proof_link');
        if (order.payment_proof) {
            proofLink.href = '/storage/' + order.payment_proof;
            proofContainer.style.display = 'block';
        } else {
            proofContainer.style.display = 'none';
        }
        
        const confirmPaymentForm = document.getElementById('detail_confirm_payment_form');
        if (order.payment_status === 'unpaid' && (order.payment_method === 'cash' || order.payment_method === 'qris')) {
            confirmPaymentForm.action = `/employee/orders/${order.id}/confirm-payment`;
            confirmPaymentForm.style.display = 'block';
        } else {
            confirmPaymentForm.style.display = 'none';
        }
        
        const svgPlaceholder = `data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 100 100" style="background:%23222;"><text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" fill="%23555" font-family="sans-serif" font-size="10">No Photo</text></svg>`;
        const storagePath = '/storage/';
        document.getElementById('detail_photo_before').src = order.photo_before ? (storagePath + order.photo_before) : svgPlaceholder;
        document.getElementById('detail_photo_after').src = order.photo_after ? (storagePath + order.photo_after) : svgPlaceholder;
        
        document.getElementById('detail_status_update_form').action = `/admin/orders/${order.id}/status`;
        document.getElementById('detail_status_select').value = order.status;
        
        const printBtn = document.getElementById('detail_print_receipt_btn');
        printBtn.href = `/customer/orders/${order.id}/receipt`;
        printBtn.style.opacity = '1';
        printBtn.style.pointerEvents = 'auto';
        
        document.getElementById('detail_view_detail_btn').href = `/customer/orders/${order.id}`;
        
        document.getElementById('detail_edit_btn').onclick = function() {
            closeModal('orderDetailModal');
            openEditModal(order, user, service);
        };
        
        document.getElementById('detail_delete_form').action = `/admin/orders/${order.id}`;
        
        document.getElementById('orderDetailModal').classList.add('active');
    }

    // Row clicks binding
    document.querySelectorAll('.clickable-row').forEach(row => {
        row.addEventListener('click', function(e) {
            if (e.target.closest('button') || e.target.closest('form') || e.target.closest('a')) {
                return;
            }
            const order = JSON.parse(this.dataset.order);
            const user = JSON.parse(this.dataset.user);
            const service = JSON.parse(this.dataset.service);
            const services = this.dataset.services ? JSON.parse(this.dataset.services) : null;
            const groupTotal = this.dataset.groupTotal ? parseFloat(this.dataset.groupTotal) : null;
            openDetailModal(order, user, service, services, groupTotal);
        });
    });

    // Prevent propagation
    document.querySelectorAll('.clickable-row form, .clickable-row button, .clickable-row a').forEach(el => {
        el.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    });
</script>
@endsection
