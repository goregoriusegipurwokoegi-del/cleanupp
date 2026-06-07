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
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">

    <button onclick="openModal('addModal')" style="background: var(--primary); color: #0f172a; border: none; padding: 0.8rem 1.5rem; border-radius: 12px; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 0.5rem; transition: 0.3s;">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
        Tambah Layanan
    </button>
</div>

@if(session('success'))
    <div style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.2); color: #10b981; padding: 1rem; border-radius: 12px; margin-bottom: 2rem; font-weight: 600;">
        {{ session('success') }}
    </div>
@endif

<div class="data-card">
    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="border-bottom: 1px solid rgba(255,255,255,0.1); opacity: 0.6; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px;">
                    <th style="padding: 1.2rem 1rem;">Layanan</th>
                    <th style="padding: 1.2rem 1rem;">Kategori</th>
                    <th style="padding: 1.2rem 1rem;">Estimasi</th>
                    <th style="padding: 1.2rem 1rem;">Harga</th>
                    <th style="padding: 1.2rem 1rem; text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($services as $service)
                <tr style="border-bottom: 1px solid rgba(255,255,255,0.05); transition: 0.3s;" onmouseover="this.style.background='rgba(255,255,255,0.02)'" onmouseout="this.style.background='transparent'">
                    <td style="padding: 1.5rem 1rem;">
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <div style="width: 50px; height: 50px; background: rgba(0, 210, 255, 0.1); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: var(--primary); overflow: hidden; border: 1px solid rgba(255,255,255,0.1);">
                                @if($service->image)
                                    <img src="{{ asset('storage/' . $service->image) }}" alt="{{ $service->name }}" style="width: 100%; height: 100%; object-fit: cover;">
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
                                <p style="font-weight: 700; color: #fff;">{{ $service->name }}</p>
                                <p style="font-size: 0.8rem; opacity: 0.5; max-width: 300px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $service->description }}</p>
                            </div>
                        </div>
                    </td>
                    <td style="padding: 1.5rem 1rem;">
                        <span class="badge {{ $service->category == 'cleaning' ? 'badge-success' : 'badge-warning' }}">
                            {{ $service->category == 'cleaning' ? 'Pencucian' : 'Reparasi' }}
                        </span>
                    </td>
                    <td style="padding: 1.5rem 1rem; opacity: 0.8;">{{ $service->estimated_time ?? '-' }}</td>
                    <td style="padding: 1.5rem 1rem; font-weight: 700;">Rp {{ number_format($service->price, 0, ',', '.') }}</td>
                    <td style="padding: 1.5rem 1rem; text-align: right;">
                        <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                            <button onclick="editService({{ $service }})" style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: #fff; padding: 0.5rem; border-radius: 8px; cursor: pointer; transition: 0.3s;">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                            </button>
                            <form action="{{ route('admin.services.destroy', $service) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus layanan ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2); color: #ef4444; padding: 0.5rem; border-radius: 8px; cursor: pointer; transition: 0.3s;">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
                @if($services->isEmpty())
                <tr>
                    <td colspan="4" style="padding: 3rem; text-align: center; opacity: 0.5;">Belum ada layanan yang ditambahkan.</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah Layanan -->
<div id="addModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.8); z-index: 1000; align-items: center; justify-content: center; backdrop-filter: blur(5px);">
    <div style="background: #1e293b; width: 90%; max-width: 500px; padding: 2.5rem; border-radius: 24px; border: 1px solid rgba(255,255,255,0.1); position: relative;">
        <button onclick="closeModal('addModal')" style="position: absolute; top: 1.5rem; right: 1.5rem; background: none; border: none; color: #fff; cursor: pointer; opacity: 0.5;"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></button>
        <h3 style="margin-bottom: 1.5rem; font-size: 1.5rem;">Tambah Layanan Baru</h3>
        <form action="{{ route('admin.services.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div style="margin-bottom: 1.2rem;">
                <label style="display: block; font-size: 0.85rem; margin-bottom: 0.5rem; opacity: 0.7;">Nama Layanan</label>
                <input type="text" name="name" required placeholder="Contoh: Deep Cleaning" style="width: 100%; padding: 0.8rem 1rem; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 10px; color: #fff;">
            </div>
            <div style="margin-bottom: 1.2rem;">
                <label style="display: block; font-size: 0.85rem; margin-bottom: 0.5rem; opacity: 0.7;">Deskripsi</label>
                <textarea name="description" rows="3" placeholder="Jelaskan detail layanan..." style="width: 100%; padding: 0.8rem 1rem; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 10px; color: #fff; resize: none;"></textarea>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.2rem;">
                <div>
                    <label style="display: block; font-size: 0.85rem; margin-bottom: 0.5rem; opacity: 0.7;">Harga (Rp)</label>
                    <input type="number" name="price" required placeholder="50000" style="width: 100%; padding: 0.8rem 1rem; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 10px; color: #fff;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.85rem; margin-bottom: 0.5rem; opacity: 0.7;">Kategori</label>
                    <select name="category" required style="width: 100%; padding: 0.8rem 1rem; background: #1e293b; border: 1px solid rgba(255,255,255,0.1); border-radius: 10px; color: #fff; cursor: pointer;">
                        <option value="cleaning">Pencucian</option>
                        <option value="repair">Reparasi</option>
                    </select>
                </div>
            </div>
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-size: 0.85rem; margin-bottom: 0.5rem; opacity: 0.7;">Estimasi Pengerjaan</label>
                <input type="text" name="estimated_time" placeholder="Contoh: 2-3 Hari" style="width: 100%; padding: 0.8rem 1rem; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 10px; color: #fff;">
            </div>
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-size: 0.85rem; margin-bottom: 0.5rem; opacity: 0.7;">Foto Layanan</label>
                <input type="file" name="image" accept="image/*" style="width: 100%; padding: 0.8rem 1rem; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 10px; color: #fff;">
            </div>
            <button type="submit" style="width: 100%; padding: 1rem; background: var(--primary); color: #0f172a; border: none; border-radius: 12px; font-weight: 700; cursor: pointer;">Simpan Layanan</button>
        </form>
    </div>
</div>

<!-- Modal Edit Layanan -->
<div id="editModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.8); z-index: 1000; align-items: center; justify-content: center; backdrop-filter: blur(5px);">
    <div style="background: #1e293b; width: 90%; max-width: 500px; padding: 2.5rem; border-radius: 24px; border: 1px solid rgba(255,255,255,0.1); position: relative;">
        <button onclick="closeModal('editModal')" style="position: absolute; top: 1.5rem; right: 1.5rem; background: none; border: none; color: #fff; cursor: pointer; opacity: 0.5;"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></button>
        <h3 style="margin-bottom: 1.5rem; font-size: 1.5rem;">Edit Layanan</h3>
        <form id="editForm" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div style="margin-bottom: 1.2rem;">
                <label style="display: block; font-size: 0.85rem; margin-bottom: 0.5rem; opacity: 0.7;">Nama Layanan</label>
                <input type="text" name="name" id="edit_name" required style="width: 100%; padding: 0.8rem 1rem; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 10px; color: #fff;">
            </div>
            <div style="margin-bottom: 1.2rem;">
                <label style="display: block; font-size: 0.85rem; margin-bottom: 0.5rem; opacity: 0.7;">Deskripsi</label>
                <textarea name="description" id="edit_description" rows="3" style="width: 100%; padding: 0.8rem 1rem; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 10px; color: #fff; resize: none;"></textarea>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.2rem;">
                <div>
                    <label style="display: block; font-size: 0.85rem; margin-bottom: 0.5rem; opacity: 0.7;">Harga (Rp)</label>
                    <input type="number" name="price" id="edit_price" required style="width: 100%; padding: 0.8rem 1rem; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 10px; color: #fff;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.85rem; margin-bottom: 0.5rem; opacity: 0.7;">Kategori</label>
                    <select name="category" id="edit_category" required style="width: 100%; padding: 0.8rem 1rem; background: #1e293b; border: 1px solid rgba(255,255,255,0.1); border-radius: 10px; color: #fff; cursor: pointer;">
                        <option value="cleaning">Pencucian</option>
                        <option value="repair">Reparasi</option>
                    </select>
                </div>
            </div>
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-size: 0.85rem; margin-bottom: 0.5rem; opacity: 0.7;">Estimasi Pengerjaan</label>
                <input type="text" name="estimated_time" id="edit_estimated_time" placeholder="Contoh: 2-3 Hari" style="width: 100%; padding: 0.8rem 1rem; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 10px; color: #fff;">
            </div>
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-size: 0.85rem; margin-bottom: 0.5rem; opacity: 0.7;">Foto Layanan (Kosongkan jika tidak ingin mengubah)</label>
                <input type="file" name="image" accept="image/*" style="width: 100%; padding: 0.8rem 1rem; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 10px; color: #fff;">
            </div>
            <button type="submit" style="width: 100%; padding: 1rem; background: var(--primary); color: #0f172a; border: none; border-radius: 12px; font-weight: 700; cursor: pointer;">Perbarui Layanan</button>
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
        if (event.target.id === 'addModal') closeModal('addModal');
        if (event.target.id === 'editModal') closeModal('editModal');
    }
</script>
@endsection
