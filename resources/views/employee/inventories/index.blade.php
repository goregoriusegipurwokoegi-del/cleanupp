@extends('layouts.employee-dashboard')

@section('page_title', 'Stok Barang Karyawan')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <p class="text-secondary mb-0">Kelola stok bahan baku dan persediaan operasional cuci/reparasi.</p>
    </div>
    <button onclick="openModal()" class="btn btn-primary fw-bold px-4 shadow-sm">
        <i class="bi bi-plus-lg me-1"></i> Tambah Barang
    </button>
</div>

@if(session('success'))
    <div class="alert alert-success shadow-sm mb-4">
        {{ session('success') }}
    </div>
@endif

<!-- Inventory Card Table -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body p-0">
        <div class="table-responsive d-none d-md-block">
            <table class="table table-hover table-striped align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="py-3 px-4 text-secondary text-uppercase fw-bold" style="font-size: 0.75rem;">Nama Barang</th>
                        <th class="py-3 px-4 text-secondary text-uppercase fw-bold" style="font-size: 0.75rem;">Sisa Stok</th>
                        <th class="py-3 px-4 text-secondary text-uppercase fw-bold" style="font-size: 0.75rem;">Satuan</th>
                        <th class="py-3 px-4 text-secondary text-uppercase fw-bold" style="font-size: 0.75rem;">Batas Minimum</th>
                        <th class="py-3 px-4 text-secondary text-uppercase fw-bold" style="font-size: 0.75rem;">Status</th>
                        <th class="py-3 px-4 text-secondary text-uppercase fw-bold text-center" style="font-size: 0.75rem; width: 120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($inventories as $inv)
                    <tr>
                        <td class="px-4 fw-bold text-dark">{{ $inv->name }}</td>
                        <td class="px-4 fw-bold fs-5 text-dark">
                            {{ rtrim(rtrim(number_format($inv->stock, 2, ',', '.'), '0'), ',') }}
                        </td>
                        <td class="px-4 text-secondary">{{ $inv->unit }}</td>
                        <td class="px-4 text-secondary">{{ rtrim(rtrim(number_format($inv->min_stock, 2, ',', '.'), '0'), ',') }}</td>
                        <td class="px-4">
                            @if($inv->stock <= 0)
                                <span class="badge bg-danger px-3 py-2 text-white fw-bold">HABIS</span>
                            @elseif($inv->stock <= $inv->min_stock)
                                <span class="badge bg-warning px-3 py-2 text-dark fw-bold">MENIPIS</span>
                            @else
                                <span class="badge bg-success px-3 py-2 text-white fw-bold">AMAN</span>
                            @endif
                        </td>
                        <td class="px-4 text-center">
                            <div class="d-flex justify-content-center gap-1">
                                <button onclick="editModal({{ $inv->id }}, '{{ addslashes($inv->name) }}', {{ $inv->stock }}, '{{ addslashes($inv->unit) }}', {{ $inv->min_stock }})" class="btn btn-outline-secondary btn-sm" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <form action="{{ route('employee.inventories.destroy', $inv->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus barang ini?');" style="margin: 0;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View Layout -->
        <div class="p-3 d-md-none">
            @foreach($inventories as $inv)
                <div class="card border shadow-sm mb-3">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <h5 class="fw-bold text-dark mb-0 fs-6">{{ $inv->name }}</h5>
                                <span class="text-secondary small">Satuan: {{ $inv->unit }}</span>
                            </div>
                            <div>
                                @if($inv->stock <= 0)
                                    <span class="badge bg-danger px-2.5 py-1.5 text-white fw-bold">HABIS</span>
                                @elseif($inv->stock <= $inv->min_stock)
                                    <span class="badge bg-warning px-2.5 py-1.5 text-dark fw-bold">MENIPIS</span>
                                @else
                                    <span class="badge bg-success px-2.5 py-1.5 text-white fw-bold">AMAN</span>
                                @endif
                            </div>
                        </div>
                        <div class="row g-2 border-top pt-2 mt-2">
                            <div class="col-6">
                                <small class="text-secondary d-block">Sisa Stok</small>
                                <strong class="fs-5 text-dark">{{ rtrim(rtrim(number_format($inv->stock, 2, ',', '.'), '0'), ',') }}</strong>
                            </div>
                            <div class="col-6">
                                <small class="text-secondary d-block">Batas Minimum</small>
                                <span class="text-secondary">{{ rtrim(rtrim(number_format($inv->min_stock, 2, ',', '.'), '0'), ',') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-light d-flex justify-content-end gap-2 py-2 px-3">
                        <button onclick="editModal({{ $inv->id }}, '{{ addslashes($inv->name) }}', {{ $inv->stock }}, '{{ addslashes($inv->unit) }}', {{ $inv->min_stock }})" class="btn btn-outline-secondary btn-sm fw-bold px-3">
                            <i class="bi bi-pencil me-1"></i> Edit
                        </button>
                        <form action="{{ route('employee.inventories.destroy', $inv->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus barang ini?');" style="margin: 0;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm fw-bold px-3">
                                <i class="bi bi-trash me-1"></i> Hapus
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        @if($inventories->isEmpty())
            <div class="p-5 text-center text-muted">
                <i class="bi bi-box fs-1 d-block mb-2 opacity-50"></i>
                <p class="mb-0">Belum ada data barang.</p>
            </div>
        @endif
    </div>
</div>

<!-- Modal Create / Edit Inventory -->
<div class="modal fade" id="inventoryModal" tabindex="-1" aria-labelledby="inventoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold" id="modal-title">Tambah Barang</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="inventory-form" method="POST" action="{{ route('employee.inventories.store') }}">
                @csrf
                <input type="hidden" name="_method" id="form-method" value="POST">
                
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary text-uppercase small">Nama Barang</label>
                        <input type="text" name="name" id="input-name" required placeholder="Contoh: Sabun Cuci" class="form-control">
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-secondary text-uppercase small">Sisa Stok</label>
                            <input type="number" step="any" name="stock" id="input-stock" required min="0" placeholder="0" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-secondary text-uppercase small">Satuan</label>
                            <input type="text" name="unit" id="input-unit" required placeholder="Pcs, Botol, Liter" class="form-control">
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-bold text-secondary text-uppercase small">Batas Minimum Stok (Peringatan)</label>
                        <input type="number" step="any" name="min_stock" id="input-min-stock" required min="0" placeholder="0" class="form-control">
                    </div>
                </div>
                
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary fw-bold" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" id="submit-button" class="btn btn-primary fw-bold">Simpan Barang</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let invModalObj = null;

    document.addEventListener('DOMContentLoaded', function() {
        invModalObj = new bootstrap.Modal(document.getElementById('inventoryModal'));
    });

    function openModal() {
        document.getElementById('modal-title').innerText = 'Tambah Barang';
        document.getElementById('submit-button').innerText = 'Simpan Barang';
        
        document.getElementById('form-method').value = 'POST';
        document.getElementById('inventory-form').action = '{{ route("employee.inventories.store") }}';
        
        document.getElementById('input-name').value = '';
        document.getElementById('input-stock').value = '';
        document.getElementById('input-unit').value = '';
        document.getElementById('input-min-stock').value = '';
        
        invModalObj.show();
    }
    
    function editModal(id, name, stock, unit, minStock) {
        document.getElementById('modal-title').innerText = 'Edit Barang';
        document.getElementById('submit-button').innerText = 'Perbarui Barang';
        
        document.getElementById('form-method').value = 'PUT';
        document.getElementById('inventory-form').action = '/employee/inventories/' + id;
        
        document.getElementById('input-name').value = name;
        document.getElementById('input-stock').value = stock;
        document.getElementById('input-unit').value = unit;
        document.getElementById('input-min-stock').value = minStock;
        
        invModalObj.show();
    }
</script>
@endpush
