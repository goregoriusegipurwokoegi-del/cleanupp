@extends('layouts.employee-dashboard')

@section('page_title', 'Laporan Kinerja Karyawan')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <!-- Global Date Filter Form -->
        <div class="card shadow-sm border-0">
            <div class="card-body py-3">
                <form action="{{ route('employee.reports.index') }}" method="GET" class="row g-3 align-items-end">
                    <div class="col-6 col-md-3">
                        <label class="form-label text-secondary text-uppercase fw-bold small">Tanggal Mulai</label>
                        <input type="date" name="start_date" value="{{ $startDate }}" class="form-control">
                    </div>
                    <div class="col-6 col-md-3">
                        <label class="form-label text-secondary text-uppercase fw-bold small">Tanggal Selesai</label>
                        <input type="date" name="end_date" value="{{ $endDate }}" class="form-control">
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label text-secondary text-uppercase fw-bold small">Pencarian Pesanan</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-secondary"><i class="bi bi-search"></i></span>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari ID Pesanan..." class="form-control">
                        </div>
                    </div>
                    <div class="col-12 col-md-2">
                        <button type="submit" class="btn btn-primary w-100 fw-bold shadow-sm">Terapkan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- TAB 1: RINGKASAN KINERJA -->
<div id="ringkasan-content" class="tab-content">
    <h4 class="fw-bold text-dark mb-3"><i class="bi bi-info-circle me-2 text-primary"></i> Ringkasan Kinerja Periode Ini</h4>
    
    <div class="row g-3 mb-4">
        <!-- Card 1: Completed Tasks -->
        <div class="col-12 col-md-4">
            <div class="card shadow-sm border-0 border-start border-primary border-4">
                <div class="card-body py-4">
                    <p class="text-secondary text-uppercase fw-bold small mb-2">Selesai Dikerjakan</p>
                    <h2 class="fw-bold mb-0 text-dark">{{ $stats['total_completed'] }} <span class="fs-5 text-muted fw-normal">Sepatu</span></h2>
                </div>
            </div>
        </div>
        <!-- Card 2: Processing Tasks -->
        <div class="col-12 col-md-4">
            <div class="card shadow-sm border-0 border-start border-info border-4">
                <div class="card-body py-4">
                    <p class="text-secondary text-uppercase fw-bold small mb-2">Sedang Diproses</p>
                    <h2 class="fw-bold mb-0 text-dark">{{ $stats['processing'] }} <span class="fs-5 text-muted fw-normal">Sepatu</span></h2>
                </div>
            </div>
        </div>
        <!-- Card 3: Avg Rating -->
        <div class="col-12 col-md-4">
            <div class="card shadow-sm border-0 border-start border-warning border-4">
                <div class="card-body py-4">
                    <p class="text-secondary text-uppercase fw-bold small mb-2">Rata-rata Rating</p>
                    <h2 class="fw-bold mb-0 text-dark">
                        {{ $stats['avg_rating'] }}
                        <span class="fs-5 text-warning fw-normal"><i class="bi bi-star-fill ms-1"></i></span>
                    </h2>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light py-3">
                    <h5 class="card-title fw-bold mb-0">Distribusi Pekerjaan Selesai</h5>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="fw-semibold">Cleaning (Cuci Sepatu)</span>
                            <span class="fw-bold text-primary">{{ $stats['cleaning_done'] }} Pasang</span>
                        </div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $stats['total_completed'] > 0 ? ($stats['cleaning_done'] / $stats['total_completed']) * 100 : 0 }}%;"></div>
                        </div>
                    </div>
                    <div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="fw-semibold">Reparasi & Cat Ulang</span>
                            <span class="fw-bold text-success">{{ $stats['repair_done'] }} Pasang</span>
                        </div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $stats['total_completed'] > 0 ? ($stats['repair_done'] / $stats['total_completed']) * 100 : 0 }}%;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- TAB 2: TUGAS SAYA -->
<div id="tugas-content" class="tab-content" style="display: none;">
    <h4 class="fw-bold text-dark mb-3"><i class="bi bi-list-task me-2 text-primary"></i> Tugas Aktif Saat Ini</h4>
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive d-none d-md-block">
                <table class="table table-hover table-striped align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="py-3 px-4 text-secondary text-uppercase fw-bold" style="font-size: 0.75rem;">Antrian</th>
                            <th class="py-3 px-4 text-secondary text-uppercase fw-bold" style="font-size: 0.75rem;">Pelanggan</th>
                            <th class="py-3 px-4 text-secondary text-uppercase fw-bold" style="font-size: 0.75rem;">Layanan</th>
                            <th class="py-3 px-4 text-secondary text-uppercase fw-bold" style="font-size: 0.75rem;">Status</th>
                            <th class="py-3 px-4 text-secondary text-uppercase fw-bold" style="font-size: 0.75rem;">Tanggal Masuk</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($myTasks as $task)
                        <tr>
                            <td class="px-4 fw-bold text-primary">#{{ $task->queue_number }}</td>
                            <td class="px-4">{{ $task->user->name }}</td>
                            <td class="px-4">{{ $task->service->name }}</td>
                            <td class="px-4">
                                <span class="badge bg-info text-white text-uppercase px-3 py-2">{{ $task->status }}</span>
                            </td>
                            <td class="px-4 text-secondary small">{{ $task->created_at->format('d M Y H:i') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="bi bi-clipboard-x fs-2 d-block mb-2 opacity-50"></i>
                                Tidak ada tugas aktif.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card View Layout -->
            <div class="p-3 d-md-none">
                @forelse($myTasks as $task)
                <div class="card border shadow-sm mb-3">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center py-2 px-3">
                        <span class="fw-bold text-primary">#{{ $task->queue_number }}</span>
                        <span class="badge bg-info text-white text-uppercase px-2.5 py-1.5" style="font-size: 0.7rem;">{{ $task->status }}</span>
                    </div>
                    <div class="card-body p-3">
                        <div class="mb-2">
                            <small class="text-secondary d-block">Pelanggan</small>
                            <span class="fw-semibold text-dark">{{ $task->user->name }}</span>
                        </div>
                        <div class="mb-2">
                            <small class="text-secondary d-block">Layanan</small>
                            <span class="text-dark">{{ $task->service->name }}</span>
                        </div>
                        <div>
                            <small class="text-secondary d-block">Tanggal Masuk</small>
                            <span class="text-secondary small">{{ $task->created_at->format('d M Y H:i') }}</span>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-clipboard-x fs-2 d-block mb-2 opacity-50"></i>
                    Tidak ada tugas aktif.
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- TAB 3: RIWAYAT PEKERJAAN -->
<div id="riwayat-content" class="tab-content" style="display: none;">
    <h4 class="fw-bold text-dark mb-3"><i class="bi bi-clock-history me-2 text-primary"></i> Riwayat Pekerjaan Selesai</h4>
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive d-none d-md-block">
                <table class="table table-hover table-striped align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="py-3 px-4 text-secondary text-uppercase fw-bold" style="font-size: 0.75rem;">Tanggal Selesai</th>
                            <th class="py-3 px-4 text-secondary text-uppercase fw-bold" style="font-size: 0.75rem;">Antrian</th>
                            <th class="py-3 px-4 text-secondary text-uppercase fw-bold" style="font-size: 0.75rem;">Layanan</th>
                            <th class="py-3 px-4 text-secondary text-uppercase fw-bold" style="font-size: 0.75rem;">Harga Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($historyTasks as $task)
                        <tr>
                            <td class="px-4 text-secondary small">{{ $task->updated_at->format('d M Y H:i') }}</td>
                            <td class="px-4 fw-bold text-primary">#{{ $task->queue_number }}</td>
                            <td class="px-4">{{ $task->service->name }}</td>
                            <td class="px-4 fw-bold">Rp {{ number_format($task->total_price, 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">
                                <i class="bi bi-calendar-x fs-2 d-block mb-2 opacity-50"></i>
                                Belum ada riwayat pekerjaan.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card View Layout -->
            <div class="p-3 d-md-none">
                @forelse($historyTasks as $task)
                <div class="card border shadow-sm mb-3">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center py-2 px-3">
                        <span class="fw-bold text-primary">#{{ $task->queue_number }}</span>
                        <span class="text-secondary small">{{ $task->updated_at->format('d M Y H:i') }}</span>
                    </div>
                    <div class="card-body p-3">
                        <div class="mb-2">
                            <small class="text-secondary d-block">Layanan</small>
                            <span class="text-dark fw-semibold">{{ $task->service->name }}</span>
                        </div>
                        <div>
                            <small class="text-secondary d-block">Harga Total</small>
                            <strong class="text-dark">Rp {{ number_format($task->total_price, 0, ',', '.') }}</strong>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-calendar-x fs-2 d-block mb-2 opacity-50"></i>
                    Belum ada riwayat pekerjaan.
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- TAB 4: REKAP ABSENSI -->
<div id="absensi-content" class="tab-content" style="display: none;">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <h4 class="fw-bold text-dark mb-0"><i class="bi bi-calendar-event me-2 text-primary"></i> Rekap Absensi Mandiri</h4>
        <div class="d-flex gap-1">
            <a href="{{ route('employee.reports.attendance.pdf', ['start_date' => $startDate, 'end_date' => $endDate]) }}" target="_blank" class="btn btn-outline-danger btn-sm fw-bold px-3">
                <i class="bi bi-file-pdf me-1"></i> Export PDF
            </a>
            <a href="{{ route('employee.reports.attendance.excel', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-outline-success btn-sm fw-bold px-3">
                <i class="bi bi-file-excel me-1"></i> Export Excel
            </a>
        </div>
    </div>
    
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive d-none d-md-block">
                <table class="table table-hover table-striped align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="py-3 px-4 text-secondary text-uppercase fw-bold" style="font-size: 0.75rem;">Hari, Tanggal</th>
                            <th class="py-3 px-4 text-secondary text-uppercase fw-bold" style="font-size: 0.75rem;">Jam Masuk</th>
                            <th class="py-3 px-4 text-secondary text-uppercase fw-bold" style="font-size: 0.75rem;">Jam Keluar</th>
                            <th class="py-3 px-4 text-secondary text-uppercase fw-bold" style="font-size: 0.75rem;">Status Kehadiran</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attendances as $att)
                        @php
                            $status = ($att->clock_in && \Carbon\Carbon::parse($att->clock_in)->format('H:i') > '09:00') ? 'Terlambat' : 'Tepat Waktu';
                            $badgeClass = $status == 'Tepat Waktu' ? 'bg-success' : 'bg-danger';
                        @endphp
                        <tr>
                            <td class="px-4 fw-bold">{{ \Carbon\Carbon::parse($att->date)->translatedFormat('l, d M Y') }}</td>
                            <td class="px-4 text-success fw-bold">{{ $att->clock_in ? \Carbon\Carbon::parse($att->clock_in)->format('H:i') : '-' }}</td>
                            <td class="px-4 text-danger fw-bold">{{ $att->clock_out ? \Carbon\Carbon::parse($att->clock_out)->format('H:i') : '-' }}</td>
                            <td class="px-4">
                                <span class="badge {{ $badgeClass }} px-3 py-2">{{ $status }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">
                                <i class="bi bi-calendar-x fs-2 d-block mb-2 opacity-50"></i>
                                Belum ada data absensi.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card View Layout -->
            <div class="p-3 d-md-none">
                @forelse($attendances as $att)
                @php
                    $status = ($att->clock_in && \Carbon\Carbon::parse($att->clock_in)->format('H:i') > '09:00') ? 'Terlambat' : 'Tepat Waktu';
                    $badgeClass = $status == 'Tepat Waktu' ? 'bg-success' : 'bg-danger';
                @endphp
                <div class="card border shadow-sm mb-3">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center py-2 px-3">
                        <span class="fw-bold text-dark">{{ \Carbon\Carbon::parse($att->date)->translatedFormat('l, d M Y') }}</span>
                        <span class="badge {{ $badgeClass }} px-2.5 py-1.5" style="font-size: 0.7rem;">{{ $status }}</span>
                    </div>
                    <div class="card-body p-3">
                        <div class="row g-2">
                            <div class="col-6">
                                <small class="text-secondary d-block">Jam Masuk</small>
                                <span class="text-success fw-bold">{{ $att->clock_in ? \Carbon\Carbon::parse($att->clock_in)->format('H:i') : '-' }}</span>
                            </div>
                            <div class="col-6">
                                <small class="text-secondary d-block">Jam Keluar</small>
                                <span class="text-danger fw-bold">{{ $att->clock_out ? \Carbon\Carbon::parse($att->clock_out)->format('H:i') : '-' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-calendar-x fs-2 d-block mb-2 opacity-50"></i>
                    Belum ada data absensi.
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- TAB 5: RATING PELANGGAN -->
<div id="rating-content" class="tab-content" style="display: none;">
    <h4 class="fw-bold text-dark mb-3"><i class="bi bi-chat-left-heart me-2 text-primary"></i> Penilaian & Feedback Pelanggan</h4>
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive d-none d-md-block">
                <table class="table table-hover table-striped align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="py-3 px-4 text-secondary text-uppercase fw-bold" style="font-size: 0.75rem; width: 150px;">Antrian</th>
                            <th class="py-3 px-4 text-secondary text-uppercase fw-bold" style="font-size: 0.75rem; width: 200px;">Rating</th>
                            <th class="py-3 px-4 text-secondary text-uppercase fw-bold" style="font-size: 0.75rem;">Komentar Pelanggan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customerRatings as $task)
                        <tr>
                            <td class="px-4 fw-bold text-primary">#{{ $task->queue_number }}</td>
                            <td class="px-4">
                                <div class="text-warning">
                                    @for($i=0; $i<$task->rating; $i++)
                                        <i class="bi bi-star-fill" style="font-size: 0.85rem;"></i>
                                    @endfor
                                </div>
                            </td>
                            <td class="px-4 text-dark italic">"{{ $task->review ?? 'Tanpa ulasan tertulis' }}"</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center py-5 text-muted">
                                <i class="bi bi-chat-square-quote fs-2 d-block mb-2 opacity-50"></i>
                                Belum ada rating atau feedback yang masuk.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card View Layout -->
            <div class="p-3 d-md-none">
                @forelse($customerRatings as $task)
                <div class="card border shadow-sm mb-3">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center py-2 px-3">
                        <span class="fw-bold text-primary">#{{ $task->queue_number }}</span>
                        <div class="text-warning">
                            @for($i=0; $i<$task->rating; $i++)
                                <i class="bi bi-star-fill" style="font-size: 0.8rem;"></i>
                            @endfor
                        </div>
                    </div>
                    <div class="card-body p-3">
                        <small class="text-secondary d-block">Ulasan</small>
                        <p class="text-dark italic mb-0">"{{ $task->review ?? 'Tanpa ulasan tertulis' }}"</p>
                    </div>
                </div>
                @empty
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-chat-square-quote fs-2 d-block mb-2 opacity-50"></i>
                    Belum ada rating atau feedback yang masuk.
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- TAB 6: STATISTIK KERJA -->
<div id="statistik-content" class="tab-content" style="display: none;">
    <h4 class="fw-bold text-dark mb-3"><i class="bi bi-activity me-2 text-primary"></i> Tren Penyelesaian Cucian Sepatu</h4>
    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <div style="height: 320px; position: relative; width: 100%;">
                <canvas id="performanceChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- TAB 7: LAPORAN ANTAR JEMPUT -->
<div id="antar_jemput-content" class="tab-content" style="display: none;">
    <h4 class="fw-bold text-dark mb-3"><i class="bi bi-truck me-2 text-primary"></i> Riwayat Layanan Antar Jemput</h4>
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive d-none d-md-block">
                <table class="table table-hover table-striped align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="py-3 px-4 text-secondary text-uppercase fw-bold" style="font-size: 0.75rem;">Antrian</th>
                            <th class="py-3 px-4 text-secondary text-uppercase fw-bold" style="font-size: 0.75rem;">Status Delivery</th>
                            <th class="py-3 px-4 text-secondary text-uppercase fw-bold" style="font-size: 0.75rem;">Alamat Lengkap</th>
                            <th class="py-3 px-4 text-secondary text-uppercase fw-bold" style="font-size: 0.75rem; width: 150px;">Biaya Kirim</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($deliveryTasks as $task)
                        <tr>
                            <td class="px-4 fw-bold text-primary">#{{ $task->queue_number }}</td>
                            <td class="px-4">
                                <span class="badge bg-secondary text-uppercase px-2 py-1">{{ $task->status }}</span>
                            </td>
                            <td class="px-4 text-dark">{{ Str::limit($task->delivery_address, 70) }}</td>
                            <td class="px-4 fw-bold">Rp {{ number_format($task->delivery_fee, 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">
                                <i class="bi bi-geo-alt fs-2 d-block mb-2 opacity-50"></i>
                                Tidak ada pesanan antar jemput terdaftar.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card View Layout -->
            <div class="p-3 d-md-none">
                @forelse($deliveryTasks as $task)
                <div class="card border shadow-sm mb-3">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center py-2 px-3">
                        <span class="fw-bold text-primary">#{{ $task->queue_number }}</span>
                        <span class="badge bg-secondary text-uppercase px-2.5 py-1.5" style="font-size: 0.7rem;">{{ $task->status }}</span>
                    </div>
                    <div class="card-body p-3">
                        <div class="mb-2">
                            <small class="text-secondary d-block">Alamat Lengkap</small>
                            <span class="text-dark small d-block" style="word-break: break-all;">{{ $task->delivery_address }}</span>
                        </div>
                        <div>
                            <small class="text-secondary d-block">Biaya Kirim</small>
                            <strong class="text-dark">Rp {{ number_format($task->delivery_fee, 0, ',', '.') }}</strong>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-geo-alt fs-2 d-block mb-2 opacity-50"></i>
                    Tidak ada pesanan antar jemput terdaftar.
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    function switchTab(tabId) {
        document.querySelectorAll('.tab-content').forEach(el => el.style.display = 'none');
        const target = document.getElementById(tabId + '-content');
        if (target) {
            target.style.display = 'block';
        }
        localStorage.setItem('employee_reports_active_tab', tabId);

        // Update active class state inside navbar
        document.querySelectorAll('.nav-treeview .nav-link').forEach(link => {
            if (link.getAttribute('href').includes('#' + tabId)) {
                link.classList.add('active');
            } else {
                link.classList.remove('active');
            }
        });
    }

    function handleHashChange() {
        let hash = window.location.hash.substring(1);
        if (!hash) {
            hash = localStorage.getItem('employee_reports_active_tab') || 'ringkasan';
        }
        if (document.getElementById(hash + '-content')) {
            switchTab(hash);
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        handleHashChange();
        window.addEventListener('hashchange', handleHashChange);

        // Initialize Performance Chart
        const chartEl = document.getElementById('performanceChart');
        if (chartEl) {
            const ctx = chartEl.getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($chartData['labels'] ?? []) !!},
                    datasets: [{
                        label: 'Sepatu Selesai',
                        data: {!! json_encode($chartData['counts'] ?? []) !!},
                        borderColor: '#0d6efd',
                        backgroundColor: 'rgba(13, 110, 253, 0.08)',
                        borderWidth: 3,
                        tension: 0.35,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { 
                        legend: { display: false }
                    },
                    scales: {
                        y: { 
                            beginAtZero: true, 
                            grid: { color: '#e9ecef' }, 
                            ticks: { color: '#6c757d' } 
                        },
                        x: { 
                            grid: { display: false }, 
                            ticks: { color: '#6c757d' } 
                        }
                    }
                }
            });
        }
    });
</script>
@endpush
