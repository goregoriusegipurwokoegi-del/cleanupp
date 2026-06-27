@extends('layouts.premium-dashboard')

@section('page_title', 'Manajemen Keuangan')

@section('nav_items')
    <li class="nav-item"><a href="{{ route('admin.dashboard') }}" class="nav-link {{ Route::is('admin.dashboard') ? 'active' : '' }}">Dashboard</a></li>
    <li class="nav-item"><a href="{{ route('admin.orders.index') }}" class="nav-link {{ Route::is('admin.orders.index') ? 'active' : '' }}">Kelola Pesanan</a></li>
    <li class="nav-item"><a href="{{ route('admin.services.index') }}" class="nav-link {{ Route::is('admin.services.index') ? 'active' : '' }}">Kelola Layanan</a></li>
    <li class="nav-item"><a href="{{ route('admin.finances.index') }}" class="nav-link {{ Route::is('admin.finances.index') ? 'active' : '' }}">Keuangan</a></li>
    <li class="nav-item"><a href="{{ route('admin.employees.index') }}" class="nav-link {{ Route::is('admin.employees.index') ? 'active' : '' }}">Manajemen Staff</a></li>
    <li class="nav-item"><a href="{{ route('admin.reports.index') }}" class="nav-link {{ Route::is('admin.reports.index') ? 'active' : '' }}">Laporan</a></li>
@endsection

@section('content')
<style>
    .tab-bar {
        display: flex;
        gap: 0.5rem;
        margin-bottom: 1.5rem;
        background: var(--surface-variant);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        padding: 0.4rem;
        overflow-x: auto;
    }
    .tab-btn {
        flex: 1;
        text-align: center;
        padding: 0.7rem 1rem;
        border-radius: 12px;
        font-weight: 700;
        font-size: 0.85rem;
        cursor: pointer;
        text-decoration: none;
        color: var(--text-secondary);
        transition: all 0.3s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.4rem;
        white-space: nowrap;
    }
    .tab-btn.active {
        background: var(--primary);
        color: #fff;
    }
    .tab-btn:not(.active):hover {
        background: var(--primary-glow);
        color: var(--primary);
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .tab-content { animation: fadeIn 0.4s ease; }
    .filter-card {
        background: var(--surface-variant);
        border: 1px solid var(--border-color);
        border-radius: 20px;
        padding: 1.2rem;
        margin-bottom: 1rem;
    }
    .form-input {
        background: var(--surface);
        border: 1px solid var(--border-color);
        color: var(--text);
        padding: 0.8rem 1rem;
        border-radius: 12px;
        outline: none;
        cursor: pointer;
    }
    .form-input:focus { border-color: var(--primary); }
</style>

<div class="header-actions" style="margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">

    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
        <button onclick="openModal('income')" style="background: var(--success); color: #fff; border: none; padding: 0.8rem 1.5rem; border-radius: 12px; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 0.5rem; transition: 0.3s;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            Catat Pemasukan
        </button>
        <button onclick="openModal('expense')" style="background: #f43f5e; color: #fff; border: none; padding: 0.8rem 1.5rem; border-radius: 12px; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 0.5rem; transition: 0.3s;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            Catat Pengeluaran
        </button>
    </div>
</div>

@if(session('success'))
    <div style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.2); color: #10b981; padding: 1rem; border-radius: 12px; margin-bottom: 1.5rem;">
        {{ session('success') }}
    </div>
@endif

{{-- Tab Bar --}}
<div class="tab-bar">
    <a href="{{ route('admin.finances.index', ['tab' => 'buku-kas']) }}" class="tab-btn {{ $tab == 'buku-kas' ? 'active' : '' }}">Buku Kas</a>
    <a href="{{ route('admin.finances.index', ['tab' => 'pemasukan']) }}" class="tab-btn {{ $tab == 'pemasukan' ? 'active' : '' }}">Pemasukan</a>
    <a href="{{ route('admin.finances.index', ['tab' => 'pengeluaran']) }}" class="tab-btn {{ $tab == 'pengeluaran' ? 'active' : '' }}">Pengeluaran</a>
    <a href="{{ route('admin.finances.index', ['tab' => 'laba-rugi']) }}" class="tab-btn {{ $tab == 'laba-rugi' ? 'active' : '' }}">Laba Rugi</a>
    <a href="{{ route('admin.finances.index', ['tab' => 'grafik']) }}" class="tab-btn {{ $tab == 'grafik' ? 'active' : '' }}">Grafik Keuangan</a>
    <a href="{{ route('admin.finances.index', ['tab' => 'export']) }}" class="tab-btn {{ $tab == 'export' ? 'active' : '' }}">Export Laporan</a>
</div>

{{-- Tab: BUKU KAS / PEMASUKAN / PENGELUARAN --}}
@if(in_array($tab, ['buku-kas', 'pemasukan', 'pengeluaran']))
<div class="tab-content">
    <div style="margin-bottom: 1.5rem;">
        <form action="{{ route('admin.finances.index') }}" method="GET" style="display: flex; gap: 10px;">
            <input type="hidden" name="tab" value="{{ $tab }}">
            <select name="filter" onchange="this.form.submit()" class="form-input">
                <option value="all" style="color: #000;" {{ $filter == 'all' ? 'selected' : '' }}>Semua Waktu</option>
                <option value="daily" style="color: #000;" {{ $filter == 'daily' ? 'selected' : '' }}>Hari Ini</option>
                <option value="monthly" style="color: #000;" {{ $filter == 'monthly' ? 'selected' : '' }}>Bulan Ini</option>
                <option value="yearly" style="color: #000;" {{ $filter == 'yearly' ? 'selected' : '' }}>Tahun Ini</option>
            </select>
        </form>
    </div>

    @if($tab == 'buku-kas')
    <div class="grid-3" style="margin-bottom: 2rem;">
        <div class="glass-card" style="border-left: 4px solid var(--success);">
            <p style="opacity: 0.6; font-size: 0.8rem; margin-bottom: 0.5rem;">Total Pemasukan</p>
            <h3 style="font-size: 1.5rem; font-weight: 800; color: var(--success);">Rp {{ number_format($totalIncome, 0, ',', '.') }}</h3>
        </div>
        <div class="glass-card" style="border-left: 4px solid #f43f5e;">
            <p style="opacity: 0.6; font-size: 0.8rem; margin-bottom: 0.5rem;">Total Pengeluaran</p>
            <h3 style="font-size: 1.5rem; font-weight: 800; color: #f43f5e;">Rp {{ number_format($totalExpense, 0, ',', '.') }}</h3>
        </div>
        <div class="glass-card" style="border-left: 4px solid var(--primary);">
            <p style="opacity: 0.6; font-size: 0.8rem; margin-bottom: 0.5rem;">Saldo Bersih</p>
            <h3 style="font-size: 1.5rem; font-weight: 800;">Rp {{ number_format($netBalance, 0, ',', '.') }}</h3>
        </div>
    </div>
    @endif

    <div class="table-container">
        <table style="width: 100%; border-collapse: collapse; text-align: left; min-width: 600px;">
            <thead>
                <tr style="background: var(--surface-variant); border-bottom: 1px solid var(--border-color);">
                    <th style="padding: 1.5rem; font-size: 0.85rem; text-transform: uppercase; opacity: 0.6;">Tanggal</th>
                    <th style="padding: 1.5rem; font-size: 0.85rem; text-transform: uppercase; opacity: 0.6;">Kategori</th>
                    <th style="padding: 1.5rem; font-size: 0.85rem; text-transform: uppercase; opacity: 0.6;">Keterangan</th>
                    <th style="padding: 1.5rem; font-size: 0.85rem; text-transform: uppercase; opacity: 0.6;">Jenis</th>
                    <th style="padding: 1.5rem; font-size: 0.85rem; text-transform: uppercase; opacity: 0.6; text-align: right;">Nominal</th>
                    <th style="padding: 1.5rem; font-size: 0.85rem; text-transform: uppercase; opacity: 0.6; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($finances as $finance)
                <tr style="border-bottom: 1px solid var(--border-color); transition: 0.3s;" onmouseover="this.style.background='var(--surface-variant)'" onmouseout="this.style.background='transparent'">
                    <td style="padding: 1.5rem; font-weight: 600;">{{ \Carbon\Carbon::parse($finance->date)->format('d/m/Y') }}</td>
                    <td style="padding: 1.5rem; opacity: 0.9;">
                        @if(isset($finance->category) && $finance->category)
                            <span style="background: var(--surface-variant); padding: 0.3rem 0.6rem; border-radius: 6px; font-size: 0.75rem;">{{ $finance->category }}</span>
                        @else
                            <span style="opacity: 0.3">-</span>
                        @endif
                    </td>
                    <td style="padding: 1.5rem; opacity: 0.9;">{{ $finance->description }}</td>
                    <td style="padding: 1.5rem;">
                        @if($finance->type == 'income')
                            <span style="background: rgba(16, 185, 129, 0.1); color: #10b981; padding: 0.4rem 0.8rem; border-radius: 8px; font-size: 0.75rem; font-weight: 700;">PEMASUKAN</span>
                        @else
                            <span style="background: rgba(244, 63, 94, 0.1); color: #f43f5e; padding: 0.4rem 0.8rem; border-radius: 8px; font-size: 0.75rem; font-weight: 700;">PENGELUARAN</span>
                        @endif
                    </td>
                    <td style="padding: 1.5rem; text-align: right; font-weight: 700; color: {{ $finance->type == 'income' ? 'var(--success)' : '#f43f5e' }};">
                        {{ $finance->type == 'income' ? '+' : '-' }} Rp {{ number_format($finance->amount, 0, ',', '.') }}
                    </td>
                    <td style="padding: 1.5rem; text-align: center;">
                        @if($finance->is_manual)
                        <form action="{{ route('admin.finances.destroy', $finance->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus data ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" style="background: transparent; border: none; color: #f43f5e; cursor: pointer; opacity: 0.7; transition: 0.3s;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.7'">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"></path><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                            </button>
                        </form>
                        @else
                        <span style="opacity: 0.3; font-size: 0.8rem;">Otomatis</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @if($finances->isEmpty())
            <div style="padding: 4rem; text-align: center; opacity: 0.4;">
                <p>Belum ada data keuangan untuk ditampilkan.</p>
            </div>
        @endif
    </div>
</div>
@endif

{{-- Tab: LABA RUGI --}}
@if($tab == 'laba-rugi')
<div class="tab-content">
    <div class="filter-card">
        <form action="{{ route('admin.finances.index') }}" method="GET" style="display: flex; gap: 1rem; align-items: flex-end; flex-wrap: wrap;">
            <input type="hidden" name="tab" value="laba-rugi">
            <div style="flex: 1;">
                <label style="display: block; font-size: 0.75rem; margin-bottom: 0.5rem; opacity: 0.6;">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="form-input" style="width: 100%;">
            </div>
            <div style="flex: 1;">
                <label style="display: block; font-size: 0.75rem; margin-bottom: 0.5rem; opacity: 0.6;">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="form-input" style="width: 100%;">
            </div>
            <div>
                <button type="submit" style="background: var(--primary); color: #fff; border: none; padding: 0.85rem 1.5rem; border-radius: 12px; font-weight: 800; cursor: pointer;">Kalkulasi</button>
            </div>
        </form>
    </div>

    <div style="text-align: center; margin-bottom: 2rem;">
        <h3 style="font-weight: 800; font-size: 1.5rem;">Laporan Laba Rugi</h3>
        <p style="opacity: 0.6;">Periode: {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</p>
    </div>

    <div class="grid-2" style="margin-bottom: 1.5rem;">
        <div class="glass-card" style="text-align: center;">
            <p style="opacity: 0.6; font-size: 0.9rem; margin-bottom: 0.5rem;">Total Pendapatan (Pesanan + Manual)</p>
            <h3 style="font-size: 2rem; font-weight: 800; color: var(--success);">Rp {{ number_format($totalIncome, 0, ',', '.') }}</h3>
        </div>
        <div class="glass-card" style="text-align: center;">
            <p style="opacity: 0.6; font-size: 0.9rem; margin-bottom: 0.5rem;">Total Biaya (Pengeluaran)</p>
            <h3 style="font-size: 2rem; font-weight: 800; color: #f43f5e;">Rp {{ number_format($totalExpense, 0, ',', '.') }}</h3>
        </div>
    </div>
    
    <div class="glass-card" style="text-align: center;">
        <p style="opacity: 0.6; font-size: 1rem; margin-bottom: 0.5rem;">Laba/Rugi Bersih</p>
        <h3 style="font-size: 3rem; font-weight: 900; color: {{ $netBalance >= 0 ? 'var(--primary)' : '#f43f5e' }};">
            Rp {{ number_format($netBalance, 0, ',', '.') }}
        </h3>
        @if($netBalance > 0)
            <p style="color: var(--success); font-weight: 700; margin-top: 10px;">▲ Profit / Untung</p>
        @elseif($netBalance < 0)
            <p style="color: #f43f5e; font-weight: 700; margin-top: 10px;">▼ Rugi</p>
        @else
            <p style="opacity: 0.6; font-weight: 700; margin-top: 10px;">Break Even (Impas)</p>
        @endif
    </div>
</div>
@endif

{{-- Tab: GRAFIK --}}
@if($tab == 'grafik')
<div class="tab-content">
    <div class="glass-card">
        <h4 style="font-size: 1.2rem; font-weight: 800; margin-bottom: 1.5rem;">Arus Kas 7 Hari Terakhir</h4>
        <canvas id="financeChart" height="100"></canvas>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('financeChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($chartData['labels']) !!},
                datasets: [
                    {
                        label: 'Pemasukan (Rp)',
                        data: {!! json_encode($chartData['income']) !!},
                        backgroundColor: '#10b981',
                        borderRadius: 6
                    },
                    {
                        label: 'Pengeluaran (Rp)',
                        data: {!! json_encode($chartData['expense']) !!},
                        backgroundColor: '#f43f5e',
                        borderRadius: 6
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(255,255,255,0.1)' },
                        ticks: { color: '#aaa' }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: '#aaa' }
                    }
                },
                plugins: {
                    legend: { labels: { color: '#fff' } }
                }
            }
        });
    });
</script>
@endif

{{-- Tab: EXPORT LAPORAN --}}
@if($tab == 'export')
<div class="tab-content">
    <div class="glass-card" style="max-width: 600px; margin: 0 auto; text-align: center;">
        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2" style="margin-bottom: 1rem;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
        <h3 style="font-size: 1.5rem; font-weight: 800; margin-bottom: 0.5rem;">Cetak & Unduh Buku Kas</h3>
        <p style="opacity: 0.6; margin-bottom: 2rem;">Silakan pilih rentang tanggal laporan keuangan (arus kas masuk & keluar) yang ingin Anda unduh.</p>
        
        <form method="GET" style="display: flex; flex-direction: column; gap: 1rem; text-align: left;">
            <div style="display: flex; gap: 1rem;">
                <div style="flex: 1;">
                    <label style="display: block; font-size: 0.85rem; margin-bottom: 0.5rem; opacity: 0.7;">Dari Tanggal</label>
                    <input type="date" name="start_date" id="exp_start" value="{{ $startDate }}" class="form-input" style="width: 100%;">
                </div>
                <div style="flex: 1;">
                    <label style="display: block; font-size: 0.85rem; margin-bottom: 0.5rem; opacity: 0.7;">Sampai Tanggal</label>
                    <input type="date" name="end_date" id="exp_end" value="{{ $endDate }}" class="form-input" style="width: 100%;">
                </div>
            </div>
            
            <div style="display: flex; gap: 1rem; margin-top: 1rem;">
                <button type="button" onclick="exportData('excel')" style="flex: 1; background: rgba(16,185,129,0.1); border: 1px solid rgba(16,185,129,0.3); color: #10b981; padding: 1rem; border-radius: 12px; font-weight: 800; cursor: pointer; transition: 0.3s;" onmouseover="this.style.background='rgba(16,185,129,0.2)'" onmouseout="this.style.background='rgba(16,185,129,0.1)'">Unduh Excel</button>
            </div>
        </form>
    </div>
</div>
<script>
    function exportData(type) {
        const start = document.getElementById('exp_start').value;
        const end = document.getElementById('exp_end').value;
        let url = '';
        if(type === 'excel') url = '{{ route("admin.finances.export.cashbook") }}?start_date='+start+'&end_date='+end;
        window.location.href = url;
    }
</script>
@endif

<!-- Modal Tambah Transaksi -->
<div id="finance-modal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.8); z-index: 1000; align-items: center; justify-content: center; backdrop-filter: blur(5px);">
    <div class="glass-card" style="width: 90%; max-width: 500px; padding: 2.5rem; border-radius: 24px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h3 id="modal-title" style="font-size: 1.5rem;">Catat Transaksi</h3>
            <button onclick="closeModal()" style="background: transparent; border: none; color: var(--text); cursor: pointer; opacity: 0.5;"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></button>
        </div>
        
        <form method="POST" action="{{ route('admin.finances.store') }}">
            @csrf
            <input type="hidden" name="type" id="finance-type" value="income">
            
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-size: 0.85rem; margin-bottom: 0.5rem; opacity: 0.7;">Tanggal</label>
                <input type="date" name="date" required value="{{ date('Y-m-d') }}" class="form-input" style="width: 100%;" onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='var(--border-color)'">
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-size: 0.85rem; margin-bottom: 0.5rem; opacity: 0.7;">Kategori</label>
                <input type="text" name="category" placeholder="Contoh: Operasional, Gaji, Bahan Baku" class="form-input" style="width: 100%;" onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='var(--border-color)'">
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-size: 0.85rem; margin-bottom: 0.5rem; opacity: 0.7;">Nominal (Rp)</label>
                <input type="number" name="amount" min="1" required placeholder="Contoh: 50000" class="form-input" style="width: 100%;" onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='var(--border-color)'">
            </div>

            <div style="margin-bottom: 2rem;">
                <label style="display: block; font-size: 0.85rem; margin-bottom: 0.5rem; opacity: 0.7;">Keterangan</label>
                <input type="text" name="description" required placeholder="Contoh: Beli Sabun" class="form-input" style="width: 100%;" onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='var(--border-color)'">
            </div>

            <button type="submit" id="submit-button" style="width: 100%; background: var(--primary); color: #fff; border: none; padding: 1rem; border-radius: 12px; font-weight: 700; cursor: pointer; transition: 0.3s;">Simpan Transaksi</button>
        </form>
    </div>
</div>

<script>
    function openModal(type) {
        document.getElementById('finance-modal').style.display = 'flex';
        document.getElementById('finance-type').value = type;
        if(type === 'income') {
            document.getElementById('modal-title').innerText = 'Catat Pemasukan Manual';
            document.getElementById('submit-button').style.background = 'var(--success)';
            document.getElementById('submit-button').innerText = 'Simpan Pemasukan';
        } else {
            document.getElementById('modal-title').innerText = 'Catat Pengeluaran';
            document.getElementById('submit-button').style.background = '#f43f5e';
            document.getElementById('submit-button').innerText = 'Simpan Pengeluaran';
        }
    }
    function closeModal() {
        document.getElementById('finance-modal').style.display = 'none';
    }
</script>
@endsection
