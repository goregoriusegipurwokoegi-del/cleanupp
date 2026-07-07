@extends('layouts.employee-dashboard')

@section('page_title', 'Cari Data Pesanan Pelanggan')

@section('content')
<div class="card shadow-sm border-0 mx-auto" style="max-width: 600px;">
    <div class="card-body p-4 text-dark">
        <h3 class="fw-bold text-primary mb-2">Pengambilan Barang</h3>
        <p class="text-secondary small mb-4">Masukkan nomor resi (Order Number) atau nomor antrean (Queue Number) pelanggan untuk mencari pesanan yang akan diambil.</p>

        @if(session('success'))
            <div class="alert alert-success shadow-sm mb-4">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('employee.orders.scan') }}" method="GET" class="d-flex gap-2 mb-4 flex-wrap">
            <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Contoh: ORD-12345 atau Q001" autofocus required class="form-control form-control-lg flex-grow-1" style="min-width: 250px;">
            <button type="submit" class="btn btn-primary fw-bold px-4 py-2 d-flex align-items-center justify-content-center gap-2">
                <i class="bi bi-search"></i>
                Cari
            </button>
        </form>

        @if(isset($search) && !$order)
            <div class="alert alert-danger shadow-sm text-center py-4">
                <p class="fw-bold mb-1">Pesanan tidak ditemukan.</p>
                <p class="small text-secondary mb-0">Pastikan nomor resi atau antrean benar.</p>
            </div>
        @endif

        @if(isset($order))
            <div class="card border shadow-sm">
                <div class="card-body p-3 text-dark">
                    <div class="d-flex justify-content-between align-items-start mb-3 flex-wrap gap-2">
                        <div>
                            <span class="badge bg-primary fs-6 px-3 py-1 font-monospace">#{{ $order->queue_number }}</span>
                            <h4 class="fw-bold text-dark mt-2 mb-1">{{ $order->shoe_name }}</h4>
                            <p class="text-secondary small mb-0">{{ $order->user->name }} • {{ $order->user->phone ?? '-' }}</p>
                        </div>
                        <div class="text-end">
                            <small class="text-secondary d-block">Status Saat Ini</small>
                            <span class="badge {{ $order->status == 'completed' ? 'bg-success' : ($order->status == 'ready' ? 'bg-primary' : 'bg-warning text-dark') }} px-3 py-2 mt-1">
                                @if($order->status == 'completed') Sudah Diambil
                                @elseif($order->status == 'ready') Siap Diambil
                                @elseif($order->status == 'cancelled') Dibatalkan
                                @elseif($order->status == 'pending') Menunggu
                                @else Sedang Diproses @endif
                            </span>
                        </div>
                    </div>

                    <div class="bg-light border rounded p-3 mb-3">
                        <small class="text-secondary d-block mb-1">Letak / Lokasi Barang (Rak):</small>
                        @if($order->storage_location)
                            <strong class="fs-4 text-dark">{{ $order->storage_location }}</strong>
                        @else
                            <span class="text-warning fw-bold italic" style="font-style: italic;">Lokasi belum diatur</span>
                        @endif
                    </div>

                    @if($order->status != 'completed')
                    <form action="{{ route('employee.orders.scan.process') }}" method="POST">
                        @csrf
                        <input type="hidden" name="order_id" value="{{ $order->id }}">
                        
                        @if(!$order->storage_location)
                        <div class="mb-3">
                            <label class="form-label fw-bold text-secondary text-uppercase small">Atur Lokasi (Opsional)</label>
                            <input type="text" name="storage_location" placeholder="Contoh: Rak A1" class="form-control">
                        </div>
                        @endif

                        <button type="submit" class="btn btn-success btn-lg w-100 fw-bold d-flex align-items-center justify-content-center gap-2 py-2.5">
                            <i class="bi bi-check-circle-fill"></i>
                            Tandai Sudah Diambil
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
