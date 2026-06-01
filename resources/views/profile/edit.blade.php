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
@if(Auth::user()->role == 'customer')
<!-- Leaflet CSS & JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
@endif

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
                <input type="text" name="name" class="form-input" value="{{ old('name', $user->name) }}" required autofocus>
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
                <input type="text" name="phone" class="form-input" value="{{ old('phone', $user->phone ?? '') }}" placeholder="Contoh: 08123456789">
                <p style="opacity: 0.45; font-size: 0.73rem; margin-top: 0.4rem;">Format: 08xxx atau 628xxx</p>
                @if($errors->get('phone'))
                    <p class="field-error">{{ $errors->get('phone')[0] }}</p>
                @endif
            </div>

            @if(Auth::user()->role == 'customer')
            <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid rgba(255,255,255,0.05); margin-bottom: 1.5rem;">
                <h4 style="font-size: 0.95rem; font-weight: 700; margin-bottom: 0.5rem; color: #fff; text-transform: uppercase; letter-spacing: 0.5px;">Alamat Saya (Lokasi Default)</h4>
                <p style="font-size: 0.8rem; opacity: 0.5; margin-bottom: 1.2rem; line-height: 1.4;">Tentukan alamat default Anda agar pengisian lokasi antar jemput terisi secara otomatis.</p>
                
                <!-- Search Address Box -->
                <div class="field-group" style="position: relative; margin-bottom: 1.2rem;">
                    <label class="form-label">Cari Alamat</label>
                    <div style="position: relative;">
                        <input type="text" id="address_search" placeholder="Ketik alamat untuk mencari..." autocomplete="off" class="form-input" style="padding-left: 2.5rem;" onfocus="this.style.borderColor='var(--primary)';" onblur="setTimeout(() => { document.getElementById('search_results').style.display='none'; }, 250)">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2.5" style="position: absolute; left: 0.8rem; top: 50%; transform: translateY(-50%); opacity: 0.7;"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                        <div id="search_results" style="display: none; position: absolute; top: calc(100% + 6px); left: 0; right: 0; background: rgba(20, 20, 30, 0.98); border: 1px solid rgba(255,255,255,0.1); border-radius: 14px; max-height: 200px; overflow-y: auto; z-index: 999; backdrop-filter: blur(20px); box-shadow: 0 12px 40px rgba(0,0,0,0.5);"></div>
                        <div id="search_loading" style="display: none; position: absolute; right: 1rem; top: 50%; transform: translateY(-50%);">
                            <div style="width: 16px; height: 16px; border: 2px solid rgba(249,115,22,0.3); border-top-color: var(--primary); border-radius: 50%; animation: spin 0.6s linear infinite;"></div>
                        </div>
                    </div>
                </div>

                <!-- Leaflet Map -->
                <div class="field-group" style="margin-bottom: 1.2rem;">
                    <label class="form-label">Pilih Lokasi di Peta</label>
                    <div id="map" style="height: 250px; width: 100%; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1); z-index: 1;"></div>
                </div>

                <!-- Address Input -->
                <div class="field-group" style="margin-bottom: 1.2rem;">
                    <label class="form-label">Detail Alamat Lengkap</label>
                    <textarea name="address" id="delivery_address" class="form-input" style="min-height: 80px;" placeholder="Pilih lokasi di peta atau cari untuk mengisi alamat otomatis...">{{ old('address', $user->address ?? '') }}</textarea>
                    @if($errors->get('address'))
                        <p class="field-error">{{ $errors->get('address')[0] }}</p>
                    @endif
                </div>

                <!-- Kecamatan & Kode Pos -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.2rem;">
                    <div>
                        <label class="form-label">Kecamatan</label>
                        <input type="text" name="kecamatan" class="form-input" value="{{ old('kecamatan', $user->kecamatan ?? '') }}" placeholder="Contoh: Sengah Temila">
                        @if($errors->get('kecamatan'))
                            <p class="field-error">{{ $errors->get('kecamatan')[0] }}</p>
                        @endif
                    </div>
                    <div>
                        <label class="form-label">Kode Pos</label>
                        <input type="text" name="postal_code" class="form-input" value="{{ old('postal_code', $user->postal_code ?? '') }}" placeholder="Contoh: 78351">
                        @if($errors->get('postal_code'))
                            <p class="field-error">{{ $errors->get('postal_code')[0] }}</p>
                        @endif
                    </div>
                </div>

                <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude', $user->latitude ?? '') }}">
                <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude', $user->longitude ?? '') }}">
            </div>
            @endif

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

@if(Auth::user()->role == 'customer')
<script>
    let map;
    let marker;
    let userLat = @json($user->latitude);
    let userLng = @json($user->longitude);
    let defaultLat = -0.0513462;
    let defaultLng = 109.3210380;

    function initProfileMap() {
        if (map) return;

        let startLat = userLat ? parseFloat(userLat) : defaultLat;
        let startLng = userLng ? parseFloat(userLng) : defaultLng;
        let zoomLevel = userLat ? 16 : 13;

        map = L.map('map').setView([startLat, startLng], zoomLevel);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(map);

        marker = L.marker([startLat, startLng], { draggable: true }).addTo(map);

        // If no userLat/userLng, try Geolocation
        if (!userLat && navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                map.setView([lat, lng], 15);
                marker.setLatLng([lat, lng]);
                updateCoordinates(lat, lng);
                reverseGeocode(lat, lng);
            });
        }

        marker.on('dragend', function (e) {
            const pos = marker.getLatLng();
            updateCoordinates(pos.lat, pos.lng);
            reverseGeocode(pos.lat, pos.lng);
        });

        map.on('click', function(e) {
            marker.setLatLng(e.latlng);
            updateCoordinates(e.latlng.lat, e.latlng.lng);
            reverseGeocode(e.latlng.lat, e.latlng.lng);
        });
    }

    function updateCoordinates(lat, lng) {
        document.getElementById('latitude').value = lat;
        document.getElementById('longitude').value = lng;
        userLat = lat;
        userLng = lng;
    }

    function reverseGeocode(lat, lng) {
        fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
            .then(res => res.json())
            .then(data => {
                if(data && data.display_name) {
                    document.getElementById('delivery_address').value = data.display_name;
                }
            })
            .catch(err => console.error(err));
    }

    // Address Search
    let searchTimeout = null;
    document.addEventListener('DOMContentLoaded', function() {
        initProfileMap();

        const searchInput = document.getElementById('address_search');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                const query = this.value.trim();
                
                if (query.length < 3) {
                    document.getElementById('search_results').style.display = 'none';
                    document.getElementById('search_loading').style.display = 'none';
                    return;
                }

                document.getElementById('search_loading').style.display = 'block';
                searchTimeout = setTimeout(() => searchAddress(query), 500);
            });
        }
    });

    function searchAddress(query) {
        const resultsContainer = document.getElementById('search_results');
        const loadingIndicator = document.getElementById('search_loading');

        fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&countrycodes=id&limit=5&addressdetails=1`)
            .then(res => res.json())
            .then(data => {
                loadingIndicator.style.display = 'none';
                resultsContainer.innerHTML = '';

                if (data.length === 0) {
                    resultsContainer.innerHTML = `
                        <div style="padding: 1rem; text-align: center; color: rgba(255,255,255,0.4); font-size: 0.85rem;">
                            <p>Alamat tidak ditemukan</p>
                        </div>`;
                    resultsContainer.style.display = 'block';
                    return;
                }

                data.forEach(place => {
                    const item = document.createElement('div');
                    item.className = 'search-result-item';
                    
                    const parts = place.display_name.split(',');
                    const mainText = parts.slice(0, 2).join(',').trim();
                    const subText = parts.slice(2).join(',').trim();
                    
                    item.innerHTML = `
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2.5" style="flex-shrink: 0; margin-top: 2px;">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                            <circle cx="12" cy="10" r="3"></circle>
                        </svg>
                        <div>
                            <div style="font-size: 0.85rem; color: #fff; font-weight: 600; margin-bottom: 2px;">${mainText}</div>
                            <div style="font-size: 0.72rem; color: rgba(255,255,255,0.4); line-height: 1.3;">${subText}</div>
                        </div>`;
                    
                    item.addEventListener('click', function() {
                        selectSearchResult(parseFloat(place.lat), parseFloat(place.lon), place.display_name);
                    });
                    
                    resultsContainer.appendChild(item);
                });
                resultsContainer.style.display = 'block';
            })
            .catch(err => {
                loadingIndicator.style.display = 'none';
                console.error(err);
            });
    }

    function selectSearchResult(lat, lng, displayName) {
        document.getElementById('address_search').value = displayName.split(',').slice(0, 3).join(',').trim();
        document.getElementById('delivery_address').value = displayName;
        document.getElementById('search_results').style.display = 'none';
        
        if (map) {
            map.setView([lat, lng], 16);
            marker.setLatLng([lat, lng]);
        }
        updateCoordinates(lat, lng);
    }
</script>
@endif
@endsection
