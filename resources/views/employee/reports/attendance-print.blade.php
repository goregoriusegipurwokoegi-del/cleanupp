<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Kehadiran - {{ $user->name }}</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #333; line-height: 1.4; padding: 2rem; background: #fff; }
        .header { text-align: center; margin-bottom: 2rem; border-bottom: 2px solid #333; padding-bottom: 1rem; }
        .header h1 { margin: 0 0 0.5rem 0; font-size: 1.8rem; text-transform: uppercase; }
        .header p { margin: 0; font-size: 0.9rem; color: #666; }
        .meta-info { display: flex; justify-content: space-between; margin-bottom: 1.5rem; font-size: 0.95rem; }
        table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        th, td { border: 1px solid #ddd; padding: 0.8rem; text-align: left; }
        th { background-color: #f5f5f5; font-weight: bold; }
        tr:nth-child(even) { background-color: #fafafa; }
        .footer { text-align: center; margin-top: 3rem; font-size: 0.8rem; color: #999; border-top: 1px solid #eee; padding-top: 1rem; }
        @media print {
            body { padding: 0; }
            button { display: none; }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>REKAP KEHADIRAN KARYAWAN</h1>
        <p>CleanUP Shoes - Jasa Pembersihan dan Perawatan Sepatu Premium</p>
    </div>
    
    <div class="meta-info">
        <div>
            <strong>Nama Karyawan:</strong> {{ $user->name }}<br>
            <strong>Email:</strong> {{ $user->email }}
        </div>
        <div style="text-align: right;">
            <strong>Periode:</strong> {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}<br>
            <strong>Tanggal Cetak:</strong> {{ now()->format('d/m/Y H:i') }}
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Jam Masuk</th>
                <th>Jam Keluar</th>
                <th>Durasi Kerja</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($attendances as $att)
                @php
                    $duration = '-';
                    if ($att->clock_in && $att->clock_out) {
                        $in = \Carbon\Carbon::parse($att->clock_in);
                        $out = \Carbon\Carbon::parse($att->clock_out);
                        $diff = $in->diff($out);
                        $duration = $diff->format('%h Jam %i Menit');
                    }
                    $status = 'Tepat Waktu';
                    if ($att->clock_in && \Carbon\Carbon::parse($att->clock_in)->format('H:i') > '09:00') {
                        $status = 'Terlambat';
                    }
                @endphp
                <tr>
                    <td>{{ \Carbon\Carbon::parse($att->date)->format('d F Y') }}</td>
                    <td>{{ $att->clock_in ? \Carbon\Carbon::parse($att->clock_in)->format('H:i:s') : '-' }}</td>
                    <td>{{ $att->clock_out ? \Carbon\Carbon::parse($att->clock_out)->format('H:i:s') : '-' }}</td>
                    <td>{{ $duration }}</td>
                    <td style="font-weight: bold; color: {{ $status == 'Terlambat' ? '#d9534f' : '#5cb85c' }}">{{ $status }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center; opacity: 0.5;">Tidak ada riwayat kehadiran pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dokumen ini dibuat secara otomatis oleh Sistem CleanUP Shoes pada {{ now()->format('d M Y H:i:s') }}.
    </div>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
