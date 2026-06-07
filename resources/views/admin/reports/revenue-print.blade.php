<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Keuangan - CleanUP Shoes</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            line-height: 1.5;
            margin: 0;
            padding: 20px;
            background: #fff;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #222;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            margin: 0 0 5px 0;
            font-size: 24px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .header p {
            margin: 0;
            color: #666;
            font-size: 14px;
        }
        .info-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .info-box {
            background: #f9f9f9;
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 8px;
            width: 45%;
        }
        .info-box h3 {
            margin: 0 0 10px 0;
            font-size: 14px;
            color: #555;
            text-transform: uppercase;
        }
        .info-box p {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
            color: #222;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f4f4f4;
            font-size: 12px;
            text-transform: uppercase;
            color: #555;
            font-weight: bold;
        }
        td {
            font-size: 13px;
        }
        .text-right {
            text-align: right;
        }
        .total-row {
            background-color: #f9f9f9;
            font-weight: bold;
        }
        .total-row td {
            border-top: 2px solid #222;
            border-bottom: 2px solid #222;
            font-size: 14px;
        }
        .footer {
            margin-top: 50px;
            text-align: right;
        }
        .signature-line {
            width: 200px;
            border-bottom: 1px solid #333;
            margin-left: auto;
            margin-top: 60px;
            margin-bottom: 5px;
        }
        @media print {
            body { padding: 0; }
            @page { margin: 1.5cm; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="header">
        <h1>CleanUP Shoes</h1>
        <p>Laporan Keuangan Pendapatan Profesional</p>
    </div>

    <div class="info-section">
        <div class="info-box">
            <h3>Periode Laporan</h3>
            <p>{{ \Carbon\Carbon::parse($startDate)->format('d F Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d F Y') }}</p>
        </div>
        <div class="info-box" style="text-align: right;">
            <h3>Total Pendapatan Bersih</h3>
            <p style="color: #10b981; font-size: 24px;">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 15%;">Tanggal</th>
                <th style="width: 25%;">No. Transaksi</th>
                <th style="width: 30%;">Layanan & Kategori</th>
                <th style="width: 15%;">Metode</th>
                <th class="text-right" style="width: 15%;">Nominal</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $order)
            <tr>
                <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                <td style="font-weight: bold; color: #444;">#{{ $order->order_number }}</td>
                <td>{{ $order->service->name }}</td>
                <td><span style="background: #eee; padding: 3px 8px; border-radius: 4px; font-size: 11px; font-weight: bold;">{{ strtoupper($order->payment_method) }}</span></td>
                <td class="text-right" style="font-weight: bold;">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align: center; color: #999; padding: 30px;">Tidak ada transaksi pendapatan pada periode ini.</td>
            </tr>
            @endforelse
            <tr class="total-row">
                <td colspan="4" class="text-right" style="padding-right: 20px;">TOTAL PENDAPATAN BERSIH</td>
                <td class="text-right" style="color: #10b981; font-size: 16px;">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <p style="color: #555; font-size: 14px;">Tercetak pada: {{ now()->format('d F Y H:i') }}</p>
        <p style="margin-top: 30px; font-size: 14px;">Mengetahui, Administrator</p>
        <div class="signature-line"></div>
        <p style="font-size: 14px; font-weight: bold;">{{ Auth::user()->name }}</p>
    </div>

</body>
</html>
