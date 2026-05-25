<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lupa Password | CleanUP Shoes</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        :root {
            --primary: #0ea5e9;
            --secondary: #2563eb;
            --bg: #0b1120;
            --surface: rgba(255, 255, 255, 0.03);
            --text-main: #f1f5f9;
            --text-dim: #94a3b8;
            --gradient: linear-gradient(135deg, #0ea5e9 0%, #2563eb 100%);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            background-color: var(--bg);
            color: var(--text-main);
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-image: 
                radial-gradient(circle at 0% 0%, rgba(14, 165, 233, 0.15) 0%, transparent 35%),
                radial-gradient(circle at 100% 100%, rgba(37, 99, 235, 0.1) 0%, transparent 35%);
        }

        .container { width: 100%; max-width: 440px; padding: 1.5rem; }

        .glass-card {
            background: var(--surface);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 32px;
            padding: 3rem 2.5rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            position: relative;
            overflow: hidden;
        }

        .logo {
            font-size: 1.8rem;
            font-weight: 800;
            text-align: center;
            margin-bottom: 1.5rem;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            text-decoration: none;
        }
        .logo span { color: var(--primary); }

        .header-text { text-align: center; margin-bottom: 2.5rem; }
        h2 { font-size: 1.5rem; font-weight: 800; margin-bottom: 0.5rem; color: #fff; }
        p.subtitle { color: var(--text-dim); font-size: 0.9rem; line-height: 1.6; }

        .form-group { margin-bottom: 1.5rem; }
        label { display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.6rem; color: var(--text-main); opacity: 0.8; }

        .input-wrapper { position: relative; }
        .input-wrapper i { position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: var(--text-dim); width: 18px; }

        input {
            width: 100%;
            padding: 1rem 1rem 1rem 3rem;
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            color: white;
            font-size: 1rem;
            font-family: inherit;
            transition: 0.3s;
        }

        input:focus {
            outline: none;
            border-color: var(--primary);
            background: rgba(15, 23, 42, 0.8);
            box-shadow: 0 0 0 4px rgba(14, 165, 233, 0.1);
        }

        .btn-submit {
            width: 100%;
            padding: 1rem;
            background: var(--gradient);
            border: none;
            border-radius: 16px;
            color: white;
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            transition: 0.4s;
            box-shadow: 0 10px 20px rgba(14, 165, 233, 0.2);
            margin-top: 1rem;
            font-family: inherit;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 30px rgba(14, 165, 233, 0.4);
        }

        .alert {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.2);
            color: #ef4444;
            padding: 1rem;
            border-radius: 16px;
            font-size: 0.85rem;
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .footer-links {
            text-align: center;
            margin-top: 2rem;
            font-size: 0.9rem;
            color: var(--text-dim);
        }
        .footer-links a { color: var(--primary); text-decoration: none; font-weight: 700; transition: 0.3s; }
        .footer-links a:hover { color: #fff; }
    </style>
</head>
<body>
    <div class="container">
        <div class="glass-card">
            <a href="/" class="logo"><i data-lucide="zap"></i>CleanUP<span>Shoes</span></a>
            
            <div class="header-text">
                <h2>Lupa Password?</h2>
                <p class="subtitle">Masukkan email Anda untuk menerima kode OTP verifikasi.</p>
            </div>

            @if ($errors->any())
                <div class="alert">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('password.otp.send') }}">
                @csrf
                <div class="form-group">
                    <label>Alamat Email</label>
                    <div class="input-wrapper">
                        <i data-lucide="mail"></i>
                        <input type="email" name="email" value="{{ old('email') }}" required placeholder="nama@email.com" autofocus>
                    </div>
                </div>

                <button type="submit" class="btn-submit">Kirim Kode OTP</button>
            </form>

            <div class="footer-links">
                Kembali ke <a href="{{ route('login') }}">Masuk</a>
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>

