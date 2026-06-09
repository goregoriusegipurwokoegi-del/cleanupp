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

<div class="header-actions" style="margin-bottom: 2rem; display: flex; justify-content: flex-end; align-items: center; flex-wrap: wrap; gap: 1rem;">
    <button onclick="document.getElementById('add-customer-modal').style.display='flex'" style="background: var(--primary); color: #0f172a; border: none; padding: 0.8rem 1.5rem; border-radius: 12px; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 0.5rem; transition: 0.3s; white-space: nowrap;">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><line x1="20" y1="8" x2="20" y2="14"></line><line x1="23" y1="11" x2="17" y2="11"></line></svg>
        Tambah Pelanggan
    </button>
</div>

@if(session('success'))
    <div style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.2); color: #10b981; padding: 1rem; border-radius: 12px; margin-bottom: 2rem;">
        {{ session('success') }}
    </div>
@endif

@if ($errors->any())
    <div style="background: rgba(244, 63, 94, 0.1); border: 1px solid rgba(244, 63, 94, 0.2); color: #f43f5e; padding: 1rem; border-radius: 12px; margin-bottom: 2rem;">
        <ul style="margin: 0; padding-left: 1.5rem;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="table-container">
    <table style="width: 100%; border-collapse: collapse; text-align: left; min-width: 700px;">
        <thead>
            <tr style="background: rgba(255,255,255,0.03); border-bottom: 1px solid rgba(255,255,255,0.05);">
                <th style="padding: 1.5rem; font-size: 0.85rem; text-transform: uppercase; opacity: 0.6;">Nama Pelanggan</th>
                <th style="padding: 1.5rem; font-size: 0.85rem; text-transform: uppercase; opacity: 0.6;">Email</th>
                <th style="padding: 1.5rem; font-size: 0.85rem; text-transform: uppercase; opacity: 0.6;">No. HP</th>
                <th style="padding: 1.5rem; font-size: 0.85rem; text-transform: uppercase; opacity: 0.6;">Tanggal Daftar</th>
                <th style="padding: 1.5rem; font-size: 0.85rem; text-transform: uppercase; opacity: 0.6; text-align: center;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($customers as $customer)
            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02); transition: 0.3s;" onmouseover="this.style.background='rgba(255,255,255,0.01)'" onmouseout="this.style.background='transparent'">
                <td style="padding: 1.5rem;">
                    <div style="display: flex; align-items: center; gap: 1rem;">
                        <div style="width: 40px; height: 40px; background: rgba(249, 115, 22, 0.1); color: var(--primary); border-radius: 10px; display: flex; align-items: center; justify-content: center; font-weight: 800;">
                            {{ strtoupper(substr($customer->name, 0, 1)) }}
                        </div>
                        <div style="font-weight: 700;">{{ $customer->name }}</div>
                    </div>
                </td>
                <td style="padding: 1.5rem; opacity: 0.8;">{{ $customer->email }}</td>
                <td style="padding: 1.5rem; opacity: 0.8;">{{ $customer->phone ?? '-' }}</td>
                <td style="padding: 1.5rem; opacity: 0.8;">
                    {{ $customer->created_at->format('d M Y') }}
                </td>
                <td style="padding: 1.5rem; text-align: center;">
                    <div style="display: flex; justify-content: center; gap: 1rem;">
                        <!-- Edit Button (Reset Password & Phone) -->
                        <button onclick="openEditModal({{ json_encode($customer) }})" style="background: transparent; border: none; color: var(--primary); cursor: pointer; opacity: 0.7; transition: 0.3s;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.7'" title="Ubah Data">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4L18.5 2.5z"></path></svg>
                        </button>

                        <!-- Delete Button -->
                        <form action="{{ route('admin.customers.destroy', $customer) }}" method="POST" onsubmit="return confirm('Hapus pelanggan ini dari sistem? Perhatian: Semua data pesanan yang terhubung juga mungkin terpengaruh.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" style="background: transparent; border: none; color: #f43f5e; cursor: pointer; opacity: 0.7; transition: 0.3s;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.7'" title="Hapus Pelanggan">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
            
            @if($customers->isEmpty())
            <tr>
                <td colspan="5" style="padding: 2rem; text-align: center; opacity: 0.5;">
                    Belum ada pelanggan terdaftar.
                </td>
            </tr>
            @endif
        </tbody>
    </table>
</div>

<!-- Modal Tambah Pelanggan -->
<div id="add-customer-modal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.8); z-index: 1000; align-items: center; justify-content: center; backdrop-filter: blur(5px);">
    <div class="glass-card" style="width: 90%; max-width: 450px; padding: 2.5rem; border-radius: 24px; background: #111827; border: 1px solid rgba(255,255,255,0.05); color: #fff;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h3 style="font-size: 1.5rem; font-weight: 800;">Tambah Pelanggan Baru</h3>
            <button onclick="document.getElementById('add-customer-modal').style.display='none'" style="background: transparent; border: none; color: #fff; cursor: pointer; opacity: 0.5;"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></button>
        </div>
        
        <form method="POST" action="{{ route('admin.customers.store') }}">
            @csrf
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-size: 0.85rem; margin-bottom: 0.5rem; opacity: 0.7;">Nama Lengkap</label>
                <input type="text" name="name" required placeholder="Nama pelanggan" style="width: 100%; padding: 0.8rem 1.2rem; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; color: #fff; outline: none; transition: 0.3s;" onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='rgba(255,255,255,0.1)'" oninput="this.value = this.value.replace(/[0-9]/g, '');">
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-size: 0.85rem; margin-bottom: 0.5rem; opacity: 0.7;">Email Login</label>
                <input type="email" name="email" required placeholder="email@domain.com" style="width: 100%; padding: 0.8rem 1.2rem; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; color: #fff; outline: none; transition: 0.3s;" onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='rgba(255,255,255,0.1)'">
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-size: 0.85rem; margin-bottom: 0.5rem; opacity: 0.7;">No. HP (WhatsApp)</label>
                <input type="text" name="phone" required placeholder="Contoh: 08123456789" style="width: 100%; padding: 0.8rem 1.2rem; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; color: #fff; outline: none; transition: 0.3s;" onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='rgba(255,255,255,0.1)'" oninput="this.value = this.value.replace(/[^0-9]/g, '');">
            </div>

            <div style="margin-bottom: 2rem;">
                <label style="display: block; font-size: 0.85rem; margin-bottom: 0.5rem; opacity: 0.7;">Password Default</label>
                <input type="text" name="password" required value="password123" style="width: 100%; padding: 0.8rem 1.2rem; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; color: #fff; outline: none;">
            </div>

            <button type="submit" style="width: 100%; background: var(--primary); color: #0f172a; border: none; padding: 1rem; border-radius: 12px; font-weight: 700; cursor: pointer; transition: 0.3s;">Daftarkan Pelanggan</button>
        </form>
    </div>
</div>

<!-- Modal Edit Pelanggan -->
<div id="edit-customer-modal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.8); z-index: 1000; align-items: center; justify-content: center; backdrop-filter: blur(5px);">
    <div class="glass-card" style="width: 90%; max-width: 450px; padding: 2.5rem; border-radius: 24px; background: #111827; border: 1px solid rgba(255,255,255,0.05); color: #fff;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h3 style="font-size: 1.5rem; font-weight: 800;">Edit Data Pelanggan</h3>
            <button onclick="document.getElementById('edit-customer-modal').style.display='none'" style="background: transparent; border: none; color: #fff; cursor: pointer; opacity: 0.5;"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></button>
        </div>
        
        <form id="edit-customer-form" method="POST">
            @csrf
            @method('PUT')
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-size: 0.85rem; margin-bottom: 0.5rem; opacity: 0.7;">Nama Lengkap</label>
                <input type="text" name="name" id="edit-name" required style="width: 100%; padding: 0.8rem 1.2rem; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; color: #fff; outline: none;" oninput="this.value = this.value.replace(/[0-9]/g, '');">
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-size: 0.85rem; margin-bottom: 0.5rem; opacity: 0.7;">Email Login</label>
                <input type="email" name="email" id="edit-email" required style="width: 100%; padding: 0.8rem 1.2rem; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; color: #fff; outline: none;">
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-size: 0.85rem; margin-bottom: 0.5rem; opacity: 0.7;">No. HP (WhatsApp)</label>
                <input type="text" name="phone" id="edit-phone" required style="width: 100%; padding: 0.8rem 1.2rem; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; color: #fff; outline: none;" oninput="this.value = this.value.replace(/[^0-9]/g, '');">
            </div>

            <div style="margin-bottom: 2rem;">
                <label style="display: block; font-size: 0.85rem; margin-bottom: 0.5rem; opacity: 0.7;">Password Baru (Kosongkan jika tidak ganti)</label>
                <input type="text" name="password" placeholder="Masukkan password baru" style="width: 100%; padding: 0.8rem 1.2rem; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; color: #fff; outline: none;">
            </div>

            <button type="submit" style="width: 100%; background: var(--primary); color: #0f172a; border: none; padding: 1rem; border-radius: 12px; font-weight: 700; cursor: pointer;">Simpan Perubahan</button>
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
