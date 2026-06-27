<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cek Status Antrian – CleanUP Shoes</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;800;900&display=swap" rel="stylesheet">
    <meta name="description" content="Cek status antrian sepatu Anda di CleanUP Shoes secara real-time.">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Outfit', sans-serif;
            background: #060b14;
            color: #fff;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* Header */
        .header {
            width: 100%;
            background: rgba(15,23,42,0.9);
            border-bottom: 1px solid rgba(249,115,22,0.2);
            padding: 1rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            backdrop-filter: blur(10px);
        }
        .brand { display: flex; align-items: center; gap: 0.8rem; text-decoration: none; color: #fff; }
        .brand-logo {
            width: 40px; height: 40px;
            background: linear-gradient(135deg, #f97316, #fb923c);
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.2rem;
        }
        .brand-name { font-size: 1.2rem; font-weight: 900; }
        .brand-name span { color: #f97316; }

        .display-btn {
            text-decoration: none;
            background: rgba(249,115,22,0.1);
            color: #f97316;
            border: 1px solid rgba(249,115,22,0.2);
            padding: 0.5rem 1rem;
            border-radius: 10px;
            font-size: 0.8rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 0.4rem;
            transition: 0.3s;
        }
        .display-btn:hover { background: rgba(249,115,22,0.2); }

        /* Main */
        .container {
            width: 100%;
            max-width: 520px;
            padding: 2.5rem 1.5rem;
        }

        .page-title {
            text-align: center;
            margin-bottom: 2rem;
        }
        .page-title h1 {
            font-size: 1.8rem;
            font-weight: 900;
            margin-bottom: 0.4rem;
        }
        .page-title h1 span { color: #f97316; }
        .page-title p { opacity: 0.5; font-size: 0.9rem; }

        /* Search Form */
        .search-form {
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.07);
            border-radius: 20px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        .form-label {
            display: block;
            font-size: 0.75rem;
            font-weight: 700;
            opacity: 0.5;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 0.6rem;
        }
        .form-row {
            display: flex;
            gap: 0.5rem;
        }
        .form-input {
            flex: 1;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            padding: 0.9rem 1rem;
            border-radius: 12px;
            color: #fff;
            font-size: 1rem;
            font-family: 'Outfit', sans-serif;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            outline: none;
            transition: 0.3s;
        }
        .form-input:focus { border-color: #f97316; }
        .form-input::placeholder { letter-spacing: 1px; opacity: 0.4; font-weight: 400; font-size: 0.85rem; text-transform: none; }
        .search-btn {
            background: #f97316;
            color: #fff;
            border: none;
            padding: 0.9rem 1.5rem;
            border-radius: 12px;
            font-weight: 800;
            font-size: 0.9rem;
            cursor: pointer;
            transition: 0.3s;
            white-space: nowrap;
            font-family: 'Outfit', sans-serif;
        }
        .search-btn:hover { background: #ea6c10; transform: translateY(-1px); }

        /* Result Card */
        .result-card {
            background: rgba(255,255,255,0.02);
            border: 1px solid rgba(255,255,255,0.07);
            border-radius: 20px;
            overflow: hidden;
            animation: fadeUp 0.5s ease;
        }
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .result-header {
            padding: 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }
        .queue-number-big {
            font-size: 2.5rem;
            font-weight: 900;
            color: #f97316;
            letter-spacing: -1px;
        }
        .status-badge-big {
            padding: 0.5rem 1.2rem;
            border-radius: 12px;
            font-weight: 800;
            font-size: 0.85rem;
        }

        .result-body { padding: 1.5rem; }
        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 0.8rem 0;
            border-bottom: 1px solid rgba(255,255,255,0.04);
        }
        .info-row:last-child { border-bottom: none; }
        .info-label { font-size: 0.78rem; opacity: 0.4; font-weight: 600; }
        .info-value { font-weight: 700; font-size: 0.95rem; text-align: right; }

        /* Status Progress */
        .progress-section {
            padding: 1.2rem 1.5rem;
            border-top: 1px solid rgba(255,255,255,0.05);
            background: rgba(255,255,255,0.01);
        }
        .progress-label { font-size: 0.75rem; opacity: 0.4; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 1rem; }
        .progress-steps { display: flex; align-items: center; gap: 0; }
        .step {
            flex: 1;
            text-align: center;
        }
        .step-dot {
            width: 28px; height: 28px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 0.4rem;
            font-size: 0.75rem;
            font-weight: 800;
            transition: 0.3s;
        }
        .step-dot.done { background: #10b981; color: #fff; }
        .step-dot.active { background: #f97316; color: #fff; box-shadow: 0 0 12px rgba(249,115,22,0.5); }
        .step-dot.waiting { background: rgba(255,255,255,0.08); color: rgba(255,255,255,0.3); }
        .step-text { font-size: 0.62rem; opacity: 0.5; font-weight: 600; }
        .step-text.active-text { opacity: 1; color: #f97316; }
        .step-line { height: 2px; flex: 0.5; background: rgba(255,255,255,0.06); margin-top: -14px; }
        .step-line.done { background: #10b981; }

        /* Not found */
        .not-found {
            text-align: center;
            padding: 3rem 1.5rem;
            background: rgba(255,255,255,0.02);
            border: 1px solid rgba(255,255,255,0.06);
            border-radius: 20px;
            animation: fadeUp 0.5s ease;
        }
        .not-found .emoji { font-size: 3rem; margin-bottom: 1rem; }
        .not-found h3 { font-size: 1.1rem; font-weight: 800; margin-bottom: 0.4rem; }
        .not-found p { opacity: 0.4; font-size: 0.85rem; }

        /* Footer link */
        .footer-link {
            margin-top: 2rem;
            text-align: center;
            font-size: 0.82rem;
            opacity: 0.4;
        }
        .footer-link a { color: #f97316; text-decoration: none; }
    </style>
</head>
<body>

<div class="header">
    <a href="{{ url('/') }}" class="brand">
        <div class="brand-logo">👟</div>
        <div class="brand-name">CleanUP <span>Shoes</span></div>
    </a>
    <a href="{{ route('queue.display') }}" class="display-btn" target="_blank">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
        Display Antrian
    </a>
</div>

<div class="container">
    <div class="page-title">
        <h1>Cek <span>Antrian</span></h1>
        <p>Masukkan nomor antrian Anda untuk melihat status pengerjaan</p>
    </div>

    <div class="search-form">
        <label class="form-label">Nomor Antrian</label>
        <form action="{{ route('queue.check') }}" method="GET" class="form-row">
            <input type="text" name="q" class="form-input" placeholder="Contoh: Q001" value="{{ $queueNumber ?? '' }}" autofocus autocomplete="off">
            <button type="submit" class="search-btn">Cek</button>
        </form>
    </div>

    @if($queueNumber && $order)
    {{-- ORDER FOUND --}}
    @php
        $statusMap = [
            'pending'    => ['label' => 'Menunggu Konfirmasi', 'step' => 0, 'color' => '#f59e0b', 'bg' => 'rgba(245,158,11,0.12)'],
            'processing' => ['label' => 'Dalam Antrian',        'step' => 1, 'color' => '#94a3b8', 'bg' => 'rgba(148,163,184,0.12)'],
            'washing'    => ['label' => ($order->service->category === 'cleaning' ? 'Sedang Dicuci' : 'Sedang Dikerjakan'), 'step' => 2, 'color' => '#3b82f6', 'bg' => 'rgba(59,130,246,0.12)'],
            'drying'     => ['label' => 'Dijemur',            'step' => 3, 'color' => '#a855f7', 'bg' => 'rgba(168,85,247,0.12)'],
            'finishing'  => ['label' => 'Finishing',          'step' => 3, 'color' => '#a855f7', 'bg' => 'rgba(168,85,247,0.12)'],
            'ready'      => ['label' => '✅ Siap Diambil!',   'step' => 4, 'color' => '#10b981', 'bg' => 'rgba(16,185,129,0.15)'],
            'completed'  => ['label' => 'Sudah Diambil',      'step' => 5, 'color' => '#2563eb', 'bg' => 'rgba(37,99,235,0.12)'],
            'cancelled'  => ['label' => 'Dibatalkan',         'step' => -1,'color' => '#f43f5e', 'bg' => 'rgba(244,63,94,0.12)'],
        ];
        $s = $statusMap[$order->status] ?? ['label' => strtoupper($order->status), 'step' => 0, 'color' => '#fff', 'bg' => 'rgba(255,255,255,0.05)'];

        $steps = $order->service->category === 'cleaning'
            ? ['Diterima', 'Dalam Antrian', 'Dicuci', 'Jemur', 'Siap']
            : ['Diterima', 'Dalam Antrian', 'Kerjakan', 'Finishing', 'Siap'];
    @endphp
    <div class="result-card">
        <div class="result-header">
            <div>
                <div style="font-size:0.72rem; opacity:0.4; font-weight:700; text-transform:uppercase; letter-spacing:1px; margin-bottom:4px;">No. Antrian</div>
                <div class="queue-number-big">{{ $order->queue_number }}</div>
            </div>
            <div class="status-badge-big" style="background: {{ $s['bg'] }}; color: {{ $s['color'] }};">
                {{ $s['label'] }}
            </div>
        </div>

        <div class="result-body">
            <div class="info-row">
                <span class="info-label">Nama</span>
                <span class="info-value">{{ $order->user->name }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Sepatu</span>
                <span class="info-value">{{ $order->shoe_name }} (Size {{ $order->shoe_size }})</span>
            </div>
            <div class="info-row">
                <span class="info-label">Layanan</span>
                <span class="info-value">{{ $order->service->name }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">No. Pesanan</span>
                <span class="info-value" style="color: #f97316; font-size: 0.85rem;">#{{ $order->order_number }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Masuk</span>
                <span class="info-value">{{ $order->created_at->format('d M Y H:i') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Total</span>
                <span class="info-value">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
            </div>
        </div>

        @if($order->status !== 'cancelled')
        <div class="progress-section">
            <div class="progress-label">Progres Pengerjaan</div>
            <div class="progress-steps">
                @foreach($steps as $i => $stepName)
                    @if($i > 0)
                        <div class="step-line {{ $s['step'] >= $i ? 'done' : '' }}"></div>
                    @endif
                    <div class="step">
                        <div class="step-dot {{ $s['step'] > $i ? 'done' : ($s['step'] == $i ? 'active' : 'waiting') }}">
                            @if($s['step'] > $i) ✓ @else {{ $i + 1 }} @endif
                        </div>
                        <div class="step-text {{ $s['step'] == $i ? 'active-text' : '' }}">{{ $stepName }}</div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    @if($order->status === 'ready')
    <div style="background: rgba(16,185,129,0.08); border: 1px solid rgba(16,185,129,0.2); border-radius: 16px; padding: 1.2rem; margin-top: 1rem; text-align: center; animation: pulseGreen 2s ease-in-out infinite;">
        <div style="font-size: 1.5rem; margin-bottom: 0.3rem;">🎉</div>
        <div style="font-weight: 800; color: #10b981;">Sepatu Anda Sudah Siap!</div>
        <div style="font-size: 0.82rem; opacity: 0.7; margin-top: 0.3rem;">Silakan ambil di outlet kami.</div>
    </div>
    <style>
        @keyframes pulseGreen {
            0%,100% { box-shadow: 0 0 0 0 rgba(16,185,129,0); }
            50% { box-shadow: 0 0 20px 4px rgba(16,185,129,0.15); }
        }
    </style>
    @endif

    @elseif($queueNumber && !$order)
    {{-- NOT FOUND --}}
    <div class="not-found">
        <div class="emoji">🔍</div>
        <h3>Antrian Tidak Ditemukan</h3>
        <p>Nomor antrian <strong style="color:#f97316;">{{ strtoupper($queueNumber) }}</strong> tidak ditemukan.<br>Pastikan nomor antrian sudah benar.</p>
    </div>
    @endif

    <div class="footer-link">
        <p>Pantau semua antrian di <a href="{{ route('queue.display') }}" target="_blank">halaman display antrian →</a></p>
    </div>
</div>

</body>
</html>
