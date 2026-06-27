@extends('layouts.premium-dashboard')

@section('page_title', 'Stok Barang')

@section('nav_items')
    <li class="nav-item"><a href="{{ route('admin.dashboard') }}" class="nav-link {{ Route::is('admin.dashboard') ? 'active' : '' }}">Dashboard</a></li>
    <li class="nav-item"><a href="{{ route('admin.orders.index') }}" class="nav-link {{ Route::is('admin.orders.index') ? 'active' : '' }}">Kelola Pesanan</a></li>
    <li class="nav-item"><a href="{{ route('admin.services.index') }}" class="nav-link {{ Route::is('admin.services.index') ? 'active' : '' }}">Kelola Layanan</a></li>
    <li class="nav-item"><a href="{{ route('admin.finances.index') }}" class="nav-link {{ Route::is('admin.finances.index') ? 'active' : '' }}">Keuangan</a></li>
    <li class="nav-item"><a href="{{ route('admin.employees.index') }}" class="nav-link {{ Route::is('admin.employees.index') ? 'active' : '' }}">Manajemen Staff</a></li>
    <li class="nav-item"><a href="{{ route('admin.reports.index') }}" class="nav-link {{ Route::is('admin.reports.index') ? 'active' : '' }}">Laporan</a></li>
    <li class="nav-item"><a href="{{ route('admin.inventories.index') }}" class="nav-link active">Stok Barang</a></li>
@endsection

@section('content')
<style>
    .glass-card {
        background: var(--surface);
        backdrop-filter: blur(20px);
        border: 1px solid var(--border-color);
        border-radius: 24px;
        padding: 1.5rem;
    }
    .table-container {
        width: 100%;
        overflow-x: auto;
        border-radius: 20px;
        background: var(--surface);
        border: 1px solid var(--border-color);
    }
    table { width: 100%; border-collapse: collapse; text-align: left; min-width: 600px; }
    th { padding: 1.5rem; font-size: 0.85rem; text-transform: uppercase; opacity: 0.6; border-bottom: 1px solid var(--border-color); }
    td { padding: 1.5rem; border-bottom: 1px solid var(--border-color); }
    tr { transition: 0.3s; }
    tr:hover { background: var(--surface-variant); }
    .form-input {
        width: 100%; padding: 0.8rem 1.2rem;
        background: var(--surface-variant);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        color: var(--text);
        outline: none;
        transition: 0.3s;
    }
    .form-input:focus { border-color: var(--primary); }
</style>

<div class="header-actions" style="margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">

    <button onclick="openModal()" style="background: var(--primary); color: #fff; border: none; padding: 0.8rem 1.5rem; border-radius: 12px; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 0.5rem; transition: 0.3s;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
        Tambah Barang
    </button>
</div>

@if(session('success'))
    <div style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.2); color: #10b981; padding: 1rem; border-radius: 12px; margin-bottom: 1.5rem;">
        {{ session('success') }}
    </div>
@endif

<div class="table-container">
    <table>
        <thead>
            <tr style="background: var(--surface-variant);">
                <th>Nama Barang</th>
                <th>Sisa Stok</th>
                <th>Satuan</th>
                <th>Batas Minimum</th>
                <th>Status</th>
                <th style="text-align: center;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($inventories as $inv)
            <tr>
                <td style="font-weight: 700;">{{ $inv->name }}</td>
                <td style="font-weight: 800; font-size: 1.1rem; color: {{ $inv->stock <= $inv->min_stock ? '#f43f5e' : 'var(--text)' }};">
                    {{ rtrim(rtrim(number_format($inv->stock, 2, ',', '.'), '0'), ',') }}
                </td>
                <td style="opacity: 0.8;">{{ $inv->unit }}</td>
                <td style="opacity: 0.8;">{{ rtrim(rtrim(number_format($inv->min_stock, 2, ',', '.'), '0'), ',') }}</td>
                <td>
                    @if($inv->stock <= 0)
                        <span style="background: rgba(244, 63, 94, 0.1); color: #f43f5e; padding: 0.4rem 0.8rem; border-radius: 8px; font-size: 0.75rem; font-weight: 700;">HABIS</span>
                    @elseif($inv->stock <= $inv->min_stock)
                        <span style="background: rgba(245, 158, 11, 0.1); color: #f59e0b; padding: 0.4rem 0.8rem; border-radius: 8px; font-size: 0.75rem; font-weight: 700;">MENIPIS</span>
                    @else
                        <span style="background: rgba(16, 185, 129, 0.1); color: #10b981; padding: 0.4rem 0.8rem; border-radius: 8px; font-size: 0.75rem; font-weight: 700;">AMAN</span>
                    @endif
                </td>
                <td style="text-align: center; display: flex; justify-content: center; gap: 0.5rem;">
                    <button onclick="editModal({{ $inv->id }}, '{{ $inv->name }}', {{ $inv->stock }}, '{{ $inv->unit }}', {{ $inv->min_stock }})" style="background: var(--surface-variant); border: 1px solid var(--border-color); color: var(--primary); padding: 0.5rem; border-radius: 8px; cursor: pointer; transition: 0.3s;"
                        onmouseover="this.style.background='var(--primary-glow)'" onmouseout="this.style.background='var(--surface-variant)'">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                    </button>
                    <form action="{{ route('admin.inventories.destroy', $inv->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus barang ini?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" style="background: rgba(244,63,94,0.05); border: none; color: #f43f5e; padding: 0.5rem; border-radius: 8px; cursor: pointer; transition: 0.3s;" onmouseover="this.style.background='rgba(244,63,94,0.1)'" onmouseout="this.style.background='rgba(244,63,94,0.05)'">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"></path><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @if($inventories->isEmpty())
        <div style="padding: 4rem; text-align: center; opacity: 0.4;">
            <p>Belum ada data barang.</p>
        </div>
    @endif
</div>

<!-- Modal Tambah/Edit Barang -->
<div id="inventory-modal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.8); z-index: 1000; align-items: center; justify-content: center; backdrop-filter: blur(5px);">
    <div class="glass-card" style="width: 90%; max-width: 500px; padding: 2.5rem; border-radius: 24px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h3 id="modal-title" style="font-size: 1.5rem;">Tambah Barang</h3>
            <button onclick="closeModal()" style="background: transparent; border: none; color: var(--text); cursor: pointer; opacity: 0.5;"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></button>
        </div>
        
        <form id="inventory-form" method="POST" action="{{ route('admin.inventories.store') }}">
            @csrf
            <input type="hidden" name="_method" id="form-method" value="POST">
            
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-size: 0.85rem; margin-bottom: 0.5rem; opacity: 0.7;">Nama Barang</label>
                <input type="text" name="name" id="input-name" required placeholder="Contoh: Sabun Cuci" class="form-input" onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='var(--border-color)'">
            </div>

            <div style="display: flex; gap: 1rem; margin-bottom: 1.5rem;">
                <div style="flex: 1;">
                    <label style="display: block; font-size: 0.85rem; margin-bottom: 0.5rem; opacity: 0.7;">Sisa Stok</label>
                    <input type="number" step="any" name="stock" id="input-stock" required min="0" placeholder="0" class="form-input" onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='var(--border-color)'">
                </div>
                <div style="flex: 1;">
                    <label style="display: block; font-size: 0.85rem; margin-bottom: 0.5rem; opacity: 0.7;">Satuan</label>
                    <input type="text" name="unit" id="input-unit" required placeholder="Pcs, Botol, Liter" class="form-input" onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='var(--border-color)'">
                </div>
            </div>

            <div style="margin-bottom: 2rem;">
                <label style="display: block; font-size: 0.85rem; margin-bottom: 0.5rem; opacity: 0.7;">Batas Minimum Stok (Peringatan)</label>
                <input type="number" step="any" name="min_stock" id="input-min-stock" required min="0" placeholder="0" class="form-input" onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='var(--border-color)'">
            </div>

            <button type="submit" id="submit-button" style="width: 100%; background: var(--primary); color: #fff; border: none; padding: 1rem; border-radius: 12px; font-weight: 700; cursor: pointer; transition: 0.3s;">Simpan Barang</button>
        </form>
    </div>
</div>

<script>
    function openModal() {
        document.getElementById('inventory-modal').style.display = 'flex';
        document.getElementById('modal-title').innerText = 'Tambah Barang';
        document.getElementById('submit-button').innerText = 'Simpan Barang';
        
        document.getElementById('form-method').value = 'POST';
        document.getElementById('inventory-form').action = '{{ route("admin.inventories.store") }}';
        
        document.getElementById('input-name').value = '';
        document.getElementById('input-stock').value = '';
        document.getElementById('input-unit').value = '';
        document.getElementById('input-min-stock').value = '';
    }
    
    function editModal(id, name, stock, unit, minStock) {
        document.getElementById('inventory-modal').style.display = 'flex';
        document.getElementById('modal-title').innerText = 'Edit Barang';
        document.getElementById('submit-button').innerText = 'Perbarui Barang';
        
        document.getElementById('form-method').value = 'PUT';
        document.getElementById('inventory-form').action = '/admin/inventories/' + id;
        
        document.getElementById('input-name').value = name;
        document.getElementById('input-stock').value = stock;
        document.getElementById('input-unit').value = unit;
        document.getElementById('input-min-stock').value = minStock;
    }
    
    function closeModal() {
        document.getElementById('inventory-modal').style.display = 'none';
    }
</script>
@endsection
