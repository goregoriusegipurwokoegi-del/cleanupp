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
    .controls-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 15px;
        margin-bottom: 25px;
        flex-wrap: wrap;
    }
    .btn-primary-custom {
        background: var(--primary);
        color: #fff !important;
        border: none;
        padding: 11px 22px;
        border-radius: 12px;
        font-weight: 800;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s ease;
        font-size: 0.9rem;
        box-shadow: 0 4px 12px rgba(13, 110, 253, 0.15);
    }
    .btn-primary-custom:hover {
        opacity: 0.95;
        transform: translateY(-1px);
    }
    .btn-primary-custom:active {
        transform: translateY(0);
    }

    .filter-bar {
        display: flex;
        gap: 12px;
        margin: 0;
        flex-wrap: wrap;
    }
    .filter-input {
        background: var(--surface);
        border: 1.5px solid var(--border-color);
        color: var(--text);
        padding: 10px 15px;
        border-radius: 12px;
        font-size: 0.9rem;
        outline: none;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
    }
    .filter-input:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.15);
    }
    
    .order-table-container {
        background: var(--surface);
        border: 1.5px solid var(--border-color);
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
    }
    
    .order-table-desktop table td {
        padding: 16px 15px;
        vertical-align: middle;
    }
    .order-table-desktop table th {
        padding: 14px 15px;
        vertical-align: middle;
    }
    .clickable-row {
        transition: background-color 0.2s ease;
    }
    .clickable-row:hover {
        background-color: var(--surface-variant) !important;
    }
    
    .order-card-mobile {
        display: none;
    }
 
    @media (max-width: 1024px) {
        .order-table-desktop { display: none; }
        .order-card-mobile { 
            display: block; 
            background: var(--surface);
            border: 1.5px solid var(--border-color);
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
        background: rgba(0, 0, 0, 0.6);
        backdrop-filter: blur(8px);
        z-index: 1100;
        overflow-y: auto;
        padding: 1.5rem;
    }
    .modal-backdrop.active {
        display: block;
    }
    .modal-box {
        background: var(--surface);
        border: 1.5px solid var(--border-color);
        border-radius: 24px;
        width: 100%;
        max-width: 650px;
        margin: 2rem auto;
        padding: 2rem;
        position: relative;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        color: var(--text);
    }
    .modal-close {
        position: absolute;
        top: 1.5rem;
        right: 1.5rem;
        background: var(--surface-variant);
        border: 1.5px solid var(--border-color);
        color: var(--text);
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
        background: var(--border-color);
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
        background: var(--surface-variant);
        border: 1px solid var(--border-color);
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
 
    .view-detail-btn {
        background: transparent !important;
        color: var(--text) !important;
        border: 1.5px solid var(--border-color) !important;
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 0.75rem;
        font-weight: 800;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .view-detail-btn:hover {
        background: var(--border-color) !important;
        border-color: var(--text-secondary) !important;
    }

    /* Modal Theme Adapters & Overrides */
    .modal-box select,
    .modal-box input:not([type="radio"]):not([type="checkbox"]) {
        background-color: var(--surface-variant) !important;
        color: var(--text) !important;
        border: 1.5px solid var(--border-color) !important;
    }
    .modal-box select:focus,
    .modal-box input:not([type="radio"]):not([type="checkbox"]):focus {
        border-color: var(--primary) !important;
        box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.15) !important;
    }
    .modal-box .service-card-label {
        background: var(--surface-variant) !important;
        border: 1.5px solid var(--border-color) !important;
    }
    .modal-box .status-radio-label {
        background: var(--surface-variant) !important;
        border: 1.5px solid var(--border-color) !important;
        color: var(--text) !important;
    }
    .service-card-label.selected {
        background: rgba(249, 115, 22, 0.1) !important;
        border-color: var(--primary) !important;
    }
    .status-radio-label:has(.status-radio:checked) {
        background: rgba(249, 115, 22, 0.1) !important;
        border-color: var(--primary) !important;
    }
    @media (max-width: 576px) {
        .modal-backdrop {
            padding: 0 !important;
        }
        #createOrderForm {
            padding: 14px 14px 20px !important;
        }
        #createOrderModal .modal-box {
            width: 100% !important;
            max-width: 100% !important;
            margin: 0 !important;
            border-radius: 0 !important;
            border: none !important;
            min-height: 100vh !important;
        }
        #createOrderModal div[style*="border-top-left-radius"] {
            border-top-left-radius: 0 !important;
            border-top-right-radius: 0 !important;
        }
        #createOrderForm > div > div {
            padding: 14px !important;
            border-radius: 12px !important;
        }
        #createOrderModal div[style*="padding: 24px 28px"] {
            padding: 16px 16px !important;
        }
        #createOrderModal h3 {
            font-size: 1.15rem !important;
        }
        #createOrderModal p[style*="color: var(--text-secondary)"] {
            font-size: 0.72rem !important;
        }
        .modal-invoice-info-box {
            flex-direction: column !important;
            align-items: flex-start !important;
            gap: 6px !important;
        }
        .modal-segmented-toggle {
            flex-direction: column !important;
            gap: 6px !important;
            background: transparent !important;
            border: none !important;
            padding: 0 !important;
        }
        .modal-segmented-toggle button {
            width: 100% !important;
            flex: none !important;
            border: 1.5px solid var(--border-color) !important;
            background: var(--surface) !important;
        }
    }
</style>

<div class="controls-row">
    @if(!request('queue'))
    <button onclick="openCreateModal()" class="btn-primary-custom">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
        Tambah Pesanan
    </button>
    @endif
    
    <form action="{{ route('admin.orders.index') }}" method="GET" class="filter-bar" style="flex-grow: 1; justify-content: flex-end;">
        @if(request('delivery'))
            <input type="hidden" name="delivery" value="1">
        @endif
        @if(request('queue'))
            <input type="hidden" name="queue" value="1">
        @endif
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, No. Order, No. Antrian..." class="filter-input" style="flex-grow: 1; max-width: 380px; min-width: 200px;">
        <select name="status" class="filter-input" style="min-width: 160px;" onchange="this.form.submit()">
            <option value="">Semua Status</option>
            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu</option>
            <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Antri</option>
            <option value="washing" {{ request('status') == 'washing' ? 'selected' : '' }}>Dicuci</option>
            <option value="finishing" {{ request('status') == 'finishing' ? 'selected' : '' }}>Finishing</option>
            <option value="ready" {{ request('status') == 'ready' ? 'selected' : '' }}>Selesai</option>
            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Diambil</option>
        </select>
    </form>
</div>

@if($errors->any())
    <div style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2); color: #ef4444; padding: 15px; border-radius: 12px; margin-bottom: 20px; font-weight: 600;">
        <div style="font-weight: 800; margin-bottom: 5px;">⚠️ Gagal Menyimpan Pesanan:</div>
        <ul style="margin: 0; padding-left: 20px; font-size: 0.85rem;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if(session('success'))
    <div style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.2); color: #10b981; padding: 12px; border-radius: 12px; margin-bottom: 20px; font-weight: 600;">
        {{ session('success') }}
    </div>
@endif

<!-- Desktop View -->
<div class="order-table-desktop order-table-container">
    <table style="width: 100%; border-collapse: collapse; text-align: left;">
        <thead>
            <tr style="background: var(--surface-variant); border-bottom: 1.5px solid var(--border-color);">
                <th style="padding: 15px; font-size: 0.8rem; font-weight: 700; color: var(--text-secondary);">ANTRIAN</th>
                <th style="padding: 15px; font-size: 0.8rem; font-weight: 700; color: var(--text-secondary);">PELANGGAN</th>
                <th style="padding: 15px; font-size: 0.8rem; font-weight: 700; color: var(--text-secondary);">SEPATU</th>
                <th style="padding: 15px; font-size: 0.8rem; font-weight: 700; color: var(--text-secondary);">LAYANAN</th>
                <th style="padding: 15px; font-size: 0.8rem; font-weight: 700; color: var(--text-secondary);">STATUS</th>
                <th style="padding: 15px; font-size: 0.8rem; font-weight: 700; color: var(--text-secondary);">AKSI</th>
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
                data-group-total="{{ $group->sum('total_price') }}">
                <td style="padding: 1rem;">
                    @if($loop->first && in_array($order->status, ['pending', 'processing', 'washing', 'finishing']))
                        <div style="margin-bottom: 5px;">
                            <span style="background: #ef4444; color: #fff; font-size: 0.65rem; padding: 4px 8px; border-radius: 6px; display: inline-flex; align-items: center; gap: 4px; font-weight: 800; text-transform: uppercase;">
                                🔥 Kerjakan Sekarang
                            </span>
                        </div>
                    @endif
                    <div style="background: var(--primary); color: #fff; padding: 2px 8px; border-radius: 6px; font-weight: 800; width: fit-content; margin-bottom: 3px; white-space: nowrap;">
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
                        <img src="{{ $order->photo_before ? asset('storage/' . $order->photo_before) : 'data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'%2394a3b8\' stroke-width=\'1.5\' stroke-linecap=\'round\' stroke-linejoin=\'round\'><rect x=\'3\' y=\'3\' width=\'18\' height=\'18\' rx=\'2\' ry=\'2\'></rect><circle cx=\'8.5\' cy=\'8.5\' r=\'1.5\'></circle><polyline points=\'21 15 16 10 5 21\'></polyline></svg>' }}" 
                             onerror="this.onerror=null; this.src='data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'%2394a3b8\' stroke-width=\'1.5\' stroke-linecap=\'round\' stroke-linejoin=\'round\'><rect x=\'3\' y=\'3\' width=\'18\' height=\'18\' rx=\'2\' ry=\'2\'></rect><circle cx=\'8.5\' cy=\'8.5\' r=\'1.5\'></circle><polyline points=\'21 15 16 10 5 21\'></polyline></svg>'; this.style.padding='8px';" 
                             style="width: 45px; height: 45px; border-radius: 8px; object-fit: cover; background: var(--surface-variant); border: 1px solid var(--border-color); {{ !$order->photo_before ? 'padding: 8px;' : '' }}">
                        <div>
                            <div style="font-weight: 700; font-size: 0.85rem;">{{ trim($order->shoe_name) ?: 'Sepatu (Tanpa Nama)' }}</div>
                            <div style="font-size: 0.75rem; color: var(--primary); font-weight: 700;">Size: {{ trim($order->shoe_size) ?: '-' }}</div>
                        </div>
                    </div>
                </td>
                <td style="padding: 15px;">
                    @foreach($group as $grpItem)
                        <div style="font-weight: 700; font-size: 0.85rem; margin-bottom: 2px;">{{ $grpItem->service->name }}</div>
                    @endforeach
                    <div style="font-size: 0.75rem; font-weight: bold; margin-top: 5px; color: var(--primary);">Rp {{ number_format($group->sum('total_price'), 0, ',', '.') }}</div>
                </td>
                <td style="padding: 15px;">
                    @php
                        $statusLabels = [
                            'pending' => 'DITERIMA',
                            'processing' => 'DALAM ANTRIAN',
                            'washing' => ($order->service->category == 'cleaning' ? 'SEDANG DICUCI' : 'SEDANG DIKERJAKAN'),
                            'finishing' => ($order->service->category == 'cleaning' ? 'DIJEMUR' : 'PROSES FINISHING'),
                            'ready' => 'SIAP DIAMBIL/KIRIM',
                            'dikirim' => 'SEDANG DIKIRIM',
                            'uncollected' => 'BELUM DIAMBIL',
                            'completed' => 'SELESAI',
                            'cancelled' => 'DIBATALKAN'
                        ];
                        $colors = [
                            'pending' => '#f59e0b',
                            'processing' => '#94a3b8',
                            'washing' => '#3b82f6',
                            'finishing' => '#a855f7',
                            'ready' => '#10b981',
                            'dikirim' => '#eab308',
                            'uncollected' => '#64748b',
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
                        @if($order->payment_proof)
                            <a href="{{ asset('storage/' . $order->payment_proof) }}" target="_blank" style="display: inline-block; margin-top: 4px; background: rgba(59, 130, 246, 0.15); color: #3b82f6; padding: 2px 6px; border-radius: 6px; font-size: 0.65rem; font-weight: 800; border: 1px solid rgba(59, 130, 246, 0.3); white-space: nowrap; text-decoration: none;">📝 Bukti Transfer</a>
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
                                    if ($order->status == 'processing') { $nextStatus = 'washing'; $nextLabel = 'Mulai Cuci'; }
                                    elseif ($order->status == 'washing') { $nextStatus = 'finishing'; $nextLabel = 'Ke Jemur'; }
                                    elseif ($order->status == 'finishing') { $nextStatus = 'ready'; $nextLabel = 'Ke Siap'; }
                                    elseif ($order->status == 'ready') { 
                                        if ($order->is_delivery) { $nextStatus = 'dikirim'; $nextLabel = 'Ke Kirim'; $btnColor = '#f59e0b'; }
                                        else { $nextStatus = 'completed'; $nextLabel = 'Ke Selesai'; $btnColor = '#10b981'; }
                                    }
                                    elseif ($order->status == 'dikirim') { $nextStatus = 'completed'; $nextLabel = 'Ke Selesai'; $btnColor = '#10b981'; }
                                } else {
                                    if ($order->status == 'processing') { $nextStatus = 'washing'; $nextLabel = 'Mulai Kerja'; }
                                    elseif ($order->status == 'washing') { $nextStatus = 'finishing'; $nextLabel = 'Ke Finishing'; }
                                    elseif ($order->status == 'finishing') { $nextStatus = 'ready'; $nextLabel = 'Ke Siap'; }
                                    elseif ($order->status == 'ready') { 
                                        if ($order->is_delivery) { $nextStatus = 'dikirim'; $nextLabel = 'Ke Kirim'; $btnColor = '#f59e0b'; }
                                        else { $nextStatus = 'completed'; $nextLabel = 'Ke Selesai'; $btnColor = '#10b981'; }
                                    }
                                    elseif ($order->status == 'dikirim') { $nextStatus = 'completed'; $nextLabel = 'Ke Selesai'; $btnColor = '#10b981'; }
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
                        <button class="view-detail-btn">Detail</button>
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
            <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                @if($loop->first && in_array($order->status, ['pending', 'processing', 'washing', 'finishing']))
                    <span style="background: #ef4444; color: #fff; font-size: 0.65rem; padding: 4px 8px; border-radius: 6px; display: inline-flex; align-items: center; gap: 4px; font-weight: 800; text-transform: uppercase;">
                        🔥 Kerjakan Sekarang
                    </span>
                @endif
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
                        'pending' => 'DITERIMA',
                        'processing' => 'ANTRI',
                        'washing' => ($order->service->category == 'cleaning' ? 'SEDANG DICUCI' : 'SEDANG DIKERJAKAN'),
                        'finishing' => ($order->service->category == 'cleaning' ? 'DIJEMUR' : 'PROSES FINISHING'),
                        'ready' => 'SIAP DIAMBIL/KIRIM',
                        'dikirim' => 'SEDANG DIKIRIM',
                        'uncollected' => 'BELUM DIAMBIL',
                        'completed' => 'SELESAI',
                        'cancelled' => 'DIBATALKAN'
                    ][$order->status] ?? strtoupper($order->status);
                @endphp
                <span style="font-size: 0.7rem; background: rgba(249,115,22,0.1); color: var(--primary); padding: 3px 8px; border-radius: 6px; font-weight: 800;">{{ $labelIndo }}</span>
                <span style="font-size: 0.7rem; margin-left: 5px; {{ $order->payment_status == 'paid' ? 'background: rgba(16, 185, 129, 0.15); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.3);' : 'background: rgba(239, 68, 68, 0.15); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.3);' }} padding: 3px 8px; border-radius: 6px; font-weight: 800;">
                    {{ $order->payment_status == 'paid' ? 'LUNAS' : 'BELUM BAYAR' }}
                </span>
                @if($order->payment_proof)
                    <a href="{{ asset('storage/' . $order->payment_proof) }}" target="_blank" style="font-size: 0.7rem; margin-left: 5px; background: rgba(59, 130, 246, 0.15); color: #3b82f6; padding: 3px 8px; border-radius: 6px; font-weight: 800; border: 1px solid rgba(59, 130, 246, 0.3); text-decoration: none; display: inline-block; margin-top: 5px;">📝 Bukti Transfer</a>
                @endif
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
                    if ($order->status == 'processing') { $nextStatus = 'washing'; $nextLabel = 'Mulai Cuci → Dicuci'; }
                    elseif ($order->status == 'washing') { $nextStatus = 'finishing'; $nextLabel = 'Selesai Cuci → Jemur'; }
                    elseif ($order->status == 'finishing') { $nextStatus = 'ready'; $nextLabel = 'Selesai Jemur → Siap'; }
                    elseif ($order->status == 'ready') { 
                        if ($order->is_delivery) { $nextStatus = 'dikirim'; $nextLabel = 'Kirim ke Pelanggan'; $btnColor = '#f59e0b'; }
                        else { $nextStatus = 'completed'; $nextLabel = 'Selesai & Diambil'; $btnColor = '#10b981'; }
                    }
                    elseif ($order->status == 'dikirim') { $nextStatus = 'completed'; $nextLabel = 'Selesai & Diterima'; $btnColor = '#10b981'; }
                } else {
                    if ($order->status == 'processing') { $nextStatus = 'washing'; $nextLabel = 'Mulai Kerja → Dikerjakan'; }
                    elseif ($order->status == 'washing') { $nextStatus = 'finishing'; $nextLabel = 'Selesai Kerja → Finishing'; }
                    elseif ($order->status == 'finishing') { $nextStatus = 'ready'; $nextLabel = 'Finishing → Siap'; }
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
        
        <div style="border-bottom: 1.5px solid var(--border-color); padding-bottom: 15px; margin-bottom: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <span id="detail_queue_number" style="background: var(--primary); color: #fff; padding: 4px 12px; border-radius: 8px; font-weight: 900; font-size: 1.1rem;">-</span>
                    <h3 id="detail_order_number" style="font-size: 1.3rem; font-weight: 800; color: var(--text); margin: 0;">-</h3>
                </div>
                <span id="detail_reception_date" style="font-size: 0.8rem; opacity: 0.5;">-</span>
            </div>
        </div>

        <div class="modal-grid-2">
            <!-- Left: Shoe & Service -->
            <div>
                <h4 style="font-size: 0.85rem; text-transform: uppercase; color: var(--primary); font-weight: 800; margin-bottom: 10px; letter-spacing: 0.5px;">👟 Detail Sepatu</h4>
                <div style="background: var(--surface-variant); padding: 12px; border-radius: 12px; border: 1.5px solid var(--border-color); margin-bottom: 15px;">
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
                <div style="background: var(--surface-variant); padding: 12px; border-radius: 12px; border: 1.5px solid var(--border-color);">
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
                    
                    <div style="border-top: 1px dashed var(--border-color); padding-top: 8px; display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-weight: 800; font-size: 0.9rem; color: var(--text);">Total Bayar:</span>
                        <span id="detail_total_price" style="font-weight: 900; font-size: 1.1rem; color: var(--primary);">-</span>
                    </div>
                </div>
            </div>

            <!-- Right: Photo & Payment / Customer -->
            <div>
                <h4 style="font-size: 0.85rem; text-transform: uppercase; color: var(--primary); font-weight: 800; margin-bottom: 10px; letter-spacing: 0.5px;">👤 Pelanggan</h4>
                <div style="background: var(--surface-variant); padding: 12px; border-radius: 12px; border: 1.5px solid var(--border-color); margin-bottom: 15px;">
                    <p id="detail_customer_name" style="font-weight: 700; font-size: 0.95rem; margin-bottom: 3px;">-</p>
                    <p id="detail_customer_phone" style="font-size: 0.8rem; opacity: 0.6; margin-bottom: 5px;">-</p>
                    <p id="detail_delivery_address" style="font-size: 0.75rem; opacity: 0.5; display: none; margin-bottom: 5px; line-height: 1.3;"></p>
                    <a id="detail_delivery_location_btn" href="#" target="_blank" style="display: none; font-size: 0.75rem; color: #fff; background: var(--primary); padding: 4px 8px; border-radius: 6px; text-decoration: none; width: fit-content; margin-bottom: 5px;">📍 Lihat Lokasi</a>
                </div>

                <h4 style="font-size: 0.85rem; text-transform: uppercase; color: var(--primary); font-weight: 800; margin-bottom: 10px; letter-spacing: 0.5px;">💳 Status Pembayaran</h4>
                <div style="background: var(--surface-variant); padding: 12px; border-radius: 12px; border: 1.5px solid var(--border-color); margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center;">
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
                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px; margin-bottom: 0;">
                    <div>
                        <p style="font-size: 0.7rem; opacity: 0.5; margin-bottom: 3px; text-align: center;">Samping</p>
                        <img id="detail_photo_before" class="modal-photo" src="" alt="Sebelum" onerror="this.onerror=null; this.src='data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'%2394a3b8\' stroke-width=\'1.5\' stroke-linecap=\'round\' stroke-linejoin=\'round\'><rect x=\'3\' y=\'3\' width=\'18\' height=\'18\' rx=\'2\' ry=\'2\'></rect><circle cx=\'8.5\' cy=\'8.5\' r=\'1.5\'></circle><polyline points=\'21 15 16 10 5 21\'></polyline></svg>'; this.style.padding='20px';" style="cursor: pointer;" onclick="if(this.src && !this.src.includes('data:image')) window.open(this.src, '_blank')">
                    </div>
                    <div>
                        <p style="font-size: 0.7rem; opacity: 0.5; margin-bottom: 3px; text-align: center;">Bawah</p>
                        <img id="detail_photo_before_2" class="modal-photo" src="" alt="Sebelum 2" onerror="this.onerror=null; this.src='data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'%2394a3b8\' stroke-width=\'1.5\' stroke-linecap=\'round\' stroke-linejoin=\'round\'><rect x=\'3\' y=\'3\' width=\'18\' height=\'18\' rx=\'2\' ry=\'2\'></rect><circle cx=\'8.5\' cy=\'8.5\' r=\'1.5\'></circle><polyline points=\'21 15 16 10 5 21\'></polyline></svg>'; this.style.padding='20px';" style="cursor: pointer;" onclick="if(this.src && !this.src.includes('data:image')) window.open(this.src, '_blank')">
                    </div>
                    <div>
                        <p style="font-size: 0.7rem; opacity: 0.5; margin-bottom: 3px; text-align: center;">Sesudah</p>
                        <img id="detail_photo_after" class="modal-photo" src="" alt="Sesudah" onerror="this.onerror=null; this.src='data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'%2394a3b8\' stroke-width=\'1.5\' stroke-linecap=\'round\' stroke-linejoin=\'round\'><rect x=\'3\' y=\'3\' width=\'18\' height=\'18\' rx=\'2\' ry=\'2\'></rect><circle cx=\'8.5\' cy=\'8.5\' r=\'1.5\'></circle><polyline points=\'21 15 16 10 5 21\'></polyline></svg>'; this.style.padding='20px';" style="cursor: pointer;" onclick="if(this.src && !this.src.includes('data:image')) window.open(this.src, '_blank')">
                    </div>
                </div>
            </div>
        </div>

        <!-- Ubah Status Section -->
        <div style="background: var(--surface-variant); border: 1.5px solid var(--border-color); padding: 15px; border-radius: 16px; margin-bottom: 20px;">
            <label style="display: block; font-size: 0.8rem; font-weight: 800; color: var(--text-secondary); margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px;">⚙️ Ubah Status Pesanan</label>
            <form id="detail_status_update_form" method="POST" style="display: flex; gap: 10px;">
                @csrf
                @method('PATCH')
                <select name="status" id="detail_status_select" class="filter-input" style="flex: 1; margin: 0;">
                    <option value="pending">MENUNGGU (PENDING)</option>
                    <option value="processing">ANTRI (QUEUE)</option>
                    <option value="washing">DICUCI/DIKERJAKAN (WASHING)</option>
                    <option value="finishing">FINISHING</option>
                    <option value="ready">SIAP DIAMBIL (READY)</option>
                    <option value="completed">DIAMBIL (COMPLETED)</option>
                    <option value="cancelled">DIBATALKAN (CANCELLED)</option>
                </select>
                <button type="submit" class="btn-primary-custom" style="padding: 0 20px;">Perbarui</button>
            </form>
        </div>

        <!-- Action Buttons Footer -->
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; border-top: 1.5px solid var(--border-color); padding-top: 20px;">
            <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                <button id="detail_edit_btn" class="btn" style="background: #3b82f6; color: #fff; border: none; padding: 10px 18px; border-radius: 12px; font-weight: 800; cursor: pointer;">Edit Pesanan</button>
                <a id="detail_print_receipt_btn" href="#" target="_blank" class="btn" style="background: var(--surface-variant); color: var(--text); text-decoration: none; border: 1.5px solid var(--border-color); padding: 10px 18px; border-radius: 12px; font-weight: 800; display: inline-flex; align-items: center; justify-content: center;">Cetak Nota</a>
                <a id="detail_view_detail_btn" href="#" target="_blank" class="btn" style="background: var(--surface-variant); color: var(--text); text-decoration: none; border: 1.5px solid var(--border-color); padding: 10px 18px; border-radius: 12px; font-weight: 800; display: inline-flex; align-items: center; justify-content: center;">Lihat Detail</a>
            </div>
            
            <form id="detail_delete_form" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pesanan ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" style="background: #ef4444; color: #fff; border: none; padding: 10px 18px; border-radius: 12px; font-weight: 800; cursor: pointer;">Hapus Pesanan</button>
            </form>
        </div>
    </div>
</div>

<!-- Modal Tambah Pesanan (Wizard) -->
<div id="createOrderModal" class="modal-backdrop" onclick="closeModal('createOrderModal')">
    <div class="modal-box" onclick="event.stopPropagation()" style="max-width: 1350px; width: 95%; padding: 0; border-radius: 24px; border: 1px solid var(--border-color); box-shadow: 0 20px 50px rgba(0,0,0,0.15); background: var(--surface);">

        {{-- Header Redesign --}}
        <div style="background: var(--surface-variant); padding: 24px 28px; border-bottom: 1.5px solid var(--border-color); border-top-left-radius: 23px; border-top-right-radius: 23px;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h3 style="font-size: 1.4rem; font-weight: 900; margin: 0; color: var(--text); letter-spacing: -0.3px;">Tambah <span style="color: var(--primary); background: linear-gradient(135deg, var(--primary) 0%, #3b82f6 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Pesanan Baru</span></h3>
                    <p style="margin: 4px 0 0; font-size: 0.8rem; color: var(--text-secondary);">Form pembuatan invoice transaksi pelanggan laundry sepatu</p>
                </div>
                <button onclick="closeModal('createOrderModal')" style="background: var(--surface); border: 1.5px solid var(--border-color); color: var(--text); width: 36px; height: 36px; border-radius: 50%; cursor: pointer; font-size: 1.2rem; display: flex; align-items: center; justify-content: center; transition: all 0.2s; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">&times;</button>
            </div>
        </div>

        <form action="{{ route('admin.orders.store') }}" method="POST" enctype="multipart/form-data" id="createOrderForm" style="margin: 0; padding: 28px 32px 32px;">
            @csrf

            <div class="modal-grid-container">
                {{-- COLUMN 1: POS Catalog --}}
                <div class="pos-catalog-container" style="background: var(--surface); border: 1px solid var(--border-color); padding: 20px; border-radius: 18px; box-shadow: 0 4px 12px rgba(0,0,0,0.01); display: flex; flex-direction: column; gap: 16px; max-height: 75vh; overflow-y: auto;">
                    <div style="font-size: 0.75rem; font-weight: 900; color: var(--primary); text-transform: uppercase; letter-spacing: 0.8px; display: flex; align-items: center; gap: 6px;">
                        <span>🛍️</span> Katalog Layanan
                    </div>
                    
                    <!-- Search Bar -->
                    <div style="position: relative;">
                        <input type="text" id="catalog-search" oninput="filterCatalog()" placeholder="Cari layanan..." class="filter-input" style="width: 100%; padding: 10px 12px; border-radius: 10px; font-size: 0.85rem;">
                    </div>
                    
                    <!-- Category Tabs -->
                    <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                        <button type="button" class="btn btn-sm active-cat-btn" onclick="filterCatalogCategory('all', this)" style="padding: 6px 12px; border-radius: 8px; font-size: 0.75rem; font-weight: 700; border: 1.5px solid var(--border-color); background: var(--primary); color: #fff; cursor: pointer; transition: 0.2s;">Semua</button>
                        <button type="button" class="btn btn-sm" onclick="filterCatalogCategory('cleaning', this)" style="padding: 6px 12px; border-radius: 8px; font-size: 0.75rem; font-weight: 700; border: 1.5px solid var(--border-color); background: transparent; color: var(--text-secondary); cursor: pointer; transition: 0.2s;">🧼 Cuci</button>
                        <button type="button" class="btn btn-sm" onclick="filterCatalogCategory('repair', this)" style="padding: 6px 12px; border-radius: 8px; font-size: 0.75rem; font-weight: 700; border: 1.5px solid var(--border-color); background: transparent; color: var(--text-secondary); cursor: pointer; transition: 0.2s;">🔧 Reparasi</button>
                    </div>
                    
                    <!-- Services List -->
                    <div id="catalog-services-list" style="display: flex; flex-direction: column; gap: 12px; overflow-y: auto; padding-right: 4px;">
                        @foreach($services as $service)
                            <div class="pos-service-card" data-id="{{ $service->id }}" data-name="{{ strtolower($service->name) }}" data-category="{{ $service->category }}">
                                <div style="flex-grow: 1;">
                                    <div style="font-weight: 800; font-size: 0.85rem; color: var(--text); line-height: 1.3;">{{ $service->name }}</div>
                                    <div style="font-size: 0.75rem; font-weight: 700; color: var(--primary); margin-top: 2px;">Rp {{ number_format($service->price, 0, ',', '.') }}</div>
                                    <div style="font-size: 0.65rem; color: var(--text-secondary); margin-top: 1px;">⏱️ {{ $service->estimated_time ?: '2-3 Hari' }}</div>
                                </div>
                                <div style="display: flex; align-items: center; gap: 8px; flex-shrink: 0;">
                                    <div class="catalog-stepper" style="display: flex; align-items: center; background: var(--surface-variant); border: 1.5px solid var(--border-color); border-radius: 8px; overflow: hidden; height: 32px; width: 80px;">
                                        <button type="button" onclick="decreaseCatalogQty(this)" style="background: transparent; border: none; color: var(--text); width: 25px; height: 100%; cursor: pointer; font-weight: bold; font-size: 0.9rem; display: flex; align-items: center; justify-content: center; transition: 0.2s;">-</button>
                                        <input type="number" class="catalog-qty-input" value="1" min="1" style="width: 30px; text-align: center; border: none; background: transparent; color: var(--text); padding: 0; margin: 0; font-weight: 700; font-size: 0.8rem; -moz-appearance: textfield; appearance: textfield; outline: none;">
                                        <button type="button" onclick="increaseCatalogQty(this)" style="background: transparent; border: none; color: var(--text); width: 25px; height: 100%; cursor: pointer; font-weight: bold; font-size: 0.9rem; display: flex; align-items: center; justify-content: center; transition: 0.2s;">+</button>
                                    </div>
                                    <button type="button" onclick="addServiceToOrder({{ $service->id }}, this)" class="btn-primary-custom" style="padding: 6px 10px; font-size: 0.72rem; border-radius: 8px; display: flex; align-items: center; gap: 4px; box-shadow: none; margin: 0;">
                                        ➕
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- COLUMN 2: Pelanggan & Sepatu --}}
                <div style="display: flex; flex-direction: column; gap: 24px;">
                    
                    {{-- Section 1: Pelanggan --}}
                    <div style="background: var(--surface); border: 1px solid var(--border-color); padding: 20px; border-radius: 18px; box-shadow: 0 4px 12px rgba(0,0,0,0.01);">
                        <div style="font-size: 0.75rem; font-weight: 900; color: var(--primary); margin-bottom: 16px; text-transform: uppercase; letter-spacing: 0.8px; display: flex; align-items: center; gap: 6px;">
                            <span>👤</span> Data Pelanggan
                        </div>

                        <!-- Info Invoice -->
                        <div class="modal-invoice-info-box" style="display: flex; justify-content: space-between; align-items: center; background: rgba(13, 110, 253, 0.03); padding: 10px 14px; border-radius: 12px; margin-bottom: 18px; font-size: 0.78rem; border: 1px dashed rgba(13, 110, 253, 0.15);">
                            <div>
                                <span style="color: var(--text-secondary);">No. Invoice:</span>
                                <span style="font-weight: 800; color: var(--primary); margin-left: 2px;">Otomatis</span>
                            </div>
                            <div>
                                <span style="color: var(--text-secondary);">Waktu:</span>
                                <span style="font-weight: 800; color: var(--text); margin-left: 2px;">{{ now()->format('d M Y, H:i') }}</span>
                            </div>
                        </div>

                        <!-- Premium Segmented Toggle Control -->
                        <div class="modal-segmented-toggle" style="display: flex; background: var(--surface-variant); padding: 4px; border-radius: 12px; gap: 4px; margin-bottom: 18px; border: 1px solid var(--border-color); width: 100%;">
                            <button type="button" id="customer-type-existing-btn" onclick="setCustomerType('existing')" style="flex: 1; padding: 10px; border-radius: 8px; border: none; font-weight: 800; font-size: 0.8rem; cursor: pointer; transition: 0.2s; background: var(--surface); color: var(--primary); box-shadow: 0 4px 12px rgba(0,0,0,0.04);">👥 Pelanggan Terdaftar</button>
                            <button type="button" id="customer-type-new-btn" onclick="setCustomerType('new')" style="flex: 1; padding: 10px; border-radius: 8px; border: none; font-weight: 800; font-size: 0.8rem; cursor: pointer; transition: 0.2s; background: transparent; color: var(--text-secondary);">➕ Registrasi Baru</button>
                        </div>
                        <div style="display: none;">
                            <input type="radio" name="customer_type" id="customer_type_existing_radio" value="existing" checked onchange="toggleCustomerType()">
                            <input type="radio" name="customer_type" id="customer_type_new_radio" value="new" onchange="toggleCustomerType()">
                        </div>

                        <!-- Existing Customer Section -->
                        <div id="existing-customer-section">
                            <label style="display: block; font-size: 0.72rem; font-weight: 800; color: var(--text-secondary); margin-bottom: 6px; text-transform: uppercase;">Pilih Pelanggan</label>
                            <select name="user_id" id="create_user_id" required class="filter-input" style="width: 100%; font-size: 0.9rem; padding: 12px; border-radius: 10px;" onchange="updateReceiptPreview()">
                                <option value="">— Pilih Pelanggan —</option>
                                @foreach($customers as $cust)
                                    @php
                                        $addr = $cust->addresses()->where('is_main_address', true)->first();
                                        $addrText = $addr ? ($addr->full_address . ', ' . $addr->village . ', ' . $addr->kecamatan) : '-';
                                    @endphp
                                    <option value="{{ $cust->id }}" data-name="{{ $cust->name }}" data-phone="{{ $cust->phone ?? '-' }}" data-address="{{ $addrText }}">{{ $cust->name }}{{ $cust->phone ? ' (' . $cust->phone . ')' : '' }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div id="new-customer-section" style="display: none;">
                            <div class="new-cust-grid">
                                <div>
                                    <label style="display: block; font-size: 0.72rem; font-weight: 800; color: var(--text-secondary); margin-bottom: 6px; text-transform: uppercase;">Nama Lengkap</label>
                                    <input type="text" name="new_customer_name" id="new_customer_name" placeholder="Budi Santoso" class="filter-input" style="width: 100%; padding: 10px; border-radius: 10px;" oninput="updateReceiptPreview()">
                                </div>
                                <div>
                                    <label style="display: block; font-size: 0.72rem; font-weight: 800; color: var(--text-secondary); margin-bottom: 6px; text-transform: uppercase;">Nomor WhatsApp</label>
                                    <input type="text" name="new_customer_phone" id="new_customer_phone" placeholder="08123456789" class="filter-input" style="width: 100%; padding: 10px; border-radius: 10px;" oninput="updateReceiptPreview()">
                                </div>
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.72rem; font-weight: 800; color: var(--text-secondary); margin-bottom: 6px; text-transform: uppercase;">Alamat Rumah (Opsional)</label>
                                <textarea name="new_customer_address" id="new_customer_address" placeholder="Contoh: Jl. Sudirman No. 24" class="filter-input" style="width: 100%; height: 60px; resize: none; padding: 10px; border-radius: 10px; line-height: 1.5;" oninput="updateReceiptPreview()"></textarea>
                            </div>
                        </div>
                    </div>

                    {{-- Section 2: Sepatu --}}
                    <div style="background: var(--surface); border: 1px solid var(--border-color); padding: 20px; border-radius: 18px; box-shadow: 0 4px 12px rgba(0,0,0,0.01);">
                        <div style="font-size: 0.75rem; font-weight: 900; color: var(--primary); margin-bottom: 14px; text-transform: uppercase; letter-spacing: 0.8px; display: flex; align-items: center; gap: 6px;">
                            <span>👟</span> Item Sepatu & Layanan
                        </div>

                        <!-- Shoes rows container -->
                        <div id="shoe-items-container"></div>

                        <!-- Add item button -->
                        <button type="button" onclick="addNewShoeRow()" class="btn" style="background: var(--surface-variant); color: var(--primary); border: 1.5px dashed var(--primary); padding: 10px; border-radius: 12px; font-weight: 800; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; width: 100%; transition: 0.2s; font-size: 0.85rem; margin-bottom: 18px;">
                            ➕ Tambah Item Sepatu Baru
                        </button>


                    </div>

                </div>

                {{-- RIGHT COLUMN: Pengiriman, Pembayaran & Total --}}
                <div style="display: flex; flex-direction: column; gap: 24px;">

                    {{-- Section 3: Pengantaran & Pembayaran --}}
                    <div style="background: var(--surface); border: 1px solid var(--border-color); padding: 20px; border-radius: 18px; box-shadow: 0 4px 12px rgba(0,0,0,0.01);">
                        <div style="font-size: 0.75rem; font-weight: 900; color: var(--primary); margin-bottom: 16px; text-transform: uppercase; letter-spacing: 0.8px; display: flex; align-items: center; gap: 6px;">
                            <span>💳</span> Pengiriman & Pembayaran
                        </div>

                        <div class="new-cust-grid">
                            <div>
                                <label style="display: block; font-size: 0.72rem; font-weight: 800; color: var(--text-secondary); margin-bottom: 6px; text-transform: uppercase;">Metode Pengantaran</label>
                                <select name="delivery_method" id="delivery_method" required class="filter-input" style="width: 100%; padding: 10px; border-radius: 10px;" onchange="toggleDeliverySection()">
                                    <option value="self">Antar Sendiri (Drop-off)</option>
                                    <option value="courier">Dijemput Kurir (Delivery)</option>
                                </select>
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.72rem; font-weight: 800; color: var(--text-secondary); margin-bottom: 6px; text-transform: uppercase;">Diskon Manual (Rp)</label>
                                <input type="number" name="discount" id="discount_input" value="0" min="0" placeholder="0" class="filter-input" style="width: 100%; padding: 10px; border-radius: 10px;" oninput="calculatePriceAndItemTotals()">
                            </div>
                        </div>

                        <!-- Delivery Address (Courier only) -->
                        <div id="delivery_address_section" style="display: none; margin-bottom: 14px; background: var(--surface-variant); padding: 12px; border-radius: 12px; border: 1px solid var(--border-color);">
                            <label style="display: block; font-size: 0.72rem; font-weight: 800; color: var(--text-secondary); margin-bottom: 6px; text-transform: uppercase;">Alamat Penjemputan</label>
                            <textarea name="delivery_address" id="delivery_address_input" placeholder="Masukkan alamat lengkap..." class="filter-input" style="width: 100%; height: 50px; resize: none; padding: 8px; border-radius: 8px;"></textarea>
                        </div>

                        <div class="new-cust-grid">
                            <div>
                                <label style="display: block; font-size: 0.72rem; font-weight: 800; color: var(--text-secondary); margin-bottom: 6px; text-transform: uppercase;">Metode Pembayaran</label>
                                <select name="payment_method" id="payment_method_input" required class="filter-input" style="width: 100%; padding: 10px; border-radius: 10px;" onchange="togglePaymentStatusSection()">
                                    <option value="cash">Tunai (Cash)</option>
                                    <option value="transfer">Transfer Bank</option>
                                    <option value="qris">QRIS</option>
                                    <option value="deferred">Belum Bayar</option>
                                </select>
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.72rem; font-weight: 800; color: var(--text-secondary); margin-bottom: 6px; text-transform: uppercase;">Status Pembayaran</label>
                                <select name="payment_status" id="payment_status_input" required class="filter-input" style="width: 100%; padding: 10px; border-radius: 10px;" onchange="togglePaymentStatusSection()">
                                    <option value="unpaid">Belum Lunas</option>
                                    <option value="paid">Lunas</option>
                                </select>
                            </div>
                        </div>

                        <!-- Cash received input section -->
                        <div id="cash_received_section" style="margin-bottom: 14px; background: rgba(16, 185, 129, 0.03); padding: 12px; border-radius: 12px; border: 1.5px dashed rgba(16, 185, 129, 0.2);">
                            <label style="display: block; font-size: 0.72rem; font-weight: 800; color: var(--text-secondary); margin-bottom: 6px; text-transform: uppercase;">Uang Tunai Diterima</label>
                            <input type="number" name="cash_amount" id="cash_received_input" placeholder="0" class="filter-input" style="width: 100%; padding: 10px; border-radius: 8px;" oninput="calculatePriceAndItemTotals()">
                        </div>

                        <input type="hidden" name="status" value="processing">
                    </div>

                    {{-- Section 4: Receipt Billing & Submit --}}
                    <div style="background: var(--surface-variant); border: 1.5px solid var(--border-color); border-radius: 18px; padding: 20px; position: relative;">
                        <div style="font-size: 0.72rem; font-weight: 900; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 12px; display: flex; align-items: center; gap: 6px;">
                            <span>🧾</span> RINCIAN PEMBAYARAN & BIAYA
                        </div>
                        
                        <!-- Live Receipt Preview Panel -->
                        <div class="live-receipt-preview" style="border: 1px dashed var(--border-color); border-radius: 12px; padding: 14px; background: var(--surface); margin-bottom: 14px; font-size: 0.75rem; font-family: monospace; color: var(--text);">
                            <div style="text-align: center; border-bottom: 1px dashed var(--border-color); padding-bottom: 8px; margin-bottom: 8px; font-weight: 700;">
                                *** PREVIEW NOTA PEMESANAN ***
                            </div>
                            
                            <!-- Customer info -->
                            <div style="margin-bottom: 8px; line-height: 1.4;">
                                <div><strong>Pelanggan:</strong> <span id="receipt_cust_name">-</span></div>
                                <div><strong>No. Telp :</strong> <span id="receipt_cust_phone">-</span></div>
                                <div style="white-space: pre-wrap;"><strong>Alamat   :</strong> <span id="receipt_cust_address">-</span></div>
                            </div>
                            
                            <!-- Items info -->
                            <div style="border-top: 1px dashed var(--border-color); border-bottom: 1px dashed var(--border-color); padding: 8px 0; margin-bottom: 8px;">
                                <div style="font-weight: 700; margin-bottom: 4px;">ITEM SEPATU:</div>
                                <div id="receipt_items_list" style="display: flex; flex-direction: column; gap: 6px;">
                                    <div style="color: var(--text-secondary); font-style: italic;">Belum ada item sepatu.</div>
                                </div>
                            </div>
                            
                            <div style="display: flex; flex-direction: column; gap: 6px;">
                                <div style="display: flex; justify-content: space-between;">
                                    <span>Subtotal</span>
                                    <span id="preview_subtotal" style="font-weight: 700;">Rp 0</span>
                                </div>
                                <div style="display: none; justify-content: space-between;" id="row_delivery_fee">
                                    <span>Ongkos Jemput</span>
                                    <span id="preview_delivery_fee" style="font-weight: 700;">Rp 0</span>
                                </div>
                                <div style="display: none; justify-content: space-between; color: #ef4444;" id="row_discount">
                                    <span>Potongan Diskon</span>
                                    <span id="preview_discount" style="font-weight: 700;">-Rp 0</span>
                                </div>
                                <div style="border-top: 1px dashed var(--border-color); padding-top: 6px; display: flex; justify-content: space-between; align-items: center; font-weight: 900; font-size: 0.85rem;">
                                    <span>GRAND TOTAL</span>
                                    <span id="preview_grand_total" style="color: var(--primary);">Rp 0</span>
                                </div>
                                <div style="display: none; justify-content: space-between; color: #10b981; border-top: 1px dashed var(--border-color); padding-top: 6px;" id="row_cash_change">
                                    <span>Kembalian</span>
                                    <span id="preview_cash_change" style="font-weight: 900;">Rp 0</span>
                                </div>
                            </div>
                        </div>

                        <div style="margin-top: 20px; border-top: 1px solid var(--border-color); padding-top: 16px; display: flex; justify-content: flex-end;">
                            <button type="submit" class="btn-primary-custom" style="width: 100%; justify-content: center; padding: 12px 28px; border-radius: 12px; font-weight: 800; font-size: 0.95rem; box-shadow: 0 4px 15px rgba(13,110,253,0.2);">🚀 Checkout</button>
                        </div>
                    </div>

                </div>
            </div>

        </form>
    </div>
</div>

<style>
.modal-grid-container {
    display: grid;
    grid-template-columns: 1fr 1.1fr 0.9fr;
    gap: 20px;
}
@media (max-width: 1100px) {
    .modal-grid-container {
        grid-template-columns: 1.1fr 1fr;
        gap: 20px;
    }
}
@media (max-width: 768px) {
    .modal-grid-container {
        grid-template-columns: 1fr;
        gap: 20px;
    }
}
.pos-service-card {
    background: var(--surface-variant);
    border: 1.5px solid var(--border-color);
    padding: 12px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    transition: all 0.2s ease;
}
.pos-service-card:hover {
    border-color: var(--primary);
    transform: translateY(-1px);
    box-shadow: 0 4px 10px rgba(0,0,0,0.02);
}
.catalog-qty-input::-webkit-outer-spin-button,
.catalog-qty-input::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}
.catalog-qty-input[type=number] {
  -moz-appearance: textfield;
}
.catalog-stepper button:hover {
  background: rgba(255,255,255,0.08) !important;
}
.catalog-stepper button:active {
  background: rgba(255,255,255,0.15) !important;
}
.shoe-row-grid-1 {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 14px;
    margin-bottom: 14px;
}
.shoe-row-grid-2 {
    display: grid;
    grid-template-columns: 1.5fr 1.2fr 130px;
    gap: 14px;
    margin-bottom: 14px;
}
.new-cust-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 14px;
    margin-bottom: 14px;
}
@media (max-width: 576px) {
    .shoe-row-grid-1, .shoe-row-grid-2, .new-cust-grid {
        grid-template-columns: 1fr;
        gap: 8px;
    }
}
.service-card-label.selected {
    background: rgba(13,110,253,0.1) !important;
    border-color: var(--primary) !important;
}
.status-radio-label:has(.status-radio:checked) {
    background: rgba(13,110,253,0.1) !important;
    border-color: var(--primary) !important;
}
#tab-step-1, #tab-step-2, #tab-step-3 { font-family: inherit; }
.shoe-item-card input, .shoe-item-card select {
    box-sizing: border-box;
}
.qty-input-item::-webkit-outer-spin-button,
.qty-input-item::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}
.qty-input-item[type=number] {
  -moz-appearance: textfield;
}
.quantity-stepper button:hover {
  background: rgba(255,255,255,0.06) !important;
}
.quantity-stepper button:active {
  background: rgba(255,255,255,0.12) !important;
}
</style>

<script>
// ---- Wizard price data ----
const serviceData = @json($services->map(fn($s) => ['id' => $s->id, 'name' => $s->name, 'price' => $s->price]));
let shoeRowIndex = 0;


function setCustomerType(type) {
    document.getElementById('customer_type_existing_radio').checked = (type === 'existing');
    document.getElementById('customer_type_new_radio').checked = (type === 'new');
    
    const btnExisting = document.getElementById('customer-type-existing-btn');
    const btnNew = document.getElementById('customer-type-new-btn');
    
    if (type === 'existing') {
        btnExisting.style.background = 'var(--surface)';
        btnExisting.style.color = 'var(--primary)';
        btnExisting.style.boxShadow = '0 4px 12px rgba(0, 0, 0, 0.05)';
        btnNew.style.background = 'transparent';
        btnNew.style.color = 'var(--text-secondary)';
        btnNew.style.boxShadow = 'none';
    } else {
        btnNew.style.background = 'var(--surface)';
        btnNew.style.color = 'var(--primary)';
        btnNew.style.boxShadow = '0 4px 12px rgba(0, 0, 0, 0.05)';
        btnExisting.style.background = 'transparent';
        btnExisting.style.color = 'var(--text-secondary)';
        btnExisting.style.boxShadow = 'none';
    }
    toggleCustomerType();
}


function toggleCustomerType() {
    const type = document.querySelector('input[name="customer_type"]:checked').value;
    const existingSection = document.getElementById('existing-customer-section');
    const newSection = document.getElementById('new-customer-section');

    if (type === 'existing') {
        existingSection.style.display = 'block';
        newSection.style.display = 'none';
        document.getElementById('create_user_id').required = true;
        document.getElementById('new_customer_name').required = false;
        document.getElementById('new_customer_phone').required = false;
    } else {
        existingSection.style.display = 'none';
        newSection.style.display = 'block';
        document.getElementById('create_user_id').required = false;
        document.getElementById('new_customer_name').required = true;
        document.getElementById('new_customer_phone').required = true;
    }
    updateReceiptPreview();
}

function toggleDeliverySection() {
    const method = document.getElementById('delivery_method').value;
    const addressSection = document.getElementById('delivery_address_section');
    const addressInput = document.getElementById('delivery_address_input');
    
    if (method === 'courier') {
        addressSection.style.display = 'block';
        addressInput.required = true;
    } else {
        addressSection.style.display = 'none';
        addressInput.required = false;
    }
    calculatePriceAndItemTotals();
}

function togglePaymentStatusSection() {
    const method = document.getElementById('payment_method_input').value;
    const cashSection = document.getElementById('cash_received_section');
    const cashInput = document.getElementById('cash_received_input');
    const statusSelect = document.getElementById('payment_status_input');
    
    if (method === 'deferred') {
        statusSelect.value = 'unpaid';
    }
    
    if (method === 'cash' && statusSelect.value === 'paid') {
        cashSection.style.display = 'block';
        cashInput.required = true;
    } else {
        cashSection.style.display = 'none';
        cashInput.required = false;
        cashInput.value = '';
    }
    calculatePriceAndItemTotals();
}

function addNewShoeRow(preselectedServiceId = null, qty = 1) {
    const container = document.getElementById('shoe-items-container');
    const row = document.createElement('div');
    row.className = 'shoe-item-card';
    row.id = 'shoe-item-row-' + shoeRowIndex;
    row.style.cssText = 'border: 1.5px solid var(--border-color); border-radius: 18px; padding: 20px; margin-bottom: 20px; background: var(--surface); position: relative; box-shadow: 0 4px 12px rgba(0,0,0,0.02); transition: 0.2s;';
    
    const deleteBtnHtml = `<button type="button" onclick="removeShoeRow(${shoeRowIndex})" class="btn-remove-shoe" style="position: absolute; top: 16px; right: 16px; background: rgba(239, 68, 68, 0.05); border: none; color: #ef4444; width: 30px; height: 30px; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 0.9rem; transition: 0.2s;"><i class="bi bi-trash"></i></button>`;
    
    const service = preselectedServiceId ? serviceData.find(s => s.id == preselectedServiceId) : null;
    const serviceName = service ? service.name : '';
    const badgeHtml = preselectedServiceId ? `
        <div style="background: rgba(13,110,253,0.05); padding: 12px 16px; border-radius: 12px; margin-bottom: 16px; font-size: 0.8rem; font-weight: 800; color: var(--primary); display: flex; justify-content: space-between; align-items: center; border: 1.5px solid rgba(13,110,253,0.15);">
            <span>📋 Layanan: ${serviceName}</span>
            <span>Jumlah: ${qty} Pasang</span>
        </div>
    ` : '';
    
    row.innerHTML = `
        ${deleteBtnHtml}
        <div style="font-size: 0.72rem; font-weight: 900; color: var(--primary); margin-bottom: 16px; text-transform: uppercase; letter-spacing: 0.8px; display: flex; align-items: center; gap: 4px;">
            <span>👟</span> ITEM SEPATU #<span class="item-index-label">${container.children.length + 1}</span>
        </div>
        
        ${badgeHtml}
        
        <div class="shoe-row-grid-1">
            <div>
                <label style="display: block; font-size: 0.72rem; font-weight: 800; color: var(--text-secondary); margin-bottom: 6px; text-transform: uppercase;">Merek / Nama Sepatu</label>
                <input type="text" name="items[${shoeRowIndex}][shoe_name]" placeholder="Contoh: Nike Air Jordan" required class="filter-input" style="width: 100%; padding: 11px; border-radius: 10px;" oninput="updateReceiptPreview()">
            </div>
            <div>
                <label style="display: block; font-size: 0.72rem; font-weight: 800; color: var(--text-secondary); margin-bottom: 6px; text-transform: uppercase;">Ukuran</label>
                <input type="text" name="items[${shoeRowIndex}][shoe_size]" placeholder="Contoh: 42" required class="filter-input" style="width: 100%; padding: 11px; border-radius: 10px;" oninput="updateReceiptPreview()">
            </div>
        </div>
        
        <div class="shoe-row-grid-2" style="${preselectedServiceId ? 'display: none;' : ''}">
            <div>
                <label style="display: block; font-size: 0.72rem; font-weight: 800; color: var(--text-secondary); margin-bottom: 6px; text-transform: uppercase;">Jenis Layanan Utama</label>
                <select name="items[${shoeRowIndex}][service_id]" required class="filter-input service-select-item" style="width: 100%; padding: 11px; border-radius: 10px;" onchange="updateAdditionalServicesVisibility(${shoeRowIndex}); calculatePriceAndItemTotals();">
                    <option value="">-- Pilih Layanan Utama --</option>
                    ${serviceData.map(s => {
                        const selected = (preselectedServiceId && s.id == preselectedServiceId) ? 'selected' : '';
                        return `<option value="${s.id}" data-price="${s.price}" ${selected}>${s.name} (Rp ${s.price.toLocaleString('id-ID')})</option>`;
                    }).join('')}
                </select>
            </div>
            <div>
                <label style="display: block; font-size: 0.72rem; font-weight: 800; color: var(--text-secondary); margin-bottom: 6px; text-transform: uppercase;">Kecepatan</label>
                <select name="items[${shoeRowIndex}][processing_speed]" required class="filter-input speed-select-item" style="width: 100%; padding: 11px; border-radius: 10px;" onchange="calculatePriceAndItemTotals()">
                    <option value="regular">Regular (+ Rp 0)</option>
                    <option value="express">Express (+ Rp 25.000 / pasang)</option>
                </select>
            </div>
            <div>
                <label style="display: block; font-size: 0.72rem; font-weight: 800; color: var(--text-secondary); margin-bottom: 6px; text-transform: uppercase;">Jumlah</label>
                <div class="quantity-stepper" style="display: flex; align-items: center; background: rgba(0, 0, 0, 0.15); border: 1.5px solid var(--border-color); border-radius: 10px; overflow: hidden; width: 100%; height: 42px;">
                    <button type="button" onclick="decreaseQty(this)" style="background: transparent; border: none; color: var(--text); width: 38px; height: 100%; cursor: pointer; font-weight: bold; font-size: 1.1rem; display: flex; align-items: center; justify-content: center; transition: 0.2s;">-</button>
                    <input type="number" name="items[${shoeRowIndex}][shoe_quantity]" value="${qty}" min="1" required class="filter-input qty-input-item" style="width: calc(100% - 76px); text-align: center; border: none; background: transparent; color: var(--text); padding: 0; margin: 0; font-weight: 700; height: 100%; outline: none;" onchange="calculatePriceAndItemTotals()" oninput="calculatePriceAndItemTotals()">
                    <button type="button" onclick="increaseQty(this)" style="background: transparent; border: none; color: var(--text); width: 38px; height: 100%; cursor: pointer; font-weight: bold; font-size: 1.1rem; display: flex; align-items: center; justify-content: center; transition: 0.2s;">+</button>
                </div>
            </div>
        </div>

        <div class="additional-services-wrapper" style="margin-bottom: 14px; display: none; position: relative;">
            <label style="display: block; font-size: 0.72rem; font-weight: 800; color: var(--text-secondary); margin-bottom: 6px; text-transform: uppercase;">Layanan Tambahan (Opsional)</label>
            
            <button type="button" class="multiselect-trigger" onclick="toggleMultiselectDropdown(${shoeRowIndex}, event)" style="width: 100%; text-align: left; padding: 11px 16px; border-radius: 10px; border: 1px solid var(--border-color); background: var(--surface); color: var(--text); display: flex; justify-content: space-between; align-items: center; cursor: pointer; font-size: 0.85rem; transition: 0.2s;">
                <span class="selected-count-label" style="font-weight: 700; opacity: 0.85;">0 Layanan Tambahan Terpilih</span>
                <span style="font-size: 0.7rem; opacity: 0.5;">▼</span>
            </button>
            
            <div class="multiselect-dropdown-panel" style="display: none; position: absolute; top: 100%; left: 0; right: 0; background: var(--surface); border: 1px solid var(--border-color); border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.12); z-index: 100; max-height: 200px; overflow-y: auto; padding: 12px; margin-top: 6px;">
                ${serviceData.map(s => `
                    <label class="additional-service-label" style="display: flex; align-items: center; gap: 10px; font-size: 0.8rem; cursor: pointer; color: var(--text); padding: 6px 8px; border-radius: 8px; transition: background 0.15s; margin-bottom: 4px;">
                        <input type="checkbox" name="items[${shoeRowIndex}][additional_services][]" value="${s.id}" data-name="${s.name}" data-price="${s.price}" class="additional-service-checkbox" onchange="updateSelectedBadges(${shoeRowIndex}); calculatePriceAndItemTotals();" style="accent-color: var(--primary); width: 15px; height: 15px; flex-shrink: 0;">
                        <span style="flex-grow: 1;">${s.name}</span>
                        <span style="font-weight: 700; color: var(--primary); font-size: 0.75rem;">+Rp ${s.price.toLocaleString('id-ID')}</span>
                    </label>
                `).join('')}
            </div>
            
            <div class="selected-services-badges" style="display: flex; flex-wrap: wrap; gap: 6px; margin-top: 8px;"></div>
        </div>
        
        <div>
            <label style="display: block; font-size: 0.72rem; font-weight: 800; color: var(--text-secondary); margin-bottom: 6px; text-transform: uppercase;">Catatan Pengerjaan (Opsional)</label>
            <input type="text" name="items[${shoeRowIndex}][handling_notes]" placeholder="Contoh: Noda lem di sol samping" class="filter-input" style="width: 100%; padding: 11px; border-radius: 10px; margin-bottom: 12px;">
        </div>
        <div>
            <label style="display: block; font-size: 0.72rem; font-weight: 800; color: var(--text-secondary); margin-bottom: 6px; text-transform: uppercase;">Foto Sebelum Pengerjaan (Opsional)</label>
            <input type="file" name="items[${shoeRowIndex}][shoe_photo]" accept="image/*" class="filter-input" style="width: 100%; padding: 8px; border-radius: 10px;">
        </div>
    `;
    
    container.appendChild(row);
    updateAdditionalServicesVisibility(shoeRowIndex);
    shoeRowIndex++;
    updateDeleteButtonsVisibility();
    calculatePriceAndItemTotals();
}

function removeShoeRow(index) {
    const row = document.getElementById('shoe-item-row-' + index);
    if (row) {
        row.remove();
        updateLabelsAndIndexes();
        updateDeleteButtonsVisibility();
        calculatePriceAndItemTotals();
    }
}

function decreaseQty(btn) {
    const input = btn.parentElement.querySelector('.qty-input-item');
    let val = parseInt(input.value) || 1;
    if (val > 1) {
        input.value = val - 1;
        const event = new Event('change', { bubbles: true });
        input.dispatchEvent(event);
    }
}

function increaseQty(btn) {
    const input = btn.parentElement.querySelector('.qty-input-item');
    let val = parseInt(input.value) || 1;
    input.value = val + 1;
    const event = new Event('change', { bubbles: true });
    input.dispatchEvent(event);
}

function updateLabelsAndIndexes() {
    const container = document.getElementById('shoe-items-container');
    Array.from(container.children).forEach((child, index) => {
        child.querySelector('.item-index-label').textContent = index + 1;
    });
}

function updateDeleteButtonsVisibility() {
    const container = document.getElementById('shoe-items-container');
    const deleteBtns = container.querySelectorAll('.btn-remove-shoe');
    deleteBtns.forEach(btn => {
        btn.style.display = container.children.length > 1 ? 'flex' : 'none';
    });
}

function updateAdditionalServicesVisibility(rowIndex) {
    const row = document.getElementById('shoe-item-row-' + rowIndex);
    if (!row) return;
    
    const mainServiceSelect = row.querySelector('.service-select-item');
    const selectedMainId = mainServiceSelect.value;
    const wrapper = row.querySelector('.additional-services-wrapper');
    
    if (!selectedMainId) {
        if (wrapper) wrapper.style.display = 'none';
        return;
    }
    
    if (wrapper) wrapper.style.display = 'block';
    
    const checkboxes = row.querySelectorAll('.additional-service-checkbox');
    checkboxes.forEach(cb => {
        const label = cb.closest('label');
        if (cb.value === selectedMainId) {
            cb.checked = false;
            cb.disabled = true;
            if (label) label.style.display = 'none';
        } else {
            cb.disabled = false;
            if (label) label.style.display = 'flex';
        }
    });
    
    updateSelectedBadges(rowIndex);
}

function toggleMultiselectDropdown(rowIndex, event) {
    if (event) event.stopPropagation();
    
    const panels = document.querySelectorAll('.multiselect-dropdown-panel');
    const rowPanel = document.querySelector(`#shoe-item-row-${rowIndex} .multiselect-dropdown-panel`);
    
    panels.forEach(p => {
        if (p !== rowPanel) p.style.display = 'none';
    });
    
    if (rowPanel) {
        rowPanel.style.display = rowPanel.style.display === 'block' ? 'none' : 'block';
    }
}

function updateSelectedBadges(rowIndex) {
    const row = document.getElementById('shoe-item-row-' + rowIndex);
    if (!row) return;
    
    const checkboxes = row.querySelectorAll('.additional-service-checkbox:checked');
    const badgeContainer = row.querySelector('.selected-services-badges');
    const triggerLabel = row.querySelector('.selected-count-label');
    
    if (triggerLabel) {
        triggerLabel.textContent = checkboxes.length + ' Layanan Tambahan Terpilih';
    }
    
    if (badgeContainer) {
        badgeContainer.innerHTML = '';
        checkboxes.forEach(cb => {
            const id = cb.value;
            const name = cb.getAttribute('data-name');
            const price = parseFloat(cb.getAttribute('data-price')) || 0;
            
            const badge = document.createElement('span');
            badge.style.cssText = 'display: inline-flex; align-items: center; gap: 6px; background: rgba(13, 110, 253, 0.08); color: var(--primary); border: 1px solid rgba(13, 110, 253, 0.15); border-radius: 8px; padding: 4px 10px; font-size: 0.72rem; font-weight: 700; cursor: default; margin-bottom: 4px;';
            badge.innerHTML = `
                <span>${name} (+Rp ${price.toLocaleString('id-ID')})</span>
                <span onclick="removeAdditionalService(${rowIndex}, '${id}', event)" style="color: #ef4444; font-weight: 900; cursor: pointer; font-size: 0.85rem; margin-left: 2px; padding: 0 2px;">&times;</span>
            `;
            badgeContainer.appendChild(badge);
        });
    }
}

function removeAdditionalService(rowIndex, serviceId, event) {
    if (event) event.stopPropagation();
    const row = document.getElementById('shoe-item-row-' + rowIndex);
    if (!row) return;
    
    const checkbox = row.querySelector(`.additional-service-checkbox[value="${serviceId}"]`);
    if (checkbox) {
        checkbox.checked = false;
        updateSelectedBadges(rowIndex);
        calculatePriceAndItemTotals();
    }
}

// Global click-outside listener to close multiselect dropdowns (using capture phase to bypass stopPropagation)
document.addEventListener('click', function(e) {
    const panels = document.querySelectorAll('.multiselect-dropdown-panel');
    panels.forEach(panel => {
        const wrapper = panel.closest('.additional-services-wrapper');
        const trigger = wrapper ? wrapper.querySelector('.multiselect-trigger') : null;
        if (panel.style.display === 'block' && !panel.contains(e.target) && e.target !== trigger && (trigger && !trigger.contains(e.target))) {
            panel.style.display = 'none';
        }
    });
}, true);

function calculatePriceAndItemTotals() {
    const container = document.getElementById('shoe-items-container');
    const rows = container.querySelectorAll('.shoe-item-card');
    let subtotal = 0;
    
    rows.forEach(row => {
        const svcSelect = row.querySelector('.service-select-item');
        const speedSelect = row.querySelector('.speed-select-item');
        const qtyInput = row.querySelector('.qty-input-item');
        
        if (svcSelect && svcSelect.value) {
            const price = parseFloat(svcSelect.options[svcSelect.selectedIndex].getAttribute('data-price')) || 0;
            const qty = parseInt(qtyInput.value) || 1;
            const speed = speedSelect.value;
            
            let itemTotal = price;
            
            // Add optional additional services
            const checkedBoxes = row.querySelectorAll('.additional-service-checkbox:checked');
            checkedBoxes.forEach(cb => {
                itemTotal += parseFloat(cb.getAttribute('data-price')) || 0;
            });
            
            itemTotal *= qty;
            
            if (speed === 'express') {
                itemTotal += (25000 * qty);
            }
            subtotal += itemTotal;
        }
    });
    
    document.getElementById('preview_subtotal').textContent = 'Rp ' + subtotal.toLocaleString('id-ID');
    
    const deliveryMethod = document.getElementById('delivery_method').value;
    const deliveryFee = deliveryMethod === 'courier' ? 25000 : 0;
    const deliveryRow = document.getElementById('row_delivery_fee');
    if (deliveryFee > 0) {
        deliveryRow.style.display = 'flex';
        document.getElementById('preview_delivery_fee').textContent = 'Rp ' + deliveryFee.toLocaleString('id-ID');
    } else {
        deliveryRow.style.display = 'none';
    }
    
    const discount = parseFloat(document.getElementById('discount_input').value) || 0;
    const discountRow = document.getElementById('row_discount');
    if (discount > 0) {
        discountRow.style.display = 'flex';
        document.getElementById('preview_discount').textContent = '-Rp ' + discount.toLocaleString('id-ID');
    } else {
        discountRow.style.display = 'none';
    }
    
    const grandTotal = Math.max(0, subtotal + deliveryFee - discount);
    document.getElementById('preview_grand_total').textContent = 'Rp ' + grandTotal.toLocaleString('id-ID');
    
    const paymentMethod = document.getElementById('payment_method_input').value;
    const cashReceived = parseFloat(document.getElementById('cash_received_input').value) || 0;
    const changeRow = document.getElementById('row_cash_change');
    
    if (paymentMethod === 'cash' && cashReceived > 0) {
        changeRow.style.display = 'flex';
        const change = Math.max(0, cashReceived - grandTotal);
        document.getElementById('preview_cash_change').textContent = 'Rp ' + change.toLocaleString('id-ID');
    } else {
        changeRow.style.display = 'none';
    }
    
    updateReceiptPreview();
}

function updateReceiptPreview() {
    // 1. Customer Info
    const isNew = document.getElementById('customer_type_new_radio').checked;
    let name = '-', phone = '-', address = '-';
    
    if (isNew) {
        name = document.getElementById('new_customer_name').value || '-';
        phone = document.getElementById('new_customer_phone').value || '-';
        address = document.getElementById('new_customer_address').value || '-';
    } else {
        const select = document.getElementById('create_user_id');
        if (select && select.selectedIndex > 0) {
            const opt = select.options[select.selectedIndex];
            name = opt.getAttribute('data-name') || '-';
            phone = opt.getAttribute('data-phone') || '-';
            address = opt.getAttribute('data-address') || '-';
        }
    }
    
    const nameEl = document.getElementById('receipt_cust_name');
    const phoneEl = document.getElementById('receipt_cust_phone');
    const addrEl = document.getElementById('receipt_cust_address');
    if (nameEl) nameEl.textContent = name;
    if (phoneEl) phoneEl.textContent = phone;
    if (addrEl) addrEl.textContent = address;
    
    // 2. Shoe Items Info
    const container = document.getElementById('shoe-items-container');
    const rows = container ? container.querySelectorAll('.shoe-item-card') : [];
    const itemsList = document.getElementById('receipt_items_list');
    
    if (!itemsList) return;
    
    if (rows.length === 0) {
        itemsList.innerHTML = '<div style="color: var(--text-secondary); font-style: italic;">Belum ada item sepatu.</div>';
        return;
    }
    
    let html = '';
    rows.forEach((row, idx) => {
        const shoeNameInput = row.querySelector('input[name*="[shoe_name]"]');
        const shoeSizeInput = row.querySelector('input[name*="[shoe_size]"]');
        const shoeName = shoeNameInput ? (shoeNameInput.value || '(Tanpa Nama)') : '(Tanpa Nama)';
        const shoeSize = shoeSizeInput ? (shoeSizeInput.value || '-') : '-';
        const qty = parseInt(row.querySelector('.qty-input-item')?.value) || 1;
        const svcSelect = row.querySelector('.service-select-item');
        const speedSelect = row.querySelector('.speed-select-item');
        let svcName = '-', basePrice = 0;
        if (svcSelect && svcSelect.selectedIndex > 0) {
            svcName = svcSelect.options[svcSelect.selectedIndex].text.split('(')[0].trim();
            basePrice = parseFloat(svcSelect.options[svcSelect.selectedIndex].getAttribute('data-price')) || 0;
        }
        const speed = speedSelect ? speedSelect.value : 'regular';
        const checkedBoxes = row.querySelectorAll('.additional-service-checkbox:checked');
        let extrasList = [], extrasPrice = 0;
        checkedBoxes.forEach(cb => {
            extrasList.push(cb.getAttribute('data-name'));
            extrasPrice += parseFloat(cb.getAttribute('data-price')) || 0;
        });
        let itemUnitPrice = basePrice + extrasPrice;
        if (speed === 'express') itemUnitPrice += 25000;
        const itemSubtotal = itemUnitPrice * qty;
        
        html += `
            <div style="border-bottom: 1px dotted var(--border-color); padding-bottom: 6px; margin-bottom: 6px;">
                <div style="font-weight: 700; display: flex; justify-content: space-between;">
                    <span>#${idx + 1} ${shoeName} (Size: ${shoeSize})</span>
                    <span>x${qty}</span>
                </div>
                <div style="color: var(--text-secondary); margin-left: 8px; font-size: 0.7rem; line-height: 1.3;">
                    - Layanan: ${svcName}
                    ${extrasList.length > 0 ? `<br>- Tambahan: ${extrasList.join(', ')}` : ''}
                    ${speed === 'express' ? '<br>- Express (+Rp 25.000)' : ''}
                </div>
                <div style="text-align: right; font-weight: 700; margin-top: 2px;">
                    Rp ${itemSubtotal.toLocaleString('id-ID')}
                </div>
            </div>`;
    });
    
    itemsList.innerHTML = html || '<div style="color: var(--text-secondary); font-style: italic;">Belum ada item sepatu.</div>';
}

document.addEventListener('DOMContentLoaded', function() {
    // Add initial shoe row
    addNewShoeRow();
    toggleCustomerType();
    toggleDeliverySection();
    togglePaymentStatusSection();
    updateReceiptPreview();
});
</script>

<!-- Modal Edit Pesanan -->
<div id="editOrderModal" class="modal-backdrop" onclick="closeModal('editOrderModal')">
    <div class="modal-box" onclick="event.stopPropagation()">
        <button class="modal-close" onclick="closeModal('editOrderModal')">&times;</button>
        <h3 style="font-size: 1.5rem; font-weight: 900; margin-bottom: 20px; border-bottom: 1.5px solid var(--border-color); padding-bottom: 15px; color: var(--text);">Edit <span style="color: var(--primary);">Pesanan</span></h3>
        
        <form id="editOrderForm" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="modal-grid-2">
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--text-secondary); margin-bottom: 5px; text-transform: uppercase;">Layanan</label>
                    <select name="service_id" id="edit_service_id" required class="filter-input" style="width: 100%;">
                        @foreach($services as $serv)
                            <option value="{{ $serv->id }}">{{ $serv->name }} (Rp{{ number_format($serv->price, 0, ',', '.') }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--text-secondary); margin-bottom: 5px; text-transform: uppercase;">Nomor Antrian</label>
                    <input type="text" name="queue_number" id="edit_queue_number" required class="filter-input" style="width: 100%;">
                </div>
            </div>
            <div class="modal-grid-2">
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--text-secondary); margin-bottom: 5px; text-transform: uppercase;">Kecepatan</label>
                    <select name="processing_speed" id="edit_processing_speed" required class="filter-input" style="width: 100%;">
                        <option value="regular">Regular</option>
                        <option value="express">Express</option>
                    </select>
                </div>
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--text-secondary); margin-bottom: 5px; text-transform: uppercase;">Nama Sepatu</label>
                    <input type="text" name="shoe_name" id="edit_shoe_name" required class="filter-input" style="width: 100%;">
                </div>
            </div>
            <div class="modal-grid-2">
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--text-secondary); margin-bottom: 5px; text-transform: uppercase;">Ukuran Sepatu</label>
                    <input type="text" name="shoe_size" id="edit_shoe_size" required class="filter-input" style="width: 100%;">
                </div>
            </div>
            <div class="modal-grid-2">
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--text-secondary); margin-bottom: 5px; text-transform: uppercase;">Jumlah Sepatu</label>
                    <input type="number" name="shoe_quantity" id="edit_shoe_quantity" min="1" required class="filter-input" style="width: 100%;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--text-secondary); margin-bottom: 5px; text-transform: uppercase;">Metode Pembayaran</label>
                    <select name="payment_method" id="edit_payment_method" required class="filter-input" style="width: 100%;">
                        <option value="cash">Tunai (Cash)</option>
                        <option value="transfer">Transfer Bank</option>
                    </select>
                </div>
            </div>
            <div class="modal-grid-2">
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--text-secondary); margin-bottom: 5px; text-transform: uppercase;">Status Pembayaran</label>
                    <select name="payment_status" id="edit_payment_status" required class="filter-input" style="width: 100%;">
                        <option value="unpaid">Belum Lunas (Unpaid)</option>
                        <option value="paid">Lunas (Paid)</option>
                    </select>
                </div>
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--text-secondary); margin-bottom: 5px; text-transform: uppercase;">Status Pesanan</label>
                    <select name="status" id="edit_status" required class="filter-input" style="width: 100%;">
                        <option value="pending">Menunggu (Pending)</option>
                        <option value="processing">Dalam Antrian (Queue/Processing)</option>
                        <option value="washing">Dicuci/Dikerjakan (Washing)</option>
                        <option value="finishing">Finishing</option>
                        <option value="ready">Siap Diambil (Ready)</option>
                        <option value="completed">Diambil (Completed)</option>
                        <option value="cancelled">Dibatalkan (Cancelled)</option>
                    </select>
                </div>
            </div>
            <div style="margin-bottom: 1.2rem;">
                <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--text-secondary); margin-bottom: 5px; text-transform: uppercase;">Uang Diterima (Khusus Tunai)</label>
                <input type="number" name="cash_amount" id="edit_cash_amount" placeholder="Contoh: 100000" class="filter-input" style="width: 100%;">
            </div>
            <div class="modal-grid-2">
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--text-secondary); margin-bottom: 5px; text-transform: uppercase;">Ubah Foto Sebelum</label>
                    <input type="file" name="shoe_photo" class="filter-input" style="width: 100%;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--text-secondary); margin-bottom: 5px; text-transform: uppercase;">Upload Foto Sesudah</label>
                    <input type="file" name="photo_after" class="filter-input" style="width: 100%;">
                </div>
            </div>
            <button type="submit" class="btn-primary-custom" style="width: 100%; justify-content: center; padding: 12px; font-size: 0.95rem;">Simpan Perubahan</button>
        </form>
    </div>
</div>

<script>
    // Modal operations
    function openCreateModal() {
        // Reset shoe items container agar tidak duplikat saat modal dibuka ulang
        const container = document.getElementById('shoe-items-container');
        container.innerHTML = '';
        shoeRowIndex = 0;
        // Auto-tambah 1 item sepatu pertama
        addNewShoeRow();
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
        const deliveryLocationBtn = document.getElementById('detail_delivery_location_btn');
        if (order.is_delivery == 1 && order.delivery_address) {
            deliveryAddressEl.innerText = '📍 Alamat Kirim: ' + order.delivery_address;
            deliveryAddressEl.style.display = 'block';
            if (order.latitude && order.longitude) {
                deliveryLocationBtn.href = `https://www.google.com/maps?q=${order.latitude},${order.longitude}`;
                deliveryLocationBtn.style.display = 'inline-block';
            } else {
                deliveryLocationBtn.style.display = 'none';
            }
        } else {
            deliveryAddressEl.style.display = 'none';
            if (deliveryLocationBtn) deliveryLocationBtn.style.display = 'none';
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
        if (order.payment_status === 'unpaid' && order.payment_method === 'cash') {
            confirmPaymentForm.action = `/employee/orders/${order.id}/confirm-payment`;
            confirmPaymentForm.style.display = 'block';
        } else {
            confirmPaymentForm.style.display = 'none';
        }
        
        const svgPlaceholder = `data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%2394a3b8' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round'><rect x='3' y='3' width='18' height='18' rx='2' ry='2'></rect><circle cx='8.5' cy='8.5' r='1.5'></circle><polyline points='21 15 16 10 5 21'></polyline></svg>`;
        const storagePath = '/storage/';
        
        const imgBefore = document.getElementById('detail_photo_before');
        if (order.photo_before) {
            imgBefore.src = storagePath + order.photo_before;
            imgBefore.style.padding = '0';
        } else {
            imgBefore.src = svgPlaceholder;
            imgBefore.style.padding = '20px';
        }

        const imgBefore2 = document.getElementById('detail_photo_before_2');
        if (order.photo_before_2) {
            imgBefore2.src = storagePath + order.photo_before_2;
            imgBefore2.style.padding = '0';
        } else {
            imgBefore2.src = svgPlaceholder;
            imgBefore2.style.padding = '20px';
        }
        
        const imgAfter = document.getElementById('detail_photo_after');
        if (order.photo_after) {
            imgAfter.src = storagePath + order.photo_after;
            imgAfter.style.padding = '0';
        } else {
            imgAfter.src = svgPlaceholder;
            imgAfter.style.padding = '20px';
        }
        
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

    function decreaseCatalogQty(btn) {
        const input = btn.parentElement.querySelector('.catalog-qty-input');
        let val = parseInt(input.value) || 1;
        if (val > 1) {
            input.value = val - 1;
        }
    }

    function increaseCatalogQty(btn) {
        const input = btn.parentElement.querySelector('.catalog-qty-input');
        let val = parseInt(input.value) || 1;
        input.value = val + 1;
    }

    function addServiceToOrder(serviceId, btn) {
        const stepper = btn.parentElement.querySelector('.catalog-qty-input');
        const qty = parseInt(stepper.value) || 1;
        
        const container = document.getElementById('shoe-items-container');
        const rows = container.querySelectorAll('.shoe-item-card');
        let reused = false;
        
        for (let i = 0; i < rows.length; i++) {
            const row = rows[i];
            const svcSelect = row.querySelector('.service-select-item');
            const shoeNameInput = row.querySelector('input[name*="[shoe_name]"]');
            
            if (svcSelect && !svcSelect.value && shoeNameInput && !shoeNameInput.value) {
                svcSelect.value = serviceId;
                const qtyInput = row.querySelector('.qty-input-item');
                if (qtyInput) qtyInput.value = qty;
                
                const grid2 = row.querySelector('.shoe-row-grid-2');
                if (grid2) grid2.style.display = 'none';
                
                const service = serviceData.find(s => s.id == serviceId);
                const serviceName = service ? service.name : '';
                const badgeHtml = `
                    <div style="background: rgba(13,110,253,0.05); padding: 12px 16px; border-radius: 12px; margin-bottom: 16px; font-size: 0.8rem; font-weight: 800; color: var(--primary); display: flex; justify-content: space-between; align-items: center; border: 1.5px solid rgba(13,110,253,0.15);">
                        <span>📋 Layanan: ${serviceName}</span>
                        <span>Jumlah: ${qty} Pasang</span>
                    </div>
                `;
                
                const indexLabel = row.querySelector('.item-index-label');
                if (indexLabel) {
                    const headerDiv = indexLabel.parentElement;
                    headerDiv.insertAdjacentHTML('afterend', badgeHtml);
                }
                
                const event = new Event('change', { bubbles: true });
                svcSelect.dispatchEvent(event);
                
                reused = true;
                break;
            }
        }
        
        if (!reused) {
            addNewShoeRow(serviceId, qty);
        }
        
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: 'Layanan ditambahkan',
                showConfirmButton: false,
                timer: 1000,
                background: '#1e293b',
                color: '#fff'
            });
        }
        
        stepper.value = 1;
    }

    let activeCategory = 'all';

    function filterCatalog() {
        const searchVal = document.getElementById('catalog-search').value.toLowerCase();
        const cards = document.querySelectorAll('.pos-service-card');
        
        cards.forEach(card => {
            const name = card.getAttribute('data-name');
            const cat = card.getAttribute('data-category');
            
            const matchesSearch = name.includes(searchVal);
            const matchesCat = (activeCategory === 'all' || cat === activeCategory);
            
            if (matchesSearch && matchesCat) {
                card.style.display = 'flex';
            } else {
                card.style.display = 'none';
            }
        });
    }

    function filterCatalogCategory(category, btn) {
        activeCategory = category;
        
        const buttons = btn.parentElement.querySelectorAll('button');
        buttons.forEach(b => {
            b.style.background = 'transparent';
            b.style.color = 'var(--text-secondary)';
            b.classList.remove('active-cat-btn');
        });
        
        btn.style.background = 'var(--primary)';
        btn.style.color = '#fff';
        btn.classList.add('active-cat-btn');
        
        filterCatalog();
    }
</script>

@if(session('success_order_id'))
<div id="successOrderModal" class="modal-backdrop active" onclick="closeModal('successOrderModal')">
    <div class="modal-box" onclick="event.stopPropagation()" style="max-width: 480px; text-align: center; padding: 30px;">
        <div style="font-size: 3.5rem; color: #10b981; margin-bottom: 15px;">🎉</div>
        <h3 style="font-size: 1.4rem; font-weight: 900; color: var(--text); margin-bottom: 10px;">Pesanan Berhasil Disimpan!</h3>
        <p style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 25px; line-height: 1.5;">
            Data pesanan telah berhasil disimpan dan nota digital telah dibuat secara otomatis.
        </p>
        
        <div style="display: flex; flex-direction: column; gap: 10px;">
            <a href="/customer/orders/{{ session('success_order_id') }}/receipt" target="_blank" class="btn-primary-custom" style="width: 100%; justify-content: center; padding: 12px; font-size: 0.95rem; text-decoration: none;">
                🖨️ Cetak Nota (Print)
            </a>
            
            @if(session('success_customer_phone'))
            @php
                $waPhone = preg_replace('/\D/', '', session('success_customer_phone'));
                if (str_starts_with($waPhone, '0')) {
                    $waPhone = '62' . substr($waPhone, 1);
                }
            @endphp
            <a href="https://api.whatsapp.com/send?phone={{ $waPhone }}&text={{ urlencode(session('success_whatsapp_message')) }}" target="_blank" class="btn" style="width: 100%; background: #25d366; color: #fff; border: none; padding: 12px; border-radius: 12px; font-weight: 800; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; gap: 6px; text-decoration: none; font-size: 0.95rem;">
                💬 Kirim Nota via WhatsApp
            </a>
            @endif
            
            <button onclick="closeModal('successOrderModal')" class="btn" style="width: 100%; background: var(--surface-variant); color: var(--text); border: 1.5px solid var(--border-color); padding: 12px; border-radius: 12px; font-weight: 800; cursor: pointer;">
                Tutup Halaman Ini
            </button>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Automatically open receipt print page in a new tab
        window.open('/customer/orders/{{ session('success_order_id') }}/receipt', '_blank');
    });
</script>
@endif

@endsection
