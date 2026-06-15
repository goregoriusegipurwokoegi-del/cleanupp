@extends('layouts.premium-dashboard')

@section('page_title', 'Monitor Antrian')

@section('nav_items')
    @if(Auth::user()->role == 'admin')
        <li class="nav-item"><a href="{{ route('admin.dashboard') }}" class="nav-link">Dashboard</a></li>
        <li class="nav-item"><a href="{{ route('admin.orders.index') }}" class="nav-link">Kelola Pesanan</a></li>
    @else
        <li class="nav-item"><a href="{{ route('employee.dashboard') }}" class="nav-link">Dashboard</a></li>
        <li class="nav-item"><a href="{{ route('employee.orders.index') }}" class="nav-link">Orderan Masuk</a></li>
    @endif
    <li class="nav-item"><a href="#" class="nav-link active">Monitor Antrian</a></li>
@endsection

@section('content')
<style>
    .queue-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1.5rem;
    }
    .q-col {
        background: rgba(255,255,255,0.02);
        border: 1px solid rgba(255,255,255,0.05);
        border-radius: 16px;
        display: flex;
        flex-direction: column;
        height: calc(100vh - 250px);
        min-height: 500px;
        overflow: hidden;
    }
    .q-col-header {
        padding: 1rem;
        font-weight: 800;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        display: flex;
        align-items: center;
        gap: 0.6rem;
        border-bottom: 1px solid rgba(255,255,255,0.05);
    }
    .q-col-body {
        flex: 1;
        overflow-y: auto;
        padding: 1rem;
        display: flex;
        flex-direction: column;
        gap: 0.8rem;
    }
    .q-col-body::-webkit-scrollbar { width: 4px; }
    .q-col-body::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 2px; }

    .q-card {
        background: rgba(255,255,255,0.03);
        border: 1px solid rgba(255,255,255,0.05);
        border-radius: 12px;
        padding: 1rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        transition: 0.3s;
    }
    .q-badge {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 900;
        font-size: 0.9rem;
        flex-shrink: 0;
    }
    
    .q-col-pending .q-col-header { color: #f59e0b; background: rgba(245,158,11,0.05); border-bottom-color: rgba(245,158,11,0.1); }
    .q-col-processing .q-col-header { color: #60a5fa; background: rgba(59,130,246,0.05); border-bottom-color: rgba(59,130,246,0.1); }
    .q-col-ready .q-col-header { color: #10b981; background: rgba(16,185,129,0.05); border-bottom-color: rgba(16,185,129,0.1); }

    .q-badge-pending { background: rgba(245,158,11,0.15); color: #f59e0b; border: 1px solid rgba(245,158,11,0.3); }
    .q-badge-processing { background: rgba(59,130,246,0.15); color: #60a5fa; border: 1px solid rgba(59,130,246,0.3); }
    .q-badge-ready { background: rgba(16,185,129,0.2); color: #10b981; border: 1px solid rgba(16,185,129,0.4); }

    .q-card.ready { background: rgba(16,185,129,0.04); border-color: rgba(16,185,129,0.2); }

    @media (max-width: 1024px) {
        .queue-grid { grid-template-columns: 1fr; }
        .q-col { height: auto; min-height: 250px; }
    }

</style>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 15px;">
    <div>
        <h2 class="desktop-hidden-title" style="font-size: 1.8rem; font-weight: 900; margin-bottom: 5px;">Monitor <span style="color: var(--primary);">Antrian</span></h2>
        <p style="opacity: 0.5;">Daftar pesanan aktif. Pesanan yang sudah selesai atau dibatalkan otomatis disembunyikan.</p>
    </div>
    <div style="display: flex; align-items: center; gap: 10px; background: rgba(255,255,255,0.03); padding: 8px 15px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.05);">
        <div style="width: 8px; height: 8px; background: #10b981; border-radius: 50%; box-shadow: 0 0 8px #10b981; animation: blink 1.5s infinite;"></div>
        <span style="font-size: 0.8rem; font-weight: 600; opacity: 0.8;">Live Update</span>
    </div>
</div>

<div class="queue-grid">
    <!-- MENUNGGU -->
    <div class="q-col q-col-pending">
        <div class="q-col-header">
            <div style="width:8px;height:8px;border-radius:50%;background:#f59e0b;"></div>
            Menunggu
            <span id="count-pending" style="margin-left:auto; background:rgba(245,158,11,0.15); padding: 2px 8px; border-radius: 10px;">0</span>
        </div>
        <div class="q-col-body" id="list-pending">
            <!-- JS will populate -->
        </div>
    </div>

    <!-- DIPROSES -->
    <div class="q-col q-col-processing">
        <div class="q-col-header">
            <div style="width:8px;height:8px;border-radius:50%;background:#60a5fa;"></div>
            Sedang Diproses
            <span id="count-processing" style="margin-left:auto; background:rgba(59,130,246,0.15); padding: 2px 8px; border-radius: 10px;">0</span>
        </div>
        <div class="q-col-body" id="list-processing">
            <!-- JS will populate -->
        </div>
    </div>

    <!-- SIAP DIAMBIL -->
    <div class="q-col q-col-ready">
        <div class="q-col-header">
            <div style="width:8px;height:8px;border-radius:50%;background:#10b981;"></div>
            Siap Diambil/Kirim
            <span id="count-ready" style="margin-left:auto; background:rgba(16,185,129,0.15); padding: 2px 8px; border-radius: 10px;">0</span>
        </div>
        <div class="q-col-body" id="list-ready">
            <!-- JS will populate -->
        </div>
    </div>
</div>

<style>
    @keyframes blink { 0%,100%{opacity:1;} 50%{opacity:0.3;} }
</style>

<script>
    const STATUS_LABELS = {
        pending    : { text: 'MENUNGGU',  cls: 'status-pending', color: '#f59e0b' },
        processing : { text: 'DIPROSES',  cls: 'status-processing', color: '#60a5fa' },
        washing    : { text: 'DICUCI',    cls: 'status-washing', color: '#38bdf8' },
        drying     : { text: 'DIJEMUR',   cls: 'status-drying', color: '#a5b4fc' },
        finishing  : { text: 'FINISHING', cls: 'status-finishing', color: '#d8b4fe' },
        ready      : { text: 'SIAP',      cls: 'status-ready', color: '#10b981' },
    };

    function buildCard(item, type) {
        const label = STATUS_LABELS[item.status] ?? { text: item.status.toUpperCase(), color: '#fff' };
        const badgeCls = `q-badge-${type}`;
        const isReady = type === 'ready' ? 'ready' : '';
        return `
            <div class="q-card ${isReady}">
                <div class="q-badge ${badgeCls}">${item.queue_number}</div>
                <div style="flex: 1; min-width: 0;">
                    <div style="font-weight: 700; font-size: 0.9rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${item.name}</div>
                    <div style="font-size: 0.75rem; opacity: 0.5; margin-top: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${item.category === 'cleaning'?'Cuci':'Reparasi'} &bull; ${item.service}</div>
                </div>
                <div style="font-size: 0.65rem; font-weight: 800; padding: 3px 8px; border-radius: 6px; background: ${label.color}20; color: ${label.color}; flex-shrink: 0;">
                    ${label.text}
                </div>
            </div>
        `;
    }

    function buildEmpty(msg) {
        return `<div style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%; opacity: 0.3;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-bottom: 8px;"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            <div style="font-size: 0.8rem;">${msg}</div>
        </div>`;
    }

    async function loadQueue() {
        try {
            const res = await fetch('{{ route("queue.data") }}');
            const data = await res.json();
            
            document.getElementById('count-pending').textContent = data.pending.length;
            document.getElementById('list-pending').innerHTML = data.pending.length 
                ? data.pending.map(i => buildCard(i, 'pending')).join('') 
                : buildEmpty('Belum ada antrian');
                
            document.getElementById('count-processing').textContent = data.processing.length;
            document.getElementById('list-processing').innerHTML = data.processing.length 
                ? data.processing.map(i => buildCard(i, 'processing')).join('') 
                : buildEmpty('Tidak ada yang diproses');
                
            document.getElementById('count-ready').textContent = data.ready.length;
            document.getElementById('list-ready').innerHTML = data.ready.length 
                ? data.ready.map(i => buildCard(i, 'ready')).join('') 
                : buildEmpty('Belum ada yang selesai');
                
        } catch (e) {
            console.error('Error fetching queue data:', e);
        }
    }

    loadQueue();
    setInterval(loadQueue, 5000); // Polling every 5 seconds
</script>
@endsection
