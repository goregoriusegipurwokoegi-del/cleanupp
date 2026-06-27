@extends('layouts.premium-dashboard')

@section('page_title', 'Kelola Layanan')

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
        white-space: nowrap;
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

    .service-table-container {
        background: var(--surface);
        border: 1.5px solid var(--border-color);
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
    }
    .service-table-container table td {
        padding: 16px 15px;
        vertical-align: middle;
    }
    .service-table-container table th {
        padding: 14px 15px;
        vertical-align: middle;
    }
    .clickable-row {
        transition: background-color 0.2s ease;
        border-bottom: 1px solid var(--border-color);
    }
    .clickable-row:hover {
        background-color: var(--surface-variant) !important;
    }
    
    /* Action Buttons */
    .action-btn {
        background: transparent;
        border: none;
        cursor: pointer;
        opacity: 0.7;
        transition: all 0.2s ease;
        padding: 6px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
    }
    .action-btn:hover {
        opacity: 1;
        background: var(--surface-variant);
        transform: scale(1.05);
    }

    /* Modal Styles */
    .modal-backdrop-custom {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.6);
        backdrop-filter: blur(8px);
        z-index: 1100;
        align-items: center;
        justify-content: center;
        padding: 1.5rem;
    }
    .modal-box-custom {
        background: var(--surface);
        border: 1.5px solid var(--border-color);
        border-radius: 24px;
        width: 100%;
        max-width: 500px;
        padding: 2rem;
        position: relative;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        color: var(--text);
        max-height: 90vh;
        overflow-y: auto;
    }
    .modal-close-custom {
        background: var(--surface-variant);
        border: 1.5px solid var(--border-color);
        color: var(--text);
        font-size: 1.2rem;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: 0.3s;
        opacity: 0.8;
    }
    .modal-close-custom:hover {
        opacity: 1;
        background: var(--border-color);
        color: var(--primary);
    }

    .input-custom {
        width: 100%;
        padding: 0.8rem 1.2rem;
        background: var(--surface-variant);
        border: 1.5px solid var(--border-color);
        border-radius: 12px;
        color: var(--text);
        outline: none;
        transition: 0.3s;
        font-size: 0.9rem;
    }
    .input-custom:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.15);
    }
</style>

<div class="controls-row">
    <button onclick="openModal('addModal')" class="btn-primary-custom">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
        Tambah Layanan
    </button>
    
    <form action="{{ route('admin.services.index') }}" method="GET" class="filter-bar" style="flex-grow: 1; justify-content: flex-end;">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama layanan atau kategori..." class="filter-input" style="flex-grow: 1; max-width: 380px; min-width: 200px;">
    </form>
</div>

@if(session('success'))
    <div style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.2); color: #10b981; padding: 12px; border-radius: 12px; margin-bottom: 20px; font-weight: 600;">
        {{ session('success') }}
    </div>
@endif

<div class="service-table-container">
    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="background: var(--surface-variant); border-bottom: 1.5px solid var(--border-color);">
                    <th style="font-size: 0.8rem; font-weight: 700; color: var(--text-secondary);">Layanan</th>
                    <th style="font-size: 0.8rem; font-weight: 700; color: var(--text-secondary);">Kategori</th>
                    <th style="font-size: 0.8rem; font-weight: 700; color: var(--text-secondary);">Estimasi</th>
                    <th style="font-size: 0.8rem; font-weight: 700; color: var(--text-secondary);">Harga</th>
                    <th style="font-size: 0.8rem; font-weight: 700; color: var(--text-secondary); text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($services as $service)
                <tr class="clickable-row">
                    <td>
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <div style="width: 50px; height: 50px; background: var(--primary-glow); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: var(--primary); overflow: hidden; border: 1px solid var(--border-color); flex-shrink: 0; position: relative;">
                                @if($service->image)
                                    <img src="{{ asset('storage/' . $service->image) }}" alt="{{ $service->name }}" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    {{-- Hidden fallback SVG shown when image fails to load --}}
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none; position:absolute;">
                                        @if($service->category == 'cleaning')
                                            <path d="M12 2v20M2 12h20M4.93 4.93l14.14 14.14M4.93 19.07l14.14-14.14"/>
                                        @else
                                            <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a2 2 0 0 1-2.83-2.83l-3.94 3.6z"/><path d="m20 13-6.83 6.83a2 2 0 0 1-2.83 0l-1.07-1.07-1.59 1.59a1 1 0 0 1-1.4 0l-1.61-1.61a1 1 0 0 1 0-1.4l1.59-1.59-1.07-1.07a2 2 0 0 1 0-2.83L11 5"/><path d="m6.41 11.59 3.18 3.18"/>
                                        @endif
                                    </svg>
                                @else
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        @if($service->category == 'cleaning')
                                            <path d="M12 2v20M2 12h20M4.93 4.93l14.14 14.14M4.93 19.07l14.14-14.14"/>
                                        @else
                                            <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a2 2 0 0 1-2.83-2.83l-3.94 3.6z"/><path d="m20 13-6.83 6.83a2 2 0 0 1-2.83 0l-1.07-1.07-1.59 1.59a1 1 0 0 1-1.4 0l-1.61-1.61a1 1 0 0 1 0-1.4l1.59-1.59-1.07-1.07a2 2 0 0 1 0-2.83L11 5"/><path d="m6.41 11.59 3.18 3.18"/>
                                        @endif
                                    </svg>
                                @endif
                            </div>
                            <div>
                                <p style="font-weight: 700; color: var(--text);">{{ $service->name }}</p>
                                <p style="font-size: 0.8rem; color: var(--text-secondary); max-width: 300px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; opacity: 0.95;">{{ $service->description }}</p>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="badge {{ $service->category == 'cleaning' ? 'badge-success' : 'badge-warning' }}">
                            {{ $service->category == 'cleaning' ? 'Pencucian' : 'Reparasi' }}
                        </span>
                    </td>
                    <td style="opacity: 0.9;">{{ $service->estimated_time ?? '-' }}</td>
                    <td style="font-weight: 700; color: var(--text);">Rp {{ number_format($service->price, 0, ',', '.') }}</td>
                    <td>
                        <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                            <button onclick="editService({{ $service }})" class="action-btn" style="color: var(--primary);" title="Edit Layanan">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                            </button>
                            <form action="{{ route('admin.services.destroy', $service) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus layanan ini?')" style="margin: 0; display: inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="action-btn" style="color: #ef4444;" title="Hapus Layanan">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
                @if($services->isEmpty())
                <tr>
                    <td colspan="5" style="padding: 3rem; text-align: center; opacity: 0.5;">Belum ada layanan yang ditambahkan.</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah Layanan -->
<div id="addModal" class="modal-backdrop-custom" onclick="closeModal('addModal')">
    <div class="modal-box-custom" onclick="event.stopPropagation()">
        <button onclick="closeModal('addModal')" class="modal-close-custom" style="position: absolute; top: 1.5rem; right: 1.5rem;">&times;</button>
        <h3 style="margin-bottom: 1.5rem; font-size: 1.3rem; font-weight: 800;">Tambah Layanan Baru</h3>
        <form action="{{ route('admin.services.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div style="margin-bottom: 1.2rem;">
                <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--text-secondary); margin-bottom: 5px; text-transform: uppercase;">Nama Layanan</label>
                <input type="text" name="name" required placeholder="Contoh: Deep Cleaning" class="input-custom">
            </div>
            <div style="margin-bottom: 1.2rem;">
                <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--text-secondary); margin-bottom: 5px; text-transform: uppercase;">Deskripsi</label>
                <textarea name="description" rows="3" placeholder="Jelaskan detail layanan..." class="input-custom" style="resize: none;"></textarea>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.2rem;">
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--text-secondary); margin-bottom: 5px; text-transform: uppercase;">Harga (Rp)</label>
                    <input type="number" name="price" required placeholder="50000" class="input-custom">
                </div>
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--text-secondary); margin-bottom: 5px; text-transform: uppercase;">Kategori</label>
                    <select name="category" required class="input-custom" style="cursor: pointer; background: var(--surface-variant);">
                        <option value="cleaning">Pencucian</option>
                        <option value="repair">Reparasi</option>
                    </select>
                </div>
            </div>
            <div style="margin-bottom: 1.2rem;">
                <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--text-secondary); margin-bottom: 5px; text-transform: uppercase;">Estimasi Pengerjaan</label>
                <input type="text" name="estimated_time" placeholder="Contoh: 2-3 Hari" class="input-custom">
            </div>
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--text-secondary); margin-bottom: 5px; text-transform: uppercase;">Foto Layanan</label>
                <input type="file" name="image" accept="image/*" class="input-custom">
            </div>
            <button type="submit" class="btn-primary-custom" style="width: 100%; justify-content: center; padding: 12px;">Simpan Layanan</button>
        </form>
    </div>
</div>

<!-- Modal Edit Layanan -->
<div id="editModal" class="modal-backdrop-custom" onclick="closeModal('editModal')">
    <div class="modal-box-custom" onclick="event.stopPropagation()">
        <button onclick="closeModal('editModal')" class="modal-close-custom" style="position: absolute; top: 1.5rem; right: 1.5rem;">&times;</button>
        <h3 style="margin-bottom: 1.5rem; font-size: 1.3rem; font-weight: 800;">Edit Layanan</h3>
        <form id="editForm" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div style="margin-bottom: 1.2rem;">
                <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--text-secondary); margin-bottom: 5px; text-transform: uppercase;">Nama Layanan</label>
                <input type="text" name="name" id="edit_name" required class="input-custom">
            </div>
            <div style="margin-bottom: 1.2rem;">
                <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--text-secondary); margin-bottom: 5px; text-transform: uppercase;">Deskripsi</label>
                <textarea name="description" id="edit_description" rows="3" class="input-custom" style="resize: none;"></textarea>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.2rem;">
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--text-secondary); margin-bottom: 5px; text-transform: uppercase;">Harga (Rp)</label>
                    <input type="number" name="price" id="edit_price" required class="input-custom">
                </div>
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--text-secondary); margin-bottom: 5px; text-transform: uppercase;">Kategori</label>
                    <select name="category" id="edit_category" required class="input-custom" style="cursor: pointer; background: var(--surface-variant);">
                        <option value="cleaning">Pencucian</option>
                        <option value="repair">Reparasi</option>
                    </select>
                </div>
            </div>
            <div style="margin-bottom: 1.2rem;">
                <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--text-secondary); margin-bottom: 5px; text-transform: uppercase;">Estimasi Pengerjaan</label>
                <input type="text" name="estimated_time" id="edit_estimated_time" placeholder="Contoh: 2-3 Hari" class="input-custom">
            </div>
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--text-secondary); margin-bottom: 5px; text-transform: uppercase;">Foto Layanan (Kosongkan jika tidak ingin mengubah)</label>
                <input type="file" name="image" accept="image/*" class="input-custom">
            </div>
            <button type="submit" class="btn-primary-custom" style="width: 100%; justify-content: center; padding: 12px;">Perbarui Layanan</button>
        </form>
    </div>
</div>

<script>
    function openModal(id) {
        document.getElementById(id).style.display = 'flex';
    }

    function closeModal(id) {
        document.getElementById(id).style.display = 'none';
    }

    function editService(service) {
        document.getElementById('edit_name').value = service.name;
        document.getElementById('edit_description').value = service.description;
        document.getElementById('edit_price').value = service.price;
        document.getElementById('edit_category').value = service.category;
        document.getElementById('edit_estimated_time').value = service.estimated_time ?? '';
        
        const form = document.getElementById('editForm');
        form.action = `/admin/services/${service.id}`;
        
        openModal('editModal');
    }

    // Close modal on click outside
    window.onclick = function(event) {
        if (event.target.classList.contains('modal-backdrop-custom')) {
            event.target.style.display = 'none';
        }
    }
</script>
@endsection
