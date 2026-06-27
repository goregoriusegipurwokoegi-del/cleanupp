@extends('layouts.employee-dashboard')

@section('page_title', 'Absensi Karyawan')

@section('content')
<div class="row">
    <!-- Attendance Card -->
    <div class="col-md-6 mb-4">
        <div class="card card-outline {{ $isClockedOut ? 'card-secondary' : ($isClockedIn ? 'card-success' : 'card-warning') }} shadow-sm h-100">
            <div class="card-header">
                <h3 class="card-title fw-bold">
                    <i class="bi bi-clock me-2"></i>
                    Status Kehadiran Hari Ini
                </h3>
            </div>
            <div class="card-body d-flex flex-column justify-content-between">
                <div>
                    <div class="mb-3">
                        @if($isClockedOut)
                            <span class="badge bg-secondary px-3 py-2">Selesai Hari Ini</span>
                        @elseif($isClockedIn)
                            <span class="badge bg-success px-3 py-2">🟢 Sedang Bekerja</span>
                        @else
                            <span class="badge bg-warning text-dark px-3 py-2">⏰ Belum Absen Masuk</span>
                        @endif
                    </div>
                    
                    <h5 class="fw-bold mb-3 text-dark">
                        @if($isClockedOut) Kerja keras hari ini, istirahat yang baik!
                        @elseif($isClockedIn) Durasi Bekerja Hari Ini:
                        @else Hai, {{ Str::words(Auth::user()->name, 1, '') }}! Yuk mulai kerja 👋
                        @endif
                    </h5>

                    @if($isClockedIn && !$isClockedOut)
                        <div class="fs-1 fw-bold text-success font-monospace" id="work-timer" data-clockin="{{ $todayAttendance->clock_in }}">00:00:00</div>
                    @elseif($isClockedOut)
                        @php
                            $dur = \Carbon\Carbon::parse($todayAttendance->clock_in)->diff(\Carbon\Carbon::parse($todayAttendance->clock_out));
                        @endphp
                        <div class="fs-1 fw-bold text-secondary font-monospace">{{ str_pad($dur->h,2,'0',STR_PAD_LEFT) }}:{{ str_pad($dur->i,2,'0',STR_PAD_LEFT) }}</div>
                        <div class="text-muted small mt-2">
                            Masuk: {{ date('H:i',strtotime($todayAttendance->clock_in)) }} — Pulang: {{ date('H:i',strtotime($todayAttendance->clock_out)) }}
                        </div>
                    @else
                        <p class="text-muted">Klik tombol di bawah ini untuk memulai absensi masuk Anda hari ini.</p>
                    @endif
                </div>

                <div class="mt-4">
                    @if(!$isClockedIn)
                        <form action="{{ route('employee.attendance.clock-in') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success w-100 py-2 fw-bold text-uppercase shadow-sm">
                                <i class="bi bi-box-arrow-in-right me-2"></i> Absen Masuk
                            </button>
                        </form>
                    @elseif(!$isClockedOut)
                        <form action="{{ route('employee.attendance.clock-out') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-danger w-100 py-2 fw-bold text-uppercase shadow-sm">
                                <i class="bi bi-box-arrow-left me-2"></i> Absen Pulang
                            </button>
                        </form>
                    @else
                        <div class="alert alert-light text-center border mb-0 py-2 shadow-sm">
                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                            <span class="fw-bold text-secondary">Absensi hari ini sudah lengkap!</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <!-- Attendance History Table / Card -->
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-light">
                <h3 class="card-title fw-bold text-dark">
                    <i class="bi bi-calendar-event me-2 text-primary"></i>
                    Riwayat Kehadiran
                </h3>
            </div>
            <div class="card-body p-0" style="max-height: 400px; overflow-y: auto;">
                <div class="table-responsive d-none d-md-block">
                    <table class="table table-hover table-striped align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="py-2.5 px-3 small text-secondary fw-bold">Tanggal</th>
                                <th class="py-2.5 px-3 small text-secondary fw-bold">Masuk</th>
                                <th class="py-2.5 px-3 small text-secondary fw-bold">Pulang</th>
                                <th class="py-2.5 px-3 small text-secondary fw-bold">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($attendances as $att)
                            @php
                                $status = ($att->clock_in && \Carbon\Carbon::parse($att->clock_in)->format('H:i') > '09:00') ? 'Terlambat' : 'Tepat Waktu';
                                $badgeClass = $status == 'Tepat Waktu' ? 'bg-success' : 'bg-danger';
                            @endphp
                            <tr>
                                <td class="px-3 fw-bold small">{{ \Carbon\Carbon::parse($att->date)->translatedFormat('d M Y') }}</td>
                                <td class="px-3 text-success fw-bold small">{{ $att->clock_in ? \Carbon\Carbon::parse($att->clock_in)->format('H:i') : '-' }}</td>
                                <td class="px-3 text-danger fw-bold small">{{ $att->clock_out ? \Carbon\Carbon::parse($att->clock_out)->format('H:i') : '-' }}</td>
                                <td class="px-3">
                                    <span class="badge {{ $badgeClass }}" style="font-size: 0.65rem;">{{ $status }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted small">Belum ada riwayat absensi.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-3 d-md-none">
                    @forelse($attendances as $att)
                    @php
                        $status = ($att->clock_in && \Carbon\Carbon::parse($att->clock_in)->format('H:i') > '09:00') ? 'Terlambat' : 'Tepat Waktu';
                        $badgeClass = $status == 'Tepat Waktu' ? 'bg-success' : 'bg-danger';
                    @endphp
                    <div class="card border shadow-sm mb-2">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center py-2 px-3">
                            <span class="fw-bold text-dark small">{{ \Carbon\Carbon::parse($att->date)->translatedFormat('d M Y') }}</span>
                            <span class="badge {{ $badgeClass }}" style="font-size: 0.65rem;">{{ $status }}</span>
                        </div>
                        <div class="card-body p-2 px-3">
                            <div class="row g-2">
                                <div class="col-6">
                                    <small class="text-secondary d-block" style="font-size: 0.75rem;">Jam Masuk</small>
                                    <span class="text-success fw-bold small">{{ $att->clock_in ? \Carbon\Carbon::parse($att->clock_in)->format('H:i') : '-' }}</span>
                                </div>
                                <div class="col-6">
                                    <small class="text-secondary d-block" style="font-size: 0.75rem;">Jam Keluar</small>
                                    <span class="text-danger fw-bold small">{{ $att->clock_out ? \Carbon\Carbon::parse($att->clock_out)->format('H:i') : '-' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-3 text-muted small">Belum ada riwayat absensi.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Work Timer Live
    const timerEl = document.getElementById('work-timer');
    if (timerEl) {
        const clockIn = new Date(timerEl.dataset.clockin);
        function tick() {
            const diff = Math.max(0, Math.floor((new Date() - clockIn) / 1000));
            const h = Math.floor(diff/3600), m = Math.floor((diff%3600)/60), s = diff%60;
            timerEl.textContent = String(h).padStart(2,'0')+':'+String(m).padStart(2,'0')+':'+String(s).padStart(2,'0');
        }
        tick(); setInterval(tick, 1000);
    }
});
</script>
@endpush
@endsection
