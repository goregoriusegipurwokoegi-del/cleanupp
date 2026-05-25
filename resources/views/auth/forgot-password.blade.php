<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lupa Kata Sandi | CleanUP Shoes</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #00d2ff;
            --secondary: #3a7bd5;
            --bg: #0f172a;
            --card-bg: rgba(255, 255, 255, 0.05);
            --text: #f8fafc;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Outfit', sans-serif; }

        body {
            background-color: var(--bg);
            color: var(--text);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: radial-gradient(circle at top right, #1e293b, #0f172a);
        }

        .container { width: 100%; max-width: 450px; padding: 2rem; }

        .glass-card {
            background: var(--card-bg);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 32px;
            padding: 3rem 2.5rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        .logo {
            font-size: 1.8rem;
            font-weight: 800;
            text-align: center;
            margin-bottom: 2rem;
            background: linear-gradient(to right, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        h2 { font-size: 1.5rem; text-align: center; margin-bottom: 1rem; }
        p.subtitle { text-align: center; opacity: 0.6; font-size: 0.9rem; margin-bottom: 2rem; line-height: 1.5; }

        input {
            width: 100%;
            padding: 1rem 1.2rem;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            color: white;
            margin-bottom: 1.5rem;
        }

        .btn-submit {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(to right, var(--primary), var(--secondary));
            border: none;
            border-radius: 12px;
            color: white;
            font-weight: 700;
            cursor: pointer;
            transition: 0.4s;
        }

        .btn-submit:hover { transform: translateY(-3px); }
        .back-link { display: block; text-align: center; margin-top: 1.5rem; color: var(--primary); text-decoration: none; font-size: 0.9rem; }
    </style>
</head>
<body>
    <div class="container">
        <div class="glass-card">
            <div class="logo">CleanUP<span>Shoes</span></div>
            <h2>Lupa Kata Sandi?</h2>
            <p class="subtitle">Jangan khawatir. Masukkan email Anda dan kami akan mengirimkan tautan untuk mengatur ulang kata sandi Anda.</p>

            @if (session('status'))
                <div style="background: rgba(16, 185, 129, 0.1); color: #10b981; padding: 1rem; border-radius: 12px; margin-bottom: 1.5rem; font-size: 0.85rem; border: 1px solid rgba(16, 185, 129, 0.2);">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf
                <input type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="Alamat Email Anda">
                <button type="submit" class="btn-submit">Kirim Tautan Atur Ulang</button>
            </form>

            <a href="{{ route('login') }}" class="back-link">← Kembali ke Halaman Masuk</a>
        </div>
    </div>
</body>
</html>
