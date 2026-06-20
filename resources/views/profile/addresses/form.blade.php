@extends('layouts.premium-dashboard')

@section('page_title', 'Pengaturan Alamat')

@section('nav_items')
    <li class="nav-item"><a href="{{ route('customer.dashboard') }}" class="nav-link {{ request()->routeIs('customer.dashboard') ? 'active' : '' }}">Dashboard</a></li>
    <li class="nav-item"><a href="{{ route('services.index') }}" class="nav-link {{ request()->routeIs('services.index') ? 'active' : '' }}">Layanan Kami</a></li>
    <li class="nav-item"><a href="{{ route('cart.index') }}" class="nav-link {{ request()->routeIs('cart.index') ? 'active' : '' }}">
        Keranjang 
        @if(Session::has('cart') && count(Session::get('cart')) > 0)
            <span style="background: var(--primary); color: #000; padding: 2px 6px; border-radius: 10px; font-size: 0.7rem; font-weight: 800; margin-left: 5px;">{{ count(Session::get('cart')) }}</span>
        @endif
    </a></li>
    <li class="nav-item"><a href="{{ route('orders.my-orders') }}" class="nav-link {{ request()->routeIs('orders.my-orders') ? 'active' : '' }}">Pesanan Saya</a></li>
    <li class="nav-item"><a href="{{ route('orders.history') }}" class="nav-link {{ request()->routeIs('orders.history') ? 'active' : '' }}">Riwayat</a></li>
    <li class="nav-item"><a href="{{ route('profile.edit') }}" class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">Pengaturan</a></li>
@endsection

@push('styles')
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
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
    select.form-input option {
        background: #0f172a;
        color: #fff;
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
@endpush

@section('content')
<div class="profile-wrap">

    {{-- Alert success --}}
    @if (session('status') === 'address-updated')
        <div class="alert-success">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
            Alamat berhasil diperbarui!
        </div>
    @endif

    <div class="dashboard-header" style="display: flex; align-items: center; gap: 1rem; margin-bottom: 2rem;">
        <a href="{{ route('addresses.index') }}" style="color: rgba(255,255,255,0.7); display: flex; align-items: center;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 18l-6-6 6-6"/></svg>
        </a>
        <h2 class="dashboard-title" style="margin-bottom: 0;">{{ $address->exists ? 'Edit Alamat' : 'Tambah Alamat Baru' }}</h2>
    </div>

    <div class="content-card" style="background: rgba(255, 255, 255, 0.02);">
        <div class="card-body">
            <form method="post" action="{{ $address->exists ? route('addresses.update', $address->id) : route('addresses.store') }}">
                @csrf
                @if($address->exists)
                    @method('PUT')
                @endif

            <h4 style="font-size: 0.9rem; color: var(--primary); margin-bottom: 1rem; margin-top: 0.5rem; text-transform: uppercase; letter-spacing: 1px;">Kontak Penerima</h4>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                <div>
                    <label class="form-label">Nama Penerima</label>
                    <input type="text" name="recipient_name" class="form-input" value="{{ old('recipient_name', $address->recipient_name ?? Auth::user()->name) }}" placeholder="Nama Lengkap" required oninput="this.value = this.value.replace(/[0-9]/g, '');">
                    @if($errors->get('recipient_name')) <p class="field-error">{{ $errors->get('recipient_name')[0] }}</p> @endif
                </div>
                <div>
                    <label class="form-label">Nomor WhatsApp</label>
                    <input type="text" name="phone" class="form-input" value="{{ old('phone', $address->phone ?? Auth::user()->phone) }}" placeholder="08xxxxxxxxxx" required oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                    @if($errors->get('phone')) <p class="field-error">{{ $errors->get('phone')[0] }}</p> @endif
                </div>
            </div>

            <h4 style="font-size: 0.9rem; color: var(--primary); margin-bottom: 1rem; text-transform: uppercase; letter-spacing: 1px;">Detail Alamat</h4>

            <!-- Label Alamat -->
            <div class="field-group" style="margin-bottom: 1.5rem;">
                <label class="form-label">Label Alamat</label>
                <div style="display: flex; gap: 1rem;">
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                        <input type="radio" name="address_label" value="Rumah" {{ (old('address_label', $address->address_label) == 'Rumah' || empty($address->address_label)) ? 'checked' : '' }}>
                        <span style="font-size: 0.9rem; color: #fff;">Rumah</span>
                    </label>
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                        <input type="radio" name="address_label" value="Kantor" {{ old('address_label', $address->address_label) == 'Kantor' ? 'checked' : '' }}>
                        <span style="font-size: 0.9rem; color: #fff;">Kantor</span>
                    </label>
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                        <input type="radio" name="address_label" value="Kos" {{ old('address_label', $address->address_label) == 'Kos' ? 'checked' : '' }}>
                        <span style="font-size: 0.9rem; color: #fff;">Kos</span>
                    </label>
                </div>
            </div>

            <!-- Provinsi & Kota -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div>
                    <label class="form-label">Provinsi</label>
                    <select id="province_select" class="form-input">
                        <option value="">Memuat Provinsi...</option>
                    </select>
                    <input type="hidden" name="province" id="province" value="{{ old('province', $address->province ?? '') }}">
                </div>
                <div>
                    <label class="form-label">Kabupaten/Kota</label>
                    <select id="city_select" class="form-input" disabled>
                        <option value="">Pilih Provinsi Dahulu</option>
                    </select>
                    <input type="hidden" name="city" id="city" value="{{ old('city', $address->city ?? '') }}">
                </div>
            </div>

            <!-- Kecamatan & Kelurahan -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div>
                    <label class="form-label">Kecamatan</label>
                    <select id="kecamatan_select" class="form-input" disabled>
                        <option value="">Pilih Kabupaten/Kota Dahulu</option>
                    </select>
                    <input type="hidden" name="kecamatan" id="kecamatan" value="{{ old('kecamatan', $address->kecamatan ?? '') }}">
                </div>
                <div>
                    <label class="form-label">Kelurahan/Desa</label>
                    <select id="village_select" class="form-input" disabled>
                        <option value="">Pilih Kecamatan Dahulu</option>
                    </select>
                    <input type="hidden" name="village" id="village" value="{{ old('village', $address->village ?? '') }}">
                </div>
            </div>

            <div class="field-group" style="margin-bottom: 1.5rem;">
                <label class="form-label">Kode Pos</label>
                <input type="text" name="postal_code" class="form-input" value="{{ old('postal_code', $address->postal_code ?? '') }}" placeholder="Contoh: 78113" style="width: 50%;" oninput="this.value = this.value.replace(/[^0-9]/g, '');">
            </div>

            <!-- Address Input with Autocomplete -->
            <div class="field-group" style="margin-bottom: 1.2rem; position: relative;">
                <label class="form-label">Alamat Lengkap (Nama Jalan, Gedung, No. Rumah)</label>
                <div style="position: relative;">
                    <textarea name="full_address" id="delivery_address" class="form-input" style="min-height: 80px;" placeholder="Mulai ketik alamat Anda..." onfocus="this.style.borderColor='var(--primary)';" onblur="setTimeout(() => { if(document.getElementById('search_results')) document.getElementById('search_results').style.display='none'; }, 250)">{{ old('full_address', $address->full_address ?? '') }}</textarea>
                    
                    <!-- Autocomplete UI -->
                    <div id="search_results" style="display: none; position: absolute; top: 100%; left: 0; right: 0; background: rgba(20, 20, 30, 0.98); border: 1px solid rgba(255,255,255,0.1); border-radius: 14px; max-height: 200px; overflow-y: auto; z-index: 999; backdrop-filter: blur(20px); box-shadow: 0 12px 40px rgba(0,0,0,0.5);"></div>
                    <div id="search_loading" style="display: none; position: absolute; right: 1rem; top: 20px;">
                        <div style="width: 16px; height: 16px; border: 2px solid rgba(249,115,22,0.3); border-top-color: var(--primary); border-radius: 50%; animation: spin 0.6s linear infinite;"></div>
                    </div>
                </div>
                @if($errors->get('full_address')) <p class="field-error">{{ $errors->get('full_address')[0] }}</p> @endif
            </div>

            <!-- Patokan Lokasi -->
            <div class="field-group" style="margin-bottom: 2rem;">
                <label class="form-label">Patokan Lokasi (Opsional)</label>
                <input type="text" name="address_landmark" class="form-input" value="{{ old('address_landmark', $address->address_landmark ?? '') }}" placeholder="Contoh: Pagar hitam, sebelah warung makan">
            </div>

            <h4 style="font-size: 0.9rem; color: var(--primary); margin-bottom: 1rem; text-transform: uppercase; letter-spacing: 1px;">Titik Lokasi (Map)</h4>

            <!-- Leaflet Map -->
            <div class="field-group" style="margin-bottom: 1.2rem;">
                <label class="form-label">Pastikan Lokasi Peta Sesuai Alamat</label>
                <div id="map" style="height: 300px; width: 100%; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1); z-index: 1;"></div>
            </div>

            <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude', $address->latitude ?? '') }}">
            <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude', $address->longitude ?? '') }}">

            <!-- Jadikan Alamat Utama -->
            <div class="field-group" style="margin-bottom: 2rem;">
                <label style="display: flex; align-items: center; gap: 0.6rem; cursor: pointer;">
                    <input type="checkbox" name="is_main_address" value="1" {{ old('is_main_address', $address->is_main_address ?? ($address->exists ? false : true)) ? 'checked' : '' }} style="width: 18px; height: 18px; accent-color: var(--primary);">
                    <span style="font-size: 0.9rem; color: #fff;">Jadikan Alamat Utama</span>
                </label>
            </div>

            <button type="submit" class="btn-save">Simpan Alamat</button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    let map;
    let marker;
    let userLat = @json(old('latitude', $address->latitude));
    let userLng = @json(old('longitude', $address->longitude));
    let defaultLat = -0.0513462;
    let defaultLng = 109.3210380;

    let initialProvince = @json(old('province', $address->province ?? ''));
    let initialCity = @json(old('city', $address->city ?? ''));
    let initialKecamatan = @json(old('kecamatan', $address->kecamatan ?? ''));
    let initialVillage = @json(old('village', $address->village ?? ''));

    function enableManualMode() {
        if (marker) {
            marker.dragging.enable();
            marker.on('dragend', function (e) {
                const pos = marker.getLatLng();
                updateCoordinates(pos.lat, pos.lng);
                reverseGeocode(pos.lat, pos.lng);
            });
        }
        
        map.on('click', function(e) {
            if (!marker) {
                marker = L.marker(e.latlng, { draggable: true }).addTo(map);
                marker.on('dragend', function (ev) {
                    const pos = marker.getLatLng();
                    updateCoordinates(pos.lat, pos.lng);
                    reverseGeocode(pos.lat, pos.lng);
                });
            } else {
                marker.setLatLng(e.latlng);
            }
            updateCoordinates(e.latlng.lat, e.latlng.lng);
            reverseGeocode(e.latlng.lat, e.latlng.lng);
        });
    }

    function initAddressMap() {
        if (map) return;

        let startLat = userLat ? parseFloat(userLat) : defaultLat;
        let startLng = userLng ? parseFloat(userLng) : defaultLng;
        let zoomLevel = userLat ? 16 : 13;

        map = L.map('map').setView([startLat, startLng], zoomLevel);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(map);

        if (userLat) {
            marker = L.marker([startLat, startLng], { draggable: true }).addTo(map);
            enableManualMode();
        }

        // Try Geolocation if no userLat/userLng
        if (!userLat && navigator.geolocation) {
            // Show some feedback that we are trying to auto-detect
            const loadingMsg = document.createElement('div');
            loadingMsg.id = 'geo-loading';
            loadingMsg.innerHTML = '<div style="background: rgba(0,0,0,0.8); color: white; padding: 10px 15px; border-radius: 8px; position: absolute; top: 10px; left: 50%; transform: translateX(-50%); z-index: 1000; font-size: 0.85rem; display: flex; align-items: center; gap: 8px;"><div style="width: 14px; height: 14px; border: 2px solid rgba(249,115,22,0.3); border-top-color: var(--primary); border-radius: 50%; animation: spin 0.6s linear infinite;"></div> Mendeteksi lokasi otomatis...</div>';
            document.getElementById('map').appendChild(loadingMsg);

            navigator.geolocation.getCurrentPosition(function(position) {
                if(document.getElementById('geo-loading')) document.getElementById('geo-loading').remove();
                
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                map.setView([lat, lng], 16);
                
                if (!marker) {
                    marker = L.marker([lat, lng], { draggable: false }).addTo(map);
                } else {
                    marker.setLatLng([lat, lng]);
                    marker.dragging.disable();
                }
                updateCoordinates(lat, lng);
                reverseGeocode(lat, lng);
            }, function(error) {
                if(document.getElementById('geo-loading')) document.getElementById('geo-loading').remove();
                console.log("Geolocation failed or denied.");
                enableManualMode();
                
                // Provide visual feedback if auto-detect fails
                const errorMsg = document.createElement('div');
                errorMsg.innerHTML = '<div style="background: rgba(249,115,22,0.9); color: white; padding: 8px 12px; border-radius: 8px; position: absolute; top: 10px; left: 50%; transform: translateX(-50%); z-index: 1000; font-size: 0.8rem; text-align: center;">GPS Gagal.<br>Mode Pencarian Manual Diaktifkan.</div>';
                document.getElementById('map').appendChild(errorMsg);
                setTimeout(() => errorMsg.remove(), 6000);
            }, { timeout: 10000 });
        } else if (!userLat) {
             enableManualMode();
        }
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

    // Address Search Auto-Complete
    let searchTimeout = null;
    document.addEventListener('DOMContentLoaded', function() {
        initAddressMap();

        const searchInput = document.getElementById('delivery_address');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                const query = this.value.trim();
                
                if (query.length < 5) {
                    document.getElementById('search_results').style.display = 'none';
                    document.getElementById('search_loading').style.display = 'none';
                    return;
                }

                document.getElementById('search_loading').style.display = 'block';
                searchTimeout = setTimeout(() => searchAddress(query), 600);
            });
        }

        // Initialize Wilayah Dropdowns
        initWilayahDropdowns();

        // Chained selects listeners
        const provinceSelect = document.getElementById('province_select');
        const citySelect = document.getElementById('city_select');
        const kecamatanSelect = document.getElementById('kecamatan_select');
        const villageSelect = document.getElementById('village_select');

        provinceSelect.addEventListener('change', function() {
            const val = this.value;
            const hidden = document.getElementById('province');
            const cityHidden = document.getElementById('city');
            const kecHidden = document.getElementById('kecamatan');
            const vilHidden = document.getElementById('village');

            if (!val) {
                hidden.value = '';
                citySelect.innerHTML = '<option value="">Pilih Provinsi Dahulu</option>';
                citySelect.disabled = true;
                cityHidden.value = '';
                kecamatanSelect.innerHTML = '<option value="">Pilih Kabupaten/Kota Dahulu</option>';
                kecamatanSelect.disabled = true;
                kecHidden.value = '';
                villageSelect.innerHTML = '<option value="">Pilih Kecamatan Dahulu</option>';
                villageSelect.disabled = true;
                vilHidden.value = '';
                return;
            }

            hidden.value = this.options[this.selectedIndex].text;
            citySelect.innerHTML = '<option value="">Memuat Kabupaten/Kota...</option>';
            citySelect.disabled = true;
            cityHidden.value = '';
            kecamatanSelect.innerHTML = '<option value="">Pilih Kabupaten/Kota Dahulu</option>';
            kecamatanSelect.disabled = true;
            kecHidden.value = '';
            villageSelect.innerHTML = '<option value="">Pilih Kecamatan Dahulu</option>';
            villageSelect.disabled = true;
            vilHidden.value = '';

            loadCities(val);
        });

        citySelect.addEventListener('change', function() {
            const val = this.value;
            const hidden = document.getElementById('city');
            const kecHidden = document.getElementById('kecamatan');
            const vilHidden = document.getElementById('village');

            if (!val) {
                hidden.value = '';
                kecamatanSelect.innerHTML = '<option value="">Pilih Kabupaten/Kota Dahulu</option>';
                kecamatanSelect.disabled = true;
                kecHidden.value = '';
                villageSelect.innerHTML = '<option value="">Pilih Kecamatan Dahulu</option>';
                villageSelect.disabled = true;
                vilHidden.value = '';
                return;
            }

            hidden.value = this.options[this.selectedIndex].text;
            kecamatanSelect.innerHTML = '<option value="">Memuat Kecamatan...</option>';
            kecamatanSelect.disabled = true;
            kecHidden.value = '';
            villageSelect.innerHTML = '<option value="">Pilih Kecamatan Dahulu</option>';
            villageSelect.disabled = true;
            vilHidden.value = '';

            loadDistricts(val);
        });

        kecamatanSelect.addEventListener('change', function() {
            const val = this.value;
            const hidden = document.getElementById('kecamatan');
            const vilHidden = document.getElementById('village');

            if (!val) {
                hidden.value = '';
                villageSelect.innerHTML = '<option value="">Pilih Kecamatan Dahulu</option>';
                villageSelect.disabled = true;
                vilHidden.value = '';
                return;
            }

            hidden.value = this.options[this.selectedIndex].text;
            villageSelect.innerHTML = '<option value="">Memuat Kelurahan/Desa...</option>';
            villageSelect.disabled = true;
            vilHidden.value = '';

            loadVillages(val);
        });

        villageSelect.addEventListener('change', function() {
            const val = this.value;
            const hidden = document.getElementById('village');
            if (!val) {
                hidden.value = '';
                return;
            }
            hidden.value = this.options[this.selectedIndex].text;
        });
    });

    async function loadProvinces() {
        try {
            const response = await fetch('/customer/api/wilayah/provinces');
            const data = await response.json();
            const select = document.getElementById('province_select');
            select.innerHTML = '<option value="">Pilih Provinsi</option>';
            data.forEach(item => {
                select.innerHTML += `<option value="${item.kode}">${item.nama}</option>`;
            });
        } catch (error) {
            console.error('Error loading provinces:', error);
        }
    }

    async function loadCities(provinceId) {
        try {
            const response = await fetch(`/customer/api/wilayah/cities?province_id=${provinceId}`);
            const data = await response.json();
            const select = document.getElementById('city_select');
            select.innerHTML = '<option value="">Pilih Kabupaten/Kota</option>';
            data.forEach(item => {
                select.innerHTML += `<option value="${item.kode}">${item.nama}</option>`;
            });
            select.disabled = false;
        } catch (error) {
            console.error('Error loading cities:', error);
        }
    }

    async function loadDistricts(cityId) {
        try {
            const response = await fetch(`/customer/api/wilayah/districts?city_id=${cityId}`);
            const data = await response.json();
            const select = document.getElementById('kecamatan_select');
            select.innerHTML = '<option value="">Pilih Kecamatan</option>';
            data.forEach(item => {
                select.innerHTML += `<option value="${item.kode}">${item.nama}</option>`;
            });
            select.disabled = false;
        } catch (error) {
            console.error('Error loading districts:', error);
        }
    }

    async function loadVillages(districtId) {
        try {
            const response = await fetch(`/customer/api/wilayah/villages?district_id=${districtId}`);
            const data = await response.json();
            const select = document.getElementById('village_select');
            select.innerHTML = '<option value="">Pilih Kelurahan/Desa</option>';
            data.forEach(item => {
                select.innerHTML += `<option value="${item.kode}">${item.nama}</option>`;
            });
            select.disabled = false;
        } catch (error) {
            console.error('Error loading villages:', error);
        }
    }

    const cleanStr = str => str ? str.toLowerCase().replace(/^(provinsi|kabupaten|kab\.|kota|kecamatan|kelurahan|desa|dki)\s+/i, '').replace(/\s+(kabupaten|kab\.|kota|kecamatan|kelurahan|desa)$/i, '').replace(/\s+/g, ' ').trim() : '';

    async function initWilayahDropdowns() {
        await loadProvinces();
        
        if (initialProvince) {
            const provSelect = document.getElementById('province_select');
            let matchProvCode = '';
            const cleanProv = cleanStr(initialProvince);
            
            for (let i = 0; i < provSelect.options.length; i++) {
                if (cleanStr(provSelect.options[i].text) === cleanProv || provSelect.options[i].text.toLowerCase().includes(cleanProv)) {
                    matchProvCode = provSelect.options[i].value;
                    provSelect.selectedIndex = i;
                    break;
                }
            }
            
            if (matchProvCode && initialCity) {
                await loadCities(matchProvCode);
                const citySelect = document.getElementById('city_select');
                let matchCityCode = '';
                const cleanCity = cleanStr(initialCity);
                
                for (let i = 0; i < citySelect.options.length; i++) {
                    if (cleanStr(citySelect.options[i].text) === cleanCity || citySelect.options[i].text.toLowerCase().includes(cleanCity)) {
                        matchCityCode = citySelect.options[i].value;
                        citySelect.selectedIndex = i;
                        break;
                    }
                }
                
                if (matchCityCode && initialKecamatan) {
                    await loadDistricts(matchCityCode);
                    const kecSelect = document.getElementById('kecamatan_select');
                    let matchKecCode = '';
                    const cleanKec = cleanStr(initialKecamatan);
                    
                    for (let i = 0; i < kecSelect.options.length; i++) {
                        if (cleanStr(kecSelect.options[i].text) === cleanKec || kecSelect.options[i].text.toLowerCase().includes(cleanKec)) {
                            matchKecCode = kecSelect.options[i].value;
                            kecSelect.selectedIndex = i;
                            break;
                        }
                    }
                    
                    if (matchKecCode && initialVillage) {
                        await loadVillages(matchKecCode);
                        const vilSelect = document.getElementById('village_select');
                        const cleanVil = cleanStr(initialVillage);
                        
                        for (let i = 0; i < vilSelect.options.length; i++) {
                            if (cleanStr(vilSelect.options[i].text) === cleanVil || vilSelect.options[i].text.toLowerCase().includes(cleanVil)) {
                                vilSelect.selectedIndex = i;
                                break;
                            }
                        }
                    }
                }
            }
        }
    }

    async function matchAndSelectRegion(provName, cityName, kecName, vilName) {
        if (!provName) return;
        
        // 1. Find matching province
        const provSelect = document.getElementById('province_select');
        const provOpts = provSelect.options;
        let matchProvValue = '';
        const cleanProvSearch = cleanStr(provName);
        
        for (let i = 0; i < provOpts.length; i++) {
            if (cleanStr(provOpts[i].text) === cleanProvSearch || provOpts[i].text.toLowerCase().includes(cleanProvSearch)) {
                matchProvValue = provOpts[i].value;
                provSelect.selectedIndex = i;
                break;
            }
        }
        
        if (matchProvValue) {
            document.getElementById('province').value = provSelect.options[provSelect.selectedIndex].text;
            
            // 2. Fetch and select city
            await loadCities(matchProvValue);
            const citySelect = document.getElementById('city_select');
            let matchCityValue = '';
            if (cityName) {
                const cleanCitySearch = cleanStr(cityName);
                const cityOpts = citySelect.options;
                for (let i = 0; i < cityOpts.length; i++) {
                    if (cleanStr(cityOpts[i].text) === cleanCitySearch || cityOpts[i].text.toLowerCase().includes(cleanCitySearch)) {
                        matchCityValue = cityOpts[i].value;
                        citySelect.selectedIndex = i;
                        break;
                    }
                }
            }
            
            if (matchCityValue) {
                document.getElementById('city').value = citySelect.options[citySelect.selectedIndex].text;
                
                // 3. Fetch and select kecamatan
                await loadDistricts(matchCityValue);
                const kecSelect = document.getElementById('kecamatan_select');
                let matchKecValue = '';
                if (kecName) {
                    const cleanKecSearch = cleanStr(kecName);
                    const kecOpts = kecSelect.options;
                    for (let i = 0; i < kecOpts.length; i++) {
                        if (cleanStr(kecOpts[i].text) === cleanKecSearch || kecOpts[i].text.toLowerCase().includes(cleanKecSearch)) {
                            matchKecValue = kecOpts[i].value;
                            kecSelect.selectedIndex = i;
                            break;
                        }
                    }
                }
                
                if (matchKecValue) {
                    document.getElementById('kecamatan').value = kecSelect.options[kecSelect.selectedIndex].text;
                    
                    // 4. Fetch and select village
                    await loadVillages(matchKecValue);
                    const vilSelect = document.getElementById('village_select');
                    let matchVilValue = '';
                    if (vilName) {
                        const cleanVilSearch = cleanStr(vilName);
                        const vilOpts = vilSelect.options;
                        for (let i = 0; i < vilOpts.length; i++) {
                            if (cleanStr(vilOpts[i].text) === cleanVilSearch || vilOpts[i].text.toLowerCase().includes(cleanVilSearch)) {
                                matchVilValue = vilOpts[i].value;
                                vilSelect.selectedIndex = i;
                                break;
                            }
                        }
                    }
                    
                    if (matchVilValue) {
                        document.getElementById('village').value = vilSelect.options[vilSelect.selectedIndex].text;
                    } else {
                        document.getElementById('village').value = '';
                    }
                } else {
                    document.getElementById('kecamatan').value = '';
                    document.getElementById('village').value = '';
                }
            } else {
                document.getElementById('city').value = '';
                document.getElementById('kecamatan').value = '';
                document.getElementById('village').value = '';
            }
        } else {
            document.getElementById('province').value = '';
            document.getElementById('city').value = '';
            document.getElementById('kecamatan').value = '';
            document.getElementById('village').value = '';
        }
    }

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
                        selectSearchResult(place);
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

    function selectSearchResult(place) {
        document.getElementById('delivery_address').value = place.display_name;
        document.getElementById('search_results').style.display = 'none';
        
        let pName = '', cName = '', kName = '', vName = '';
        
        // Auto-fill form fields if available
        if (place.address) {
            pName = place.address.state || place.address.region || place.address.state_district || '';
            cName = place.address.city || place.address.county || place.address.municipality || place.address.town || place.address.city_district || '';
            kName = place.address.city_district || place.address.suburb || place.address.county || place.address.municipality || '';
            vName = place.address.village || place.address.neighbourhood || place.address.residential || place.address.suburb || place.address.hamlet || '';
            
            if (place.address.postcode) document.querySelector('input[name="postal_code"]').value = place.address.postcode;
        } else {
            // Fallback: parse display_name
            const parts = place.display_name.split(',').map(s => s.trim());
            if (parts.length >= 4) {
                pName = parts[parts.length - 2] || '';
                cName = parts[parts.length - 3] || '';
            }
        }
        
        // Match and select dropdowns
        matchAndSelectRegion(pName, cName, kName, vName);

        const lat = parseFloat(place.lat);
        const lng = parseFloat(place.lon);
        
        if (map) {
            map.setView([lat, lng], 16);
            if (marker) {
                marker.setLatLng([lat, lng]);
            } else {
                marker = L.marker([lat, lng], { draggable: true }).addTo(map);
            }
        }
        updateCoordinates(lat, lng);
    }
</script>
@endpush
