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


@if(session('success'))
    <div style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.2); color: #10b981; padding: 1rem; border-radius: 12px; margin-bottom: 2rem;">
        {{ session('success') }}
    </div>
@endif

<div class="table-container">
    <table style="width: 100%; border-collapse: collapse; text-align: left; min-width: 700px;">
        <thead>
            <tr style="background: rgba(255,255,255,0.03); border-bottom: 1px solid rgba(255,255,255,0.05);">
                <th style="padding: 1.5rem; font-size: 0.85rem; text-transform: uppercase; opacity: 0.6;">Nama Pelanggan</th>
                <th style="padding: 1.5rem; font-size: 0.85rem; text-transform: uppercase; opacity: 0.6;">Email</th>
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
                <td style="padding: 1.5rem; opacity: 0.8;">
                    {{ $customer->created_at->format('d M Y') }}
                </td>
                <td style="padding: 1.5rem; text-align: center;">
                    <div style="display: flex; justify-content: center; gap: 1rem;">
                        <!-- Edit Button (Reset Password) -->
                        <button onclick="openEditModal({{ json_encode($customer) }})" style="background: transparent; border: none; color: var(--primary); cursor: pointer; opacity: 0.7; transition: 0.3s;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.7'" title="Ubah Password">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
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
                <td colspan="4" style="padding: 2rem; text-align: center; opacity: 0.5;">
                    Belum ada pelanggan terdaftar.
                </td>
            </tr>
            @endif
        </tbody>
    </table>
</div>

<!-- Modal Edit Pelanggan (Reset Password) -->
<div id="edit-customer-modal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.8); z-index: 1000; align-items: center; justify-content: center; backdrop-filter: blur(5px);">
    <div class="glass-card" style="width: 90%; max-width: 450px; padding: 2.5rem; border-radius: 24px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h3 style="font-size: 1.5rem; font-weight: 800;">Edit Data Pelanggan</h3>
            <button onclick="document.getElementById('edit-customer-modal').style.display='none'" style="background: transparent; border: none; color: #fff; cursor: pointer; opacity: 0.5;"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></button>
        </div>
        
        <form id="edit-customer-form" method="POST">
            @csrf
            @method('PUT')
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-size: 0.85rem; margin-bottom: 0.5rem; opacity: 0.7;">Nama Lengkap</label>
                <input type="text" name="name" id="edit-name" required style="width: 100%; padding: 0.8rem 1.2rem; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; color: #fff; outline: none;">
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-size: 0.85rem; margin-bottom: 0.5rem; opacity: 0.7;">Email Login</label>
                <input type="email" name="email" id="edit-email" required style="width: 100%; padding: 0.8rem 1.2rem; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; color: #fff; outline: none;">
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
        document.getElementById('edit-customer-modal').style.display = 'flex';
    }
</script>
@endsection
