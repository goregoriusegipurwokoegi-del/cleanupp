@extends('layouts.employee-dashboard')

@section('page_title', 'Dashboard Karyawan')

@section('content')
<style>
    @media (max-width: 767.98px) {
        .mobile-full-width {
            flex: 0 0 100% !important;
            max-width: 100% !important;
            width: 100% !important;
        }
    }
</style>
<!-- Dashboard Stats Cards Grid (Android App Style) -->
<div class="row row-cols-2 row-cols-md-3 row-cols-lg-5 g-3">
    <!-- Blue Box: Butuh Validasi -->
    <div class="col">
        <div class="card border-0 shadow-sm text-white h-100" style="background: linear-gradient(135deg, #0d6efd, #0a58ca); border-radius: 16px;">
            <div class="card-body p-3 d-flex flex-column justify-content-between">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                        <i class="bi bi-exclamation-triangle-fill fs-6"></i>
                    </span>
                    <strong class="fs-4">{{ $pendingOrdersCount }}</strong>
                </div>
                <div class="small fw-semibold text-white-50" style="font-size: 0.78rem; line-height: 1.2;">Butuh Validasi</div>
            </div>
        </div>
    </div>
    <!-- Green Box: Selesai Minggu Ini -->
    <div class="col">
        <div class="card border-0 shadow-sm text-white h-100" style="background: linear-gradient(135deg, #198754, #146c43); border-radius: 16px;">
            <div class="card-body p-3 d-flex flex-column justify-content-between">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                        <i class="bi bi-check-circle-fill fs-6"></i>
                    </span>
                    <strong class="fs-4">{{ $weeklyCompletedCount }}</strong>
                </div>
                <div class="small fw-semibold text-white-50" style="font-size: 0.78rem; line-height: 1.2;">Selesai Minggu Ini</div>
            </div>
        </div>
    </div>
    <!-- Yellow Box: Antrian Cuci -->
    <div class="col">
        <div class="card border-0 shadow-sm text-dark h-100" style="background: linear-gradient(135deg, #ffc107, #e0a800); border-radius: 16px;">
            <div class="card-body p-3 d-flex flex-column justify-content-between">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="bg-dark bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                        <i class="bi bi-droplet-fill fs-6 text-dark"></i>
                    </span>
                    <strong class="fs-4 text-dark">{{ $cleaningCounts['queue'] }}</strong>
                </div>
                <div class="small fw-semibold text-dark-50" style="font-size: 0.78rem; line-height: 1.2; color: rgba(0,0,0,0.6) !important;">Antrian Cuci</div>
            </div>
        </div>
    </div>
    <!-- Red Box: Antrian Reparasi -->
    <div class="col">
        <div class="card border-0 shadow-sm text-white h-100" style="background: linear-gradient(135deg, #dc3545, #b02a37); border-radius: 16px;">
            <div class="card-body p-3 d-flex flex-column justify-content-between">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                        <i class="bi bi-tools fs-6"></i>
                    </span>
                    <strong class="fs-4">{{ $repairCounts['queue'] }}</strong>
                </div>
                <div class="small fw-semibold text-white-50" style="font-size: 0.78rem; line-height: 1.2;">Antrian Reparasi</div>
            </div>
        </div>
    </div>
    <!-- Purple Box: Antar Jemput -->
    <div class="col mobile-full-width">
        <div class="card border-0 shadow-sm text-white h-100" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed); border-radius: 16px;">
            <div class="card-body p-3 d-flex flex-column justify-content-between">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                        <i class="bi bi-truck fs-6"></i>
                    </span>
                    <strong class="fs-4">{{ $deliveryOrdersCount }}</strong>
                </div>
                <div class="small fw-semibold text-white-50" style="font-size: 0.78rem; line-height: 1.2;">Antar Jemput</div>
            </div>
        </div>
    </div>
</div>

<!-- Incoming Orders Alert Banner -->
@if($incomingOrders->isNotEmpty())
<div class="alert alert-info border-0 shadow-sm d-flex flex-column flex-sm-row align-items-start align-items-sm-center justify-content-between my-4 p-3 gap-3 alert-recent-order" style="border-radius: 16px;">
    <div class="d-flex align-items-start gap-2">
        <i class="bi bi-info-circle-fill fs-4 text-primary mt-0.5"></i>
        <div>
            <strong style="font-size: 0.95rem; display: block; margin-bottom: 2px;">Pesanan Masuk Terbaru!</strong>
            <span style="font-size: 0.85rem;">Ada orderan {{ $incomingOrders->first()->service->name }} (Nomor Antrian: <strong>{{ $incomingOrders->first()->queue_number }}</strong>).</span>
        </div>
    </div>
    <a href="{{ route('employee.orders.index') }}" class="btn btn-primary btn-sm fw-bold px-3 py-2 text-nowrap align-self-stretch align-self-sm-center text-center" style="border-radius: 10px;">Proses Sekarang</a>
</div>
@endif

<!-- Monitoring Pipelines -->
<div class="row g-2">
    <!-- Cleaning -->
    <div class="col-6 mb-3">
        <div class="card shadow-sm pipeline-card">
            <div class="card-header bg-light py-2 py-md-3">
                <h3 class="card-title fw-bold text-primary mb-0 pipeline-card-title d-flex align-items-center gap-1">
                    <i class="bi bi-droplet-half text-primary"></i>
                    <span class="d-none d-md-inline">Monitoring</span> Cuci <span class="d-none d-sm-inline">Sepatu (Cleaning)</span>
                </h3>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <div class="list-group-item d-flex justify-content-between align-items-center py-2 py-md-3 px-2 px-md-3">
                        <span class="pipeline-item-text"><i class="bi bi-hourglass me-2 text-muted"></i> Dalam Antrian</span>
                        <span class="badge bg-secondary px-2 px-md-3 py-1 pipeline-badge">{{ $cleaningCounts['queue'] }}</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center py-2 py-md-3 px-2 px-md-3">
                        <span class="pipeline-item-text"><i class="bi bi-water me-2 text-info"></i> Dicuci</span>
                        <span class="badge bg-info px-2 px-md-3 py-1 pipeline-badge text-white">{{ $cleaningCounts['washing'] }}</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center py-2 py-md-3 px-2 px-md-3">
                        <span class="pipeline-item-text"><i class="bi bi-wind me-2 text-warning"></i> Pengeringan</span>
                        <span class="badge bg-warning px-2 px-md-3 py-1 pipeline-badge text-dark">{{ $cleaningCounts['drying'] }}</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center py-2 py-md-3 px-2 px-md-3">
                        <span class="pipeline-item-text"><i class="bi bi-check-circle me-2 text-success"></i> Siap Ambil</span>
                        <span class="badge bg-success px-2 px-md-3 py-1 pipeline-badge">{{ $cleaningCounts['ready'] }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Repair -->
    <div class="col-6 mb-3">
        <div class="card shadow-sm pipeline-card">
            <div class="card-header bg-light py-2 py-md-3">
                <h3 class="card-title fw-bold text-danger mb-0 pipeline-card-title d-flex align-items-center gap-1">
                    <i class="bi bi-tools text-danger"></i>
                    <span class="d-none d-md-inline">Monitoring</span> Reparasi <span class="d-none d-sm-inline">Sepatu (Repair)</span>
                </h3>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <div class="list-group-item d-flex justify-content-between align-items-center py-2 py-md-3 px-2 px-md-3">
                        <span class="pipeline-item-text"><i class="bi bi-hourglass me-2 text-muted"></i> Dalam Antrian</span>
                        <span class="badge bg-secondary px-2 px-md-3 py-1 pipeline-badge">{{ $repairCounts['queue'] }}</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center py-2 py-md-3 px-2 px-md-3">
                        <span class="pipeline-item-text"><i class="bi bi-hammer me-2 text-info"></i> Dikerjakan</span>
                        <span class="badge bg-info px-2 px-md-3 py-1 pipeline-badge text-white">{{ $repairCounts['processing'] }}</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center py-2 py-md-3 px-2 px-md-3">
                        <span class="pipeline-item-text"><i class="bi bi-stars me-2 text-warning"></i> Finishing</span>
                        <span class="badge bg-warning px-2 px-md-3 py-1 pipeline-badge text-dark">{{ $repairCounts['finishing'] }}</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center py-2 py-md-3 px-2 px-md-3">
                        <span class="pipeline-item-text"><i class="bi bi-check-circle me-2 text-success"></i> Siap Ambil</span>
                        <span class="badge bg-success px-2 px-md-3 py-1 pipeline-badge">{{ $repairCounts['ready'] }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Adaptive colors for the original alert banner */
    .alert-recent-order {
        background-color: #e0f2fe;
        color: #0369a1;
    }
    
    [data-bs-theme="dark"] .alert-recent-order {
        background-color: rgba(13, 110, 253, 0.12) !important;
        color: #60a5fa !important;
        border: 1px solid rgba(13, 110, 253, 0.25) !important;
    }
    
    [data-bs-theme="dark"] .alert-recent-order i {
        color: #60a5fa !important;
    }

    /* Styling for pipeline monitoring cards on mobile/small viewports */
    @media (max-width: 767.98px) {
        .pipeline-card {
            margin-bottom: 0.5rem !important;
        }
        .pipeline-card .card-header {
            padding: 8px 10px !important;
        }
        .pipeline-card-title {
            font-size: 0.82rem !important;
        }
        .pipeline-card-title i {
            font-size: 0.9rem !important;
        }
        .pipeline-card .list-group-item {
            padding: 8px 10px !important;
        }
        .pipeline-item-text {
            font-size: 0.76rem !important;
        }
        .pipeline-item-text i {
            font-size: 0.8rem !important;
            margin-right: 4px !important;
        }
        .pipeline-badge {
            font-size: 0.68rem !important;
            padding: 2px 6px !important;
        }
    }
</style>
@endpush

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
