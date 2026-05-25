<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Display Antrian – CleanUP Shoes</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Outfit', sans-serif;
            background: #050810;
            color: #fff;
            height: 100vh;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        /* ─── Header ─── */
        .header {
            background: linear-gradient(135deg, #0f172a 0%, #1e1040 100%);
            border-bottom: 2px solid rgba(249,115,22,0.3);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-shrink: 0;
        }
        .brand {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .brand-logo {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, #f97316, #fb923c);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
        .brand-name {
            font-size: 1.5rem;
            font-weight: 900;
            letter-spacing: -0.5px;
        }
        .brand-name span { color: #f97316; }
        .brand-tagline { font-size: 0.75rem; opacity: 0.5; margin-top: 1px; }

        .header-right {
            text-align: right;
        }
        .live-time {
            font-size: 2rem;
            font-weight: 800;
            color: #f97316;
            letter-spacing: 2px;
            font-variant-numeric: tabular-nums;
        }
        .live-date { font-size: 0.8rem; opacity: 0.5; margin-top: 2px; }

        /* ─── Main Grid ─── */
        .main {
            flex: 1;
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 1.2rem;
            padding: 1.2rem 2rem;
            overflow: hidden;
        }

        /* ─── Column ─── */
        .col-panel {
            background: rgba(255,255,255,0.02);
            border: 1px solid rgba(255,255,255,0.06);
            border-radius: 20px;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        .col-header {
            padding: 1rem 1.2rem;
            font-weight: 800;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 2px;
            display: flex;
            align-items: center;
            gap: 0.6rem;
            flex-shrink: 0;
        }
        .col-header .dot {
            width: 10px; height: 10px;
            border-radius: 50%;
            flex-shrink: 0;
        }
        .col-pending   .col-header { background: rgba(245,158,11,0.08); border-bottom: 1px solid rgba(245,158,11,0.15); color: #f59e0b; }
        .col-processing .col-header { background: rgba(59,130,246,0.08); border-bottom: 1px solid rgba(59,130,246,0.15); color: #60a5fa; }
        .col-ready     .col-header { background: rgba(16,185,129,0.08); border-bottom: 1px solid rgba(16,185,129,0.15); color: #10b981; }

        .col-body {
            flex: 1;
            overflow-y: auto;
            padding: 0.8rem;
            display: flex;
            flex-direction: column;
            gap: 0.6rem;
        }
        .col-body::-webkit-scrollbar { width: 4px; }
        .col-body::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 2px; }

        /* ─── Queue Card ─── */
        .queue-card {
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.06);
            border-radius: 14px;
            padding: 0.9rem 1rem;
            display: flex;
            align-items: center;
            gap: 0.9rem;
            transition: all 0.4s;
            animation: slideIn 0.5s ease;
        }
        .queue-card:hover {
            background: rgba(255,255,255,0.05);
        }
        @keyframes slideIn {
            from { opacity: 0; transform: translateX(-20px); }
            to   { opacity: 1; transform: translateX(0); }
        }

        .q-badge {
            width: 54px;
            height: 54px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 900;
            font-size: 1rem;
            flex-shrink: 0;
            letter-spacing: -0.5px;
        }
        .q-badge-pending    { background: rgba(245,158,11,0.15); color: #f59e0b; border: 1.5px solid rgba(245,158,11,0.3); }
        .q-badge-processing { background: rgba(59,130,246,0.15); color: #60a5fa; border: 1.5px solid rgba(59,130,246,0.3); }
        .q-badge-ready      { background: rgba(16,185,129,0.2); color: #10b981; border: 1.5px solid rgba(16,185,129,0.4); }

        .q-info { flex: 1; min-width: 0; }
        .q-name {
            font-weight: 700;
            font-size: 0.95rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .q-service {
            font-size: 0.72rem;
            opacity: 0.5;
            margin-top: 2px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .q-status {
            font-size: 0.68rem;
            font-weight: 800;
            padding: 0.25rem 0.6rem;
            border-radius: 8px;
            flex-shrink: 0;
        }

        /* Ready card glow */
        .queue-card.ready {
            border-color: rgba(16,185,129,0.3);
            background: rgba(16,185,129,0.04);
            animation: slideIn 0.5s ease, pulseReady 3s ease-in-out infinite;
        }
        @keyframes pulseReady {
            0%,100% { box-shadow: 0 0 0 0 rgba(16,185,129,0); }
            50%      { box-shadow: 0 0 18px 2px rgba(16,185,129,0.15); }
        }

        /* ─── Status labels ─── */
        .status-washing    { background: rgba(14,165,233,0.15); color: #38bdf8; }
        .status-drying     { background: rgba(99,102,241,0.15); color: #a5b4fc; }
        .status-processing { background: rgba(59,130,246,0.15); color: #60a5fa; }
        .status-finishing  { background: rgba(168,85,247,0.15); color: #d8b4fe; }
        .status-ready      { background: rgba(16,185,129,0.2); color: #10b981; }
        .status-pending    { background: rgba(245,158,11,0.15); color: #fbbf24; }

        /* ─── Footer ─── */
        .footer {
            background: rgba(0,0,0,0.3);
            border-top: 1px solid rgba(255,255,255,0.04);
            padding: 0.6rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-shrink: 0;
        }
        .ticker {
            font-size: 0.78rem;
            opacity: 0.4;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .pulse-dot {
            width: 7px; height: 7px;
            background: #10b981;
            border-radius: 50%;
            animation: blink 1.5s ease-in-out infinite;
        }
        @keyframes blink {
            0%,100% { opacity: 1; }
            50% { opacity: 0.2; }
        }

        .empty-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            flex: 1;
            opacity: 0.2;
            text-align: center;
            padding: 2rem;
        }
        .empty-state svg { margin-bottom: 0.5rem; }
        .empty-state p { font-size: 0.8rem; }

        /* Responsive for smaller monitors */
        @media (max-width: 900px) {
            .main { grid-template-columns: 1fr; overflow-y: auto; }
            .col-panel { min-height: 220px; }
        }
    </style>
</head>
<body>

<!-- Header -->
<div class="header">
    <div class="brand">
        <div class="brand-logo">👟</div>
        <div>
            <div class="brand-name">CleanUP <span>Shoes</span></div>
            <div class="brand-tagline">Display Antrian Layanan</div>
        </div>
    </div>
    <div class="header-right">
        <div class="live-time" id="live-time">--:--:--</div>
        <div class="live-date" id="live-date">–</div>
    </div>
</div>

<!-- Main Grid -->
<div class="main">

    <!-- MENUNGGU -->
    <div class="col-panel col-pending">
        <div class="col-header">
            <div class="dot" style="background:#f59e0b;"></div>
            Menunggu
            <span id="pending-count" style="margin-left:auto; background:rgba(245,158,11,0.15); color:#f59e0b; padding: 2px 10px; border-radius: 20px; font-size:0.8rem;">0</span>
        </div>
        <div class="col-body" id="pending-list">
            <div class="empty-state">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                <p>Belum ada antrian</p>
            </div>
        </div>
    </div>

    <!-- SEDANG DIPROSES -->
    <div class="col-panel col-processing">
        <div class="col-header">
            <div class="dot" style="background:#60a5fa;"></div>
            Sedang Diproses
            <span id="processing-count" style="margin-left:auto; background:rgba(59,130,246,0.15); color:#60a5fa; padding: 2px 10px; border-radius: 20px; font-size:0.8rem;">0</span>
        </div>
        <div class="col-body" id="processing-list">
            <div class="empty-state">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/><path d="M9 12l2 2 4-4"/></svg>
                <p>Tidak ada yang diproses</p>
            </div>
        </div>
    </div>

    <!-- SIAP DIAMBIL -->
    <div class="col-panel col-ready">
        <div class="col-header">
            <div class="dot" style="background:#10b981; box-shadow: 0 0 8px #10b981;"></div>
            ✅ Siap Diambil
            <span id="ready-count" style="margin-left:auto; background:rgba(16,185,129,0.2); color:#10b981; padding: 2px 10px; border-radius: 20px; font-size:0.8rem;">0</span>
        </div>
        <div class="col-body" id="ready-list">
            <div class="empty-state">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                <p>Belum ada yang selesai</p>
            </div>
        </div>
    </div>

</div>

<!-- Footer -->
<div class="footer">
    <div class="ticker">
        <div class="pulse-dot"></div>
        Data diperbarui otomatis setiap 5 detik
    </div>
    <div style="font-size: 0.75rem; opacity: 0.3;">
        Cek status antrian: <strong>{{ url('/cek-antrian') }}</strong>
    </div>
</div>

<script>
    // ─── Clock ───
    function updateClock() {
        const now = new Date();
        const h = String(now.getHours()).padStart(2,'0');
        const m = String(now.getMinutes()).padStart(2,'0');
        const s = String(now.getSeconds()).padStart(2,'0');
        document.getElementById('live-time').textContent = `${h}:${m}:${s}`;
    }
    setInterval(updateClock, 1000);
    updateClock();

    // ─── Status Labels ───
    const STATUS_LABELS = {
        pending    : { text: 'MENUNGGU',  cls: 'status-pending' },
        processing : { text: 'DIPROSES',  cls: 'status-processing' },
        washing    : { text: 'DICUCI',    cls: 'status-washing' },
        drying     : { text: 'DIJEMUR',   cls: 'status-drying' },
        finishing  : { text: 'FINISHING', cls: 'status-finishing' },
        ready      : { text: 'SIAP ✓',   cls: 'status-ready' },
    };

    // ─── Build Queue Card ───
    function buildCard(item, type) {
        const label = STATUS_LABELS[item.status] ?? { text: item.status.toUpperCase(), cls: '' };
        const badgeCls = `q-badge-${type}`;
        const category = item.category === 'cleaning' ? '🫧 Cuci' : '🔧 Reparasi';
        return `
            <div class="queue-card ${type === 'ready' ? 'ready' : ''}">
                <div class="q-badge ${badgeCls}">${item.queue_number}</div>
                <div class="q-info">
                    <div class="q-name">${item.name}</div>
                    <div class="q-service">${category} · ${item.service}</div>
                </div>
                <div class="q-status ${label.cls}">${label.text}</div>
            </div>`;
    }

    function emptyState(icon, msg) {
        return `<div class="empty-state">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">${icon}</svg>
            <p>${msg}</p>
        </div>`;
    }

    // ─── Fetch & Render ───
    async function fetchQueue() {
        try {
            const res  = await fetch('{{ route("queue.data") }}');
            const data = await res.json();

            // Date from server
            document.getElementById('live-date').textContent = data.date;

            // Pending
            const pendingList = document.getElementById('pending-list');
            document.getElementById('pending-count').textContent = data.pending.length;
            if (data.pending.length === 0) {
                pendingList.innerHTML = emptyState('<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>', 'Tidak ada antrian');
            } else {
                pendingList.innerHTML = data.pending.map(i => buildCard(i, 'pending')).join('');
            }

            // Processing
            const processingList = document.getElementById('processing-list');
            document.getElementById('processing-count').textContent = data.processing.length;
            if (data.processing.length === 0) {
                processingList.innerHTML = emptyState('<path d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/><path d="M9 12l2 2 4-4"/>', 'Tidak ada yang diproses');
            } else {
                processingList.innerHTML = data.processing.map(i => buildCard(i, 'processing')).join('');
            }

            // Ready
            const readyList = document.getElementById('ready-list');
            document.getElementById('ready-count').textContent = data.ready.length;
            if (data.ready.length === 0) {
                readyList.innerHTML = emptyState('<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>', 'Belum ada yang selesai');
            } else {
                readyList.innerHTML = data.ready.map(i => buildCard(i, 'ready')).join('');
            }
        } catch (e) {
            console.error('Queue fetch error:', e);
        }
    }

    fetchQueue();
    setInterval(fetchQueue, 5000);
</script>
</body>
</html>
