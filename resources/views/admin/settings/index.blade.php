@extends('layouts.premium-dashboard')

@section('page_title', 'Pengaturan')

@section('nav_items')
    <li class="nav-item"><a href="{{ route('admin.dashboard') }}" class="nav-link {{ Route::is('admin.dashboard') ? 'active' : '' }}">Dashboard</a></li>
    <li class="nav-item"><a href="{{ route('admin.orders.index') }}" class="nav-link {{ Route::is('admin.orders.index') ? 'active' : '' }}">Kelola Pesanan</a></li>
    <li class="nav-item"><a href="{{ route('admin.services.index') }}" class="nav-link {{ Route::is('admin.services.index') ? 'active' : '' }}">Kelola Layanan</a></li>
    <li class="nav-item"><a href="{{ route('admin.finances.index') }}" class="nav-link {{ Route::is('admin.finances.index') ? 'active' : '' }}">Keuangan</a></li>
    <li class="nav-item"><a href="{{ route('admin.employees.index') }}" class="nav-link {{ Route::is('admin.employees.index') ? 'active' : '' }}">Manajemen Staff</a></li>
    <li class="nav-item"><a href="{{ route('admin.reports.index') }}" class="nav-link {{ Route::is('admin.reports.index') ? 'active' : '' }}">Laporan</a></li>
    <li class="nav-item"><a href="{{ route('admin.settings.index') }}" class="nav-link active">Pengaturan</a></li>
@endsection

@section('content')
<style>
    .tab-bar {
        display: flex;
        gap: 0.5rem;
        margin-bottom: 2rem;
        background: rgba(255,255,255,0.03);
        border: 1px solid rgba(255,255,255,0.06);
        border-radius: 16px;
        padding: 0.4rem;
        overflow-x: auto;
    }
    .tab-btn {
        flex: 1;
        text-align: center;
        padding: 0.8rem 1rem;
        border-radius: 12px;
        font-weight: 700;
        font-size: 0.85rem;
        cursor: pointer;
        text-decoration: none;
        color: rgba(255,255,255,0.5);
        transition: all 0.3s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.4rem;
        white-space: nowrap;
    }
    .tab-btn.active {
        background: var(--primary);
        color: #0f172a;
    }
    .tab-btn:not(.active):hover {
        background: rgba(255,255,255,0.05);
        color: #fff;
    }
    .setting-group {
        margin-bottom: 1.5rem;
    }
    .setting-label {
        display: block; font-size: 0.85rem; font-weight: 700; margin-bottom: 0.5rem; opacity: 0.7;
    }
    .setting-input {
        width: 100%; padding: 1rem 1.2rem; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; color: #fff; outline: none; transition: 0.3s;
    }
    .setting-input:focus {
        border-color: var(--primary);
        background: rgba(255,255,255,0.05);
    }
    .save-btn {
        background: var(--primary); color: #0f172a; border: none; padding: 1rem 2rem; border-radius: 12px; font-weight: 800; cursor: pointer; transition: 0.3s;
    }
    .save-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(226, 232, 240, 0.2);
    }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    .tab-content { animation: fadeIn 0.4s ease; max-width: 800px; }
</style>



@if(session('success'))
<div style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.3); color: #10b981; padding: 1rem; border-radius: 12px; margin-bottom: 1.5rem; font-weight: 700;">
    {{ session('success') }}
</div>
@endif

@if($errors->any())
<div style="background: rgba(244, 63, 94, 0.1); border: 1px solid rgba(244, 63, 94, 0.3); color: #f43f5e; padding: 1rem; border-radius: 12px; margin-bottom: 1.5rem; font-weight: 700;">
    <ul>
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

{{-- Tab Bar --}}
<div class="tab-bar">
    <a href="{{ route('admin.settings.index', ['tab' => 'profil-toko']) }}" class="tab-btn {{ $tab == 'profil-toko' ? 'active' : '' }}">Profil Toko</a>
    <a href="{{ route('admin.settings.index', ['tab' => 'pembayaran']) }}" class="tab-btn {{ $tab == 'pembayaran' ? 'active' : '' }}">Pembayaran</a>
    <a href="{{ route('admin.settings.index', ['tab' => 'antar-jemput']) }}" class="tab-btn {{ $tab == 'antar-jemput' ? 'active' : '' }}">Antar Jemput</a>
    <a href="{{ route('admin.settings.index', ['tab' => 'notifikasi']) }}" class="tab-btn {{ $tab == 'notifikasi' ? 'active' : '' }}">Notifikasi</a>
    <a href="{{ route('admin.settings.index', ['tab' => 'akun-admin']) }}" class="tab-btn {{ $tab == 'akun-admin' ? 'active' : '' }}">Akun Admin</a>
    <a href="{{ route('admin.settings.index', ['tab' => 'backup']) }}" class="tab-btn {{ $tab == 'backup' ? 'active' : '' }}">Backup Data</a>
</div>

<div class="glass-card tab-content">
    
    {{-- TAB: PROFIL TOKO --}}
    @if($tab == 'profil-toko')
    <form action="{{ route('admin.settings.update') }}" method="POST">
        @csrf
        <input type="hidden" name="tab" value="profil-toko">
        <h3 style="font-size: 1.2rem; font-weight: 800; margin-bottom: 1.5rem;">Pengaturan Profil Toko</h3>
        
        <div class="setting-group">
            <label class="setting-label">Nama Toko</label>
            <input type="text" name="store_name" class="setting-input" value="{{ $settings['store_name'] ?? 'CleanUP Shoes' }}">
        </div>
        <div class="setting-group">
            <label class="setting-label">Alamat</label>
            <textarea name="store_address" class="setting-input" rows="3">{{ $settings['store_address'] ?? '' }}</textarea>
        </div>
        <div class="setting-group">
            <label class="setting-label">Titik Lokasi (Maps)</label>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div>
                    <label style="font-size: 0.75rem; opacity: 0.7;">Latitude</label>
                    <input type="text" name="store_latitude" class="setting-input" value="{{ $settings['store_latitude'] ?? '-0.0513462' }}" placeholder="-0.0513462">
                </div>
                <div>
                    <label style="font-size: 0.75rem; opacity: 0.7;">Longitude</label>
                    <input type="text" name="store_longitude" class="setting-input" value="{{ $settings['store_longitude'] ?? '109.3210380' }}" placeholder="109.3210380">
                </div>
            </div>
            <small style="opacity: 0.5;">Koordinat ini digunakan untuk menghitung jarak tarif Antar Jemput pelanggan.</small>
        </div>
        <div class="setting-group">
            <label class="setting-label">WhatsApp</label>
            <input type="text" name="store_whatsapp" class="setting-input" value="{{ $settings['store_whatsapp'] ?? '' }}" oninput="this.value = this.value.replace(/[^0-9]/g, '');">
        </div>
        <div class="setting-group">
            <label class="setting-label">Email</label>
            <input type="email" name="store_email" class="setting-input" value="{{ $settings['store_email'] ?? '' }}">
        </div>
        <div class="setting-group">
            <label class="setting-label">Jam Operasional</label>
            <input type="text" name="store_hours" class="setting-input" value="{{ $settings['store_hours'] ?? 'Senin - Minggu: 09:00 - 20:00' }}">
        </div>
        <div style="text-align: right; margin-top: 2rem;">
            <button type="submit" class="save-btn">Simpan Perubahan</button>
        </div>
    </form>
    @endif

    {{-- TAB: PEMBAYARAN --}}
    @if($tab == 'pembayaran')
    <form action="{{ route('admin.settings.update') }}" method="POST">
        @csrf
        <input type="hidden" name="tab" value="pembayaran">
        <h3 style="font-size: 1.2rem; font-weight: 800; margin-bottom: 1.5rem;">Pengaturan Pembayaran</h3>
        
        <div class="setting-group" id="bank-accounts-container">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <label class="setting-label" style="margin-bottom: 0;">Daftar Rekening Bank</label>
                <button type="button" onclick="addBankAccount()" style="background: rgba(16, 185, 129, 0.1); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.3); padding: 5px 12px; border-radius: 8px; font-size: 0.75rem; font-weight: 800; cursor: pointer;">+ Tambah Rekening</button>
            </div>
            
            <div id="bank-list">
                @php
                    $banks = json_decode($settings['bank_accounts'] ?? '[]', true);
                    if (empty($banks)) {
                        $banks = [
                            ['bank_name' => 'BCA', 'account_number' => '0292.771.400', 'account_holder' => 'Melitha Anggraeni'],
                            ['bank_name' => 'Mandiri', 'account_number' => '146.001.124.9393', 'account_holder' => 'Melitha Anggraeni']
                        ];
                    }
                @endphp
                
                @foreach($banks as $index => $bank)
                <div class="bank-row" style="display: flex; gap: 10px; margin-bottom: 15px; align-items: flex-start;">
                    <div style="flex: 1;">
                        <input type="text" name="bank_accounts[{{ $index }}][bank_name]" class="setting-input" value="{{ $bank['bank_name'] }}" placeholder="Nama Bank (mis: BCA)" required style="margin-bottom: 8px; padding: 0.8rem 1rem;">
                        <input type="text" name="bank_accounts[{{ $index }}][account_number]" class="setting-input" value="{{ $bank['account_number'] }}" placeholder="Nomor Rekening" required style="margin-bottom: 8px; padding: 0.8rem 1rem;">
                        <input type="text" name="bank_accounts[{{ $index }}][account_holder]" class="setting-input" value="{{ $bank['account_holder'] }}" placeholder="Atas Nama" required style="padding: 0.8rem 1rem;">
                    </div>
                    <button type="button" onclick="this.closest('.bank-row').remove()" style="background: rgba(244, 63, 94, 0.1); color: #f43f5e; border: 1px solid rgba(244, 63, 94, 0.2); width: 45px; height: 45px; border-radius: 12px; display: flex; align-items: center; justify-content: center; cursor: pointer; flex-shrink: 0;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                    </button>
                </div>
                @endforeach
            </div>
        </div>
        <hr style="border: 0; border-top: 1px solid rgba(255,255,255,0.1); margin: 2rem 0;">

        <div style="text-align: right; margin-top: 2rem;">
            <button type="submit" class="save-btn">Simpan Perubahan</button>
        </div>
    </form>
    <script>
    let bankCount = {{ isset($banks) ? count($banks) : 2 }};
    function addBankAccount() {
        const container = document.getElementById('bank-list');
        const row = document.createElement('div');
        row.className = 'bank-row';
        row.style.cssText = 'display: flex; gap: 10px; margin-bottom: 15px; align-items: flex-start;';
        row.innerHTML = `
            <div style="flex: 1;">
                <input type="text" name="bank_accounts[${bankCount}][bank_name]" class="setting-input" placeholder="Nama Bank (mis: BCA)" required style="margin-bottom: 8px; padding: 0.8rem 1rem;">
                <input type="text" name="bank_accounts[${bankCount}][account_number]" class="setting-input" placeholder="Nomor Rekening" required style="margin-bottom: 8px; padding: 0.8rem 1rem;">
                <input type="text" name="bank_accounts[${bankCount}][account_holder]" class="setting-input" placeholder="Atas Nama" required style="padding: 0.8rem 1rem;">
            </div>
            <button type="button" onclick="this.closest('.bank-row').remove()" style="background: rgba(244, 63, 94, 0.1); color: #f43f5e; border: 1px solid rgba(244, 63, 94, 0.2); width: 45px; height: 45px; border-radius: 12px; display: flex; align-items: center; justify-content: center; cursor: pointer; flex-shrink: 0;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
            </button>
        `;
        container.appendChild(row);
        bankCount++;
    }
    </script>
    @endif

    {{-- TAB: ANTAR JEMPUT --}}
    @if($tab == 'antar-jemput')
    <form action="{{ route('admin.settings.update') }}" method="POST">
        @csrf
        <input type="hidden" name="tab" value="antar-jemput">
        <h3 style="font-size: 1.2rem; font-weight: 800; margin-bottom: 1.5rem;">Pengaturan Lokasi & Tarif Antar Jemput</h3>
        
        <div class="setting-group">
            <label class="setting-label">Link Google Maps Toko</label>
            <input type="text" name="store_map_link" class="setting-input" value="{{ $settings['store_map_link'] ?? '' }}" placeholder="https://maps.app.goo.gl/...">
        </div>
        <div class="setting-group">
            <label class="setting-label">Batas Jarak Pengantaran Gratis / Standar (KM)</label>
            <input type="number" name="delivery_threshold_km" class="setting-input" value="{{ $settings['delivery_threshold_km'] ?? '5' }}">
            <small style="opacity: 0.5;">Pesanan dengan jarak di bawah angka ini akan menggunakan tarif standar/gratis.</small>
        </div>
        <div class="setting-group">
            <label class="setting-label">Biaya Ekstra jika Melebihi Batas Jarak (Rp)</label>
            <input type="number" name="delivery_fee_above_threshold" class="setting-input" value="{{ $settings['delivery_fee_above_threshold'] ?? '25000' }}">
            <small style="opacity: 0.5;">Biaya yang dikenakan jika jarak melebihi batas KM di atas.</small>
        </div>
        <div style="text-align: right; margin-top: 2rem;">
            <button type="submit" class="save-btn">Simpan Perubahan</button>
        </div>
    </form>
    @endif

    {{-- TAB: NOTIFIKASI --}}
    @if($tab == 'notifikasi')
    <form action="{{ route('admin.settings.update') }}" method="POST">
        @csrf
        <input type="hidden" name="tab" value="notifikasi">
        <h3 style="font-size: 1.2rem; font-weight: 800; margin-bottom: 1.5rem;">Pengaturan Notifikasi Otomatis</h3>
        
        <div class="setting-group">
            <label class="setting-label">Kirim Notifikasi WA saat Pesanan Selesai?</label>
            <select name="notify_order_completed" class="setting-input">
                <option value="yes" {{ ($settings['notify_order_completed'] ?? 'yes') == 'yes' ? 'selected' : '' }} style="color: #000;">Ya, Aktif</option>
                <option value="no" {{ ($settings['notify_order_completed'] ?? 'yes') == 'no' ? 'selected' : '' }} style="color: #000;">Tidak</option>
            </select>
        </div>
        <div class="setting-group">
            <label class="setting-label">Pesan Template WhatsApp</label>
            <textarea name="wa_template_completed" class="setting-input" rows="4">{{ $settings['wa_template_completed'] ?? 'Halo, pesanan cuci sepatu Anda dengan nomor {order_id} sudah selesai dikerjakan! Silakan diambil di toko kami.' }}</textarea>
            <small style="opacity: 0.5;">Gunakan {order_id} untuk menyisipkan nomor pesanan.</small>
        </div>
        <div style="text-align: right; margin-top: 2rem;">
            <button type="submit" class="save-btn">Simpan Perubahan</button>
        </div>
    </form>
    @endif

    {{-- TAB: AKUN ADMIN --}}
    @if($tab == 'akun-admin')
    <form action="{{ route('admin.settings.update-admin') }}" method="POST">
        @csrf
        <h3 style="font-size: 1.2rem; font-weight: 800; margin-bottom: 1.5rem;">Informasi Akun Anda</h3>
        
        <div class="setting-group">
            <label class="setting-label">Nama Admin</label>
            <input type="text" name="name" class="setting-input" value="{{ $user->name }}" required oninput="this.value = this.value.replace(/[0-9]/g, '');">
        </div>
        <div class="setting-group">
            <label class="setting-label">Email Login</label>
            <input type="email" name="email" class="setting-input" value="{{ $user->email }}" required>
        </div>
        <hr style="border: 0; border-top: 1px solid rgba(255,255,255,0.1); margin: 2rem 0;">
        <h4 style="font-size: 1rem; font-weight: 700; margin-bottom: 1rem; color: #f43f5e;">Ubah Password (Opsional)</h4>
        <div class="setting-group">
            <label class="setting-label">Password Baru</label>
            <input type="password" name="password" class="setting-input" placeholder="Biarkan kosong jika tidak ingin mengubah">
        </div>
        <div class="setting-group">
            <label class="setting-label">Konfirmasi Password Baru</label>
            <input type="password" name="password_confirmation" class="setting-input">
        </div>
        <div style="text-align: right; margin-top: 2rem;">
            <button type="submit" class="save-btn">Perbarui Akun</button>
        </div>
    </form>
    @endif

    {{-- TAB: BACKUP DATA --}}
    @if($tab == 'backup')
    <div style="text-align: center; padding: 2rem 0;">
        <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="1.5" style="margin-bottom: 1.5rem;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
        <h3 style="font-size: 1.5rem; font-weight: 800; margin-bottom: 0.5rem;">Backup Database (Simulasi)</h3>
        <p style="opacity: 0.6; margin-bottom: 2rem; max-width: 400px; margin-left: auto; margin-right: auto;">Unduh salinan data seluruh pengguna, pesanan, layanan, dan keuangan sistem Anda.</p>
        
        <button type="button" onclick="Swal.fire({ icon: 'info', title: 'Fitur Segera Hadir', text: 'Fitur backup database sedang diproses. Mohon tunggu update sistem berikutnya!', confirmButtonColor: '#f97316', background: '#121214', color: '#fff' });" style="background: rgba(226, 232, 240, 1); color: #0f172a; border: none; padding: 1rem 2rem; border-radius: 12px; font-weight: 800; cursor: pointer; transition: 0.3s;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
            Download SQL Dump (.sql)
        </button>
    </div>
    @endif

</div>
@endsection
