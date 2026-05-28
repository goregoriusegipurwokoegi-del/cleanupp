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
    <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
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

        <div style="margin-top: 2rem; margin-bottom: 2rem; border-top: 1px solid rgba(255,255,255,0.08); padding-top: 2rem;">
            <h3 style="font-size: 1.2rem; font-weight: 700; margin-bottom: 1rem; color: #8b5cf6;">QRIS Pembayaran</h3>
            <p style="font-size: 0.8rem; opacity: 0.6; margin-bottom: 1.5rem;">Unggah gambar kode QRIS toko Anda agar pelanggan dapat memindai saat melakukan checkout.</p>
            
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-size: 0.85rem; margin-bottom: 0.5rem; opacity: 0.7;">Gambar QRIS</label>
                @if($qris_image)
                    <div style="margin-bottom: 1rem; background: rgba(255,255,255,0.02); padding: 1rem; border-radius: 12px; border: 1px solid rgba(255,255,255,0.05); display: inline-block;">
                        <img src="{{ asset('storage/' . $qris_image) }}" alt="QRIS Code Preview" style="max-height: 200px; border-radius: 8px; display: block;">
                        <span style="display: block; font-size: 0.75rem; text-align: center; opacity: 0.5; margin-top: 0.5rem;">QRIS Saat Ini</span>
                    </div>
                @endif
                <input type="file" name="qris_image" accept="image/*" style="width: 100%; padding: 0.8rem; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; color: #fff; outline: none; transition: 0.3s;" onfocus="this.style.borderColor='#8b5cf6'" onblur="this.style.borderColor='rgba(255,255,255,0.1)'">
                @error('qris_image')
                    <span style="color: #ef4444; font-size: 0.8rem; display: block; margin-top: 0.5rem;">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <button type="submit" style="width: 100%; background: var(--primary); color: #0f172a; border: none; padding: 1rem; border-radius: 12px; font-weight: 700; cursor: pointer; transition: 0.3s;" onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">Simpan Perubahan</button>
    </form>
</div>
@endsection
