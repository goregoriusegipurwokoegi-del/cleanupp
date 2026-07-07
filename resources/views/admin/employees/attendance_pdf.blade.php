<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekap Absensi Karyawan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        h2 {
            text-align: center;
            margin-bottom: 5px;
        }
        p.period {
            text-align: center;
            margin-bottom: 20px;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .late {
            color: #d9534f;
            font-weight: bold;
        }
        .on-time {
            color: #5cb85c;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <h2>Rekap Absensi Karyawan</h2>
    <p class="period">Periode: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Karyawan</th>
                <th>Tanggal</th>
                <th>Jam Masuk</th>
                <th>Jam Pulang</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($attendances as $index => $att)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        {{ $att->user->name }}<br>
                        <small>Target: {{ \Carbon\Carbon::parse($att->user->work_start)->format('H:i') }}</small>
                    </td>
                    <td>{{ \Carbon\Carbon::parse($att->date)->format('d M Y') }}</td>
                    <td>{{ $att->clock_in ? \Carbon\Carbon::parse($att->clock_in)->format('H:i:s') : '--:--' }}</td>
                    <td>{{ $att->clock_out ? \Carbon\Carbon::parse($att->clock_out)->format('H:i:s') : '--:--' }}</td>
                    <td>
                        @php
                            $isLate = false;
                            if($att->clock_in) {
                                $in = \Carbon\Carbon::parse($att->clock_in)->format('H:i');
                                $target = \Carbon\Carbon::parse($att->user->work_start)->format('H:i');
                                $isLate = $in > $target;
                            }
                        @endphp
                        @if($isLate)
                            <span class="late">Terlambat</span>
                        @else
                            <span class="on-time">Tepat Waktu</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center;">Belum ada data absensi untuk periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
