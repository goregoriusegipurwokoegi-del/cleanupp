<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CleanUP Shoes | Berikan Sepatu Anda Kehidupan Baru</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        :root {
            --primary: #4f46e5;       /* Premium Indigo */
            --primary-glow: rgba(79, 70, 229, 0.35);
            --bg: #05070c;            /* Deep black-blue background */
            --card-bg: rgba(15, 23, 42, 0.45);
            --text-main: #ffffff;     /* Solid white */
            --text-dim: #94a3b8;      /* Slate gray */
            --text-muted: #475569;    /* Muted gray for tags */
            --gradient-blue: linear-gradient(135deg, #6366f1 0%, #3b82f6 100%);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            background-color: var(--bg);
            color: var(--text-main);
            font-family: 'Plus Jakarta Sans', sans-serif;
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
            position: relative;
        }

        /* Ambient Glow Background Effects */
        body::before {
            content: '';
            position: absolute;
            top: -20%;
            left: -10%;
            width: 60%;
            height: 60%;
            background: radial-gradient(circle, rgba(79, 70, 229, 0.08) 0%, transparent 70%);
            z-index: -1;
            pointer-events: none;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1.5rem;
            width: 100%;
        }

        /* Sticky Navbar */
        nav {
            position: sticky;
            top: 0;
            z-index: 100;
            background: rgba(5, 7, 12, 0.75);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.04);
            padding: 1.1rem 0;
            transition: all 0.3s ease;
        }

        nav.scrolled {
            padding: 0.8rem 0;
            background: rgba(5, 7, 12, 0.9);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        nav .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 1.4rem;
            font-weight: 900;
            color: #ffffff;
            text-decoration: none;
            letter-spacing: -0.02em;
        }

        .logo span {
            color: var(--primary);
            font-weight: 900;
        }

        .nav-auth {
            display: flex;
            gap: 1.8rem;
            align-items: center;
        }

        .nav-auth a.btn-login {
            color: #cbd5e1;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.2s ease;
        }

        .nav-auth a.btn-login:hover {
            color: #ffffff;
        }

        .btn-register {
            background: var(--gradient-blue);
            padding: 0.65rem 1.6rem;
            border-radius: 100px;
            color: #ffffff !important;
            font-weight: 700;
            font-size: 0.95rem;
            text-decoration: none;
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.25);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(37, 99, 235, 0.45);
        }

        /* Hero Section (Centered layout) */
        .hero {
            padding: 6rem 0 4rem;
            position: relative;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            flex: 1;
        }

        .hero-text {
            max-width: 800px;
            margin: 0 auto;
        }

        .hero-text h1 {
            font-size: 4rem;
            font-weight: 900;
            line-height: 1.2;
            margin-bottom: 1.8rem;
            color: #ffffff;
            letter-spacing: -0.03em;
        }

        .hero-text h1 span {
            color: var(--primary);
            background: linear-gradient(135deg, #6366f1 0%, #3b82f6 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-text p {
            color: var(--text-dim);
            font-size: 1.1rem;
            margin: 0 auto 3rem;
            max-width: 600px;
            line-height: 1.8;
            font-weight: 500;
        }

        .hero-btns {
            display: flex;
            gap: 1.2rem;
            align-items: center;
            justify-content: center;
        }

        .btn {
            padding: 0.95rem 2.5rem;
            border-radius: 100px;
            font-weight: 700;
            text-decoration: none;
            font-size: 0.95rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
        }

        .btn-book {
            background: var(--gradient-blue);
            color: #ffffff;
            box-shadow: 0 8px 24px var(--primary-glow);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .btn-book:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 32px rgba(37, 99, 235, 0.55);
        }

        .btn-services {
            background: rgba(79, 70, 229, 0.03);
            border: 1.5px solid rgba(79, 70, 229, 0.35);
            color: #cbd5e1;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        .btn-services:hover {
            transform: translateY(-3px);
            border-color: #6366f1;
            background: rgba(79, 70, 229, 0.1);
            color: #ffffff;
            box-shadow: 0 6px 20px rgba(79, 70, 229, 0.2);
        }

        /* Services Section */
        .services {
            padding: 4rem 0 6rem;
            position: relative;
        }

        .section-header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .section-tag {
            text-transform: uppercase;
            letter-spacing: 0.15em;
            font-size: 0.75rem;
            color: var(--text-muted);
            font-weight: 700;
            margin-bottom: 0.5rem;
            display: block;
        }

        .services-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2.5rem;
            max-width: 900px;
            margin: 0 auto;
        }

        .service-card {
            background: var(--card-bg);
            padding: 3rem 2.5rem;
            border-radius: 28px;
            text-align: center;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            cursor: default;
        }

        /* Service cards subtle styling */
        .service-card.cuci,
        .service-card.reparasi {
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.2);
        }

        .service-card.cuci:hover,
        .service-card.reparasi:hover {
            transform: translateY(-6px);
            border-color: rgba(255, 255, 255, 0.2);
            box-shadow: 0 15px 35px rgba(255, 255, 255, 0.05);
        }

        .service-card h3 {
            font-size: 1.5rem;
            font-weight: 800;
            margin-bottom: 1.2rem;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.6rem;
            letter-spacing: -0.01em;
        }

        .service-card p {
            color: var(--text-dim);
            font-size: 0.95rem;
            line-height: 1.7;
            font-weight: 500;
        }

        /* Footer Section */
        footer {
            margin-top: auto;
            padding: 3rem 0;
            border-top: 1px solid rgba(255, 255, 255, 0.04);
            background: rgba(5, 7, 12, 0.9);
            text-align: center;
        }

        footer p {
            color: var(--text-muted);
            font-size: 0.85rem;
            font-weight: 600;
            letter-spacing: 0.02em;
        }

        /* Responsive Breakpoints */
        @media (max-width: 1024px) {
            .hero-text h1 {
                font-size: 3.5rem;
            }

            .services-grid {
                max-width: 700px;
            }
        }

        @media (max-width: 640px) {
            .logo {
                font-size: 1.3rem;
            }

            .nav-auth {
                gap: 1.2rem;
            }

            .nav-auth a.btn-login {
                font-size: 0.9rem;
            }

            .btn-register {
                padding: 0.55rem 1.3rem;
                font-size: 0.9rem;
            }

            .hero {
                padding: 3rem 0 5rem;
            }

            .hero-text h1 {
                font-size: 2.3rem;
                line-height: 1.25;
            }

            .hero-text p {
                font-size: 0.95rem;
                margin-bottom: 2rem;
            }

            .hero-btns {
                flex-direction: column;
                width: 100%;
                gap: 0.8rem;
            }

            .hero-btns .btn {
                width: 100%;
                padding: 0.9rem 2rem;
            }

            .services {
                padding: 3rem 0 5rem;
            }

            .section-header {
                margin-bottom: 2rem;
            }

            .services-grid {
                grid-template-columns: 1fr;
            }

            .service-card {
                padding: 2.5rem 1.5rem;
                border-radius: 24px;
            }

            .service-card h3 {
                font-size: 1.4rem;
                margin-bottom: 1rem;
            }

            .service-card p {
                font-size: 0.9rem;
            }
        }
    </style>
</head>

<body>

    <!-- Navigation -->
    <nav id="navbar">
        <div class="container">
            <a href="/" class="logo">CleanUP<span>Shoes</span></a>
            <div class="nav-auth">
                <a href="{{ route('login') }}" class="btn-login">Masuk</a>
                <a href="{{ route('register') }}" class="btn-register">Daftar</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <main class="hero">
        <div class="container">
            <div class="hero-text">
                <!-- Natural text flow for perfect balancing without forced line breaks -->
                <h1 style="text-wrap: balance;">Berikan <span>Sepatu</span> Anda Kehidupan Baru</h1>
                <p style="text-wrap: balance;">Layanan cuci dan restorasi profesional untuk sepatu kesayangan Anda. Mulai dari sneakers hingga sepatu formal, kami tangani dengan sepenuh hati.</p>
                <div class="hero-btns">
                    <a href="{{ route('register') }}" class="btn btn-book">Pesan Sekarang</a>
                    <a href="#services" class="btn btn-services">Layanan Kami</a>
                </div>
            </div>
        </div>
    </main>

    <!-- Services Section -->
    <section id="services" class="services">
        <div class="container">
            <div class="section-header">
                <span class="section-tag">LAYANAN UTAMA KAMI</span>
            </div>
            <div class="services-grid">
                <div class="service-card cuci">
                    <!-- Clean Lucide Icon instead of OS emoji -->
                    <h3>
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: inline-block; vertical-align: middle; margin-right: 8px;">
                            <path d="M4 6h5.426a1 1 0 0 1 .863 .496l1.064 1.823a3 3 0 0 0 1.896 1.407l4.677 1.114a4 4 0 0 1 3.074 3.89v2.27a1 1 0 0 1 -1 1h-16a1 1 0 0 1 -1 -1v-10a1 1 0 0 1 1 -1" />
                            <path d="M14 13l1 -2" />
                            <path d="M8 18v-1a4 4 0 0 0 -4 -4h-1" />
                            <path d="M10 12l1.5 -3" />
                            <path d="M18 3l.5 1.5l1.5 .5l-1.5 .5l-.5 1.5l-.5 -1.5l-1.5 -.5l1.5 -.5z" fill="#94a3b8" stroke="none" />
                        </svg>
                        Cuci Sepatu
                    </h3>
                    <p>Pembersihan mendalam untuk semua jenis material, menghilangkan noda membandel dan bau tidak sedap.</p>
                </div>
                <div class="service-card reparasi">
                    <!-- Clean Lucide Icon instead of OS emoji -->
                    <h3>
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: inline-block; vertical-align: middle; margin-right: 8px;">
                            <path d="M4 6h5.426a1 1 0 0 1 .863 .496l1.064 1.823a3 3 0 0 0 1.896 1.407l4.677 1.114a4 4 0 0 1 3.074 3.89v2.27a1 1 0 0 1 -1 1h-16a1 1 0 0 1 -1 -1v-10a1 1 0 0 1 1 -1" />
                            <path d="M14 13l1 -2" />
                            <path d="M8 18v-1a4 4 0 0 0 -4 -4h-1" />
                            <path d="M10 12l1.5 -3" />
                            <g transform="translate(11, -1) scale(0.55)">
                                <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z" />
                            </g>
                        </svg>
                        Reparasi Sepatu
                    </h3>
                    <p>Perbaikan sol, jahitan, hingga restorasi warna agar sepatu lama Anda kembali terlihat seperti baru.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <p>&copy; 2026 CleanUPShoes. All Rights Reserved.</p>
    </footer>

    <!-- Scripts -->
    <script>
        // Initialize Lucide Icons
        lucide.createIcons();

        // Add scrolled class to navbar
        window.addEventListener('scroll', function() {
            const navbar = document.getElementById('navbar');
            if (window.scrollY > 20) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
    </script>
</body>

</html>