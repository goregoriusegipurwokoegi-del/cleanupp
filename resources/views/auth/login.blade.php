<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk | CleanUP Shoes</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #3b82f6;
            --bg: #0b1120;
            --card-bg: #1e293b;
            --input-bg: #eef2ff;
            --text-main: #f1f5f9;
            --text-dim: #94a3b8;
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
        }

        .login-container { width: 100%; max-width: 440px; padding: 2rem; }

        .login-card {
            background: #111827;
            padding: 3rem 2.5rem;
            border-radius: 12px;
            text-align: center;
        }

        .header-text { margin-bottom: 2.5rem; }
        .header-text h2 { font-size: 1.5rem; font-weight: 800; color: #fff; margin-bottom: 0.5rem; }
        .header-text h2 span { color: var(--primary); }
        .header-text p { color: var(--text-dim); font-size: 0.9rem; }

        .form-group { text-align: left; margin-bottom: 1.25rem; }
        label { display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.5rem; color: #fff; }

        input {
            width: 100%;
            padding: 0.85rem 1rem;
            background: var(--input-bg);
            border: none;
            border-radius: 8px;
            color: #1e293b;
            font-size: 0.95rem;
            font-weight: 500;
            font-family: inherit;
        }

        input:focus { outline: 2px solid var(--primary); }

        .forgot-link {
            display: block;
            text-align: right;
            font-size: 0.8rem;
            color: var(--primary);
            text-decoration: none;
            margin-top: 0.5rem;
            font-weight: 600;
        }

        .btn-login {
            width: 100%;
            padding: 0.9rem;
            background: var(--primary);
            border: none;
            border-radius: 8px;
            color: white;
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            margin-top: 1.5rem;
            transition: 0.2s;
        }
        .btn-login:hover { opacity: 0.9; }

        .divider {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 2rem 0;
            color: var(--text-dim);
            font-size: 0.8rem;
        }
        .divider::before, .divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #374151;
        }
        .divider:not(:empty)::before { margin-right: 1rem; }
        .divider:not(:empty)::after { margin-left: 1rem; }

        .btn-google {
            width: 100%;
            padding: 0.8rem;
            background: transparent;
            border: 1px solid #374151;
            border-radius: 8px;
            color: white;
            font-weight: 600;
            font-size: 0.9rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            text-decoration: none;
            transition: 0.2s;
        }
        .btn-google:hover { background: rgba(255, 255, 255, 0.05); }
        .btn-google img { width: 18px; height: 18px; }

        .footer-text {
            text-align: center;
            margin-top: 2rem;
            font-size: 0.85rem;
            color: var(--text-dim);
        }
        .footer-text a { color: var(--primary); text-decoration: none; font-weight: 700; }

        .alert {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
            padding: 0.75rem;
            border-radius: 8px;
            font-size: 0.8rem;
            margin-bottom: 1rem;
            text-align: left;
        }
    </style>
</head>
<body>

    <div class="login-container">
        <div class="login-card">
            <div class="header-text">
                <h2>Selamat Datang di <span>CleanUPShoes</span></h2>
                <p>Masuk untuk mengelola sepatu</p>
            </div>

            @if ($errors->any())
                <div class="alert">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" autocomplete="off">
                @csrf
                <div class="form-group">
                    <label>Alamat Email</label>
                    <input type="email" name="email" value="" required autofocus placeholder="customer@shoe.com" autocomplete="off">
                </div>

                <div class="form-group">
                    <label>Kata Sandi</label>
                    <input type="password" name="password" required placeholder="••••••••" autocomplete="new-password">
                    <a href="{{ route('password.request') }}" class="forgot-link">Lupa Password?</a>
                </div>

                <button type="submit" class="btn-login">Masuk</button>
            </form>

            <div class="divider">atau</div>

            <a href="{{ url('auth/google') }}" class="btn-google">
                <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" alt="Google">
                Lanjutkan dengan Google
            </a>

            <div class="footer-text">
                Belum punya akun? <a href="{{ route('register') }}">Daftar</a>
            </div>
        </div>
    </div>

</body>
</html>
