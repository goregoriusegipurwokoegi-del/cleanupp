<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verifikasi OTP | CleanUP Shoes</title>
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
            padding: 1rem;
            background: #374151;
            border: 1px solid #4b5563;
            border-radius: 8px;
            color: white;
            font-size: 1.5rem;
            text-align: center;
            letter-spacing: 10px;
        }
        input:focus { outline: none; border-color: var(--primary); }
        .btn-submit { width: 100%; padding: 0.8rem; background: var(--primary); border: none; border-radius: 8px; color: white; font-weight: 700; cursor: pointer; margin-top: 1rem; }
        .alert { background: rgba(59, 130, 246, 0.1); color: #3b82f6; padding: 0.8rem; border-radius: 8px; font-size: 0.8rem; margin-bottom: 1rem; text-align: center; }
        .error { background: rgba(239, 68, 68, 0.1); color: #ef4444; padding: 0.8rem; border-radius: 8px; font-size: 0.8rem; margin-bottom: 1rem; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <div class="glass-card">
            <div class="logo">CleanUP<span>Shoes</span></div>
            <h2>Verifikasi Kode OTP</h2>
            <p class="subtitle">Masukkan 6 digit kode yang dikirim ke email <strong>{{ $email }}</strong></p>

            @if (session('status'))
                <div class="alert">{{ session('status') }}</div>
            @endif

            @if ($errors->any())
                <div class="error">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('password.otp.verify.post') }}">
                @csrf
                <input type="hidden" name="email" value="{{ $email }}">
                <div class="form-group">
                    <label>Kode OTP</label>
                    <input type="text" name="otp" maxlength="6" required autofocus oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                </div>

                <button type="submit" class="btn-submit">Verifikasi Kode</button>
            </form>

            <p style="text-align: center; margin-top: 1.5rem; font-size: 0.85rem; opacity: 0.6;">
                Tidak menerima kode? <a href="{{ route('password.request') }}" style="color: var(--primary); text-decoration: none;">Kirim ulang</a>
            </p>
        </div>
    </div>
</body>
</html>
