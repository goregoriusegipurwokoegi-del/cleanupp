<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar | CleanUP Shoes</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        :root {
            --primary: #3b82f6;
            --bg: #0b1120;
            --card-bg: #111827;
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
            padding: 2rem 0;
        }

        .container { width: 100%; max-width: 460px; padding: 1.5rem; }

        .register-card {
            background: var(--card-bg);
            border-radius: 16px;
            padding: 3rem 2.5rem;
            text-align: center;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        .header-section { margin-bottom: 2.5rem; }
        h2 { font-size: 1.6rem; font-weight: 800; color: #fff; margin-bottom: 0.5rem; }
        p.subtitle { color: var(--text-dim); font-size: 0.9rem; }

        .form-group { text-align: left; margin-bottom: 1.5rem; }
        label { display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.5rem; color: #fff; }

        input {
            width: 100%;
            padding: 0.9rem 1.2rem;
            background: var(--input-bg);
            border: none;
            border-radius: 10px;
            color: #1e293b;
            font-size: 1rem;
            font-weight: 500;
            font-family: inherit;
            transition: 0.2s;
        }

        input:focus { outline: 3px solid rgba(59, 130, 246, 0.3); }

        .btn-submit {
            width: 100%;
            padding: 1rem;
            background: var(--primary);
            border: none;
            border-radius: 10px;
            color: white;
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            transition: 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            font-family: inherit;
        }

        .btn-submit:hover { transform: translateY(-2px); opacity: 0.9; }

        .btn-back {
            background: transparent;
            color: var(--text-dim);
            border: 1px solid #374151;
            padding: 0.9rem;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            font-family: inherit;
        }
        .btn-back:hover { background: rgba(255, 255, 255, 0.05); color: #fff; }

        .alert-error {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
            padding: 1rem;
            border-radius: 10px;
            font-size: 0.85rem;
            margin-bottom: 1.5rem;
            text-align: left;
        }

        .register-footer { text-align: center; margin-top: 2rem; font-size: 0.9rem; color: var(--text-dim); }
        .register-footer a { color: var(--primary); text-decoration: none; font-weight: 700; }

        @keyframes slideIn { from { opacity: 0; transform: translateX(10px); } to { opacity: 1; transform: translateX(0); } }
        .step-content { animation: slideIn 0.4s ease-out; }

        .divider {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 1.5rem 0;
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
            padding: 0.9rem;
            background: transparent;
            border: 1px solid #374151;
            border-radius: 10px;
            color: white;
            font-weight: 600;
            font-size: 0.95rem;
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
    </style>
</head>
<body>
    <div class="container">
        <div class="register-card">
            <div class="header-section">
                <h2>Daftar Akun</h2>
                <p class="subtitle">Bergabung untuk perawatan sepatu terbaik</p>
            </div>

            @if ($errors->any())
                <div class="alert-error">
                    @foreach ($errors->all() as $error)
                        <p>• {{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form id="registerForm" method="POST" action="{{ route('register') }}" autocomplete="off">
                @csrf
                <!-- Step 1: Info Personal -->
                <div id="step1" class="step-content">
                    <div class="form-group">
                        <label>Nama Lengkap</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus placeholder="Masukkan nama Anda" oninput="this.value = this.value.replace(/[0-9]/g, '');">
                    </div>
                    <div class="form-group">
                        <label>No. HP (WhatsApp)</label>
                        <!-- Dummy field to catch browser autofill -->
                        <input type="text" style="display:none;" name="email_fake_autofill">
                        <input type="text" id="wa_phone_field" name="field_wa_customer" value="{{ request('phone', old('field_wa_customer')) }}" required placeholder="Contoh: 08123456789" autocomplete="chrome-off" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                    </div>
                    <button type="button" class="btn-submit" onclick="nextStep()">
                        Lanjut <i data-lucide="arrow-right" size="20"></i>
                    </button>
                </div>

                <!-- Step 2: Info Akun -->
                <div id="step2" class="step-content" style="display: none;">
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" required placeholder="email@contoh.com" autocomplete="new-password">
                    </div>
                    
                    <div class="form-group">
                        <label>Kata Sandi</label>
                        <div style="position: relative;">
                            <input type="password" id="password" name="password" required placeholder="Minimal 8 karakter" autocomplete="new-password" style="padding-right: 3rem;">
                            <button type="button" onclick="togglePassword('password', this)" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: transparent; border: none; color: var(--text-dim); cursor: pointer; display: flex; align-items: center; justify-content: center;">
                                <i data-lucide="eye" size="18"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Konfirmasi Kata Sandi</label>
                        <div style="position: relative;">
                            <input type="password" id="password_confirmation" name="password_confirmation" required placeholder="Ulangi kata sandi" autocomplete="new-password" style="padding-right: 3rem;">
                            <button type="button" onclick="togglePassword('password_confirmation', this)" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: transparent; border: none; color: var(--text-dim); cursor: pointer; display: flex; align-items: center; justify-content: center;">
                                <i data-lucide="eye" size="18"></i>
                            </button>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1rem; margin-top: 1.5rem;">
                        <button type="button" class="btn-back" onclick="prevStep()">Kembali</button>
                        <button type="submit" class="btn-submit">Selesaikan</button>
                    </div>
                </div>
            </form>

            <div class="divider">atau</div>

            <a href="{{ url('auth/google') }}" class="btn-google">
                <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" alt="Google">
                Lanjutkan dengan Google
            </a>

            <div class="register-footer">
                Sudah punya akun? <a href="{{ route('login') }}">Masuk</a>
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();

        function nextStep() {
            const name = document.getElementById('name').value;
            const phone = document.getElementById('wa_phone_field').value;

            if (name && phone) {
                document.getElementById('step1').style.display = 'none';
                document.getElementById('step2').style.display = 'block';
            } else {
                alert('Harap isi Nama dan No. HP terlebih dahulu!');
            }
        }

        function prevStep() {
            document.getElementById('step1').style.display = 'block';
            document.getElementById('step2').style.display = 'none';
        }
        window.addEventListener('load', () => {
            const clearField = () => {
                const el = document.getElementById('wa_phone_field');
                // HANYA hapus jika tidak ada parameter 'phone' di URL
                @if(!request()->has('phone'))
                    if (el) el.value = '';
                @endif
            };
            // Clear multiple times to beat persistent autofill
            clearField();
            setTimeout(clearField, 100);
            
            // Auto-skip to step 2 if there are validation errors for email or password
            @if($errors->has('email') || $errors->has('password'))
                document.getElementById('step1').style.display = 'none';
                document.getElementById('step2').style.display = 'block';
            @endif
        });

        function togglePassword(fieldId, btn) {
            const field = document.getElementById(fieldId);
            const icon = btn.querySelector('i');
            
            if (field.type === 'password') {
                field.type = 'text';
                icon.setAttribute('data-lucide', 'eye-off');
            } else {
                field.type = 'password';
                icon.setAttribute('data-lucide', 'eye');
            }
            lucide.createIcons();
        }
    </script>
</body>
</html>
