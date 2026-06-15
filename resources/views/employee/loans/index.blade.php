@extends('layouts.premium-dashboard')

@section('page_title', 'Kasbon Saya')

@section('nav_items')
    <li class="nav-item"><a href="{{ route('employee.dashboard') }}" class="nav-link">Dashboard</a></li>
    <li class="nav-item"><a href="{{ route('employee.orders.index') }}" class="nav-link">Orderan Masuk</a></li>
    <li class="nav-item"><a href="{{ route('employee.loans.index') }}" class="nav-link active">Pinjaman</a></li>
@endsection

@section('content')
<style>
    .loan-card { display: none; }
    @media (max-width: 768px) {
        .loans-header { flex-direction: column; align-items: flex-start !important; gap: 1rem; }
        .loans-header button { width: 100%; }
        .table-desktop { display: none; }
        .loan-card { 
            display: block; 
            background: rgba(255,255,255,0.03); 
            border: 1px solid rgba(255,255,255,0.05); 
            border-radius: 16px; 
            padding: 1.2rem; 
            margin-bottom: 1rem;
        }
        .loan-card-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.8rem; }
        .loan-card-body { margin-bottom: 0.8rem; }
        .loan-card-footer { border-top: 1px solid rgba(255,255,255,0.05); padding-top: 0.8rem; }
    }
</style>

<div class="loans-header" style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center;">
    <div>
        <h2 style="font-size: 1.8rem; font-weight: 800; margin-bottom: 0.5rem;">Kasbon Saya</h2>
        <p style="opacity: 0.6;">Ajukan dan pantau status pinjaman Anda.</p>
    </div>
    @php $hasPending = $loans->where('status', 'pending')->isNotEmpty(); @endphp
    @if($hasPending)
        <button disabled style="background: rgba(255,255,255,0.05); color: rgba(255,255,255,0.3); border: 1px solid rgba(255,255,255,0.1); padding: 0.8rem 1.5rem; border-radius: 12px; font-weight: 700; cursor: not-allowed; transition: 0.3s;">
            Sudah Mengajukan
        </button>
    @else
        <button onclick="document.getElementById('add-loan-modal').style.display='flex'" style="background: var(--primary); color: #0f172a; border: none; padding: 0.8rem 1.5rem; border-radius: 12px; font-weight: 700; cursor: pointer; transition: 0.3s;">
            Ajukan Pinjaman
        </button>
    @endif
</div>

@if(session('success'))
    <div style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.2); color: #10b981; padding: 1rem; border-radius: 12px; margin-bottom: 2rem;">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div style="background: rgba(244, 63, 94, 0.1); border: 1px solid rgba(244, 63, 94, 0.2); color: #f43f5e; padding: 1rem; border-radius: 12px; margin-bottom: 2rem;">
        {{ session('error') }}
    </div>
@endif

<div class="glass-card" style="border-radius: 24px; overflow: hidden; padding: 0; background: transparent; border: none;">
    <!-- Desktop Table -->
    <div class="table-desktop table-container">
        <table style="width: 100%; border-collapse: collapse; text-align: left; min-width: 600px;">
            <thead>
                <tr style="background: rgba(255,255,255,0.03); border-bottom: 1px solid rgba(255,255,255,0.05);">
                    <th style="padding: 1.5rem; font-size: 0.85rem; text-transform: uppercase; opacity: 0.6;">Tanggal</th>
                    <th style="padding: 1.5rem; font-size: 0.85rem; text-transform: uppercase; opacity: 0.6;">Nominal</th>
                    <th style="padding: 1.5rem; font-size: 0.85rem; text-transform: uppercase; opacity: 0.6;">Status</th>
                    <th style="padding: 1.5rem; font-size: 0.85rem; text-transform: uppercase; opacity: 0.6;">Catatan Admin</th>
                </tr>
            </thead>
            <tbody>
                @foreach($loans as $loan)
                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02); transition: 0.3s;" onmouseover="this.style.background='rgba(255,255,255,0.01)'" onmouseout="this.style.background='transparent'">
                    <td style="padding: 1.5rem;">{{ $loan->created_at->format('d/m/Y') }}</td>
                    <td style="padding: 1.5rem; font-weight: 700; color: var(--primary);">Rp {{ number_format($loan->amount, 0, ',', '.') }}</td>
                    <td style="padding: 1.5rem;">
                        @if($loan->status == 'pending')
                            <span style="color: #f59e0b;">Menunggu Persetujuan</span>
                        @elseif($loan->status == 'approved')
                            <span style="color: #10b981;">Disetujui</span>
                        @else
                            <span style="color: #f43f5e;">Ditolak</span>
                        @endif
                    </td>
                    <td style="padding: 1.5rem; opacity: 0.6;">{{ $loan->admin_note ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Mobile Cards -->
    <div class="mobile-cards">
        @foreach($loans as $loan)
        <div class="loan-card">
            <div class="loan-card-header">
                <span style="font-weight: 800; font-size: 1.2rem; color: var(--primary);">Rp {{ number_format($loan->amount, 0, ',', '.') }}</span>
                <span style="font-size: 0.85rem; opacity: 0.6;">{{ $loan->created_at->format('d/m/Y') }}</span>
            </div>
            <div class="loan-card-body">
                @if($loan->status == 'pending')
                    <span style="color: #f59e0b; font-weight: 600; font-size: 0.9rem;">Menunggu Persetujuan</span>
                @elseif($loan->status == 'approved')
                    <span style="color: #10b981; font-weight: 600; font-size: 0.9rem;">Disetujui</span>
                @else
                    <span style="color: #f43f5e; font-weight: 600; font-size: 0.9rem;">Ditolak</span>
                @endif
            </div>
            <div class="loan-card-footer">
                <p style="font-size: 0.8rem; opacity: 0.7; margin-bottom: 0.2rem;">Catatan Admin:</p>
                <p style="font-size: 0.85rem; font-style: italic;">{{ $loan->admin_note ?? '-' }}</p>
            </div>
        </div>
        @endforeach
    </div>

    @if($loans->isEmpty())
        <div style="padding: 3rem; text-align: center; opacity: 0.3;">
            <p>Belum ada riwayat pinjaman.</p>
        </div>
    @endif
</div>

<!-- Modal Ajukan Pinjaman -->
<div id="add-loan-modal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.8); z-index: 1000; align-items: center; justify-content: center; backdrop-filter: blur(5px);">
    <div class="glass-card" style="width: 90%; max-width: 450px; padding: 2.5rem; border-radius: 24px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h3 style="font-size: 1.5rem; font-weight: 800;">Ajukan Kasbon</h3>
            <button onclick="document.getElementById('add-loan-modal').style.display='none'" style="background: transparent; border: none; color: #fff; cursor: pointer; opacity: 0.5;"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></button>
        </div>
        
        <form method="POST" action="{{ route('employee.loans.store') }}" onsubmit="disableSubmitButton(this)">
            @csrf
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-size: 0.85rem; margin-bottom: 0.5rem; opacity: 0.7;">Nominal Pinjaman (Rp)</label>
                <input type="number" name="amount" min="1000" step="1000" required placeholder="Contoh: 50000" style="width: 100%; padding: 0.8rem 1.2rem; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; color: #fff; outline: none;">
            </div>

            <div style="margin-bottom: 2rem;">
                <label style="display: block; font-size: 0.85rem; margin-bottom: 0.5rem; opacity: 0.7;">Alasan Pinjaman</label>
                <textarea name="reason" required placeholder="Jelaskan alasan peminjaman..." style="width: 100%; padding: 0.8rem 1.2rem; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; color: #fff; outline: none; height: 100px; resize: none;"></textarea>
            </div>

            <button type="submit" id="btn-submit-loan" style="width: 100%; background: var(--primary); color: #0f172a; border: none; padding: 1rem; border-radius: 12px; font-weight: 700; cursor: pointer; transition: 0.3s;">Kirim Permintaan</button>
        </form>
    </div>
</div>

<script>
    function disableSubmitButton(form) {
        const btn = document.getElementById('btn-submit-loan');
        btn.disabled = true;
        btn.style.opacity = '0.5';
        btn.style.cursor = 'not-allowed';
        btn.innerText = 'Mengirim Permintaan...';
    }
</script>
@endsection
