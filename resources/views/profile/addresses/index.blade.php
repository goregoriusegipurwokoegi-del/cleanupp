@extends('layouts.premium-dashboard')

@section('title', 'Alamat Anda')

@section('content')
<div class="dashboard-header" style="display: flex; align-items: center; gap: 1rem; margin-bottom: 2rem;">
    <h2 class="dashboard-title" style="margin-bottom: 0;">Alamat Anda</h2>
</div>

<div class="content-card" style="background: rgba(255, 255, 255, 0.02); padding: 0;">
    <div style="padding: 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.05); display: flex; justify-content: space-between; align-items: center;">
        <h3 style="font-size: 1rem; font-weight: 500; color: #fff;">Buku Alamat</h3>
        <a href="{{ route('addresses.create') }}" style="display: inline-flex; align-items: center; gap: 0.5rem; color: var(--primary); text-decoration: none; font-weight: 500;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
            Tambah alamat
        </a>
    </div>

    @if(session('success'))
        <div style="padding: 1rem 1.5rem; background: rgba(34, 197, 94, 0.1); color: #4ade80; font-size: 0.9rem; border-bottom: 1px solid rgba(255,255,255,0.05);">
            {{ session('success') }}
        </div>
    @endif

    <div style="padding: 1.5rem;">
        @forelse($addresses as $address)
            <div style="border-bottom: 1px solid rgba(255,255,255,0.05); padding-bottom: 1.5rem; margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: flex-start;">
                <div>
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                        <span style="font-weight: 600; color: #fff; font-size: 1rem;">{{ $address->recipient_name }}</span>
                        @if($address->address_label)
                            <span style="font-size: 0.75rem; padding: 0.2rem 0.5rem; border-radius: 4px; background: rgba(255,255,255,0.1); color: #ccc;">{{ $address->address_label }}</span>
                        @endif
                    </div>
                    <div style="color: rgba(255,255,255,0.6); font-size: 0.9rem; margin-bottom: 0.5rem;">
                        {{ $address->phone }}
                    </div>
                    <div style="color: rgba(255,255,255,0.8); font-size: 0.95rem; line-height: 1.5; margin-bottom: 0.5rem;">
                        {{ $address->full_address }}<br>
                        {{ $address->village ? $address->village . ', ' : '' }}{{ $address->kecamatan ? $address->kecamatan . ', ' : '' }}{{ $address->city ? $address->city . ', ' : '' }}{{ $address->province ? $address->province . ', ' : '' }}Indonesia{{ $address->postal_code ? ' ' . $address->postal_code : '' }}
                    </div>
                    @if($address->address_landmark)
                        <div style="color: rgba(255,255,255,0.5); font-size: 0.85rem; font-style: italic; margin-bottom: 0.5rem;">
                            Patokan: {{ $address->address_landmark }}
                        </div>
                    @endif
                    @if($address->is_main_address)
                        <div style="margin-top: 0.5rem;">
                            <span style="font-size: 0.75rem; padding: 0.2rem 0.5rem; border-radius: 4px; border: 1px solid var(--primary); color: var(--primary);">Default</span>
                        </div>
                    @endif
                </div>
                <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 1rem;">
                    <a href="{{ route('addresses.edit', $address->id) }}" style="color: #ef4444; font-weight: 500; font-size: 0.9rem; text-decoration: none;">Edit</a>
                </div>
            </div>
        @empty
            <div style="text-align: center; padding: 2rem 0; color: rgba(255,255,255,0.5);">
                Belum ada alamat yang tersimpan.
            </div>
        @endforelse
    </div>
</div>
@endsection
