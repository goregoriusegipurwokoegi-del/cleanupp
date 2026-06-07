@extends('layouts.premium-dashboard')

@section('page_title', 'Pengaturan Profil')

@section('nav_items')
    @if(Auth::user()->role == 'admin')
        <li class="nav-item"><a href="{{ route('admin.dashboard') }}" class="nav-link">Dashboard</a></li>
        <li class="nav-item"><a href="{{ route('admin.services.index') }}" class="nav-link">Kelola Layanan</a></li>
    @elseif(Auth::user()->role == 'employee')
        <li class="nav-item"><a href="{{ route('employee.dashboard') }}" class="nav-link">Dashboard</a></li>
    @else
        <li class="nav-item"><a href="{{ route('customer.dashboard') }}" class="nav-link">Dashboard</a></li>
        <li class="nav-item"><a href="{{ route('services.index') }}" class="nav-link">Pesan Layanan</a></li>
        <li class="nav-item"><a href="{{ route('orders.my-orders') }}" class="nav-link">Pesanan Saya</a></li>
        <li class="nav-item"><a href="{{ route('orders.history') }}" class="nav-link">Riwayat</a></li>
    @endif
    <li class="nav-item"><a href="{{ route('profile.edit') }}" class="nav-link active">Pengaturan Profil</a></li>
@endsection

@section('content')


<style>
    .profile-wrap {
        max-width: 680px;
        width: 100%;
    }
    .profile-section {
        margin-bottom: 1.5rem;
    }
    .form-label {
        display: block;
        font-size: 0.82rem;
        margin-bottom: 0.5rem;
        opacity: 0.65;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .form-input {
        width: 100%;
        padding: 0.75rem 1rem;
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 12px;
        color: white;
        font-size: 0.95rem;
        transition: 0.3s;
        font-family: 'Outfit', sans-serif;
    }
    .form-input:focus {
        outline: none;
        border-color: var(--primary);
        background: rgba(255, 255, 255, 0.08);
        box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.15);
    }
    .btn-save {
        background: var(--primary);
        color: #0f172a;
        border: none;
        padding: 0.75rem 1.8rem;
        border-radius: 12px;
        font-weight: 700;
        font-size: 0.9rem;
        cursor: pointer;
        transition: 0.3s;
        font-family: 'Outfit', sans-serif;
        width: 100%;
    }
    .btn-save:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(249, 115, 22, 0.35);
    }
    .alert-success {
        background: rgba(16, 185, 129, 0.1);
        border: 1px solid rgba(16, 185, 129, 0.3);
        color: #10b981;
        padding: 0.9rem 1.2rem;
        border-radius: 12px;
        margin-bottom: 1.5rem;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        gap: 0.6rem;
    }
    .profile-avatar-banner {
        display: flex;
        align-items: center;
        gap: 1.2rem;
        margin-bottom: 1.5rem;
        padding: 1.5rem;
        background: rgba(249, 115, 22, 0.06);
        border: 1px solid rgba(249, 115, 22, 0.12);
        border-radius: 20px;
    }
    .profile-avatar-large {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: var(--primary);
        color: #0f172a;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 900;
        font-size: 1.6rem;
        flex-shrink: 0;
        box-shadow: 0 0 20px rgba(249, 115, 22, 0.3);
    }
    .section-card {
        background: rgba(255,255,255,0.03);
        border: 1px solid rgba(255,255,255,0.07);
        border-radius: 20px;
        padding: 1.8rem;
        margin-bottom: 1.2rem;
    }
    .section-card h3 {
        font-size: 1rem;
        font-weight: 700;
        margin-bottom: 0.3rem;
        color: #fff;
    }
    .section-card p.hint {
        font-size: 0.8rem;
        opacity: 0.5;
        margin-bottom: 1.5rem;
        line-height: 1.5;
    }
    .field-group {
        margin-bottom: 1.2rem;
    }
    .field-error {
        color: #ef4444;
        font-size: 0.78rem;
        margin-top: 0.4rem;
    }
    .logout-card {
        background: rgba(239, 68, 68, 0.04);
        border: 1px solid rgba(239, 68, 68, 0.15);
        border-radius: 20px;
        padding: 1.3rem 1.8rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        flex-wrap: wrap;
    }
    .logout-card h3 {
        font-size: 0.95rem;
        font-weight: 700;
        margin-bottom: 0.2rem;
    }
    .logout-card p {
        font-size: 0.78rem;
        opacity: 0.5;
    }
    .btn-logout-profile {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        background: rgba(239, 68, 68, 0.12);
        color: #ef4444;
        border: 1px solid rgba(239, 68, 68, 0.25);
        padding: 0.65rem 1.3rem;
        border-radius: 10px;
        font-weight: 700;
        font-size: 0.88rem;
        cursor: pointer;
        transition: 0.3s;
        font-family: 'Outfit', sans-serif;
        white-space: nowrap;
    }
    .btn-logout-profile:hover {
        background: rgba(239, 68, 68, 0.22);
    }
    @media (max-width: 480px) {
        .profile-avatar-banner {
            padding: 1.2rem;
            gap: 1rem;
        }
        .profile-avatar-large {
            width: 50px;
            height: 50px;
            font-size: 1.3rem;
        }
        .section-card {
            padding: 1.3rem;
        }
        .logout-card {
            padding: 1.1rem 1.3rem;
        }
    }
    .search-result-item {
        padding: 0.9rem 1.2rem;
        cursor: pointer;
        border-bottom: 1px solid rgba(255,255,255,0.04);
        transition: background 0.2s;
        display: flex;
        align-items: flex-start;
        gap: 0.8rem;
    }
    .search-result-item:hover {
        background: rgba(249, 115, 22, 0.1);
    }
    .search-result-item:last-child {
        border-bottom: none;
    }
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
</style>

<div class="profile-wrap">

    {{-- Alert success --}}
    @if (session('status') === 'profile-updated' || session('status') === 'password-updated')
        <div class="alert-success">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
            Perubahan berhasil disimpan!
        </div>
    @endif

    @if (session('warning'))
        <div style="background: rgba(245,158,11,0.08); border: 1px solid rgba(245,158,11,0.2); color: #f59e0b; padding: 0.9rem 1.2rem; border-radius: 12px; margin-bottom: 1.5rem; font-size: 0.88rem; display: flex; align-items: center; gap: 0.6rem;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            {{ session('warning') }}
        </div>
    @endif

    {{-- Avatar Banner --}}
    <div class="profile-avatar-banner">
        <div class="profile-avatar-large">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
        <div>
            <p style="font-weight: 800; font-size: 1.1rem; margin-bottom: 0.2rem;">{{ Auth::user()->name }}</p>
            <p style="font-size: 0.82rem; opacity: 0.5;">
                @if(Auth::user()->role == 'admin') Administrator
                @elseif(Auth::user()->role == 'employee') Karyawan
                @else Pelanggan @endif
                &nbsp;·&nbsp; {{ Auth::user()->email }}
            </p>
        </div>
    </div>

    {{-- Informasi Profil --}}
    <div class="section-card profile-section">
        <h3>Informasi Profil</h3>
        <p class="hint">Perbarui nama, email, dan nomor WhatsApp Anda.</p>

        <form method="post" action="{{ route('profile.update') }}">
            @csrf
            @method('patch')

            <div class="field-group">
                <label class="form-label">Nama Lengkap</label>
                <input type="text" name="name" class="form-input" value="{{ old('name', $user->name) }}" required autofocus oninput="this.value = this.value.replace(/[0-9]/g, '');">
                @if($errors->get('name'))
                    <p class="field-error">{{ $errors->get('name')[0] }}</p>
                @endif
            </div>

            <div class="field-group">
                <label class="form-label">Alamat Email</label>
                <input type="email" name="email" class="form-input" value="{{ old('email', $user->email) }}" required>
                @if($errors->get('email'))
                    <p class="field-error">{{ $errors->get('email')[0] }}</p>
                @endif
            </div>

            <div class="field-group" style="margin-bottom: 1.5rem;">
                <label class="form-label">Nomor WhatsApp</label>
                <input type="text" name="phone" class="form-input" value="{{ old('phone', $user->phone ?? '') }}" placeholder="Contoh: 08123456789" oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                <p style="opacity: 0.45; font-size: 0.73rem; margin-top: 0.4rem;">Format: 08xxx atau 628xxx</p>
                @if($errors->get('phone'))
                    <p class="field-error">{{ $errors->get('phone')[0] }}</p>
                @endif
            </div>



            <button type="submit" class="btn-save">Simpan Perubahan</button>
        </form>
    </div>

    {{-- Update Password --}}
    <div class="section-card profile-section">
        <h3>Perbarui Kata Sandi</h3>
        <p class="hint">Gunakan kata sandi yang kuat dan unik untuk keamanan akun Anda.</p>

        <form method="post" action="{{ route('password.update') }}">
            @csrf
            @method('put')

            <div class="field-group">
                <label class="form-label">Kata Sandi Saat Ini</label>
                <input type="password" name="current_password" class="form-input" autocomplete="current-password">
                @if($errors->updatePassword->get('current_password'))
                    <p class="field-error">{{ $errors->updatePassword->get('current_password')[0] }}</p>
                @endif
            </div>

            <div class="field-group">
                <label class="form-label">Kata Sandi Baru</label>
                <input type="password" name="password" class="form-input" autocomplete="new-password">
                @if($errors->updatePassword->get('password'))
                    <p class="field-error">{{ $errors->updatePassword->get('password')[0] }}</p>
                @endif
            </div>

            <div class="field-group" style="margin-bottom: 1.5rem;">
                <label class="form-label">Konfirmasi Kata Sandi</label>
                <input type="password" name="password_confirmation" class="form-input" autocomplete="new-password">
                @if($errors->updatePassword->get('password_confirmation'))
                    <p class="field-error">{{ $errors->updatePassword->get('password_confirmation')[0] }}</p>
                @endif
            </div>

            <button type="submit" class="btn-save">Perbarui Kata Sandi</button>
        </form>
    </div>

    {{-- Keluar --}}
    <div class="logout-card">
        <div>
            <h3>Keluar dari Akun</h3>
            <p>Akhiri sesi Anda di perangkat ini.</p>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn-logout-profile">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                    <polyline points="16 17 21 12 16 7"></polyline>
                    <line x1="21" y1="12" x2="9" y2="12"></line>
                </svg>
                Keluar
            </button>
        </form>
    </div>

</div>


@endsection
