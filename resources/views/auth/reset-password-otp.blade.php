<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Atur Ulang Kata Sandi | CleanUP Shoes</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #3b82f6;
            --bg: #111827;
            --card-bg: #1f2937;
            --text: #f3f4f6;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Outfit', sans-serif; }
        body { background-color: var(--bg); color: var(--text); height: 100vh; display: flex; align-items: center; justify-content: center; }
        .container { width: 100%; max-width: 400px; padding: 1rem; }
        .glass-card { background: var(--card-bg); border-radius: 20px; padding: 2.5rem 2rem; box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2); }
        .logo { font-size: 1.8rem; font-weight: 800; text-align: center; margin-bottom: 1.5rem; color: var(--primary); }
        h2 { font-size: 1.4rem; text-align: center; margin-bottom: 0.5rem; }
        p.subtitle { text-align: center; opacity: 0.6; font-size: 0.85rem; margin-bottom: 2rem; }
        .form-group { margin-bottom: 1.2rem; }
        label { display: block; font-size: 0.85rem; margin-bottom: 0.4rem; opacity: 0.7; }
        input {
            width: 100%;
            padding: 0.8rem 1rem;
            background: #374151;
            border: 1px solid #4b5563;
            border-radius: 8px;
            color: white;
            font-size: 1rem;
        }
        input:focus { outline: none; border-color: var(--primary); }
        /* Hide browser default password reveal button */
        input::-ms-reveal,
        input::-ms-clear {
            display: none;
        }
        .btn-submit { width: 100%; padding: 0.8rem; background: var(--primary); border: none; border-radius: 8px; color: white; font-weight: 700; cursor: pointer; margin-top: 1rem; }
        .error { background: rgba(239, 68, 68, 0.1); color: #ef4444; padding: 0.8rem; border-radius: 8px; font-size: 0.8rem; margin-bottom: 1rem; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <div class="glass-card">
            <div class="logo">CleanUP<span>Shoes</span></div>
            <h2>Buat Kata Sandi Baru</h2>
            <p class="subtitle">Email: <strong>{{ $email }}</strong></p>

            @if ($errors->any())
                <div class="error">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('password.otp.reset') }}">
                @csrf
                <input type="hidden" name="email" value="{{ $email }}">
                <input type="hidden" name="otp" value="{{ $otp }}">
                
                <div class="form-group">
                    <label>Kata Sandi Baru</label>
                    <input type="password" name="password" required autofocus placeholder="Minimal 8 karakter">
                </div>

                <div class="form-group">
                    <label>Konfirmasi Kata Sandi</label>
                    <input type="password" name="password_confirmation" required placeholder="Ulangi kata sandi">
                </div>

                <button type="submit" class="btn-submit">Simpan Kata Sandi</button>
            </form>
        </div>
    </div>
</body>
</html>
