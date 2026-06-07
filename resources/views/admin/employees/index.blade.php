@extends('layouts.premium-dashboard')

@section('page_title', 'Manajemen Karyawan')

@section('nav_items')
    <li class="nav-item"><a href="{{ route('admin.dashboard') }}" class="nav-link {{ Route::is('admin.dashboard') ? 'active' : '' }}">Dashboard</a></li>
    <li class="nav-item"><a href="{{ route('admin.orders.index') }}" class="nav-link {{ Route::is('admin.orders.index') ? 'active' : '' }}">Kelola Pesanan</a></li>
    <li class="nav-item"><a href="{{ route('admin.services.index') }}" class="nav-link {{ Route::is('admin.services.index') ? 'active' : '' }}">Kelola Layanan</a></li>
    <li class="nav-item"><a href="{{ route('admin.finances.index') }}" class="nav-link {{ Route::is('admin.finances.index') ? 'active' : '' }}">Keuangan</a></li>
    <li class="nav-item"><a href="{{ route('admin.employees.index') }}" class="nav-link {{ Route::is('admin.employees.index') ? 'active' : '' }}">Manajemen Staff</a></li>
    <li class="nav-item"><a href="{{ route('admin.reports.index') }}" class="nav-link {{ Route::is('admin.reports.index') ? 'active' : '' }}">Laporan</a></li>
@endsection

@section('content')
<div class="header-actions" style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">

    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
        <a href="{{ route('admin.employees.attendance') }}" style="background: rgba(255,255,255,0.05); color: #fff; text-decoration: none; padding: 0.8rem 1.5rem; border-radius: 12px; font-weight: 700; border: 1px solid rgba(255,255,255,0.1); display: flex; align-items: center; gap: 0.5rem;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
            Rekap Absensi
        </a>
        <button onclick="document.getElementById('add-employee-modal').style.display='flex'" style="background: var(--primary); color: #0f172a; border: none; padding: 0.8rem 1.5rem; border-radius: 12px; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 0.5rem; transition: 0.3s; white-space: nowrap;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><line x1="20" y1="8" x2="20" y2="14"></line><line x1="23" y1="11" x2="17" y2="11"></line></svg>
            Tambah Karyawan
        </button>
    </div>
</div>

@if(session('success'))
    <div style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.2); color: #10b981; padding: 1rem; border-radius: 12px; margin-bottom: 2rem;">
        {{ session('success') }}
    </div>
@endif

<div class="table-container">
    <table style="width: 100%; border-collapse: collapse; text-align: left; min-width: 700px;">
        <thead>
            <tr style="background: rgba(255,255,255,0.03); border-bottom: 1px solid rgba(255,255,255,0.05);">
                <th style="padding: 1.5rem; font-size: 0.85rem; text-transform: uppercase; opacity: 0.6;">Nama Karyawan</th>
                <th style="padding: 1.5rem; font-size: 0.85rem; text-transform: uppercase; opacity: 0.6;">Email</th>
                <th style="padding: 1.5rem; font-size: 0.85rem; text-transform: uppercase; opacity: 0.6;">Jam Kerja</th>
                <th style="padding: 1.5rem; font-size: 0.85rem; text-transform: uppercase; opacity: 0.6;">Password</th>
                <th style="padding: 1.5rem; font-size: 0.85rem; text-transform: uppercase; opacity: 0.6; text-align: center;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($employees as $employee)
            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02); transition: 0.3s;" onmouseover="this.style.background='rgba(255,255,255,0.01)'" onmouseout="this.style.background='transparent'">
                <td style="padding: 1.5rem;">
                    <div style="display: flex; align-items: center; gap: 1rem;">
                        <div style="width: 40px; height: 40px; background: rgba(0, 210, 255, 0.1); color: var(--primary); border-radius: 10px; display: flex; align-items: center; justify-content: center; font-weight: 800;">
                            {{ strtoupper(substr($employee->name, 0, 1)) }}
                        </div>
                        <div style="font-weight: 700;">{{ $employee->name }}</div>
                    </div>
                </td>
                <td style="padding: 1.5rem; opacity: 0.8;">{{ $employee->email }}</td>
                <td style="padding: 1.5rem;">
                    <div style="background: rgba(16, 185, 129, 0.1); color: #10b981; padding: 0.4rem 0.8rem; border-radius: 8px; font-size: 0.75rem; font-weight: 700; width: fit-content;">
                        {{ \Carbon\Carbon::parse($employee->work_start)->format('H:i') }} - {{ \Carbon\Carbon::parse($employee->work_end)->format('H:i') }}
                    </div>
                </td>
                <td style="padding: 1.5rem;">
                    <div style="display: flex; align-items: center; gap: 0.5rem; background: rgba(255,255,255,0.05); padding: 0.4rem 0.8rem; border-radius: 8px; font-family: monospace; font-size: 0.85rem; width: fit-content;">
                        <span id="pwd-{{ $employee->id }}" style="filter: blur(4px); transition: 0.3s;">{{ $employee->password_plain ?? '********' }}</span>
                        <button onclick="let s=document.getElementById('pwd-{{ $employee->id }}'); s.style.filter=(s.style.filter=='none'?'blur(4px)':'none')" style="background: transparent; border: none; color: var(--primary); cursor: pointer; padding: 0;"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg></button>
                    </div>
                </td>
                <td style="padding: 1.5rem; text-align: center;">
                    <div style="display: flex; justify-content: center; gap: 1rem;">
                        <!-- Edit Button -->
                        <button onclick="openEditModal({{ json_encode($employee) }})" style="background: transparent; border: none; color: var(--primary); cursor: pointer; opacity: 0.7; transition: 0.3s;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.7'">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4L18.5 2.5z"></path></svg>
                        </button>

                        <!-- Delete Button -->
                        <form action="{{ route('admin.employees.destroy', $employee) }}" method="POST" onsubmit="return confirm('Hapus karyawan ini dari sistem?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" style="background: transparent; border: none; color: #f43f5e; cursor: pointer; opacity: 0.7; transition: 0.3s;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.7'">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Modal Tambah Karyawan -->
<div id="add-employee-modal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.8); z-index: 1000; align-items: center; justify-content: center; backdrop-filter: blur(5px);">
    <div class="glass-card" style="width: 90%; max-width: 450px; padding: 2.5rem; border-radius: 24px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h3 style="font-size: 1.5rem; font-weight: 800;">Tambah Staf Baru</h3>
            <button onclick="document.getElementById('add-employee-modal').style.display='none'" style="background: transparent; border: none; color: #fff; cursor: pointer; opacity: 0.5;"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></button>
        </div>
        
        <form method="POST" action="{{ route('admin.employees.store') }}">
            @csrf
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-size: 0.85rem; margin-bottom: 0.5rem; opacity: 0.7;">Nama Lengkap</label>
                <input type="text" name="name" required placeholder="Nama karyawan" style="width: 100%; padding: 0.8rem 1.2rem; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; color: #fff; outline: none; transition: 0.3s;" onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='rgba(255,255,255,0.1)'" oninput="this.value = this.value.replace(/[0-9]/g, '');">
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-size: 0.85rem; margin-bottom: 0.5rem; opacity: 0.7;">Email Login</label>
                <input type="email" name="email" required placeholder="email@cleanup.com" style="width: 100%; padding: 0.8rem 1.2rem; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; color: #fff; outline: none; transition: 0.3s;" onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='rgba(255,255,255,0.1)'">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 2rem;">
                <div>
                    <label style="display: block; font-size: 0.85rem; margin-bottom: 0.5rem; opacity: 0.7;">Jam Masuk</label>
                    <input type="time" name="work_start" required value="09:00" style="width: 100%; padding: 0.8rem 1.2rem; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; color: #fff; outline: none;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.85rem; margin-bottom: 0.5rem; opacity: 0.7;">Jam Pulang</label>
                    <input type="time" name="work_end" required value="17:00" style="width: 100%; padding: 0.8rem 1.2rem; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; color: #fff; outline: none;">
                </div>
            </div>

            <div style="margin-bottom: 2rem;">
                <label style="display: block; font-size: 0.85rem; margin-bottom: 0.5rem; opacity: 0.7;">Password Default</label>
                <input type="text" name="password" required value="password123" style="width: 100%; padding: 0.8rem 1.2rem; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; color: #fff; outline: none;">
            </div>

            <button type="submit" style="width: 100%; background: var(--primary); color: #0f172a; border: none; padding: 1rem; border-radius: 12px; font-weight: 700; cursor: pointer; transition: 0.3s;">Daftarkan Karyawan</button>
        </form>
    </div>
</div>

<!-- Modal Edit Karyawan -->
<div id="edit-employee-modal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.8); z-index: 1000; align-items: center; justify-content: center; backdrop-filter: blur(5px);">
    <div class="glass-card" style="width: 90%; max-width: 450px; padding: 2.5rem; border-radius: 24px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h3 style="font-size: 1.5rem; font-weight: 800;">Edit Data Karyawan</h3>
            <button onclick="document.getElementById('edit-employee-modal').style.display='none'" style="background: transparent; border: none; color: #fff; cursor: pointer; opacity: 0.5;"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></button>
        </div>
        
        <form id="edit-employee-form" method="POST">
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

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                <div>
                    <label style="display: block; font-size: 0.85rem; margin-bottom: 0.5rem; opacity: 0.7;">Jam Masuk</label>
                    <input type="time" name="work_start" id="edit-work-start" required style="width: 100%; padding: 0.8rem 1.2rem; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; color: #fff; outline: none;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.85rem; margin-bottom: 0.5rem; opacity: 0.7;">Jam Pulang</label>
                    <input type="time" name="work_end" id="edit-work-end" required style="width: 100%; padding: 0.8rem 1.2rem; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; color: #fff; outline: none;">
                </div>
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
    function openEditModal(employee) {
        const form = document.getElementById('edit-employee-form');
        form.action = `/admin/employees/${employee.id}`;
        document.getElementById('edit-name').value = employee.name;
        document.getElementById('edit-email').value = employee.email;
        document.getElementById('edit-work-start').value = employee.work_start.substring(0, 5);
        document.getElementById('edit-work-end').value = employee.work_end.substring(0, 5);
        document.getElementById('edit-employee-modal').style.display = 'flex';
    }
</script>
@endsection
