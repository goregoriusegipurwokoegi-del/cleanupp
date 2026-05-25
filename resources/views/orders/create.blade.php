@extends('layouts.premium-dashboard')

@section('page_title', '')

@section('nav_items')
    <li class="nav-item"><a href="{{ route('customer.dashboard') }}" class="nav-link">Dashboard</a></li>
    <li class="nav-item"><a href="{{ route('services.index') }}" class="nav-link active">Layanan Kami</a></li>
    <li class="nav-item"><a href="{{ route('orders.my-orders') }}" class="nav-link">Pesanan Saya</a></li>
    <li class="nav-item"><a href="{{ route('orders.history') }}" class="nav-link">Riwayat</a></li>
    <li class="nav-item"><a href="{{ route('profile.edit') }}" class="nav-link">Pengaturan</a></li>
@endsection

@section('content')
<style>
    .order-grid {
        display: grid; 
        grid-template-columns: 1.6fr 1fr; 
        gap: 2rem;
        align-items: start;
    }
    @media (max-width: 1024px) {
        .order-grid {
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }
        .summary-container {
            position: static !important;
            order: 2;
        }
    }
    @media (max-width: 480px) {
        .glass-card {
            padding: 1.2rem !important;
        }
    }
    .payment-card.active {
        background: rgba(249, 115, 22, 0.05) !important;
        border: 2px solid var(--primary) !important;
    }
</style>

<div style="max-width: 1100px; margin: 0 auto; padding-top: 1rem;" class="form-container">
    <form id="order_form" action="{{ route('orders.store') }}" method="POST" enctype="multipart/form-data" onsubmit="return handleFormSubmit()">
        @csrf
        
        <div class="order-grid">
            <!-- Left Side: Inputs -->
            <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                <div class="glass-card" style="padding: 2rem; border-radius: 24px; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05);">
                        <label style="display: block; font-size: 0.9rem; font-weight: 700; margin-bottom: 1.2rem; color: #fff; text-transform: uppercase; letter-spacing: 1px;">
                            Layanan Utama 
                            @if(isset($selectedService))
                                <span onclick="toggleServiceList()" id="change-service-btn" style="float: right; font-size: 0.7rem; color: var(--primary); cursor: pointer; text-transform: none; font-weight: 600; text-decoration: underline;">Ubah Layanan</span>
                            @endif
                        </label>

                        <!-- Selected Service Display -->
                        <div id="selected-service-card" style="display: {{ isset($selectedService) ? 'block' : 'none' }}; margin-bottom: 1rem;">
                            @if(isset($selectedService))
                            <div class="service-card active" style="background: rgba(249, 115, 22, 0.05); border: 2px solid var(--primary); padding: 1.2rem; border-radius: 20px; display: flex; align-items: center; gap: 1rem;">
                                <div style="width: 45px; height: 45px; background: rgba(249, 115, 22, 0.1); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2.5"><path d="M12 2v20M2 12h20"/></svg>
                                </div>
                                <div>
                                    <h5 style="font-size: 1.1rem; margin-bottom: 0.2rem; color: #fff;" id="selected-service-name">{{ $selectedService->name }}</h5>
                                    <p style="font-size: 0.9rem; font-weight: 700; color: var(--primary);" id="selected-service-price">Rp {{ number_format($selectedService->price, 0, ',', '.') }}</p>
                                </div>
                                <div style="margin-left: auto; background: var(--primary); color: #0f172a; width: 22px; height: 22px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="4"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                </div>
                            </div>
                            @endif
                        </div>

                        <!-- Full Service List (Hidden if service is selected) -->
                        <div id="service-list-container" style="display: {{ isset($selectedService) ? 'none' : 'grid' }}; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1rem;">
                            @foreach($services->where('category', $selectedService->category ?? 'cleaning') as $service)
                                <div class="service-card {{ (isset($selectedService) && $selectedService->id == $service->id) ? 'active' : '' }}" 
                                     onclick="selectMainService(this, {{ $service->id }}, {{ $service->price }}, '{{ $service->estimated_time ?? '2-3 Hari' }}', '{{ $service->name }}')"
                                     style="background: rgba(255,255,255,0.03); border: 2px solid {{ (isset($selectedService) && $selectedService->id == $service->id) ? 'var(--primary)' : 'rgba(255,255,255,0.05)' }}; padding: 1.5rem; border-radius: 20px; cursor: pointer; transition: 0.3s; position: relative; overflow: hidden;"
                                     onmouseover="if(!this.classList.contains('active')) this.style.background='rgba(255,255,255,0.06)'" 
                                     onmouseout="if(!this.classList.contains('active')) this.style.background='rgba(255,255,255,0.03)'">
                                    
                                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
                                        <div style="width: 35px; height: 35px; background: {{ $service->category == 'cleaning' ? 'rgba(249, 115, 22, 0.1)' : 'rgba(251, 146, 60, 0.1)' }}; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                                            @if($service->category == 'cleaning')
                                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2"><path d="M12 2v20M2 12h20"/></svg>
                                            @else
                                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--secondary)" stroke-width="2"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a2 2 0 0 1-2.83-2.83l-3.94 3.6z"/></svg>
                                            @endif
                                        </div>
                                    </div>

                                    <h5 style="font-size: 1rem; margin-bottom: 0.3rem; color: #fff;">{{ $service->name }}</h5>
                                    <p style="font-size: 0.85rem; font-weight: 700; color: {{ $service->category == 'cleaning' ? 'var(--primary)' : 'var(--secondary)' }};">Rp {{ number_format($service->price, 0, ',', '.') }}</p>
                                </div>
                            @endforeach
                        </div>
                        <input type="hidden" name="service_id" id="selected_service_id" value="{{ $selectedService->id ?? '' }}">


                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; font-size: 0.9rem; font-weight: 600; margin-bottom: 1rem; color: #fff;">Perawatan Tambahan (Opsional)</label>
                        
                        <!-- Group: Cuci -->
                        <div onclick="toggleAddonSection('cleaning')" style="display: flex; justify-content: space-between; align-items: center; cursor: pointer; padding: 0.5rem 0; border-bottom: 1px solid rgba(255,255,255,0.05); margin-bottom: 0.8rem;">
                            <p style="font-size: 0.75rem; color: var(--primary); font-weight: 800; text-transform: uppercase; margin: 0; letter-spacing: 1px;">Pembersihan</p>
                            <svg id="icon-cleaning" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" style="transition: 0.3s; opacity: 0.5;"><path d="M6 9l6 6 6-6"/></svg>
                        </div>
                        <div id="section-cleaning" style="display: none; flex-direction: column; gap: 0.8rem; margin-bottom: 1.5rem;">
                            @foreach($services->where('category', 'cleaning') as $service)
                                @if(!isset($selectedService) || $selectedService->id != $service->id)
                                    <label style="display: flex; align-items: center; gap: 0.8rem; cursor: pointer; padding: 0.8rem; border-radius: 12px; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05); transition: 0.3s;" onmouseover="this.style.background='rgba(255,255,255,0.05)'" onmouseout="this.style.background='rgba(255,255,255,0.03)'">
                                        <input type="checkbox" name="additional_services[]" value="{{ $service->id }}" data-price="{{ $service->price }}" onchange="updatePrice()" style="width: 18px; height: 18px; accent-color: var(--primary);">
                                        <span style="font-size: 0.9rem;">{{ $service->name }} (+Rp {{ number_format($service->price, 0, ',', '.') }})</span>
                                    </label>
                                @endif
                            @endforeach
                        </div>

                        <!-- Group: Reparasi -->
                        <div onclick="toggleAddonSection('repair')" style="display: flex; justify-content: space-between; align-items: center; cursor: pointer; padding: 0.5rem 0; border-bottom: 1px solid rgba(255,255,255,0.05); margin-bottom: 0.8rem;">
                            <p style="font-size: 0.75rem; color: var(--secondary); font-weight: 800; text-transform: uppercase; margin: 0; letter-spacing: 1px;">Reparasi & Perbaikan</p>
                            <svg id="icon-repair" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" style="transition: 0.3s; opacity: 0.5;"><path d="M6 9l6 6 6-6"/></svg>
                        </div>
                        <div id="section-repair" style="display: none; flex-direction: column; gap: 0.8rem;">
                            @foreach($services->where('category', 'repair') as $service)
                                @if(!isset($selectedService) || $selectedService->id != $service->id)
                                    <label style="display: flex; align-items: center; gap: 0.8rem; cursor: pointer; padding: 0.8rem; border-radius: 12px; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05); transition: 0.3s;" onmouseover="this.style.background='rgba(255,255,255,0.05)'" onmouseout="this.style.background='rgba(255,255,255,0.03)'">
                                        <input type="checkbox" name="additional_services[]" value="{{ $service->id }}" data-price="{{ $service->price }}" onchange="updatePrice()" style="width: 18px; height: 18px; accent-color: var(--secondary);">
                                        <span style="font-size: 0.9rem;">{{ $service->name }} (+Rp {{ number_format($service->price, 0, ',', '.') }})</span>
                                    </label>
                                @endif
                            @endforeach
                        </div>
                    </div>

                    <!-- Pilih Pengerjaan (Segmented Control) -->
                    <div style="margin-bottom: 2rem;">
                        <label style="display: block; font-size: 0.8rem; font-weight: 800; margin-bottom: 1rem; color: #fff; text-transform: uppercase; letter-spacing: 1.5px; opacity: 0.8;">Pilih Kecepatan</label>
                        <div style="display: flex; background: rgba(255,255,255,0.03); padding: 0.4rem; border-radius: 16px; border: 1px solid rgba(255,255,255,0.05); gap: 0.4rem;">
                            <label style="flex: 1; cursor: pointer; position: relative;">
                                <input type="radio" name="processing_speed" value="regular" checked style="display: none;" onchange="toggleSpeed(this)">
                                <div class="speed-option-v2 active" style="padding: 0.8rem; border-radius: 12px; text-align: center; font-weight: 700; font-size: 0.9rem; transition: 0.4s; color: #fff; display: flex; align-items: center; justify-content: center; gap: 0.6rem; background: var(--primary); color: #0f172a; box-shadow: 0 4px 15px rgba(249, 115, 22, 0.2);">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 2v20M2 12h20"/></svg>
                                    Regular
                                </div>
                            </label>
                            <label style="flex: 1; cursor: pointer; position: relative;">
                                <input type="radio" name="processing_speed" value="express" style="display: none;" onchange="toggleSpeed(this)">
                                <div class="speed-option-v2" style="padding: 0.8rem; border-radius: 12px; text-align: center; font-weight: 700; font-size: 0.9rem; transition: 0.4s; color: #fff; display: flex; align-items: center; justify-content: center; gap: 0.6rem; opacity: 0.4;">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
                                    Express
                                </div>
                            </label>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1rem; margin-bottom: 2rem;">
                        <div>
                            <label style="display: block; font-size: 0.8rem; font-weight: 800; margin-bottom: 0.8rem; color: #fff; text-transform: uppercase; letter-spacing: 1.5px; opacity: 0.8;">Nama Sepatu</label>
                            <input type="text" name="shoe_name" placeholder="Nike, Adidas, dll" required style="width: 100%; padding: 1rem 1.2rem; border-radius: 14px; background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.1); color: #fff; font-size: 1rem; outline: none; transition: 0.3s;" onfocus="this.style.borderColor='var(--primary)'; this.style.background='rgba(255,255,255,0.04)'" onblur="this.style.borderColor='rgba(255,255,255,0.1)'; this.style.background='rgba(255,255,255,0.02)'">
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.8rem; font-weight: 800; margin-bottom: 0.8rem; color: #fff; text-transform: uppercase; letter-spacing: 1.5px; opacity: 0.8;">Size</label>
                            <input type="text" name="shoe_size" placeholder="42" required style="width: 100%; padding: 1rem; border-radius: 14px; background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.1); color: #fff; font-size: 1rem; outline: none; transition: 0.3s; text-align: center;" onfocus="this.style.borderColor='var(--primary)'; this.style.background='rgba(255,255,255,0.04)'" onblur="this.style.borderColor='rgba(255,255,255,0.1)'; this.style.background='rgba(255,255,255,0.02)'">
                        </div>
                    </div>

                    <!-- Metode Pembayaran (Collapsible List) -->
                    <div style="margin-bottom: 2.5rem;">
                        <label style="display: block; font-size: 0.8rem; font-weight: 800; margin-bottom: 1rem; color: #fff; text-transform: uppercase; letter-spacing: 1.5px; opacity: 0.8;">Metode Pembayaran</label>
                        
                        <!-- Current Selection Display / Toggle Button -->
                        <div onclick="togglePaymentList()" id="payment-selector" style="display: flex; align-items: center; justify-content: space-between; padding: 1.2rem; border-radius: 16px; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.1); cursor: pointer; transition: 0.3s;" onmouseover="this.style.borderColor='var(--primary)'" onmouseout="this.style.borderColor='rgba(255,255,255,0.1)'">
                            <div style="display: flex; align-items: center; gap: 1rem;">
                                <div id="selected-method-icon" style="width: 32px; height: 32px; background: rgba(249, 115, 22, 0.1); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2.5"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                                </div>
                                <span id="selected-method-name" style="font-weight: 700; color: #fff;">Tunai (Bayar di Outlet)</span>
                            </div>
                            <svg id="payment-chevron" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="transition: 0.3s; opacity: 0.5;"><polyline points="6 9 12 15 18 9"></polyline></svg>
                        </div>

                        <!-- Dua Pilihan Metode Pembayaran -->
                        <div id="payment-methods-list" style="display: none; margin-top: 1rem; overflow: hidden; border-radius: 20px; background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05);">

                            <!-- Bayar di Tempat -->
                            <div style="padding: 0.6rem 1.2rem; background: rgba(255,255,255,0.03); border-bottom: 1px solid rgba(255,255,255,0.05);">
                                <p style="font-size: 0.65rem; font-weight: 800; color: var(--primary); text-transform: uppercase; letter-spacing: 1px;">Bayar di Tempat</p>
                            </div>
                            <label style="display: flex; align-items: center; justify-content: space-between; padding: 1.2rem; cursor: pointer; transition: 0.3s; border-bottom: 1px solid rgba(255,255,255,0.03);" onmouseover="this.style.background='rgba(255,255,255,0.03)'" onmouseout="this.style.background='transparent'">
                                <input type="radio" name="payment_method" value="cash" checked style="display: none;" onchange="updatePaymentUI(this, 'Bayar di Tempat (Tunai)')">
                                <div style="display: flex; align-items: center; gap: 1rem;">
                                    <div style="width: 36px; height: 36px; background: rgba(249, 115, 22, 0.1); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                                    </div>
                                    <div>
                                        <span style="font-weight: 700; font-size: 0.95rem; color: #fff; display: block;">Bayar di Tempat</span>
                                        <span style="font-size: 0.78rem; color: var(--text-secondary);">Bayar tunai saat antar sepatu ke outlet</span>
                                    </div>
                                </div>
                                <div class="checkmark-icon active" style="width: 22px; height: 22px; border-radius: 50%; border: 2px solid var(--primary); background: var(--primary); display: flex; align-items: center; justify-content: center; transition: 0.3s; flex-shrink: 0;">
                                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="#000" stroke-width="4"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                </div>
                            </label>

                            <!-- Transfer Bank -->
                            <div style="padding: 0.6rem 1.2rem; background: rgba(255,255,255,0.03); border-bottom: 1px solid rgba(255,255,255,0.05);">
                                <p style="font-size: 0.65rem; font-weight: 800; color: var(--primary); text-transform: uppercase; letter-spacing: 1px;">Transfer Bank</p>
                            </div>
                            <label style="display: flex; align-items: center; justify-content: space-between; padding: 1.2rem; cursor: pointer; transition: 0.3s;" onmouseover="this.style.background='rgba(255,255,255,0.03)'" onmouseout="this.style.background='transparent'">
                                <input type="radio" name="payment_method" value="transfer" style="display: none;" onchange="updatePaymentUI(this, 'Transfer Bank')">
                                <div style="display: flex; align-items: center; gap: 1rem;">
                                    <div style="width: 36px; height: 36px; background: rgba(59, 130, 246, 0.1); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#3b82f6" stroke-width="2"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                                    </div>
                                    <div>
                                        <span style="font-weight: 700; font-size: 0.95rem; color: #fff; display: block;">Transfer Bank</span>
                                        <span style="font-size: 0.78rem; color: var(--text-secondary);">Transfer ke rekening outlet, konfirmasi via admin</span>
                                    </div>
                                </div>
                                <div class="checkmark-icon" style="width: 22px; height: 22px; border-radius: 50%; border: 2px solid rgba(255,255,255,0.15); background: transparent; display: flex; align-items: center; justify-content: center; transition: 0.3s; flex-shrink: 0;">
                                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="#000" stroke-width="4" style="display: none;"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                </div>
                            </label>
                        </div>
                    </div>

                        <label style="display: block; font-size: 0.9rem; font-weight: 600; margin-bottom: 0.8rem; color: #fff;">Foto Sepatu (Wajib 2 Foto)</label>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                            <!-- Photo 1 -->
                            <div style="position: relative;">
                                <input type="file" name="shoe_photo" accept="image/*" capture="environment" style="display: none;" id="shoe_photo_input" required onchange="previewImage(this, 'photo_preview_1', 'photo_label_1')">
                                <div onclick="document.getElementById('shoe_photo_input').click()" style="padding: 1.5rem 1rem; border-radius: 12px; border: 2px dashed rgba(255,255,255,0.1); background: rgba(255,255,255,0.02); text-align: center; cursor: pointer; transition: 0.3s; min-height: 180px; display: flex; flex-direction: column; align-items: center; justify-content: center;" onmouseover="this.style.borderColor='var(--primary)'; this.style.background='rgba(0, 210, 255, 0.02)'" onmouseout="this.style.borderColor='rgba(255,255,255,0.1)'; this.style.background='rgba(255,255,255,0.02)'">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="opacity: 0.5; margin-bottom: 0.5rem;"><path d="M14.5 4h-5L7 7H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-3l-2.5-3z"/><circle cx="12" cy="13" r="3"/></svg>
                                    <p style="font-size: 0.7rem; opacity: 0.5; line-height: 1.2;" id="photo_label_1">Foto Tampak Samping</p>
                                    <img id="photo_preview_1" style="display: none; width: 100%; max-height: 120px; object-fit: contain; margin-top: 0.8rem; border-radius: 8px;">
                                </div>
                            </div>
                            <!-- Photo 2 -->
                            <div style="position: relative;">
                                <input type="file" name="shoe_photo_2" accept="image/*" capture="environment" style="display: none;" id="shoe_photo_input_2" required onchange="previewImage(this, 'photo_preview_2', 'photo_label_2')">
                                <div onclick="document.getElementById('shoe_photo_input_2').click()" style="padding: 1.5rem 1rem; border-radius: 12px; border: 2px dashed rgba(255,255,255,0.1); background: rgba(255,255,255,0.02); text-align: center; cursor: pointer; transition: 0.3s; min-height: 180px; display: flex; flex-direction: column; align-items: center; justify-content: center;" onmouseover="this.style.borderColor='var(--primary)'; this.style.background='rgba(0, 210, 255, 0.02)'" onmouseout="this.style.borderColor='rgba(255,255,255,0.1)'; this.style.background='rgba(255,255,255,0.02)'">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="opacity: 0.5; margin-bottom: 0.5rem;"><path d="M14.5 4h-5L7 7H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-3l-2.5-3z"/><circle cx="12" cy="13" r="3"/></svg>
                                    <p style="font-size: 0.7rem; opacity: 0.5; line-height: 1.2;" id="photo_label_2">Foto Tampak Bawah/Satu Lagi</p>
                                    <img id="photo_preview_2" style="display: none; width: 100%; max-height: 120px; object-fit: contain; margin-top: 0.8rem; border-radius: 8px;">
                                </div>
                            </div>
                        </div>
                    <div id="form_action_step" style="margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid rgba(255,255,255,0.05);">
                        <button type="button" onclick="showSummary()" style="width: 100%; padding: 1.2rem; border-radius: 16px; background: var(--primary); color: #0f172a; border: none; font-weight: 800; font-size: 1rem; cursor: pointer; transition: 0.3s; box-shadow: 0 10px 20px rgba(249, 115, 22, 0.2);" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform='translateY(0)'">
                            Buat Pesanan
                        </button>
                    </div>
                </div>
            </div>

            <!-- Right Side: Summary -->
            <div class="summary-container" id="summary_section" style="display: none;">
                <div class="glass-card" style="padding: 2rem; border-radius: 24px; background: linear-gradient(135deg, rgba(255,255,255,0.05), rgba(255,255,255,0.02)); border: 1px solid rgba(255,255,255,0.1); position: sticky; top: 2rem;">
                    <h3 style="font-size: 1.2rem; font-weight: 700; margin-bottom: 1.5rem;">Ringkasan Biaya</h3>
                    
                    <div style="display: flex; justify-content: space-between; margin-bottom: 1rem;">
                        <span style="opacity: 0.6;">Biaya Layanan</span>
                        <span id="display_price" style="font-weight: 600;">Rp 0</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 1.5rem;">
                        <span style="opacity: 0.6;">Pajak & Biaya Admin</span>
                        <span style="font-weight: 600; color: #10b981;">FREE</span>
                    </div>
                    
                    <div style="display: flex; justify-content: space-between; margin-bottom: 1.5rem;">
                        <span style="opacity: 0.6;">Estimasi Selesai</span>
                        <span id="display_time" style="font-weight: 600; color: var(--primary);">2-3 Hari</span>
                    </div>

                    <div style="border-top: 1px solid rgba(255,255,255,0.1); padding-top: 1.5rem; margin-bottom: 2rem;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-weight: 700; font-size: 1.1rem;">Total Bayar</span>
                            <span id="display_total" style="font-weight: 800; font-size: 1.5rem; color: var(--primary);">Rp 0</span>
                        </div>
                    </div>

                    <button type="submit" id="submit_button" style="width: 100%; padding: 1.2rem; border-radius: 16px; background: var(--primary); color: #0f172a; border: none; font-weight: 800; font-size: 1rem; cursor: pointer; transition: 0.3s; box-shadow: 0 10px 20px rgba(249, 115, 22, 0.2);" onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 15px 30px rgba(249, 115, 22, 0.3)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 10px 20px rgba(249, 115, 22, 0.2)'">
                        Konfirmasi Pesanan
                    </button>
                    <p style="text-align: center; font-size: 0.75rem; opacity: 0.4; margin-top: 1.5rem;">
                        Dengan mengklik tombol di atas, Anda menyetujui syarat dan ketentuan layanan kami.
                    </p>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    let currentMainPrice = {{ $selectedService->price ?? 0 }};
    let currentEstimatedTime = '{{ $selectedService->estimated_time ?? '2-3 Hari' }}';

    function toggleServiceList() {
        const list = document.getElementById('service-list-container');
        const btn = document.getElementById('change-service-btn');
        if (list.style.display === 'none') {
            list.style.display = 'grid';
            btn.innerText = 'Batal Ubah';
        } else {
            list.style.display = 'none';
            btn.innerText = 'Ubah Layanan';
        }
    }

    function selectMainService(element, id, price, time, name) {
        // Remove active class from all cards
        document.querySelectorAll('.service-card').forEach(card => {
            card.classList.remove('active');
            card.style.borderColor = 'rgba(255,255,255,0.05)';
            card.style.background = 'rgba(255,255,255,0.03)';
        });

        // Add active class to selected card
        element.classList.add('active');
        element.style.borderColor = 'var(--primary)';
        element.style.background = 'rgba(255,255,255,0.06)';
        
        // Update selected service display
        document.getElementById('selected-service-name').innerText = name;
        document.getElementById('selected-service-price').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(price);
        document.getElementById('selected-service-card').style.display = 'block';
        
        // Hide list and reset button
        document.getElementById('service-list-container').style.display = 'none';
        const changeBtn = document.getElementById('change-service-btn');
        if (changeBtn) changeBtn.innerText = 'Ubah Layanan';

        // Update hidden input and local variables
        document.getElementById('selected_service_id').value = id;
        currentMainPrice = price;
        currentEstimatedTime = time;

        updatePrice();
    }

    function updatePrice() {
        let totalPrice = currentMainPrice;
        let estimatedTime = currentEstimatedTime;
        
        // Add additional services
        const checkboxes = document.querySelectorAll('input[name="additional_services[]"]:checked');
        checkboxes.forEach(cb => {
            totalPrice += parseInt(cb.getAttribute('data-price'));
        });

        // Add express premium
        const expressRadio = document.querySelector('input[name="processing_speed"][value="express"]');
        if (expressRadio && expressRadio.checked) {
            totalPrice += 25000;
            estimatedTime = "1 Hari (Kilat)";
        }

        const formatted = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(totalPrice);
        
        document.getElementById('display_price').innerText = formatted;
        document.getElementById('display_total').innerText = formatted;
        document.getElementById('display_time').innerText = estimatedTime;
    }

    function togglePaymentList() {
        const list = document.getElementById('payment-methods-list');
        const chevron = document.getElementById('payment-chevron');
        if (list.style.display === 'none') {
            list.style.display = 'block';
            chevron.style.transform = 'rotate(180deg)';
        } else {
            list.style.display = 'none';
            chevron.style.transform = 'rotate(0deg)';
        }
    }

    function updatePaymentUI(el, name) {
        // Update selection display
        document.getElementById('selected-method-name').innerText = name;
        
        // Find all checkmark icons in the payment list
        const checks = document.querySelectorAll('.checkmark-icon');
        checks.forEach(check => {
            check.style.background = 'transparent';
            check.style.borderColor = 'rgba(255,255,255,0.1)';
            check.querySelector('svg').style.display = 'none';
        });
        
        // Find all parent labels to reset background
        const labels = document.querySelectorAll('#payment-methods-list label');
        labels.forEach(label => {
            label.style.background = 'transparent';
        });

        const activeCheck = el.parentElement.querySelector('.checkmark-icon');
        if (activeCheck) {
            activeCheck.style.background = 'var(--primary)';
            activeCheck.style.borderColor = 'var(--primary)';
            activeCheck.querySelector('svg').style.display = 'block';
            activeCheck.querySelector('svg').style.stroke = '#000';
        }

        // Keep background active
        el.parentElement.style.background = 'rgba(249, 115, 22, 0.05)';

        // Close list after selection (optional, but requested behavior usually implies this)
        setTimeout(togglePaymentList, 300);
    }

    function toggleSpeed(el) {
        const options = el.closest('.order-grid').querySelectorAll('.speed-option-v2');
        options.forEach(opt => {
            opt.classList.remove('active');
            opt.style.background = 'transparent';
            opt.style.color = '#fff';
            opt.style.boxShadow = 'none';
            opt.style.opacity = '0.4';
        });
        
        const div = el.nextElementSibling;
        div.classList.add('active');
        div.style.background = 'var(--primary)';
        div.style.color = '#0f172a';
        div.style.boxShadow = '0 4px 15px rgba(249, 115, 22, 0.2)';
        div.style.opacity = '1';
        
        updatePrice();
    }

    function previewImage(input, previewId, labelId) {
        const preview = document.getElementById(previewId);
        const label = document.getElementById(labelId);
        
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
                label.innerText = 'Foto dipilih';
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function toggleAddonSection(type) {
        const section = document.getElementById('section-' + type);
        const icon = document.getElementById('icon-' + type);
        
        if (section.style.display === 'none') {
            section.style.display = 'flex';
            icon.style.transform = 'rotate(180deg)';
        } else {
            section.style.display = 'none';
            icon.style.transform = 'rotate(0deg)';
        }
    }

    function showSummary() {
        const shoeName = document.querySelector('input[name="shoe_name"]').value;
        const shoeSize = document.querySelector('input[name="shoe_size"]').value;

        if (!shoeName || !shoeSize) {
            alert('Silakan isi nama sepatu dan ukuran terlebih dahulu.');
            return;
        }

        const summarySection = document.getElementById('summary_section');
        const formActionStep = document.getElementById('form_action_step');
        
        summarySection.style.display = 'block';
        formActionStep.style.display = 'none';

        // Smooth scroll to summary on mobile
        if (window.innerWidth <= 768) {
            summarySection.scrollIntoView({ behavior: 'smooth' });
        }
    }

    function handleFormSubmit() {
        const btn = document.getElementById('submit_button');
        btn.disabled = true;
        btn.innerText = 'Memproses Pesanan...';
        btn.style.opacity = '0.7';
        btn.style.cursor = 'not-allowed';
        return true;
    }

    // Initialize price on load
    window.onload = updatePrice;
</script>
@endsection
