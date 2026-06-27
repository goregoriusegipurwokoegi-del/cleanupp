@extends('layouts.employee-dashboard')

@section('page_title', request('queue') ? (request('category') == 'cleaning' ? 'Monitor Antrian - Cuci' : (request('category') == 'repair' ? 'Monitor Antrian - Reparasi' : 'Monitor Antrian')) : (request('delivery') ? 'Antar Jemput' : 'Orderan Masuk'))

@section('content')
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
                                        <button onclick='openEditModal(@json($order))' class="btn btn-outline-secondary btn-sm w-100 fw-bold py-1">Edit Detail</button>
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
                                <button onclick='openEditModal(@json($order))' class="btn btn-outline-secondary btn-sm w-100 fw-bold py-2">Edit Detail</button>
                                <a href="{{ route('orders.receipt', $order->id) }}" target="_blank" class="btn btn-outline-dark btn-sm w-100 fw-bold py-2 text-decoration-none text-center">Cetak Struk</a>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Modal Create Order -->
<div class="modal fade" id="createOrderModal" tabindex="-1" aria-labelledby="createOrderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold" id="createOrderModalLabel">Tambah Pesanan Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('employee.orders.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-secondary text-uppercase small">Nama Pelanggan</label>
                            <input type="text" name="customer_name" placeholder="Nama Lengkap" required class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-secondary text-uppercase small">Nomor HP</label>
                            <input type="text" name="customer_phone" placeholder="Contoh: 0812345678" required class="form-control">
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-secondary text-uppercase small">Layanan</label>
                            <select name="service_id" required class="form-select">
                                @foreach($services as $serv)
                                    <option value="{{ $serv->id }}">{{ $serv->name }} (Rp{{ number_format($serv->price, 0, ',', '.') }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-secondary text-uppercase small">Nomor Antrian</label>
                            <input type="text" name="queue_number" placeholder="Contoh: Q001" required class="form-control">
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-secondary text-uppercase small">Kecepatan</label>
                            <select name="processing_speed" required class="form-select">
                                <option value="regular">Regular</option>
                                <option value="express">Express</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-secondary text-uppercase small">Layanan Pengiriman</label>
                            <select name="is_delivery" required class="form-select">
                                <option value="0">Toko (Ambil Sendiri)</option>
                                <option value="1">Delivery (Antar Jemput)</option>
                            </select>
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-secondary text-uppercase small">Nama Sepatu</label>
                            <input type="text" name="shoe_name" placeholder="Contoh: Nike Air Jordan" required class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-secondary text-uppercase small">Ukuran Sepatu</label>
                            <input type="text" name="shoe_size" placeholder="Contoh: 42" required class="form-control">
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-secondary text-uppercase small">Jumlah Sepatu</label>
                            <input type="number" name="shoe_quantity" min="1" value="1" required class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-secondary text-uppercase small">Metode Pembayaran</label>
                            <select name="payment_method" required class="form-select">
                                <option value="cash">Tunai (Cash)</option>
                                <option value="transfer">Transfer Bank</option>
                            </select>
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-secondary text-uppercase small">Status Pembayaran</label>
                            <select name="payment_status" required class="form-select">
                                <option value="unpaid">Belum Lunas (Unpaid)</option>
                                <option value="paid">Lunas (Paid)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-secondary text-uppercase small">Uang Diterima (Tunai)</label>
                            <input type="number" name="cash_amount" placeholder="Contoh: 100000" class="form-control">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary text-uppercase small">Foto Sepatu (Sebelum)</label>
                        <input type="file" name="shoe_photo" required class="form-control">
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary fw-bold" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary fw-bold">Buat Pesanan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Order -->
<div class="modal fade" id="editOrderModal" tabindex="-1" aria-labelledby="editOrderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-secondary text-white">
                <h5 class="modal-title fw-bold" id="editOrderModalLabel">Edit Detail Pesanan</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editOrderForm" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
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
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-secondary text-uppercase small">Ubah Foto Sebelum</label>
                            <input type="file" name="shoe_photo" class="form-control">
                        </div>
                        <div class="col-md-6">
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
@endsection

@push('scripts')
<script>
    let createModalObj = null;
    let editModalObj = null;

    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Bootstrap Modals
        createModalObj = new bootstrap.Modal(document.getElementById('createOrderModal'));
        editModalObj = new bootstrap.Modal(document.getElementById('editOrderModal'));
    });

    function openCreateModal() {
        createModalObj.show();
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
        document.getElementById('edit_cash_amount').value = order.cash_amount || '';
        
        const proofContainer = document.getElementById('edit_payment_proof_container');
        const proofLink = document.getElementById('edit_payment_proof_link');
        if (order.payment_proof) {
            proofLink.href = '/storage/' + order.payment_proof;
            proofContainer.style.display = 'block';
        } else {
            proofContainer.style.display = 'none';
        }
        
        editModalObj.show();
    }
</script>
@endpush
