@extends('layouts.premium-dashboard')

@section('page_title', 'Manajemen Pelanggan')

@section('nav_items')
    <li class="nav-item"><a href="{{ route('admin.dashboard') }}" class="nav-link {{ Route::is('admin.dashboard') ? 'active' : '' }}">Dashboard</a></li>
    <li class="nav-item"><a href="{{ route('admin.orders.index') }}" class="nav-link {{ Route::is('admin.orders.index') ? 'active' : '' }}">Kelola Pesanan</a></li>
    <li class="nav-item"><a href="{{ route('admin.services.index') }}" class="nav-link {{ Route::is('admin.services.index') ? 'active' : '' }}">Kelola Layanan</a></li>
    <li class="nav-item"><a href="{{ route('admin.finances.index') }}" class="nav-link {{ Route::is('admin.finances.index') ? 'active' : '' }}">Keuangan</a></li>
    <li class="nav-item"><a href="{{ route('admin.employees.index') }}" class="nav-link {{ Route::is('admin.employees.index') ? 'active' : '' }}">Manajemen Staff</a></li>
    <li class="nav-item"><a href="{{ route('admin.customers.index') }}" class="nav-link {{ Route::is('admin.customers.index') ? 'active' : '' }}">Pelanggan</a></li>
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

    .customer-table-container {
        background: var(--surface);
        border: 1.5px solid var(--border-color);
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
    }
    .customer-table-container table td {
        padding: 16px 15px;
        vertical-align: middle;
    }
    .customer-table-container table th {
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
        max-width: 450px;
        padding: 2rem;
        position: relative;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        color: var(--text);
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
    <button onclick="document.getElementById('add-customer-modal').style.display='flex'" class="btn-primary-custom">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><line x1="20" y1="8" x2="20" y2="14"></line><line x1="23" y1="11" x2="17" y2="11"></line></svg>
        Tambah Pelanggan
    </button>
    
    <form action="{{ route('admin.customers.index') }}" method="GET" class="filter-bar" style="flex-grow: 1; justify-content: flex-end;">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, email, no WhatsApp..." class="filter-input" style="flex-grow: 1; max-width: 380px; min-width: 200px;">
    </form>
</div>

@if(session('success'))
    <div style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.2); color: #10b981; padding: 12px; border-radius: 12px; margin-bottom: 20px; font-weight: 600;">
        {{ session('success') }}
    </div>
@endif

@if ($errors->any())
    <div style="background: rgba(244, 63, 94, 0.1); border: 1px solid rgba(244, 63, 94, 0.2); color: #f43f5e; padding: 12px; border-radius: 12px; margin-bottom: 20px; font-weight: 600;">
        <ul style="margin: 0; padding-left: 1.5rem; font-size: 0.85rem;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="customer-table-container">
    <table style="width: 100%; border-collapse: collapse; text-align: left; min-width: 700px;">
        <thead>
            <tr style="background: var(--surface-variant); border-bottom: 1.5px solid var(--border-color);">
                <th style="font-size: 0.8rem; font-weight: 700; color: var(--text-secondary);">Nama Pelanggan</th>
                <th style="font-size: 0.8rem; font-weight: 700; color: var(--text-secondary);">Email</th>
                <th style="font-size: 0.8rem; font-weight: 700; color: var(--text-secondary);">No. HP</th>
                <th style="font-size: 0.8rem; font-weight: 700; color: var(--text-secondary);">Tanggal Daftar</th>
                <th style="font-size: 0.8rem; font-weight: 700; color: var(--text-secondary); text-align: center;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($customers as $customer)
            <tr class="clickable-row">
                <td>
                    <div style="display: flex; align-items: center; gap: 1rem;">
                        <div style="width: 40px; height: 40px; background: var(--primary-glow); color: var(--primary); border-radius: 10px; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1rem;">
                            {{ strtoupper(substr($customer->name, 0, 1)) }}
                        </div>
                        <div style="font-weight: 700;">{{ $customer->name }}</div>
                    </div>
                </td>
                <td style="opacity: 0.95;">{{ $customer->email }}</td>
                <td style="opacity: 0.95;">{{ $customer->phone ?: '-' }}</td>
                <td style="opacity: 0.85; font-size: 0.85rem;">
                    {{ $customer->created_at->format('d M Y') }}
                </td>
                <td>
                    <div style="display: flex; justify-content: center; gap: 0.5rem;">
                        <!-- Edit Button -->
                        <button onclick="openEditModal({{ json_encode($customer) }})" class="action-btn" style="color: var(--primary);" title="Ubah Data">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4L18.5 2.5z"></path></svg>
                        </button>

                        <!-- Delete Button -->
                        <form action="{{ route('admin.customers.destroy', $customer) }}" method="POST" onsubmit="return confirm('Hapus pelanggan ini dari sistem? Perhatian: Semua data pesanan yang terhubung juga mungkin terpengaruh.');" style="margin: 0; display: inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="action-btn" style="color: #f43f5e;" title="Hapus Pelanggan">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
            
            @if($customers->isEmpty())
            <tr>
                <td colspan="5" style="text-align: center; opacity: 0.5; padding: 40px;">
                    Belum ada pelanggan terdaftar.
                </td>
            </tr>
            @endif
        </tbody>
    </table>
</div>

<!-- Modal Tambah Pelanggan -->
<div id="add-customer-modal" class="modal-backdrop-custom" onclick="this.style.display='none'">
    <div class="modal-box-custom" onclick="event.stopPropagation()">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h3 style="font-size: 1.3rem; font-weight: 800;">Tambah Pelanggan Baru</h3>
            <button onclick="document.getElementById('add-customer-modal').style.display='none'" class="modal-close-custom">&times;</button>
        </div>
        
        <form method="POST" action="{{ route('admin.customers.store') }}">
            @csrf
            <div style="margin-bottom: 1.2rem;">
                <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--text-secondary); margin-bottom: 5px; text-transform: uppercase;">Nama Lengkap</label>
                <input type="text" name="name" required placeholder="Nama pelanggan" class="input-custom" oninput="this.value = this.value.replace(/[0-9]/g, '');">
            </div>

            <div style="margin-bottom: 1.2rem;">
                <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--text-secondary); margin-bottom: 5px; text-transform: uppercase;">Email Login</label>
                <input type="email" name="email" required placeholder="email@domain.com" class="input-custom">
            </div>

            <div style="margin-bottom: 1.2rem;">
                <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--text-secondary); margin-bottom: 5px; text-transform: uppercase;">No. HP (WhatsApp)</label>
                <input type="text" name="phone" required placeholder="Contoh: 08123456789" class="input-custom" oninput="this.value = this.value.replace(/[^0-9]/g, '');">
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--text-secondary); margin-bottom: 5px; text-transform: uppercase;">Password Default</label>
                <input type="text" name="password" required value="password123" class="input-custom">
            </div>

            <button type="submit" class="btn-primary-custom" style="width: 100%; justify-content: center; padding: 12px;">Daftarkan Pelanggan</button>
        </form>
    </div>
</div>

<!-- Modal Edit Pelanggan -->
<div id="edit-customer-modal" class="modal-backdrop-custom" onclick="this.style.display='none'">
    <div class="modal-box-custom" onclick="event.stopPropagation()">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h3 style="font-size: 1.3rem; font-weight: 800;">Edit Data Pelanggan</h3>
            <button onclick="document.getElementById('edit-customer-modal').style.display='none'" class="modal-close-custom">&times;</button>
        </div>
        
        <form id="edit-customer-form" method="POST">
            @csrf
            @method('PUT')
            <div style="margin-bottom: 1.2rem;">
                <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--text-secondary); margin-bottom: 5px; text-transform: uppercase;">Nama Lengkap</label>
                <input type="text" name="name" id="edit-name" required class="input-custom" oninput="this.value = this.value.replace(/[0-9]/g, '');">
            </div>

            <div style="margin-bottom: 1.2rem;">
                <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--text-secondary); margin-bottom: 5px; text-transform: uppercase;">Email Login</label>
                <input type="email" name="email" id="edit-email" required class="input-custom">
            </div>

            <div style="margin-bottom: 1.2rem;">
                <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--text-secondary); margin-bottom: 5px; text-transform: uppercase;">No. HP (WhatsApp)</label>
                <input type="text" name="phone" id="edit-phone" required class="input-custom" oninput="this.value = this.value.replace(/[^0-9]/g, '');">
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--text-secondary); margin-bottom: 5px; text-transform: uppercase;">Password Baru (Kosongkan jika tidak ganti)</label>
                <input type="text" name="password" placeholder="Masukkan password baru" class="input-custom">
            </div>

            <button type="submit" class="btn-primary-custom" style="width: 100%; justify-content: center; padding: 12px;">Simpan Perubahan</button>
        </form>
    </div>
</div>

<script>
    function openEditModal(customer) {
        const form = document.getElementById('edit-customer-form');
        form.action = `/admin/customers/${customer.id}`;
        document.getElementById('edit-name').value = customer.name;
        document.getElementById('edit-email').value = customer.email;
        document.getElementById('edit-phone').value = customer.phone || '';
        document.getElementById('edit-customer-modal').style.display = 'flex';
    }
</script>
@endsection
