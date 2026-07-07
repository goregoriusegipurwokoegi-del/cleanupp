@extends('layouts.employee-dashboard')

@section('page_title', request('queue') ? (request('category') == 'cleaning' ? 'Monitor Antrian - Cuci' : (request('category') == 'repair' ? 'Monitor Antrian - Reparasi' : 'Monitor Antrian')) : (request('delivery') ? 'Antar Jemput' : 'Orderan Masuk'))

@section('content')
<style>
    @media (max-width: 576px) {
        #createOrderModal .modal-body {
            padding: 12px !important;
        }
        #createOrderModal .border.rounded-4.p-4 {
            padding: 14px !important;
            border-radius: 12px !important;
        }
        #createOrderModal .rounded-4.p-4 {
            padding: 14px !important;
            border-radius: 12px !important;
        }
        #createOrderModal .p-3 {
            padding: 10px !important;
        }
        #createOrderModal .px-4.py-4 {
            padding: 16px 16px !important;
        }
        #createOrderModal h5.fw-bold {
            font-size: 1.15rem !important;
        }
        #createOrderModal p.text-muted {
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
            border: 1.5px solid #dee2e6 !important;
            background: #fff !important;
        }
        #createOrderModal.modal {
            padding: 0 !important;
        }
        #createOrderModal .modal-dialog {
            margin: 0 !important;
            width: 100% !important;
            max-width: 100% !important;
            height: 100vh !important;
        }
        #createOrderModal .modal-content {
            height: 100% !important;
            border-radius: 0 !important;
            min-height: 100vh !important;
        }
    }
    .emp-qty-input-item::-webkit-outer-spin-button,
    .emp-qty-input-item::-webkit-inner-spin-button {
      -webkit-appearance: none;
      margin: 0;
    }
    .emp-qty-input-item[type=number] {
      -moz-appearance: textfield;
    }
</style>
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <p class="text-secondary mb-0">
            @if(request('queue'))
                @if(request('category') == 'cleaning')
                    Pantau antrian aktif cuci sepatu. Pesanan selesai otomatis disembunyikan.
                @elseif(request('category') == 'repair')
                    Pantau antrian aktif reparasi sepatu. Pesanan selesai otomatis disembunyikan.
                @else
                    Pantau pesanan aktif. Pesanan selesai otomatis disembunyikan.
                @endif
            @elseif(request('delivery'))
                Kelola dan perbarui status antar jemput sepatu pelanggan.
            @else
                Kelola dan validasi orderan masuk dari pelanggan.
            @endif
        </p>
    </div>
    @if(!request('queue'))
    <button onclick="openCreateModal()" class="btn btn-primary fw-bold px-4 shadow-sm">
        <i class="bi bi-plus-lg me-1"></i> Tambah Pesanan
    </button>
    @endif
</div>

@if($errors->any())
    <div class="alert alert-danger shadow-sm mb-4">
        <strong class="d-block mb-1">⚠️ Gagal Menyimpan Pesanan:</strong>
        <ul class="mb-0 ps-3 small">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if(session('success'))
    <div class="alert alert-success shadow-sm mb-4">
        {{ session('success') }}
    </div>
@endif

<!-- Main Orders List Table -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body p-0">
        <!-- Desktop Table View -->
        <div class="table-responsive d-none d-md-block">
            <table class="table table-hover table-striped align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="py-3 px-4 text-secondary text-uppercase fw-bold" style="font-size: 0.75rem; width: 100px;">Antrian</th>
                        <th class="py-3 px-4 text-secondary text-uppercase fw-bold" style="font-size: 0.75rem; width: 180px;">Pelanggan</th>
                        <th class="py-3 px-4 text-secondary text-uppercase fw-bold" style="font-size: 0.75rem; width: 220px;">Sepatu</th>
                        <th class="py-3 px-4 text-secondary text-uppercase fw-bold" style="font-size: 0.75rem; width: 180px;">Layanan</th>
                        <th class="py-3 px-4 text-secondary text-uppercase fw-bold" style="font-size: 0.75rem; width: 180px;">Status Pengerjaan</th>
                        <th class="py-3 px-4 text-secondary text-uppercase fw-bold" style="font-size: 0.75rem; width: 150px;">Tanggal Masuk</th>
                        <th class="py-3 px-4 text-secondary text-uppercase fw-bold" style="font-size: 0.75rem; width: 180px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $group)
                        @php
                            $order = $group->first();
                        @endphp
                        <tr>
                            <td class="px-4">
                                @if($loop->first && in_array($order->status, ['pending', 'processing', 'washing', 'finishing']))
                                    <div class="mb-2">
                                        <span class="badge shadow-sm" style="background-color: #dc3545; color: #fff; font-size: 0.65rem; padding: 4px 8px;">
                                            <i class="bi bi-exclamation-triangle-fill me-1"></i> PRIORITAS UTAMA
                                        </span>
                                    </div>
                                @endif
                                <span class="badge bg-primary fs-6 px-3 py-1 font-monospace shadow-sm">
                                    @foreach($group->pluck('queue_number')->unique() as $qNum)
                                        {{ $qNum }}{{ !$loop->last ? ',' : '' }}
                                    @endforeach
                                </span>
                            </td>
                            <td class="px-4">
                                <div class="fw-bold text-dark mb-1" style="font-size: 0.95rem;">
                                    {{ $order->user->name }}
                                </div>
                                @if($order->is_delivery)
                                    <span class="badge bg-warning text-dark" style="font-size: 0.65rem;"><i class="bi bi-truck me-1"></i> Antar Jemput</span>
                                    @if($order->latitude && $order->longitude)
                                        <div class="mt-1">
                                            <a href="https://www.google.com/maps?q={{ $order->latitude }},{{ $order->longitude }}" target="_blank" class="badge bg-primary text-white text-decoration-none" style="font-size: 0.65rem;"><i class="bi bi-geo-alt-fill me-1"></i> Lihat Lokasi</a>
                                        </div>
                                    @endif
                                @endif
                            </td>
                            <td class="px-4">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="border rounded bg-light" style="width: 44px; height: 44px; overflow: hidden; flex-shrink: 0;">
                                        <img src="{{ asset('storage/' . $order->photo_before) }}" style="width: 100%; height: 100%; object-fit: cover;">
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark" style="font-size: 0.9rem; text-transform: capitalize;">{{ $order->shoe_name }}</div>
                                        <div class="text-secondary small">Ukuran: {{ $order->shoe_size }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4">
                                @foreach($group as $grpItem)
                                    <div class="mb-2">
                                        <div class="fw-semibold text-dark" style="font-size: 0.88rem;">{{ $grpItem->service->name }}</div>
                                        <span class="badge {{ $grpItem->service->category == 'cleaning' ? 'badge-success' : 'badge-warning' }}" style="font-size: 0.65rem; padding: 2px 6px;">
                                            {{ $grpItem->service->category == 'cleaning' ? 'Cuci' : 'Reparasi' }}
                                        </span>
                                    </div>
                                @endforeach
                            </td>
                            <td class="px-4">
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
                                        'processing' => '#6c757d',
                                        'washing' => '#0dcaf0',
                                        'finishing' => '#6f42c1',
                                        'ready' => '#198754',
                                        'dikirim' => '#fd7e14',
                                        'uncollected' => '#0d6efd',
                                        'completed' => '#198754',
                                        'cancelled' => '#dc3545'
                                    ];
                                    $currentColor = $colors[$order->status] ?? '#6c757d';
                                    $currentLabel = $statusLabels[$order->status] ?? strtoupper($order->status);
                                @endphp
                                <span class="badge fs-7 px-3 py-2 text-white" style="background-color: {{ $currentColor }};">
                                    {{ $currentLabel }}
                                </span>
                                <div class="mt-2">
                                    @if($order->payment_status == 'paid')
                                        <span class="badge badge-success" style="font-size: 0.68rem; padding: 3px 8px;">LUNAS</span>
                                    @else
                                        <span class="badge badge-warning" style="font-size: 0.68rem; padding: 3px 8px;">BELUM BAYAR</span>
                                    @endif
                                    @if($order->payment_proof)
                                        <div class="mt-1">
                                            <a href="{{ asset('storage/' . $order->payment_proof) }}" target="_blank" class="badge bg-primary text-white text-decoration-none" style="font-size: 0.65rem; padding: 3px 8px;"><i class="bi bi-file-earmark-image me-1"></i>Bukti Transfer</a>
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 text-secondary small">
                                {{ $order->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-4">
                                @if($order->status == 'pending')
                                    <div class="d-flex flex-column gap-1">
                                        <div class="d-flex gap-1">
                                            <form action="{{ route('orders.status.update', $order) }}" method="POST" style="flex: 1;">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="processing">
                                                <button type="submit" class="btn btn-success btn-sm w-100 fw-bold py-1">Terima</button>
                                            </form>
                                            <form action="{{ route('orders.status.update', $order) }}" method="POST" style="flex: 1;">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="cancelled">
                                                <button type="submit" onclick="return confirm('Apakah Anda yakin ingin menolak pesanan ini?')" class="btn btn-danger btn-sm w-100 fw-bold py-1">Tolak</button>
                                            </form>
                                        </div>
                                        <button onclick='openEditModal(@json($order))' class="btn btn-outline-primary btn-sm w-100 fw-bold">Edit</button>
                                        <a href="{{ route('orders.receipt', $order->id) }}" target="_blank" class="btn btn-outline-secondary btn-sm w-100 fw-bold text-decoration-none text-center">Cetak Struk</a>
                                    </div>
                                @else
                                    <div class="d-flex flex-column gap-1">
                                        @php
                                            $nextStatus = null;
                                            $nextLabel = '';
                                            $btnClass = 'btn-primary';

                                            if ($order->service->category == 'cleaning') {
                                                if ($order->status == 'processing') { $nextStatus = 'washing'; $nextLabel = 'Mulai Cuci'; }
                                                elseif ($order->status == 'washing') { $nextStatus = 'finishing'; $nextLabel = 'Ke Jemur'; }
                                                elseif ($order->status == 'finishing') { $nextStatus = 'ready'; $nextLabel = 'Ke Siap'; }
                                                elseif ($order->status == 'ready') { 
                                                    if ($order->is_delivery) { $nextStatus = 'dikirim'; $nextLabel = 'Ke Kirim'; $btnClass = 'btn-warning text-dark'; }
                                                    else { $nextStatus = 'completed'; $nextLabel = 'Ke Selesai'; $btnClass = 'btn-success'; }
                                                }
                                                elseif ($order->status == 'dikirim') { $nextStatus = 'completed'; $nextLabel = 'Ke Selesai'; $btnClass = 'btn-success'; }
                                            } else {
                                                if ($order->status == 'processing') { $nextStatus = 'washing'; $nextLabel = 'Mulai Kerja'; }
                                                elseif ($order->status == 'washing') { $nextStatus = 'finishing'; $nextLabel = 'Ke Finishing'; }
                                                elseif ($order->status == 'finishing') { $nextStatus = 'ready'; $nextLabel = 'Ke Siap'; }
                                                elseif ($order->status == 'ready') { 
                                                    if ($order->is_delivery) { $nextStatus = 'dikirim'; $nextLabel = 'Ke Kirim'; $btnClass = 'btn-warning text-dark'; }
                                                    else { $nextStatus = 'completed'; $nextLabel = 'Ke Selesai'; $btnClass = 'btn-success'; }
                                                }
                                                elseif ($order->status == 'dikirim') { $nextStatus = 'completed'; $nextLabel = 'Ke Selesai'; $btnClass = 'btn-success'; }
                                            }
                                        @endphp

                                        @if($nextStatus)
                                            <form action="{{ route('orders.status.update', $order) }}" method="POST" style="margin: 0;">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="{{ $nextStatus }}">
                                                <button type="submit" class="btn {{ $btnClass }} btn-sm w-100 fw-bold py-1">{{ $nextLabel }}</button>
                                            </form>
                                        @endif
                                        @if($order->status == 'ready')
                                            <button onclick='openUploadPhotoModal({{ $order->id }}, "{{ $order->photo_after ? asset('storage/' . $order->photo_after) : '' }}")' class="btn btn-outline-info btn-sm w-100 fw-bold py-1">Upload Foto Sesudah</button>
                                        @endif
                                        <a href="{{ route('orders.receipt', $order->id) }}" target="_blank" class="btn btn-outline-dark btn-sm w-100 fw-bold py-1 text-decoration-none text-center">Cetak Struk</a>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View Layout -->
        <div class="p-3 d-md-none">
            @foreach($orders as $group)
                @php
                    $order = $group->first();
                    
                    // Status styling logic
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
                        'processing' => '#6c757d',
                        'washing' => '#0dcaf0',
                        'finishing' => '#6f42c1',
                        'ready' => '#198754',
                        'dikirim' => '#fd7e14',
                        'uncollected' => '#0d6efd',
                        'completed' => '#198754',
                        'cancelled' => '#dc3545'
                    ];
                    $currentColor = $colors[$order->status] ?? '#6c757d';
                    $currentLabel = $statusLabels[$order->status] ?? strtoupper($order->status);
                @endphp
                
                <div class="card border shadow-sm mb-3">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center py-2 px-3">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-primary fs-6 px-3 py-1 font-monospace">
                                @foreach($group->pluck('queue_number')->unique() as $qNum)
                                    {{ $qNum }}{{ !$loop->last ? ',' : '' }}
                                @endforeach
                            </span>
                            @if($order->is_delivery)
                                <span class="badge bg-warning text-dark"><i class="bi bi-truck me-1"></i> Delivery</span>
                                @if($order->latitude && $order->longitude)
                                    <a href="https://www.google.com/maps?q={{ $order->latitude }},{{ $order->longitude }}" target="_blank" class="badge bg-primary text-white text-decoration-none ms-1"><i class="bi bi-geo-alt-fill me-1"></i> Lokasi</a>
                                @endif
                            @endif
                        </div>
                        <small class="text-secondary fw-semibold">{{ $order->created_at->format('d/m/Y H:i') }}</small>
                    </div>
                    <div class="card-body p-3">
                        <div class="mb-3">
                            <div class="text-secondary small fw-bold text-uppercase">Pelanggan</div>
                            <div class="fw-bold text-dark fs-5">{{ $order->user->name }}</div>
                        </div>
                        
                        <div class="mb-3 border-top pt-2">
                            <div class="text-secondary small fw-bold text-uppercase">Sepatu</div>
                            <div class="d-flex align-items-center gap-2 mt-1">
                                <div class="border rounded bg-light" style="width: 48px; height: 48px; overflow: hidden; flex-shrink: 0;">
                                    <img src="{{ asset('storage/' . $order->photo_before) }}" style="width: 100%; height: 100%; object-fit: cover;">
                                </div>
                                <div>
                                    <div class="fw-bold text-dark text-capitalize">{{ $order->shoe_name }}</div>
                                    <div class="text-secondary small">Ukuran: {{ $order->shoe_size }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3 border-top pt-2">
                            <div class="text-secondary small fw-bold text-uppercase">Layanan & Kategori</div>
                            @foreach($group as $grpItem)
                                <div class="d-flex justify-content-between align-items-center mt-2 bg-light p-2 rounded">
                                    <span class="fw-semibold text-dark small">{{ $grpItem->service->name }}</span>
                                    <span class="badge {{ $grpItem->service->category == 'cleaning' ? 'bg-success text-white' : 'bg-warning text-dark' }}" style="font-size: 0.65rem;">
                                        {{ $grpItem->service->category == 'cleaning' ? 'Cuci' : 'Reparasi' }}
                                    </span>
                                </div>
                            @endforeach
                        </div>

                        <div class="border-top pt-2 d-flex flex-column gap-2">
                            <div>
                                <span class="badge fs-7 px-3 py-2 text-white" style="background-color: {{ $currentColor }};">{{ $currentLabel }}</span>
                                <span class="badge {{ $order->payment_status == 'paid' ? 'bg-success' : 'bg-warning text-dark' }} border ms-1 py-2 px-3">{{ $order->payment_status == 'paid' ? 'LUNAS' : 'BELUM BAYAR' }}</span>
                            </div>
                            @if($order->payment_proof)
                                <div>
                                    <a href="{{ asset('storage/' . $order->payment_proof) }}" target="_blank" class="badge bg-primary text-white text-decoration-none border py-2 px-3"><i class="bi bi-file-earmark-image me-1"></i>Bukti Transfer</a>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Action Buttons for Mobile -->
                    <div class="card-footer bg-transparent border-top p-3">
                        @if($order->status == 'pending')
                            <div class="d-flex flex-column gap-2">
                                <div class="d-flex gap-2">
                                    <form action="{{ route('orders.status.update', $order) }}" method="POST" style="flex: 1; margin: 0;">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="processing">
                                        <button type="submit" class="btn btn-success btn-sm w-100 fw-bold py-2">Terima</button>
                                    </form>
                                    <form action="{{ route('orders.status.update', $order) }}" method="POST" style="flex: 1; margin: 0;">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="cancelled">
                                        <button type="submit" onclick="return confirm('Apakah Anda yakin ingin menolak pesanan ini?')" class="btn btn-danger btn-sm w-100 fw-bold py-2">Tolak</button>
                                    </form>
                                </div>
                                <button onclick='openEditModal(@json($order))' class="btn btn-outline-primary btn-sm w-100 fw-bold py-2">Edit</button>
                                <a href="{{ route('orders.receipt', $order->id) }}" target="_blank" class="btn btn-outline-secondary btn-sm w-100 fw-bold py-2 text-decoration-none text-center">Cetak Struk</a>
                            </div>
                        @else
                            <div class="d-flex flex-column gap-2">
                                @php
                                    $nextStatus = null;
                                    $nextLabel = '';
                                    $btnClass = 'btn-primary';

                                    if ($order->service->category == 'cleaning') {
                                        if ($order->status == 'processing') { $nextStatus = 'washing'; $nextLabel = 'Mulai Cuci'; }
                                        elseif ($order->status == 'washing') { $nextStatus = 'finishing'; $nextLabel = 'Ke Jemur'; }
                                        elseif ($order->status == 'finishing') { $nextStatus = 'ready'; $nextLabel = 'Ke Siap'; }
                                        elseif ($order->status == 'ready') { 
                                            if ($order->is_delivery) { $nextStatus = 'dikirim'; $nextLabel = 'Ke Kirim'; $btnClass = 'btn-warning text-dark'; }
                                            else { $nextStatus = 'completed'; $nextLabel = 'Ke Selesai'; $btnClass = 'btn-success'; }
                                        }
                                        elseif ($order->status == 'dikirim') { $nextStatus = 'completed'; $nextLabel = 'Ke Selesai'; $btnClass = 'btn-success'; }
                                    } else {
                                        if ($order->status == 'processing') { $nextStatus = 'washing'; $nextLabel = 'Mulai Kerja'; }
                                        elseif ($order->status == 'washing') { $nextStatus = 'finishing'; $nextLabel = 'Ke Finishing'; }
                                        elseif ($order->status == 'finishing') { $nextStatus = 'ready'; $nextLabel = 'Ke Siap'; }
                                        elseif ($order->status == 'ready') { 
                                            if ($order->is_delivery) { $nextStatus = 'dikirim'; $nextLabel = 'Ke Kirim'; $btnClass = 'btn-warning text-dark'; }
                                            else { $nextStatus = 'completed'; $nextLabel = 'Ke Selesai'; $btnClass = 'btn-success'; }
                                        }
                                        elseif ($order->status == 'dikirim') { $nextStatus = 'completed'; $nextLabel = 'Ke Selesai'; $btnClass = 'btn-success'; }
                                    }
                                @endphp

                                @if($nextStatus)
                                    <form action="{{ route('orders.status.update', $order) }}" method="POST" style="margin: 0;">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="{{ $nextStatus }}">
                                        <button type="submit" class="btn {{ $btnClass }} btn-sm w-100 fw-bold py-2">{{ $nextLabel }}</button>
                                    </form>
                                @endif
                                @if($order->status == 'ready')
                                    <button onclick='openUploadPhotoModal({{ $order->id }}, "{{ $order->photo_after ? asset('storage/' . $order->photo_after) : '' }}")' class="btn btn-outline-info btn-sm w-100 fw-bold py-2">Upload Foto Sesudah</button>
                                @endif
                                <a href="{{ route('orders.receipt', $order->id) }}" target="_blank" class="btn btn-outline-dark btn-sm w-100 fw-bold py-2 text-decoration-none text-center">Cetak Struk</a>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>


<!-- Modal Tambah Pesanan (Layout 2 Kolom - Sama Seperti Admin) -->
<div class="modal fade" id="createOrderModal" tabindex="-1" aria-labelledby="createOrderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 1350px; width: 95%;">
        <form action="{{ route('employee.orders.store') }}" method="POST" enctype="multipart/form-data" id="empCreateOrderForm" class="modal-content border-0 overflow-hidden" style="border-radius: 24px; box-shadow: 0 20px 50px rgba(0,0,0,0.15); margin: 0;">
            @csrf

            {{-- Header --}}
            <div class="bg-light px-4 py-4 border-bottom">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-bold mb-0" style="letter-spacing: -0.3px; font-size: 1.3rem;">Tambah <span class="text-primary">Pesanan Baru</span></h5>
                        <p class="text-muted mb-0" style="font-size: 0.8rem;">Form pembuatan invoice transaksi pelanggan oleh Karyawan</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>

            <div class="modal-body p-4">
                    <div class="row g-4">

                        {{-- COLUMN 1: POS Catalog --}}
                        <div class="col-xl-3 col-lg-4" style="max-height: 75vh; overflow-y: auto;">
                            <div class="border rounded-4 p-4" style="box-shadow: 0 4px 12px rgba(0,0,0,0.02); display: flex; flex-direction: column; gap: 16px; background: #fff;">
                                <div class="fw-bold text-primary text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.8px;">Katalog Layanan</div>
                                
                                <!-- Search Bar -->
                                <div style="position: relative;">
                                    <input type="text" id="emp-catalog-search" oninput="empFilterCatalog()" placeholder="Cari layanan..." class="form-control" style="border-radius: 10px; font-size: 0.85rem; padding: 10px 12px;">
                                </div>
                                
                                <!-- Category Tabs -->
                                <div style="display: flex; gap: 6px; flex-wrap: wrap;">
                                    <button type="button" class="btn btn-sm active-cat-btn btn-primary" onclick="empFilterCatalogCategory('all', this)" style="border-radius: 8px; font-size: 0.72rem; font-weight: 700; transition: 0.2s;">Semua</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="empFilterCatalogCategory('cleaning', this)" style="border-radius: 8px; font-size: 0.72rem; font-weight: 700; transition: 0.2s;">Cuci</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="empFilterCatalogCategory('repair', this)" style="border-radius: 8px; font-size: 0.72rem; font-weight: 700; transition: 0.2s;">Reparasi</button>
                                </div>
                                
                                <!-- Services List -->
                                <div id="emp-catalog-services-list" style="display: flex; flex-direction: column; gap: 12px; overflow-y: auto; padding-right: 4px;">
                                    @foreach($services as $service)
                                        <div class="emp-pos-service-card border rounded-3 p-3 bg-light d-flex align-items-center justify-content-between gap-2" data-id="{{ $service->id }}" data-name="{{ strtolower($service->name) }}" data-category="{{ $service->category }}" style="transition: all 0.2s ease;">
                                            <div style="flex-grow: 1;">
                                                <div class="fw-bold text-dark" style="font-size: 0.85rem; line-height: 1.3;">{{ $service->name }}</div>
                                                <div class="fw-bold text-primary small mt-1">Rp {{ number_format($service->price, 0, ',', '.') }}</div>
                                                <div class="text-muted mt-1" style="font-size: 0.65rem;">Estimasi: {{ $service->estimated_time ?: '2-3 Hari' }}</div>
                                            </div>
                                            <div class="d-flex align-items-center gap-2 flex-shrink-0">
                                                <div class="input-group" style="width: 80px; height: 32px; overflow: hidden; border-radius: 6px;">
                                                    <button type="button" class="btn btn-sm btn-outline-secondary px-2 py-0" onclick="empDecreaseCatalogQty(this)" style="font-weight: bold; font-size: 0.8rem;">-</button>
                                                    <input type="number" class="form-control form-control-sm text-center emp-catalog-qty-input p-0" value="1" min="1" style="font-weight: 700; font-size: 0.8rem; -moz-appearance: textfield; appearance: textfield; outline: none; border-left: none; border-right: none;">
                                                    <button type="button" class="btn btn-sm btn-outline-secondary px-2 py-0" onclick="empIncreaseCatalogQty(this)" style="font-weight: bold; font-size: 0.8rem;">+</button>
                                                </div>
                                                <button type="button" onclick="empAddServiceToOrder({{ $service->id }}, this)" class="btn btn-sm btn-primary py-1 px-3 fw-bold" style="font-size: 0.72rem; border-radius: 6px; margin: 0;">
                                                    Pilih
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        {{-- COLUMN 2: Pelanggan & Sepatu --}}
                        <div class="col-xl-5 col-lg-4 d-flex flex-column gap-4">

                            {{-- Section 1: Pelanggan --}}
                            <div class="border rounded-4 p-4" style="box-shadow: 0 4px 12px rgba(0,0,0,0.02);">
                                <div class="fw-bold text-primary text-uppercase mb-3" style="font-size: 0.75rem; letter-spacing: 0.8px;">Data Pelanggan</div>

                                {{-- Info Invoice --}}
                                <div class="modal-invoice-info-box d-flex justify-content-between align-items-center p-3 rounded-3 mb-4" style="background: rgba(13,110,253,0.03); font-size: 0.78rem; border: 1px dashed rgba(13,110,253,0.15);">
                                    <div>
                                        <span class="text-muted">No. Invoice:</span>
                                        <span class="fw-bold text-primary ms-1">Otomatis</span>
                                    </div>
                                    <div>
                                        <span class="text-muted">Waktu:</span>
                                        <span class="fw-bold ms-1">{{ now()->format('d M Y, H:i') }}</span>
                                    </div>
                                </div>

                                {{-- Segmented Toggle --}}
                                <div class="modal-segmented-toggle d-flex bg-light p-1 rounded-3 mb-4 border">
                                    <button type="button" id="emp-customer-type-existing-btn" onclick="empSetCustomerType('existing')" class="btn btn-sm py-2 fw-bold flex-fill text-primary bg-white shadow-sm" style="border-radius: 8px; font-size: 0.8rem; border: none;">Pelanggan Terdaftar</button>
                                    <button type="button" id="emp-customer-type-new-btn" onclick="empSetCustomerType('new')" class="btn btn-sm py-2 fw-bold flex-fill text-muted" style="border-radius: 8px; font-size: 0.8rem; border: none; background: transparent;">Registrasi Baru</button>
                                </div>
                                <div style="display: none;">
                                    <input type="radio" name="customer_type" id="emp_customer_type_existing_radio" value="existing" checked onchange="empToggleCustomerType()">
                                    <input type="radio" name="customer_type" id="emp_customer_type_new_radio" value="new" onchange="empToggleCustomerType()">
                                </div>

                                {{-- Existing Customer --}}
                                <div id="emp-existing-customer-section">
                                    <label class="form-label fw-bold text-secondary text-uppercase" style="font-size: 0.72rem;">Pilih Pelanggan</label>
                                    <select name="user_id" id="emp_user_id" required class="form-select" style="border-radius: 10px; padding: 12px; font-size: 0.9rem;" onchange="empUpdateReceiptPreview()">
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

                                {{-- New Customer --}}
                                <div id="emp-new-customer-section" style="display: none;">
                                    <div class="row g-3 mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold text-secondary text-uppercase" style="font-size: 0.72rem;">Nama Lengkap</label>
                                            <input type="text" name="new_customer_name" id="emp_new_customer_name" placeholder="Contoh: Budi Santoso" class="form-control" style="border-radius: 10px;" oninput="empUpdateReceiptPreview()">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold text-secondary text-uppercase" style="font-size: 0.72rem;">Nomor WhatsApp</label>
                                            <input type="text" name="new_customer_phone" id="emp_new_customer_phone" placeholder="Contoh: 08123456789" class="form-control" style="border-radius: 10px;" oninput="empUpdateReceiptPreview()">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="form-label fw-bold text-secondary text-uppercase" style="font-size: 0.72rem;">Alamat Rumah (Opsional)</label>
                                        <textarea name="new_customer_address" id="emp_new_customer_address" placeholder="Contoh: Jl. Sudirman No. 24" class="form-control" style="height: 60px; resize: none; border-radius: 10px;" oninput="empUpdateReceiptPreview()"></textarea>
                                    </div>
                                </div>
                            </div>

                            {{-- Section 2: Sepatu --}}
                            <div class="border rounded-4 p-4" style="box-shadow: 0 4px 12px rgba(0,0,0,0.02);">
                                <div class="fw-bold text-primary text-uppercase mb-3" style="font-size: 0.75rem; letter-spacing: 0.8px;">Item Sepatu & Layanan</div>

                                <div id="emp-shoe-items-container"></div>

                                <button type="button" onclick="empAddNewShoeRow()" class="btn fw-bold w-100 py-2 mb-4" style="background: #f8f9fa; color: #0d6efd; border: 1.5px dashed #0d6efd; border-radius: 12px; font-size: 0.85rem;">
                                    Tambah Item Sepatu Baru
                                </button>


                            </div>

                        </div>

                        {{-- RIGHT COLUMN: Pengiriman, Pembayaran & Total --}}
                        <div class="col-xl-4 col-lg-4 d-flex flex-column gap-4">

                            {{-- Section 3: Pengiriman & Pembayaran --}}
                            <div class="border rounded-4 p-4" style="box-shadow: 0 4px 12px rgba(0,0,0,0.02);">
                                <div class="fw-bold text-primary text-uppercase mb-4" style="font-size: 0.75rem; letter-spacing: 0.8px;">Pengiriman & Pembayaran</div>

                                <div class="row g-3 mb-3">
                                    <div class="col-12">
                                        <label class="form-label fw-bold text-secondary text-uppercase" style="font-size: 0.72rem;">Metode Pengantaran</label>
                                        <select name="delivery_method" id="emp_delivery_method" required class="form-select" style="border-radius: 10px;" onchange="empToggleDeliverySection()">
                                            <option value="self">Antar Sendiri (Drop-off)</option>
                                            <option value="courier">Dijemput Kurir (Delivery)</option>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-bold text-secondary text-uppercase" style="font-size: 0.72rem;">Diskon Manual (Rp)</label>
                                        <input type="number" name="discount" id="emp_discount_input" value="0" min="0" placeholder="0" class="form-control" style="border-radius: 10px;" oninput="empCalculateTotals()">
                                    </div>
                                </div>

                                {{-- Delivery Address --}}
                                <div id="emp_delivery_address_section" style="display: none; margin-bottom: 14px; background: #f8f9fa; padding: 12px; border-radius: 12px; border: 1px solid #dee2e6;">
                                    <label class="form-label fw-bold text-secondary text-uppercase" style="font-size: 0.72rem;">Alamat Penjemputan</label>
                                    <textarea name="delivery_address" id="emp_delivery_address_input" placeholder="Masukkan alamat lengkap..." class="form-control" style="height: 50px; resize: none; border-radius: 8px;"></textarea>
                                </div>

                                <div class="row g-3 mb-3">
                                    <div class="col-12">
                                        <label class="form-label fw-bold text-secondary text-uppercase" style="font-size: 0.72rem;">Metode Pembayaran</label>
                                        <select name="payment_method" id="emp_payment_method_input" required class="form-select" style="border-radius: 10px;" onchange="empTogglePaymentStatusSection()">
                                            <option value="cash">Tunai (Cash)</option>
                                            <option value="transfer">Transfer Bank</option>
                                            <option value="qris">QRIS</option>
                                            <option value="deferred">Belum Bayar</option>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-bold text-secondary text-uppercase" style="font-size: 0.72rem;">Status Pembayaran</label>
                                        <select name="payment_status" id="emp_payment_status_input" required class="form-select" style="border-radius: 10px;" onchange="empTogglePaymentStatusSection()">
                                            <option value="unpaid">Belum Lunas</option>
                                            <option value="paid">Lunas</option>
                                        </select>
                                    </div>
                                </div>

                                {{-- Cash Received --}}
                                <div id="emp_cash_received_section" style="margin-bottom: 14px; background: rgba(25,135,84,0.05); padding: 12px; border-radius: 12px; border: 1.5px dashed rgba(25,135,84,0.25);">
                                    <label class="form-label fw-bold text-secondary text-uppercase" style="font-size: 0.72rem;">Uang Tunai Diterima</label>
                                    <input type="number" name="cash_amount" id="emp_cash_received_input" placeholder="0" class="form-control" style="border-radius: 8px;" oninput="empCalculateTotals()">
                                </div>

                                <input type="hidden" name="status" value="processing">
                            </div>

                            {{-- Section 4: Rincian Biaya & Submit --}}
                            <div class="rounded-4 p-4" style="background: #f8f9fa; border: 1.5px solid #dee2e6;">
                                <div class="fw-bold text-secondary text-uppercase mb-3" style="font-size: 0.72rem; letter-spacing: 0.8px;">RINCIAN PEMBAYARAN & BIAYA</div>

                                <div class="p-3 rounded-3 mb-4 font-monospace" style="border: 1px dashed #dee2e6; background: #fff; font-size: 0.75rem;">
                                    <div class="text-center border-bottom border-dashed pb-2 mb-2 fw-bold">*** PREVIEW NOTA PEMESANAN ***</div>

                                    <div class="mb-2" style="line-height: 1.4;">
                                        <div><strong>Pelanggan:</strong> <span id="emp_receipt_cust_name">-</span></div>
                                        <div><strong>No. Telp :</strong> <span id="emp_receipt_cust_phone">-</span></div>
                                        <div style="white-space: pre-wrap;"><strong>Alamat   :</strong> <span id="emp_receipt_cust_address">-</span></div>
                                    </div>

                                    <div style="border-top: 1px dashed #dee2e6; border-bottom: 1px dashed #dee2e6; padding: 8px 0; margin-bottom: 8px;">
                                        <div class="fw-bold mb-2">ITEM SEPATU:</div>
                                        <div id="emp_receipt_items_list" style="display: flex; flex-direction: column; gap: 6px;">
                                            <div class="text-muted" style="font-style: italic;">Belum ada item sepatu.</div>
                                        </div>
                                    </div>

                                    <div style="display: flex; flex-direction: column; gap: 6px;">
                                        <div class="d-flex justify-content-between">
                                            <span>Subtotal</span>
                                            <span id="emp_preview_subtotal" class="fw-bold">Rp 0</span>
                                        </div>
                                        <div style="display: none; justify-content: space-between;" id="emp_row_delivery_fee">
                                            <span>Ongkos Jemput</span>
                                            <span id="emp_preview_delivery_fee" class="fw-bold">Rp 0</span>
                                        </div>
                                        <div style="display: none; justify-content: space-between; color: #ef4444;" id="emp_row_discount">
                                            <span>Potongan Diskon</span>
                                            <span id="emp_preview_discount" class="fw-bold">-Rp 0</span>
                                        </div>
                                        <div style="border-top: 1px dashed #dee2e6; padding-top: 6px; display: flex; justify-content: space-between; align-items: center; font-weight: 900; font-size: 0.85rem;">
                                            <span>GRAND TOTAL</span>
                                            <span id="emp_preview_grand_total" class="text-primary">Rp 0</span>
                                        </div>
                                        <div style="display: none; justify-content: space-between; color: #198754; border-top: 1px dashed #dee2e6; padding-top: 6px;" id="emp_row_cash_change">
                                            <span>Kembalian</span>
                                            <span id="emp_preview_cash_change" class="fw-bold">Rp 0</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-3 border-top pt-3">
                                    <button type="submit" class="btn btn-primary fw-bold w-100 py-3" style="border-radius: 12px; font-weight: 800; font-size: 0.95rem; box-shadow: 0 4px 15px rgba(13,110,253,0.2);">Checkout</button>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

        </form>
    </div>
</div>
</div>

<div class="modal fade" id="editOrderModal" tabindex="-1" aria-labelledby="editOrderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <form id="editOrderForm" method="POST" enctype="multipart/form-data" class="modal-content" style="margin: 0;">
            @csrf
            @method('PUT')
            <div class="modal-header bg-secondary text-white">
                <h5 class="modal-title fw-bold" id="editOrderModalLabel">Edit Detail Pesanan</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-secondary text-uppercase small">Layanan</label>
                            <select name="service_id" id="edit_service_id" required class="form-select">
                                @foreach($services as $serv)
                                    <option value="{{ $serv->id }}">{{ $serv->name }} (Rp{{ number_format($serv->price, 0, ',', '.') }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-secondary text-uppercase small">Nomor Antrian</label>
                            <input type="text" name="queue_number" id="edit_queue_number" required class="form-control">
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-secondary text-uppercase small">Kecepatan</label>
                            <select name="processing_speed" id="edit_processing_speed" required class="form-select">
                                <option value="regular">Regular</option>
                                <option value="express">Express</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-secondary text-uppercase small">Nama Sepatu</label>
                            <input type="text" name="shoe_name" id="edit_shoe_name" required class="form-control">
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-secondary text-uppercase small">Ukuran Sepatu</label>
                            <input type="text" name="shoe_size" id="edit_shoe_size" required class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-secondary text-uppercase small">Jumlah Sepatu</label>
                            <input type="number" name="shoe_quantity" id="edit_shoe_quantity" min="1" required class="form-control">
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-secondary text-uppercase small">Metode Pembayaran</label>
                            <select name="payment_method" id="edit_payment_method" required class="form-select">
                                <option value="cash">Tunai (Cash)</option>
                                <option value="transfer">Transfer Bank</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-secondary text-uppercase small">Status Pembayaran</label>
                            <select name="payment_status" id="edit_payment_status" required class="form-select">
                                <option value="unpaid">Belum Lunas (Unpaid)</option>
                                <option value="paid">Lunas (Paid)</option>
                            </select>
                            <div id="edit_payment_proof_container" style="display: none;" class="mt-2">
                                <a id="edit_payment_proof_link" href="#" target="_blank" class="small text-decoration-underline text-primary"><i class="bi bi-file-earmark-image me-1"></i>Lihat Bukti Pembayaran</a>
                            </div>
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-secondary text-uppercase small">Status Pesanan</label>
                            <select name="status" id="edit_status" required class="form-select">
                                <option value="pending">Menunggu (Pending)</option>
                                <option value="processing">Dalam Antrian (Queue/Processing)</option>
                                <option value="washing">Dicuci/Dikerjakan (Washing)</option>
                                <option value="drying">Dikeringkan (Drying)</option>
                                <option value="finishing">Finishing</option>
                                <option value="ready">Siap Diambil (Ready)</option>
                                <option value="completed">Diambil (Completed)</option>
                                <option value="cancelled">Dibatalkan (Cancelled)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-secondary text-uppercase small">Uang Diterima (Tunai)</label>
                            <input type="number" name="cash_amount" id="edit_cash_amount" class="form-control">
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-12">
                            <label class="form-label fw-bold text-secondary text-uppercase small">Foto Sepatu</label>
                            <div class="d-flex gap-3 overflow-auto pb-2">
                                <div class="text-center" style="min-width: 120px;">
                                    <p class="small text-muted mb-1">Samping</p>
                                    <img id="edit_display_photo_before" src="" class="img-thumbnail" style="width: 120px; height: 120px; object-fit: cover; cursor: pointer;" onclick="if(this.src && !this.src.includes('data:image')) window.open(this.src, '_blank')">
                                </div>
                                <div class="text-center" style="min-width: 120px;">
                                    <p class="small text-muted mb-1">Bawah</p>
                                    <img id="edit_display_photo_before_2" src="" class="img-thumbnail" style="width: 120px; height: 120px; object-fit: cover; cursor: pointer;" onclick="if(this.src && !this.src.includes('data:image')) window.open(this.src, '_blank')">
                                </div>
                                <div class="text-center" style="min-width: 120px;">
                                    <p class="small text-muted mb-1">Sesudah</p>
                                    <img id="edit_display_photo_after" src="" class="img-thumbnail" style="width: 120px; height: 120px; object-fit: cover; cursor: pointer;" onclick="if(this.src && !this.src.includes('data:image')) window.open(this.src, '_blank')">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-secondary text-uppercase small">Ubah Foto Sebelum (Samping)</label>
                            <input type="file" name="shoe_photo" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-secondary text-uppercase small">Ubah Foto Sebelum (Bawah)</label>
                            <input type="file" name="shoe_photo_2" class="form-control">
                        </div>
                    </div>
                    <div class="row g-3 mt-2">
                        <div class="col-md-12">
                            <label class="form-label fw-bold text-secondary text-uppercase small">Upload Foto Sesudah</label>
                            <input type="file" name="photo_after" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary fw-bold" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success fw-bold">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@if(session('success_order_id'))
<div class="modal fade show" id="empSuccessOrderModal" tabindex="-1" aria-hidden="false" style="display: block; background: rgba(0,0,0,0.5); z-index: 1060;">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 480px;">
        <div class="modal-content border-0" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-body text-center p-5">
                <div style="font-size: 3.5rem; color: #198754;" class="mb-3">🎉</div>
                <h4 class="fw-bold text-dark mb-2">Pesanan Berhasil Disimpan!</h4>
                <p class="text-muted small mb-4">
                    Data pesanan telah berhasil disimpan dan nota digital telah dibuat secara otomatis.
                </p>
                
                <div class="d-grid gap-2">
                    <a href="/customer/orders/{{ session('success_order_id') }}/receipt" target="_blank" class="btn btn-success fw-bold py-2">
                        🖨️ Cetak Nota (Print)
                    </a>
                    
                    @if(session('success_customer_phone'))
                    @php
                        $waPhone = preg_replace('/\D/', '', session('success_customer_phone'));
                        if (str_starts_with($waPhone, '0')) {
                            $waPhone = '62' . substr($waPhone, 1);
                        }
                    @endphp
                    <a href="https://api.whatsapp.com/send?phone={{ $waPhone }}&text={{ urlencode(session('success_whatsapp_message')) }}" target="_blank" class="btn btn-primary fw-bold py-2" style="background-color: #25d366; border-color: #25d366;">
                        💬 Kirim Nota via WhatsApp
                    </a>
                    @endif
                    
                    <button type="button" onclick="document.getElementById('empSuccessOrderModal').remove()" class="btn btn-light fw-bold py-2 border">
                        Tutup Halaman Ini
                    </button>
                </div>
            </div>
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
<!-- Modal Upload Foto Sesudah -->
<div class="modal fade" id="uploadPhotoModal" tabindex="-1" aria-labelledby="uploadPhotoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0" style="border-radius: 16px;">
            <div class="modal-header bg-info text-white" style="border-top-left-radius: 16px; border-top-right-radius: 16px;">
                <h5 class="modal-title fw-bold" id="uploadPhotoModalLabel">Upload Foto Sesudah Pengerjaan</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="uploadPhotoForm" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <div class="text-center mb-3">
                        <p class="small text-muted mb-2">Foto Saat Ini (Jika Ada):</p>
                        <img id="upload_display_photo_after" src="" class="img-thumbnail" style="max-width: 200px; max-height: 200px; object-fit: cover; display: none;">
                        <div id="no_photo_after_placeholder" class="text-secondary my-3">
                            <i class="bi bi-image" style="font-size: 3rem;"></i>
                            <p class="small mb-0">Belum ada foto sesudah pengerjaan</p>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary text-uppercase small">Pilih Foto Hasil Pengerjaan</label>
                        <input type="file" name="photo_after" required class="form-control">
                        <div class="form-text">Maksimal file 2MB dengan format JPEG, PNG, atau JPG.</div>
                    </div>
                </div>
                <div class="modal-footer bg-light" style="border-bottom-left-radius: 16px; border-bottom-right-radius: 16px;">
                    <button type="button" class="btn btn-secondary fw-bold" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-info text-white fw-bold">Unggah Foto</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    let createModalObj = null;
    let editModalObj = null;
    let uploadPhotoModalObj = null;

    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Bootstrap Modals
        createModalObj = new bootstrap.Modal(document.getElementById('createOrderModal'));
        editModalObj = new bootstrap.Modal(document.getElementById('editOrderModal'));
        uploadPhotoModalObj = new bootstrap.Modal(document.getElementById('uploadPhotoModal'));
    });

    function openCreateModal() {
        // Reset shoe items container agar tidak duplikat saat modal dibuka ulang
        const empContainer = document.getElementById('emp-shoe-items-container');
        empContainer.innerHTML = '';
        empShoeRowIndex = 0;
        // Auto-tambah 1 item sepatu pertama
        empAddNewShoeRow();
        empToggleCustomerType();
        empToggleDeliverySection();
        empTogglePaymentStatusSection();
        empCalculateTotals();
        createModalObj.show();
    }

    // ---- Employee Wizard Functions ----
    const empServiceData = @json($services->map(fn($s) => ['id' => $s->id, 'name' => $s->name, 'price' => $s->price]));
    let empShoeRowIndex = 0;

    function empGoToStep(step) {
        [1, 2, 3].forEach(s => {
            const el = document.getElementById('emp-step-' + s);
            if (el) el.style.display = s === step ? 'block' : 'none';
            
            // Update step node indicator
            const badge = document.querySelector(`#emp-step-node-${s} .emp-step-badge`);
            const label = document.querySelector(`#emp-step-node-${s} span`);
            if (badge && label) {
                if (s === step) {
                    badge.classList.remove('bg-secondary-subtle', 'text-muted', 'bg-success-subtle', 'text-success');
                    badge.classList.add('bg-primary', 'text-white');
                    badge.style.boxShadow = '0 4px 10px rgba(13, 110, 253, 0.25)';
                    badge.innerHTML = s;
                    label.classList.remove('text-muted');
                    label.classList.add('text-dark');
                } else if (s < step) {
                    badge.classList.remove('bg-primary', 'text-white', 'bg-secondary-subtle', 'text-muted');
                    badge.classList.add('bg-success-subtle', 'text-success');
                    badge.style.boxShadow = 'none';
                    badge.innerHTML = '✓';
                    label.classList.remove('text-dark');
                    label.classList.add('text-muted');
                } else {
                    badge.classList.remove('bg-primary', 'text-white', 'bg-success-subtle', 'text-success');
                    badge.classList.add('bg-secondary-subtle', 'text-muted');
                    badge.style.boxShadow = 'none';
                    badge.innerHTML = s;
                    label.classList.remove('text-dark');
                    label.classList.add('text-muted');
                }
            }
            
            // Update connector line
            if (s < 3) {
                const line = document.getElementById(`emp-step-line-${s}`);
                if (line) {
                    if (s < step) {
                        line.classList.remove('bg-secondary-subtle');
                        line.style.background = '#198754'; // green
                    } else {
                        line.style.background = '';
                        line.classList.add('bg-secondary-subtle');
                    }
                }
            }
        });
        if (step === 3) empBuildSummary();
    }

    function empSetCustomerType(type) {
        document.getElementById('emp_customer_type_existing_radio').checked = (type === 'existing');
        document.getElementById('emp_customer_type_new_radio').checked = (type === 'new');
        
        const btnExisting = document.getElementById('emp-customer-type-existing-btn');
        const btnNew = document.getElementById('emp-customer-type-new-btn');
        
        if (type === 'existing') {
            btnExisting.classList.add('bg-white', 'shadow-sm', 'text-primary');
            btnExisting.classList.remove('text-muted');
            btnExisting.style.background = '';
            
            btnNew.classList.remove('bg-white', 'shadow-sm', 'text-primary');
            btnNew.classList.add('text-muted');
            btnNew.style.background = 'transparent';
        } else {
            btnNew.classList.add('bg-white', 'shadow-sm', 'text-primary');
            btnNew.classList.remove('text-muted');
            btnNew.style.background = '';
            
            btnExisting.classList.remove('bg-white', 'shadow-sm', 'text-primary');
            btnExisting.classList.add('text-muted');
            btnExisting.style.background = 'transparent';
        }
        empToggleCustomerType();
    }



    function empToggleCustomerType() {
        const type = document.querySelector('#empCreateOrderForm input[name="customer_type"]:checked').value;
        const existingSection = document.getElementById('emp-existing-customer-section');
        const newSection = document.getElementById('emp-new-customer-section');

        if (type === 'existing') {
            existingSection.style.display = 'block';
            newSection.style.display = 'none';
            document.getElementById('emp_user_id').required = true;
            document.getElementById('emp_new_customer_name').required = false;
            document.getElementById('emp_new_customer_phone').required = false;
        } else {
            existingSection.style.display = 'none';
            newSection.style.display = 'block';
            document.getElementById('emp_user_id').required = false;
            document.getElementById('emp_new_customer_name').required = true;
            document.getElementById('emp_new_customer_phone').required = true;
        }
        empUpdateReceiptPreview();
    }

    function empToggleDeliverySection() {
        const method = document.getElementById('emp_delivery_method').value;
        const addressSection = document.getElementById('emp_delivery_address_section');
        const addressInput = document.getElementById('emp_delivery_address_input');
        
        if (method === 'courier') {
            addressSection.style.display = 'block';
            addressInput.required = true;
        } else {
            addressSection.style.display = 'none';
            addressInput.required = false;
        }
        empCalculateTotals();
    }

    function empTogglePaymentStatusSection() {
        const method = document.getElementById('emp_payment_method_input').value;
        const cashSection = document.getElementById('emp_cash_received_section');
        const cashInput = document.getElementById('emp_cash_received_input');
        const statusSelect = document.getElementById('emp_payment_status_input');
        
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
        empCalculateTotals();
    }

    function empAddNewShoeRow(preselectedServiceId = null, qty = 1) {
        const container = document.getElementById('emp-shoe-items-container');
        const row = document.createElement('div');
        row.className = 'emp-shoe-row';
        row.id = 'emp-shoe-item-row-' + empShoeRowIndex;
        row.style.cssText = 'border: 1.5px solid var(--bs-border-color); border-radius: 18px; padding: 20px; margin-bottom: 20px; background: var(--bs-body-bg); position: relative; box-shadow: 0 4px 12px rgba(0,0,0,0.02); transition: 0.2s;';
        
        const deleteBtnHtml = `<button type="button" onclick="empRemoveShoeRow(${empShoeRowIndex})" class="btn-emp-remove-shoe text-danger" style="position: absolute; top: 16px; right: 16px; background: rgba(239, 68, 68, 0.08); border: none; padding: 6px 12px; border-radius: 8px; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 800; transition: 0.2s;">Hapus</button>`;
        
        const service = preselectedServiceId ? empServiceData.find(s => s.id == preselectedServiceId) : null;
        const serviceName = service ? service.name : '';
        const badgeHtml = preselectedServiceId ? `
            <div class="alert alert-primary py-2 px-3 mb-3 d-flex justify-content-between align-items-center" style="border-radius: 12px; font-weight: 700; font-size: 0.8rem;">
                <span>Layanan: ${serviceName}</span>
                <span>Jumlah: ${qty} Pasang</span>
            </div>
        ` : '';
 
        row.innerHTML = `
            ${deleteBtnHtml}
            <div style="font-size: 0.72rem; font-weight: 900; color: var(--bs-primary); margin-bottom: 16px; text-transform: uppercase; letter-spacing: 0.8px; display: flex; align-items: center; gap: 4px;">
                ITEM SEPATU #<span class="emp-item-index-label">${container.children.length + 1}</span>
            </div>
            
            ${badgeHtml}
            
            <div class="row g-3 mb-3">
                <div class="col-md-5">
                    <label class="form-label fw-bold text-secondary text-uppercase small" style="font-size: 0.72rem; letter-spacing: 0.3px;">Merek / Nama Sepatu</label>
                    <input type="text" name="items[${empShoeRowIndex}][shoe_name]" placeholder="Contoh: Nike Air Jordan" required class="form-control" style="border-radius: 10px; padding: 10px;" oninput="empUpdateReceiptPreview()">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold text-secondary text-uppercase small" style="font-size: 0.72rem; letter-spacing: 0.3px;">Ukuran</label>
                    <input type="text" name="items[${empShoeRowIndex}][shoe_size]" placeholder="Contoh: 42" required class="form-control" style="border-radius: 10px; padding: 10px;" oninput="empUpdateReceiptPreview()">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold text-secondary text-uppercase small" style="font-size: 0.72rem; letter-spacing: 0.3px;">Kecepatan</label>
                    <select name="items[${empShoeRowIndex}][processing_speed]" required class="form-select emp-speed-select-item" style="border-radius: 10px; padding: 10px;" onchange="empCalculateTotals()">
                        <option value="regular">Regular (+ Rp 0)</option>
                        <option value="express">Express (+ Rp 25.000 / pasang)</option>
                    </select>
                </div>
            </div>
            
            <div class="row g-3 mb-3 emp-service-qty-row" style="${preselectedServiceId ? 'display: none !important;' : ''}">
                <div class="col-md-8">
                    <label class="form-label fw-bold text-secondary text-uppercase small" style="font-size: 0.72rem; letter-spacing: 0.3px;">Jenis Layanan Utama</label>
                    <select name="items[${empShoeRowIndex}][service_id]" ${preselectedServiceId ? '' : 'required'} class="form-select emp-service-select-item" style="border-radius: 10px; padding: 10px;" onchange="empUpdateAdditionalServicesVisibility(${empShoeRowIndex}); empCalculateTotals();">
                        <option value="">-- Pilih Layanan Utama --</option>
                        ${empServiceData.map(s => {
                            const selected = (preselectedServiceId && s.id == preselectedServiceId) ? 'selected' : '';
                            return `<option value="${s.id}" data-price="${s.price}" ${selected}>${s.name} (Rp ${s.price.toLocaleString('id-ID')})</option>`;
                        }).join('')}
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold text-secondary text-uppercase small" style="font-size: 0.72rem; letter-spacing: 0.3px;">Jumlah</label>
                    <div class="input-group" style="border-radius: 10px; overflow: hidden;">
                        <button class="btn btn-outline-secondary px-3" type="button" onclick="empDecreaseQty(this)" style="font-weight: bold; font-size: 1.1rem; border-color: #dee2e6;">-</button>
                        <input type="number" name="items[${empShoeRowIndex}][shoe_quantity]" value="${qty}" min="1" required class="form-control text-center emp-qty-input-item" style="padding: 10px; border-color: #dee2e6; border-left: none; border-right: none; font-weight: 700; -moz-appearance: textfield; appearance: textfield;" onchange="empCalculateTotals()" oninput="empCalculateTotals()">
                        <button class="btn btn-outline-secondary px-3" type="button" onclick="empIncreaseQty(this)" style="font-weight: bold; font-size: 1.1rem; border-color: #dee2e6;">+</button>
                    </div>
                </div>
            </div>

            <div class="emp-additional-services-wrapper mb-3" style="display: none; position: relative;">
                <label class="form-label fw-bold text-secondary text-uppercase small" style="font-size: 0.72rem; letter-spacing: 0.3px;">Layanan Tambahan (Opsional)</label>
                
                <button type="button" class="emp-multiselect-trigger btn btn-outline-secondary w-100 text-start d-flex justify-content-between align-items-center" onclick="empToggleMultiselectDropdown(${empShoeRowIndex}, event)" style="border-radius: 10px; padding: 10px; font-size: 0.85rem; background: var(--bs-body-bg); color: var(--bs-body-color);">
                    <span class="emp-selected-count-label" style="font-weight: 700;">0 Layanan Tambahan Terpilih</span>
                    <span style="font-size: 0.7rem; opacity: 0.5;">▼</span>
                </button>
                
                <div class="emp-multiselect-dropdown-panel border rounded-3 shadow-sm p-3 bg-body" style="display: none; position: absolute; top: 100%; left: 0; right: 0; z-index: 100; max-height: 200px; overflow-y: auto; margin-top: 6px;">
                    ${empServiceData.map(s => `
                        <label class="emp-additional-service-label d-flex align-items-center gap-2 small text-dark cursor-pointer py-1 px-2 rounded mb-1" style="transition: background 0.15s;">
                            <input type="checkbox" name="items[${empShoeRowIndex}][additional_services][]" value="${s.id}" data-name="${s.name}" data-price="${s.price}" class="emp-additional-service-checkbox form-check-input" onchange="empUpdateSelectedBadges(${empShoeRowIndex}); empCalculateTotals();" style="width: 15px; height: 15px; flex-shrink: 0; margin-top: 0;">
                            <span class="flex-grow-1 text-body">${s.name}</span>
                            <span class="fw-bold text-primary font-monospace" style="font-size: 0.75rem;">+Rp ${s.price.toLocaleString('id-ID')}</span>
                        </label>
                    `).join('')}
                </div>
                
                <div class="emp-selected-services-badges d-flex flex-wrap gap-2 mt-2"></div>
            </div>
            
            <div class="mb-3">
                <label class="form-label fw-bold text-secondary text-uppercase small" style="font-size: 0.72rem; letter-spacing: 0.3px;">Catatan Pengerjaan (Opsional)</label>
                <input type="text" name="items[${empShoeRowIndex}][handling_notes]" placeholder="Contoh: Noda membandel di sol samping" class="form-control" style="border-radius: 10px; padding: 10px;">
            </div>
            <div>
                <label class="form-label fw-bold text-secondary text-uppercase small" style="font-size: 0.72rem; letter-spacing: 0.3px;">Foto Sebelum Pengerjaan (Opsional)</label>
                <input type="file" name="items[${empShoeRowIndex}][shoe_photo]" accept="image/*" class="form-control" style="border-radius: 10px; padding: 8px;">
            </div>
        `;
        
        container.appendChild(row);
        empUpdateAdditionalServicesVisibility(empShoeRowIndex);
        empShoeRowIndex++;
        empUpdateDeleteButtons();
        empCalculateTotals();
    }

    function empRemoveShoeRow(index) {
        const row = document.getElementById('emp-shoe-item-row-' + index);
        if (row) {
            row.remove();
            empUpdateLabelsAndIndexes();
            empUpdateDeleteButtons();
            empCalculateTotals();
        }
    }

    function empDecreaseQty(btn) {
        const input = btn.parentElement.querySelector('.emp-qty-input-item');
        let val = parseInt(input.value) || 1;
        if (val > 1) {
            input.value = val - 1;
            const event = new Event('change', { bubbles: true });
            input.dispatchEvent(event);
        }
    }

    function empIncreaseQty(btn) {
        const input = btn.parentElement.querySelector('.emp-qty-input-item');
        let val = parseInt(input.value) || 1;
        input.value = val + 1;
        const event = new Event('change', { bubbles: true });
        input.dispatchEvent(event);
    }

    function empUpdateLabelsAndIndexes() {
        const container = document.getElementById('emp-shoe-items-container');
        Array.from(container.children).forEach((child, index) => {
            child.querySelector('.emp-item-index-label').textContent = index + 1;
        });
    }

    // Row clicks binding
    function empUpdateDeleteButtons() {
        const container = document.getElementById('emp-shoe-items-container');
        const deleteBtns = container.querySelectorAll('.btn-emp-remove-shoe');
        deleteBtns.forEach(btn => {
            btn.style.display = container.children.length > 1 ? 'flex' : 'none';
        });
    }

    function empUpdateAdditionalServicesVisibility(rowIndex) {
        const row = document.getElementById('emp-shoe-item-row-' + rowIndex);
        if (!row) return;
        
        const mainServiceSelect = row.querySelector('.emp-service-select-item');
        const selectedMainId = mainServiceSelect.value;
        const wrapper = row.querySelector('.emp-additional-services-wrapper');
        
        if (!selectedMainId) {
            if (wrapper) wrapper.style.display = 'none';
            return;
        }
        
        if (wrapper) wrapper.style.display = 'block';
        
        const checkboxes = row.querySelectorAll('.emp-additional-service-checkbox');
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
        
        empUpdateSelectedBadges(rowIndex);
    }

    function empToggleMultiselectDropdown(rowIndex, event) {
        if (event) event.stopPropagation();
        
        const panels = document.querySelectorAll('.emp-multiselect-dropdown-panel');
        const rowPanel = document.querySelector(`#emp-shoe-item-row-${rowIndex} .emp-multiselect-dropdown-panel`);
        
        panels.forEach(p => {
            if (p !== rowPanel) p.style.display = 'none';
        });
        
        if (rowPanel) {
            rowPanel.style.display = rowPanel.style.display === 'block' ? 'none' : 'block';
        }
    }

    function empUpdateSelectedBadges(rowIndex) {
        const row = document.getElementById('emp-shoe-item-row-' + rowIndex);
        if (!row) return;
        
        const checkboxes = row.querySelectorAll('.emp-additional-service-checkbox:checked');
        const badgeContainer = row.querySelector('.emp-selected-services-badges');
        const triggerLabel = row.querySelector('.emp-selected-count-label');
        
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
                badge.className = 'badge bg-light text-primary border border-primary-subtle d-inline-flex align-items-center gap-1 py-1.5 px-2.5';
                badge.style.cssText = 'font-size: 0.72rem; font-weight: 700; cursor: default;';
                badge.innerHTML = `
                    <span>${name} (+Rp ${price.toLocaleString('id-ID')})</span>
                    <span onclick="empRemoveAdditionalService(${rowIndex}, '${id}', event)" style="color: #dc3545; font-weight: 900; cursor: pointer; font-size: 0.85rem; margin-left: 2px; padding: 0 2px;">&times;</span>
                `;
                badgeContainer.appendChild(badge);
            });
        }
    }

    function empRemoveAdditionalService(rowIndex, serviceId, event) {
        if (event) event.stopPropagation();
        const row = document.getElementById('emp-shoe-item-row-' + rowIndex);
        if (!row) return;
        
        const checkbox = row.querySelector(`.emp-additional-service-checkbox[value="${serviceId}"]`);
        if (checkbox) {
            checkbox.checked = false;
            empUpdateSelectedBadges(rowIndex);
            empCalculateTotals();
        }
    }

    // Global click-outside listener for employee multiselect dropdowns (using capture phase to bypass stopPropagation)
    document.addEventListener('click', function(e) {
        const panels = document.querySelectorAll('.emp-multiselect-dropdown-panel');
        panels.forEach(panel => {
            const wrapper = panel.closest('.emp-additional-services-wrapper');
            const trigger = wrapper ? wrapper.querySelector('.emp-multiselect-trigger') : null;
            if (panel.style.display === 'block' && !panel.contains(e.target) && e.target !== trigger && (trigger && !trigger.contains(e.target))) {
                panel.style.display = 'none';
            }
        });
    }, true);

    function empCalculateTotals() {
        const container = document.getElementById('emp-shoe-items-container');
        const rows = container.querySelectorAll('.emp-shoe-row');
        let subtotal = 0;
        
        rows.forEach(row => {
            const svcSelect = row.querySelector('.emp-service-select-item');
            const speedSelect = row.querySelector('.emp-speed-select-item');
            const qtyInput = row.querySelector('.emp-qty-input-item');
            
            if (svcSelect && svcSelect.value) {
                const price = parseFloat(svcSelect.options[svcSelect.selectedIndex].getAttribute('data-price')) || 0;
                const qty = parseInt(qtyInput.value) || 1;
                const speed = speedSelect.value;
                
                let itemTotal = price;
                
                // Add optional additional services
                const checkedBoxes = row.querySelectorAll('.emp-additional-service-checkbox:checked');
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
        
        document.getElementById('emp_preview_subtotal').textContent = 'Rp ' + subtotal.toLocaleString('id-ID');
        
        const deliveryMethod = document.getElementById('emp_delivery_method').value;
        const deliveryFee = deliveryMethod === 'courier' ? 25000 : 0;
        const deliveryRow = document.getElementById('emp_row_delivery_fee');
        if (deliveryFee > 0) {
            deliveryRow.style.setProperty('display', 'flex', 'important');
            document.getElementById('emp_preview_delivery_fee').textContent = 'Rp ' + deliveryFee.toLocaleString('id-ID');
        } else {
            deliveryRow.style.setProperty('display', 'none', 'important');
        }
        
        const discount = parseFloat(document.getElementById('emp_discount_input').value) || 0;
        const discountRow = document.getElementById('emp_row_discount');
        if (discount > 0) {
            discountRow.style.setProperty('display', 'flex', 'important');
            document.getElementById('emp_preview_discount').textContent = '-Rp ' + discount.toLocaleString('id-ID');
        } else {
            discountRow.style.setProperty('display', 'none', 'important');
        }
        
        const grandTotal = Math.max(0, subtotal + deliveryFee - discount);
        document.getElementById('emp_preview_grand_total').textContent = 'Rp ' + grandTotal.toLocaleString('id-ID');
        
        const paymentMethod = document.getElementById('emp_payment_method_input').value;
        const cashReceived = parseFloat(document.getElementById('emp_cash_received_input').value) || 0;
        const changeRow = document.getElementById('emp_row_cash_change');
        
        if (paymentMethod === 'cash' && cashReceived > 0) {
            changeRow.style.setProperty('display', 'flex', 'important');
            const change = Math.max(0, cashReceived - grandTotal);
            document.getElementById('emp_preview_cash_change').textContent = 'Rp ' + change.toLocaleString('id-ID');
        } else {
            changeRow.style.setProperty('display', 'none', 'important');
        }
        
        empUpdateReceiptPreview();
    }

    function empStatusChange() {
        document.querySelectorAll('.emp-status-label').forEach(lbl => {
            const radio = lbl.querySelector('.emp-status-radio');
            lbl.style.borderColor = (radio && radio.checked) ? '#0d6efd' : '';
            lbl.style.background = (radio && radio.checked) ? 'rgba(13,110,253,0.06)' : '';
        });
    }

    function empBuildSummary() {
        const type = document.querySelector('#empCreateOrderForm input[name="customer_type"]:checked').value;
        let customerName = '';
        
        if (type === 'existing') {
            const custEl = document.getElementById('emp_user_id');
            customerName = custEl?.options[custEl?.selectedIndex]?.text || '-';
        } else {
            customerName = (document.getElementById('emp_new_customer_name').value || '-') + ' (Baru)';
        }
        
        const container = document.getElementById('emp-shoe-items-container');
        const rows = container.querySelectorAll('.emp-shoe-row');
        let itemsHtml = '';
        
        rows.forEach((row, index) => {
            const name = row.querySelector('input[name*="[shoe_name]"]').value || '-';
            const size = row.querySelector('input[name*="[shoe_size]"]').value || '-';
            const qty = row.querySelector('input[name*="[shoe_quantity]"]').value || '1';
            const svcEl = row.querySelector('.emp-service-select-item');
            const svc = svcEl?.options[svcEl?.selectedIndex]?.text || '-';
            
            itemsHtml += `<div>• <b>${name}</b> (Size ${size}, Qty ${qty}) - ${svc}</div>`;
        });
        
        document.getElementById('emp-summary-content').innerHTML = `
            <div><b>Pelanggan:</b> ${customerName}</div>
            <div class="mt-1"><b>Daftar Item:</b></div>
            <div style="padding-left: 10px; opacity: 0.9;">${itemsHtml}</div>
        `;
    }

    // Call on load inside DOMContentLoaded push below
    function initEmpCreateWizard() {
        empAddNewShoeRow();
        empToggleCustomerType();
        empToggleDeliverySection();
        empTogglePaymentStatusSection();
        empUpdateReceiptPreview();
    }

    function empUpdateReceiptPreview() {
        // 1. Customer info
        const isNew = document.getElementById('emp_customer_type_new_radio').checked;
        let name = '-', phone = '-', address = '-';

        if (isNew) {
            name = document.getElementById('emp_new_customer_name').value || '-';
            phone = document.getElementById('emp_new_customer_phone').value || '-';
            address = document.getElementById('emp_new_customer_address').value || '-';
        } else {
            const select = document.getElementById('emp_user_id');
            if (select && select.selectedIndex > 0) {
                const opt = select.options[select.selectedIndex];
                name = opt.getAttribute('data-name') || opt.text.split('(')[0].trim() || '-';
                phone = opt.getAttribute('data-phone') || '-';
                address = opt.getAttribute('data-address') || '-';
            }
        }

        const nameEl = document.getElementById('emp_receipt_cust_name');
        const phoneEl = document.getElementById('emp_receipt_cust_phone');
        const addrEl = document.getElementById('emp_receipt_cust_address');
        if (nameEl) nameEl.textContent = name;
        if (phoneEl) phoneEl.textContent = phone;
        if (addrEl) addrEl.textContent = address;

        // 2. Shoe items info
        const container = document.getElementById('emp-shoe-items-container');
        const rows = container ? container.querySelectorAll('.emp-shoe-row') : [];
        const itemsList = document.getElementById('emp_receipt_items_list');

        if (!itemsList) return;

        if (rows.length === 0) {
            itemsList.innerHTML = '<div class="text-muted" style="font-style: italic;">Belum ada item sepatu.</div>';
            return;
        }

        let html = '';
        rows.forEach((row, idx) => {
            const shoeNameInput = row.querySelector('input[name*="[shoe_name]"]');
            const shoeSizeInput = row.querySelector('input[name*="[shoe_size]"]');
            const qtyInput = row.querySelector('.emp-qty-input-item');

            const shoeName = shoeNameInput ? (shoeNameInput.value || '(Tanpa Nama)') : '(Tanpa Nama)';
            const shoeSize = shoeSizeInput ? (shoeSizeInput.value || '-') : '-';
            const qty = parseInt(qtyInput ? qtyInput.value : 1) || 1;
            const svcSelect = row.querySelector('.emp-service-select-item');
            const speedSelect = row.querySelector('.emp-speed-select-item');

            let svcName = '-', basePrice = 0;
            if (svcSelect && svcSelect.selectedIndex > 0) {
                svcName = svcSelect.options[svcSelect.selectedIndex].text.split('(')[0].trim();
                basePrice = parseFloat(svcSelect.options[svcSelect.selectedIndex].getAttribute('data-price')) || 0;
            }

            const speed = speedSelect ? speedSelect.value : 'regular';
            const checkedBoxes = row.querySelectorAll('.emp-additional-service-checkbox:checked');
            let extrasList = [], extrasPrice = 0;
            checkedBoxes.forEach(cb => {
                extrasList.push(cb.getAttribute('data-name'));
                extrasPrice += parseFloat(cb.getAttribute('data-price')) || 0;
            });

            let itemUnitPrice = basePrice + extrasPrice;
            if (speed === 'express') itemUnitPrice += 25000;
            const itemSubtotal = itemUnitPrice * qty;

            html += `
                <div class="border-bottom border-dashed pb-1 mb-1">
                    <div class="d-flex justify-content-between fw-bold">
                        <span>#${idx + 1} ${shoeName} (Sz:${shoeSize})</span>
                        <span>x${qty}</span>
                    </div>
                    <div class="text-muted" style="padding-left: 6px; font-size: 0.68rem; line-height: 1.3;">
                        - ${svcName}${extrasList.length > 0 ? '<br>- Tambahan: ' + extrasList.join(', ') : ''}${speed === 'express' ? '<br>- Express (+Rp 25.000)' : ''}
                    </div>
                    <div class="text-end fw-bold">Rp ${itemSubtotal.toLocaleString('id-ID')}</div>
                </div>`;
        });

        itemsList.innerHTML = html || '<div class="text-muted" style="font-style: italic;">Belum ada item sepatu.</div>';
    }

    // DOMContentLoaded extensions
    document.addEventListener('DOMContentLoaded', function() {
        initEmpCreateWizard();
        @if(request('create') == 1)
            setTimeout(function() {
                openCreateModal();
            }, 100);
        @endif
    });

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
        document.getElementById('edit_cash_amount').value = order.cash_amount || '';
        
        const proofContainer = document.getElementById('edit_payment_proof_container');
        const proofLink = document.getElementById('edit_payment_proof_link');
        if (order.payment_proof) {
            proofLink.href = '/storage/' + order.payment_proof;
            proofContainer.style.display = 'block';
        } else {
            proofContainer.style.display = 'none';
        }
        
        const svgPlaceholder = `data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%2394a3b8' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round'><rect x='3' y='3' width='18' height='18' rx='2' ry='2'></rect><circle cx='8.5' cy='8.5' r='1.5'></circle><polyline points='21 15 16 10 5 21'></polyline></svg>`;
        
        const dispBefore = document.getElementById('edit_display_photo_before');
        dispBefore.src = order.photo_before ? ('/storage/' + order.photo_before) : svgPlaceholder;
        dispBefore.style.padding = order.photo_before ? '0' : '20px';
        
        const dispBefore2 = document.getElementById('edit_display_photo_before_2');
        dispBefore2.src = order.photo_before_2 ? ('/storage/' + order.photo_before_2) : svgPlaceholder;
        dispBefore2.style.padding = order.photo_before_2 ? '0' : '20px';
        
        const dispAfter = document.getElementById('edit_display_photo_after');
        dispAfter.src = order.photo_after ? ('/storage/' + order.photo_after) : svgPlaceholder;
        dispAfter.style.padding = order.photo_after ? '0' : '20px';
        
        editModalObj.show();
    }

    function openUploadPhotoModal(orderId, currentPhotoUrl) {
        document.getElementById('uploadPhotoForm').action = `/employee/orders/${orderId}/photo-after`;
        
        const dispAfter = document.getElementById('upload_display_photo_after');
        const placeholder = document.getElementById('no_photo_after_placeholder');
        
        if (currentPhotoUrl) {
            dispAfter.src = currentPhotoUrl;
            dispAfter.style.display = 'inline-block';
            placeholder.style.display = 'none';
        } else {
            dispAfter.src = '';
            dispAfter.style.display = 'none';
            placeholder.style.display = 'block';
        }
        
        uploadPhotoModalObj.show();
    }

    function empDecreaseCatalogQty(btn) {
        const input = btn.parentElement.querySelector('.emp-catalog-qty-input');
        let val = parseInt(input.value) || 1;
        if (val > 1) {
            input.value = val - 1;
        }
    }

    function empIncreaseCatalogQty(btn) {
        const input = btn.parentElement.querySelector('.emp-catalog-qty-input');
        let val = parseInt(input.value) || 1;
        input.value = val + 1;
    }

    function empAddServiceToOrder(serviceId, btn) {
        const stepper = btn.parentElement.querySelector('.emp-catalog-qty-input');
        const qty = parseInt(stepper.value) || 1;
        
        const container = document.getElementById('emp-shoe-items-container');
        const rows = container.querySelectorAll('.emp-shoe-row');
        let reused = false;
        
        for (let i = 0; i < rows.length; i++) {
            const row = rows[i];
            const svcSelect = row.querySelector('.emp-service-select-item');
            const shoeNameInput = row.querySelector('input[name*="[shoe_name]"]');
            
            if (svcSelect && !svcSelect.value && shoeNameInput && !shoeNameInput.value) {
                svcSelect.value = serviceId;
                const qtyInput = row.querySelector('.emp-qty-input-item');
                if (qtyInput) qtyInput.value = qty;
                
                const grid2 = row.querySelector('.emp-service-qty-row') || svcSelect.closest('.row');
                if (grid2) grid2.style.setProperty('display', 'none', 'important');
                
                const service = empServiceData.find(s => s.id == serviceId);
                const serviceName = service ? service.name : '';
                const badgeHtml = `
                    <div class="alert alert-primary py-2 px-3 mb-3 d-flex justify-content-between align-items-center" style="border-radius: 12px; font-weight: 700; font-size: 0.8rem;">
                        <span>Layanan: ${serviceName}</span>
                        <span>Jumlah: ${qty} Pasang</span>
                    </div>
                `;
                
                const indexLabel = row.querySelector('.emp-item-index-label');
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
            empAddNewShoeRow(serviceId, qty);
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

    let empActiveCategory = 'all';

    function empFilterCatalog() {
        const searchVal = document.getElementById('emp-catalog-search').value.toLowerCase();
        const cards = document.querySelectorAll('.emp-pos-service-card');
        
        cards.forEach(card => {
            const name = card.getAttribute('data-name');
            const cat = card.getAttribute('data-category');
            
            const matchesSearch = name.includes(searchVal);
            const matchesCat = (empActiveCategory === 'all' || cat === empActiveCategory);
            
            if (matchesSearch && matchesCat) {
                card.style.setProperty('display', 'flex', 'important');
            } else {
                card.style.setProperty('display', 'none', 'important');
            }
        });
    }

    function empFilterCatalogCategory(category, btn) {
        empActiveCategory = category;
        
        const buttons = btn.parentElement.querySelectorAll('button');
        buttons.forEach(b => {
            b.classList.remove('btn-primary', 'active-cat-btn');
            b.classList.add('btn-outline-secondary');
        });
        
        btn.classList.add('btn-primary', 'active-cat-btn');
        btn.classList.remove('btn-outline-secondary');
        
        empFilterCatalog();
    }
</script>
@endpush
