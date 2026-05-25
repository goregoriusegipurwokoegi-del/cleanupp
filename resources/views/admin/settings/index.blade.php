@extends('layouts.premium-dashboard')

@section('page_title', 'Pengaturan Rekening')

@section('content')
<div style="margin-bottom: 2rem;">
    <h2 style="font-size: 1.8rem; font-weight: 800; margin-bottom: 0.5rem;">Pengaturan Toko</h2>
    <p style="opacity: 0.6;">Atur informasi rekening bank untuk pembayaran transfer pelanggan.</p>
</div>

@if(session('success'))
    <div style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.2); color: #10b981; padding: 1rem; border-radius: 12px; margin-bottom: 2rem;">
        {{ session('success') }}
    </div>
@endif

<div class="glass-card" style="max-width: 600px; padding: 2.5rem; border-radius: 24px;">
    <form action="{{ route('admin.settings.update') }}" method="POST">
        @csrf
        <div style="margin-bottom: 1.5rem;">
            <label style="display: block; font-size: 0.85rem; margin-bottom: 0.5rem; opacity: 0.7;">Nama Bank</label>
            <input type="text" name="bank_name" value="{{ $bank_name }}" placeholder="Contoh: Bank BCA" required style="width: 100%; padding: 0.8rem 1.2rem; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; color: #fff; outline: none; transition: 0.3s;" onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='rgba(255,255,255,0.1)'">
        </div>

        <div style="margin-bottom: 1.5rem;">
            <label style="display: block; font-size: 0.85rem; margin-bottom: 0.5rem; opacity: 0.7;">Nomor Rekening</label>
            <input type="text" name="bank_account" value="{{ $bank_account }}" placeholder="Contoh: 1234567890" required style="width: 100%; padding: 0.8rem 1.2rem; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; color: #fff; outline: none; transition: 0.3s;" onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='rgba(255,255,255,0.1)'">
        </div>

        <div style="margin-bottom: 2rem;">
            <label style="display: block; font-size: 0.85rem; margin-bottom: 0.5rem; opacity: 0.7;">Nama Pemilik Rekening</label>
            <input type="text" name="bank_holder" value="{{ $bank_holder }}" placeholder="Contoh: PT CleanUP Shoes Indonesia" required style="width: 100%; padding: 0.8rem 1.2rem; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; color: #fff; outline: none; transition: 0.3s;" onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='rgba(255,255,255,0.1)'">
        </div>

        <button type="submit" style="width: 100%; background: var(--primary); color: #0f172a; border: none; padding: 1rem; border-radius: 12px; font-weight: 700; cursor: pointer; transition: 0.3s;" onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">Simpan Perubahan</button>
    </form>
</div>
@endsection
