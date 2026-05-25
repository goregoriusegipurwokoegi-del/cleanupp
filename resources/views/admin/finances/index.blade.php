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
<div class="header-actions" style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h2 style="font-size: 1.8rem; font-weight: 800; margin-bottom: 0.5rem;">Buku Kas</h2>
        <p style="opacity: 0.6;">Pantau otomatis pemasukan dari pesanan dan catat manual pengeluaran.</p>
    </div>
    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
        <form action="{{ route('admin.finances.index') }}" method="GET" style="display: flex; align-items: center; gap: 0.5rem;">
            <select name="filter" onchange="this.form.submit()" style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: #fff; padding: 0.8rem 1rem; border-radius: 12px; font-weight: 600; outline: none; cursor: pointer;">
                <option value="all" style="background: #0f172a; color: #fff;" {{ $filter == 'all' ? 'selected' : '' }}>Semua Waktu</option>
                <option value="daily" style="background: #0f172a; color: #fff;" {{ $filter == 'daily' ? 'selected' : '' }}>Hari Ini</option>
                <option value="monthly" style="background: #0f172a; color: #fff;" {{ $filter == 'monthly' ? 'selected' : '' }}>Bulan Ini</option>
                <option value="yearly" style="background: #0f172a; color: #fff;" {{ $filter == 'yearly' ? 'selected' : '' }}>Tahun Ini</option>
            </select>
        </form>
        <button onclick="openModal('income')" style="background: var(--success); color: #fff; border: none; padding: 0.8rem 1.5rem; border-radius: 12px; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 0.5rem; transition: 0.3s;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            Catat Pemasukan
        </button>
        <button onclick="openModal('expense')" style="background: #f43f5e; color: #fff; border: none; padding: 0.8rem 1.5rem; border-radius: 12px; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 0.5rem; transition: 0.3s;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            Catat Pengeluaran
        </button>
    </div>
</div>

@if(session('success'))
    <div style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.2); color: #10b981; padding: 1rem; border-radius: 12px; margin-bottom: 2rem;">
        {{ session('success') }}
    </div>
@endif

<!-- Summary Cards -->
<div class="grid-3" style="margin-bottom: 2.5rem;">
    <div class="glass-card" style="border-left: 4px solid var(--success);">
        <p style="opacity: 0.6; font-size: 0.8rem; margin-bottom: 0.5rem; text-transform: uppercase;">Total Pemasukan</p>
        <h3 style="font-size: 1.5rem; font-weight: 800; color: var(--success);">Rp {{ number_format($totalIncome, 0, ',', '.') }}</h3>
    </div>
    <div class="glass-card" style="border-left: 4px solid #f43f5e;">
        <p style="opacity: 0.6; font-size: 0.8rem; margin-bottom: 0.5rem; text-transform: uppercase;">Total Pengeluaran</p>
        <h3 style="font-size: 1.5rem; font-weight: 800; color: #f43f5e;">Rp {{ number_format($totalExpense, 0, ',', '.') }}</h3>
    </div>
    <div class="glass-card" style="border-left: 4px solid var(--primary);">
        <p style="opacity: 0.6; font-size: 0.8rem; margin-bottom: 0.5rem; text-transform: uppercase;">Saldo Bersih</p>
        <h3 style="font-size: 1.5rem; font-weight: 800;">Rp {{ number_format($netBalance, 0, ',', '.') }}</h3>
    </div>
</div>

<!-- Transactions Table -->
<div class="table-container">
    <table style="width: 100%; border-collapse: collapse; text-align: left; min-width: 600px;">
        <thead>
            <tr style="background: rgba(255,255,255,0.03); border-bottom: 1px solid rgba(255,255,255,0.05);">
                <th style="padding: 1.5rem; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px; opacity: 0.6;">Tanggal</th>
                <th style="padding: 1.5rem; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px; opacity: 0.6;">Keterangan</th>
                <th style="padding: 1.5rem; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px; opacity: 0.6;">Jenis</th>
                <th style="padding: 1.5rem; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px; opacity: 0.6; text-align: right;">Nominal</th>
                <th style="padding: 1.5rem; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px; opacity: 0.6; text-align: center;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($finances as $finance)
            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02); transition: 0.3s;" onmouseover="this.style.background='rgba(255,255,255,0.01)'" onmouseout="this.style.background='transparent'">
                <td style="padding: 1.5rem; font-weight: 600;">{{ \Carbon\Carbon::parse($finance->date)->format('d/m/Y') }}</td>
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
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"></path><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
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
        <div style="padding: 5rem; text-align: center; opacity: 0.3;">
            <p>Belum ada data pencatatan.</p>
        </div>
    @endif
</div>

<!-- Modal Tambah Transaksi -->
<div id="finance-modal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.8); z-index: 1000; align-items: center; justify-content: center; backdrop-filter: blur(5px);">
    <div class="glass-card" style="width: 90%; max-width: 500px; padding: 2.5rem; border-radius: 24px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h3 id="modal-title" style="font-size: 1.5rem;">Catat Transaksi</h3>
            <button onclick="closeModal()" style="background: transparent; border: none; color: #fff; cursor: pointer; opacity: 0.5;"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></button>
        </div>
        
        <form method="POST" action="{{ route('admin.finances.store') }}">
            @csrf
            <input type="hidden" name="type" id="finance-type" value="income">
            
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-size: 0.85rem; margin-bottom: 0.5rem; opacity: 0.7;">Nominal (Rp)</label>
                <input type="number" name="amount" min="1" required placeholder="Contoh: 50000" style="width: 100%; padding: 0.8rem 1.2rem; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; color: #fff; outline: none; transition: 0.3s;" onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='rgba(255,255,255,0.1)'">
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-size: 0.85rem; margin-bottom: 0.5rem; opacity: 0.7;">Keterangan</label>
                <input type="text" name="description" required placeholder="Contoh: Pemasukan Lain-lain, Beli Sabun, dll" style="width: 100%; padding: 0.8rem 1.2rem; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; color: #fff; outline: none; transition: 0.3s;" onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='rgba(255,255,255,0.1)'">
            </div>

            <div style="margin-bottom: 2rem;">
                <label style="display: block; font-size: 0.85rem; margin-bottom: 0.5rem; opacity: 0.7;">Tanggal</label>
                <input type="date" name="date" required value="{{ date('Y-m-d') }}" style="width: 100%; padding: 0.8rem 1.2rem; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; color: #fff; outline: none; transition: 0.3s;" onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='rgba(255,255,255,0.1)'">
            </div>

            <button type="submit" id="submit-button" style="width: 100%; background: var(--primary); color: #0f172a; border: none; padding: 1rem; border-radius: 12px; font-weight: 700; cursor: pointer; transition: 0.3s;">Simpan Transaksi</button>
        </form>
    </div>
</div>

<script>
    function openModal(type) {
        const modal = document.getElementById('finance-modal');
        const title = document.getElementById('modal-title');
        const typeInput = document.getElementById('finance-type');
        const submitBtn = document.getElementById('submit-button');
        
        typeInput.value = type;
        if(type === 'income') {
            title.innerText = 'Catat Pemasukan';
            submitBtn.style.background = 'var(--success)';
            submitBtn.style.color = '#fff';
            submitBtn.innerText = 'Simpan Pemasukan';
        } else {
            title.innerText = 'Catat Pengeluaran';
            submitBtn.style.background = '#f43f5e';
            submitBtn.style.color = '#fff';
            submitBtn.innerText = 'Simpan Pengeluaran';
        }
        
        modal.style.display = 'flex';
    }
    
    function closeModal() {
        document.getElementById('finance-modal').style.display = 'none';
    }
</script>
@endsection
