@extends('layouts.premium-dashboard')

@section('page_title', 'Selamat Datang, ' . Auth::user()->name)

@section('nav_items')
    <li class="nav-item"><a href="{{ route('customer.dashboard') }}" class="nav-link active">Dashboard</a></li>
    <li class="nav-item"><a href="{{ route('services.index') }}" class="nav-link">Layanan Kami</a></li>
    <li class="nav-item"><a href="{{ route('orders.my-orders') }}" class="nav-link">Pesanan Saya</a></li>
    <li class="nav-item"><a href="{{ route('orders.history') }}" class="nav-link">Riwayat</a></li>
    <li class="nav-item"><a href="{{ route('profile.edit') }}" class="nav-link">Pengaturan</a></li>
@endsection

@section('content')
<div class="section-title" style="margin-bottom: 1rem;">
    <h3 style="font-size: 0.9rem; opacity: 0.6; text-transform: uppercase; letter-spacing: 1px;">Pilih Jenis Layanan Kami</h3>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.8rem; margin-bottom: 2.5rem;">
    <!-- Card Cuci Sepatu -->
    <a href="{{ route('services.index', ['category' => 'cleaning']) }}" style="text-decoration: none; color: inherit; display: block;">
        <div class="glass-card" style="background: linear-gradient(135deg, rgba(249, 115, 22, 0.1), rgba(234, 88, 12, 0.1)); border: 1px solid rgba(249, 115, 22, 0.2); transition: 0.4s; height: 100%; position: relative; overflow: hidden; padding: 1rem;" onmouseover="this.style.transform='translateY(-5px)'; this.style.borderColor='rgba(249, 115, 22, 0.4)'" onmouseout="this.style.transform='translateY(0)'; this.style.borderColor='rgba(249, 115, 22, 0.2)'">
            <div style="background: var(--primary); width: 35px; height: 35px; border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-bottom: 0.8rem; box-shadow: 0 4px 10px rgba(249, 115, 22, 0.3);">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20M2 12h20M4.93 4.93l14.14 14.14M4.93 19.07l14.14-14.14"/></svg>
            </div>
            <h4 style="font-size: 0.95rem; margin-bottom: 0.3rem; color: #fff; font-weight: 800;">Cuci Sepatu</h4>
            <p style="font-size: 0.65rem; opacity: 0.5; line-height: 1.3; margin-bottom: 1rem; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">Bersih menyeluruh, noda & bakteri hilang.</p>
            <div style="display: flex; align-items: center; gap: 0.3rem; color: var(--primary); font-size: 0.7rem; font-weight: 800; text-transform: uppercase;">
                Layanan
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M5 12h14M12 5l7 7-7 7"></path></svg>
            </div>
        </div>
    </a>

    <!-- Card Reparasi Sepatu -->
    <a href="{{ route('services.index', ['category' => 'repair']) }}" style="text-decoration: none; color: inherit; display: block;">
        <div class="glass-card" style="background: linear-gradient(135deg, rgba(251, 146, 60, 0.1), rgba(249, 115, 22, 0.1)); border: 1px solid rgba(251, 146, 60, 0.2); transition: 0.4s; height: 100%; position: relative; overflow: hidden; padding: 1rem;" onmouseover="this.style.transform='translateY(-5px)'; this.style.borderColor='rgba(251, 146, 60, 0.4)'" onmouseout="this.style.transform='translateY(0)'; this.style.borderColor='rgba(251, 146, 60, 0.2)'">
            <div style="background: var(--secondary); width: 35px; height: 35px; border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-bottom: 0.8rem; box-shadow: 0 4px 10px rgba(251, 146, 60, 0.3);">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a2 2 0 0 1-2.83-2.83l-3.94 3.6z"/><path d="m20 13-6.83 6.83a2 2 0 0 1-2.83 0l-1.07-1.07-1.59 1.59a1 1 0 0 1-1.4 0l-1.61-1.61a1 1 0 0 1 0-1.4l1.59-1.59-1.07-1.07a2 2 0 0 1 0-2.83L11 5"/><path d="m6.41 11.59 3.18 3.18"/></svg>
            </div>
            <h4 style="font-size: 0.95rem; margin-bottom: 0.3rem; color: #fff; font-weight: 800;">Reparasi</h4>
            <p style="font-size: 0.65rem; opacity: 0.5; line-height: 1.3; margin-bottom: 1rem; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">Sol lepas, jahit ulang & restorasi total.</p>
            <div style="display: flex; align-items: center; gap: 0.3rem; color: var(--secondary); font-size: 0.7rem; font-weight: 800; text-transform: uppercase;">
                Layanan
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M5 12h14M12 5l7 7-7 7"></path></svg>
            </div>
        </div>
    </a>
</div>

<div class="section-title" style="margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem;">
    <h3>Lacak Pengerjaan Realtime</h3>
    <span style="font-size: 0.75rem; background: rgba(16, 185, 129, 0.2); color: #10b981; padding: 0.3rem 0.7rem; border-radius: 20px; font-weight: 600;">TERUPDATE</span>
</div>

<div class="orders-grid" style="display: grid; grid-template-columns: 1fr; gap: 1.5rem;">
    @forelse($orders->whereNotIn('status', ['completed', 'cancelled']) as $order)
    <a href="{{ route('orders.show', $order->id) }}" style="text-decoration: none; color: inherit; display: block; margin-bottom: 1.5rem;">
        <div class="glass-card" style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05); transition: 0.3s;" onmouseover="this.style.borderColor='var(--primary)'; this.style.background='rgba(255,255,255,0.05)'; this.style.transform='translateY(-3px)'" onmouseout="this.style.borderColor='rgba(255,255,255,0.05)'; this.style.background='rgba(255,255,255,0.03)'; this.style.transform='translateY(0)'">
        <div style="display: flex; justify-content: space-between; margin-bottom: 1.5rem; align-items: flex-start; flex-wrap: wrap; gap: 1rem;">
            <div>
                <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.3rem;">
                    @php
                        $statusColor = match($order->status) {
                            'pending' => 'var(--secondary)',
                            'processing', 'finishing' => 'var(--primary)',
                            'ready' => '#10b981',
                            default => '#6b7280'
                        };
                    @endphp
                    <span style="width: 8px; height: 8px; background: {{ $statusColor }}; border-radius: 50%; box-shadow: 0 0 8px {{ $statusColor }};"></span>
                    <h4 style="color: {{ $statusColor }}; text-transform: uppercase; font-size: 0.9rem;">{{ $order->service->name }}</h4>
                </div>
                <p style="font-weight: 700; font-size: 1.1rem;">Order #{{ $order->order_number }}</p>
                <div style="display: inline-flex; align-items: center; gap: 0.4rem; background: rgba(255,255,255,0.04); padding: 0.3rem 0.6rem; border-radius: 8px; border: 1px solid rgba(255,255,255,0.06); font-size: 0.75rem; margin-top: 0.2rem;">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0;"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    <span style="opacity: 0.6; color: #fff;">Estimasi:</span>
                    <span style="color: var(--primary); font-weight: 700;">{{ $order->service->estimated_time ?: '2-3 Hari' }}</span>
                </div>
            </div>
            <div style="text-align: right; flex-grow: 1;">
                @php
                    $statusIndo = match($order->status) {
                        'pending' => 'Menunggu',
                        'processing' => ($order->service->category == 'cleaning' ? 'Dicuci' : 'Dikerjakan'),
                        'finishing' => ($order->service->category == 'cleaning' ? 'Pengeringan' : 'Finishing'),
                        'ready' => 'Siap Ambil',
                        'uncollected' => 'Belum Diambil',
                        'completed' => 'Selesai',
                        'cancelled' => 'Ditolak',
                        default => $order->status
                    };
                @endphp
                <span style="background: {{ $statusColor }}33; color: {{ $statusColor }}; padding: 0.4rem 0.8rem; border-radius: 10px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">{{ $statusIndo }}</span>
                <p style="font-size: 0.75rem; margin-top: 0.5rem; opacity: 0.5;">Metode: <span style="color: #fff; text-transform: uppercase;">{{ $order->payment_method }}</span></p>
            </div>
        </div>

        <!-- Realtime Progress Bar with Dots & Labels Aligned -->
        @php
            // Define stage indicators
            $isPending = true;
            $isProcessing = in_array($order->status, ['processing', 'finishing', 'ready', 'uncollected', 'completed']);
            $isFinishing = in_array($order->status, ['finishing', 'ready', 'uncollected', 'completed']);
            $isReady = in_array($order->status, ['ready', 'uncollected', 'completed']);
            
            $steps = [
                ['label' => 'Menunggu', 'active' => $isPending],
                ['label' => ($order->service->category == 'cleaning' ? 'Dicuci' : 'Dikerjakan'), 'active' => $isProcessing],
                ['label' => ($order->service->category == 'cleaning' ? 'Pengeringan' : 'Finishing'), 'active' => $isFinishing],
                ['label' => 'Siap Ambil', 'active' => $isReady],
            ];
        @endphp
        
        <div style="position: relative; margin-top: 1rem;">
            <!-- Connection Line (Background) -->
            <div style="position: absolute; top: 12px; left: 12.5%; right: 12.5%; height: 3px; background: rgba(255,255,255,0.05); z-index: 1;"></div>
            
            <!-- Connection Line (Progress) -->
            @php
                $lineWidth = match($order->status) {
                    'pending' => 0,
                    'processing' => 33.33,
                    'finishing' => 66.66,
                    'ready', 'uncollected', 'completed' => 100,
                    default => 0
                };
            @endphp
            <div style="position: absolute; top: 12px; left: 12.5%; width: calc({{ $lineWidth }}% * 0.75); height: 3px; background: {{ $statusColor }}; z-index: 2; transition: width 1s ease; box-shadow: 0 0 10px {{ $statusColor }}66;"></div>

            <div style="display: flex; justify-content: space-between; position: relative; z-index: 3;">
                @foreach($steps as $index => $step)
                    <div style="display: flex; flex-direction: column; align-items: center; width: 25%; gap: 0.8rem;">
                        <!-- Dot -->
                        <div style="width: 24px; height: 24px; background: {{ $step['active'] ? ($index == 3 ? '#10b981' : $statusColor) : '#1e293b' }}; border: 4px solid #0f172a; border-radius: 50%; box-shadow: {{ $step['active'] ? '0 0 15px '.($index == 3 ? '#10b981' : $statusColor).'88' : 'none' }}; transition: 0.4s; display: flex; align-items: center; justify-content: center;">
                            @if($step['active'])
                                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            @endif
                        </div>
                        <!-- Label -->
                        <span style="font-size: clamp(0.55rem, 2.2vw, 0.75rem); font-weight: 800; text-align: center; color: {{ $step['active'] ? ($index == 3 ? '#10b981' : '#fff') : 'rgba(255,255,255,0.2)' }}; transition: 0.3s; text-transform: uppercase; letter-spacing: 0.5px;">
                            {{ $step['label'] }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
        </div>
    </a>
    @empty
    <div style="padding: 3rem; text-align: center; background: rgba(255,255,255,0.02); border-radius: 24px; border: 1px dashed rgba(255,255,255,0.1);">
        <p style="opacity: 0.5;">Belum ada pesanan aktif.</p>
        <a href="{{ route('services.index') }}" style="display: inline-block; margin-top: 1rem; color: var(--primary); font-weight: 600; text-decoration: none;">Pesan Sekarang →</a>
    </div>
    @endforelse
</div>

<style>
    @keyframes move {
        0% { background-position: 0 0; }
        100% { background-position: 50px 50px; }
    }
</style>
    <script>
        // Auto refresh every 5 seconds for instant tracking status updates
        setTimeout(() => { window.location.reload(); }, 5000);
    </script>
@endsection
