<!DOCTYPE html>
<html lang="id">
<head>
    @if(Auth::check() && in_array(Auth::user()->role, ['admin', 'employee']))
    <script>
        (function() {
            const defaultTheme = 'dark';
            const theme = localStorage.getItem('premium-theme') || defaultTheme;
            document.documentElement.setAttribute('data-theme', theme);
        })();
    </script>
    @endif
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('page_title', 'CleanUP Shoes')</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #f97316; /* Orange 500 */
            --secondary: #fb923c; /* Orange 400 */
            --bg: #09090b; /* Zinc 950 (Modern Black) */
            --surface: #121214; /* Slightly lighter surface */
            --surface-variant: #1c1c1f;
            --sidebar-bg: #111114;
            --card-bg: rgba(255, 255, 255, 0.03);
            --text: #f8fafc;
            --text-secondary: #94a3b8;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --sidebar-width: 280px;
            --radius-xl: 32px;
            --radius-lg: 24px;
            --radius-md: 16px;
            --border-color: rgba(255, 255, 255, 0.1);
            
            /* Glow blobs variables */
            --primary-glow: rgba(249, 115, 22, 0.10);
            --primary-glow-subtle: rgba(249, 115, 22, 0.03);

            /* Sidebar variables for default/customer view */
            --sidebar-border: rgba(255, 255, 255, 0.1);
            --sidebar-text: #94a3b8;
            --sidebar-text-hover: #f8fafc;
            --sidebar-text-active: #f97316;
            --sidebar-logo-text: #ffffff;
            --sidebar-active-bg: rgba(255, 255, 255, 0.05);
        }

        [data-theme="light"] {
            --bg: #edf1f8; /* Cool Blue-Gray Background */
            --surface: #ffffff; /* White surface */
            --surface-variant: #f1f5f9; /* Slate 100 */
            --card-bg: #ffffff; /* White card */
            --text: #0f172a; /* Slate 900 */
            --text-secondary: #64748b; /* Slate 500 */
            --border-color: #e2e8f0;
            
            /* Light mode sidebar variables (retains dark sidebar identity) */
            --sidebar-bg: #111114;
            --sidebar-border: rgba(255, 255, 255, 0.08);
            --sidebar-text: #94a3b8;
            --sidebar-text-hover: #ffffff;
            --sidebar-text-active: #f97316;
            --sidebar-logo-text: #ffffff;
            --sidebar-active-bg: rgba(255, 255, 255, 0.05);
        }

        [data-theme="dark"] {
            --bg: #1a1b24; /* Dark Slate Navy Background */
            --surface: #2b2e3c; /* Dark surface for header/navbar */
            --surface-variant: #343a40; /* Dark slate 800 */
            --card-bg: #343a40; /* Dark Card Background */
            --text: #ffffff; /* White Text */
            --text-secondary: #c2c7d0; /* Soft secondary text */
            --border-color: rgba(255, 255, 255, 0.08);
            
            /* Dark mode sidebar variables (retains dark sidebar identity) */
            --sidebar-bg: #111114;
            --sidebar-border: rgba(255, 255, 255, 0.08);
            --sidebar-text: #c2c7d0;
            --sidebar-text-hover: #ffffff;
            --sidebar-text-active: #f97316;
            --sidebar-logo-text: #ffffff;
            --sidebar-active-bg: rgba(255, 255, 255, 0.05);
        }

        /* Styling overrides that ONLY apply to theme-controlled states (Admin/Employee) */
        [data-theme="light"] .header,
        [data-theme="dark"] .header {
            background: var(--surface);
            padding: 0.85rem 1.5rem;
            border-radius: 18px;
            border: 1.5px solid var(--border-color);
            box-shadow: 0 2px 12px rgba(0,0,0,0.05);
        }

        [data-theme="light"] .main-content,
        [data-theme="dark"] .main-content {
            background-color: var(--bg);
            background-image: radial-gradient(circle at 0% 0%, var(--primary-glow) 0%, transparent 70%);
            background-size: auto;
        }

        [data-theme="light"] .header h1,
        [data-theme="dark"] .header h1 {
            color: var(--text) !important;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Outfit', sans-serif;
        }

        body {
            background-color: var(--bg);
            color: var(--text);
            display: flex;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Sidebar */
        .sidebar {
            width: var(--sidebar-width);
            background-color: var(--sidebar-bg);
            padding: 2rem 1.5rem;
            display: flex;
            flex-direction: column;
            border-right: 1px solid var(--sidebar-border);
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), background-color 0.3s, border-color 0.3s;
            position: fixed;
            height: 100vh;
            left: 0;
            top: 0;
            z-index: 1000;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: 900;
            margin-bottom: 2.5rem;
            color: var(--sidebar-logo-text);
            letter-spacing: -1px;
            transition: color 0.3s;
        }

        .logo span {
            color: var(--primary);
        }

        .nav-menu {
            list-style: none;
            flex-grow: 1;
            overflow-y: auto;
            scrollbar-width: none; /* Firefox */
        }

        .nav-menu::-webkit-scrollbar {
            display: none; /* Chrome, Safari, Opera */
        }

        .nav-item {
            margin-bottom: 0.4rem;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 0.8rem 1.2rem;
            color: var(--sidebar-text);
            text-decoration: none;
            border-radius: 12px;
            transition: all 0.3s ease;
            opacity: 0.85;
        }

        .nav-link:hover {
            background: var(--sidebar-active-bg);
            opacity: 1;
            color: var(--sidebar-text-hover);
        }

        .nav-link.active {
            background: var(--sidebar-active-bg);
            opacity: 1;
            color: var(--sidebar-text-active);
            font-weight: 700;
            box-shadow: 0 4px 12px rgba(249, 115, 22, 0.08);
        }

        /* Logout Button styling */
        .btn-logout {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            padding: 0.8rem 1.2rem;
            color: var(--sidebar-text);
            text-decoration: none;
            border-radius: 12px;
            transition: all 0.3s ease;
            opacity: 0.85;
            width: 100%;
            background: none;
            border: none;
            cursor: pointer;
            text-align: left;
            font-family: 'Outfit', sans-serif;
            font-size: 1rem;
        }

        .btn-logout:hover {
            background: rgba(239, 68, 68, 0.08);
            color: var(--danger);
            opacity: 1;
        }

        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 2rem;
            flex-grow: 1;
            transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            width: calc(100% - var(--sidebar-width));
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            gap: 1rem;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--primary);
            color: #000;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 900;
        }

        /* Mobile Menu Toggle */
        .menu-toggle {
            display: none;
            background: var(--surface-variant);
            border: 1.5px solid var(--border-color);
            color: var(--text);
            padding: 0.55rem;
            border-radius: 10px;
            cursor: pointer;
            z-index: 1001;
            flex-shrink: 0;
            transition: background 0.2s, color 0.2s;
        }
        .menu-toggle:hover {
            background: var(--primary-glow);
            border-color: var(--primary);
            color: var(--primary);
        }

        /* Backdrop */
        .sidebar-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            backdrop-filter: blur(4px);
            z-index: 999;
        }

        /* Responsive Utilities */
        .grid-3 {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
        }

        .grid-2 {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
        }

        .table-container {
            width: 100%;
            overflow-x: auto;
            border-radius: 20px;
            background: var(--card-bg);
            border: 1px solid rgba(255,255,255,0.05);
        }

        .glass-card {
            background: var(--card-bg);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: var(--radius-lg);
            padding: 1.5rem;
        }

        /* Only interactive/clickable glass cards should have scale transitions & active states */
        a .glass-card,
        a.glass-card,
        .glass-card.interactive {
            transition: transform 0.2s ease, background 0.2s ease;
        }

        a:active .glass-card,
        a.glass-card:active,
        .glass-card.interactive:active {
            transform: scale(0.98);
            background: rgba(255, 255, 255, 0.08);
        }

        @media (max-width: 1024px) {
            .grid-3 {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                width: 280px;
            }
            .sidebar.active {
                transform: translateX(0);
            }
            .sidebar-backdrop.active {
                display: block;
            }
            .main-content {
                margin-left: 0;
                width: 100%;
                padding: 0.9rem;
                padding-bottom: 120px !important;
            }
            .menu-toggle {
                display: block;
            }
            .grid-3, .grid-2 {
                grid-template-columns: 1fr;
                gap: 0.8rem;
            }
            .header {
                flex-direction: row;
                align-items: center;
                gap: 0.5rem;
                margin-bottom: 1.2rem;
                padding: 0.75rem 1rem;
                border-radius: 14px;
                border-bottom: none;
            }
            /* Hide user name on mobile but keep avatar */
            .user-info {
                display: none;
            }
            .header h1 {
                font-size: clamp(0.9rem, 3.5vw, 1.15rem) !important;
                font-weight: 800;
                letter-spacing: -0.3px;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }
            /* Compact right icon group gap on mobile */
            .header > div:last-child {
                gap: 0.4rem !important;
            }
            /* Compact avatar on mobile */
            .avatar {
                width: 34px;
                height: 34px;
                font-size: 0.85rem;
            }
            /* Compact notification bell on mobile */
            .notification-bell {
                padding: 0.3rem !important;
            }
            #theme-toggle {
                padding: 0.3rem !important;
            }
            .glass-card {
                padding: 1.1rem !important;
                border-radius: 16px !important;
            }
            table {
                font-size: 0.82rem;
            }
            table th, table td {
                padding: 0.6rem 0.8rem !important;
            }
        }

        @media (max-width: 576px) {
            .main-content {
                padding: 0.7rem;
                padding-bottom: 120px !important;
            }
            .glass-card {
                padding: 1rem !important;
                border-radius: 14px !important;
            }
            h1, h2 {
                font-size: clamp(0.95rem, 4vw, 1.2rem) !important;
            }
            h3 {
                font-size: clamp(0.9rem, 3.5vw, 1.1rem) !important;
            }
            /* Global SweetAlert2 Mobile Scaling */
            .swal2-popup {
                font-size: 0.8rem !important;
                padding: 1rem !important;
                width: 90% !important;
                border-radius: 16px !important;
            }
            .swal2-title {
                font-size: 1.15rem !important;
                margin-top: 5px !important;
            }
            .swal2-html-container {
                font-size: 0.85rem !important;
                margin: 8px 0 0 0 !important;
            }
            .swal2-styled {
                padding: 8px 16px !important;
                font-size: 0.8rem !important;
                border-radius: 8px !important;
                margin: 5px !important;
            }
            .swal2-icon {
                transform: scale(0.85);
                margin: 10px auto 5px auto !important;
            }
        }

        .btn-logout {
            margin-top: auto;
            color: #ef4444;
            text-decoration: none;
            font-weight: 600;
            padding: 0.8rem 1.2rem;
            border-radius: 12px;
            transition: 0.3s;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-logout:hover {
            background: rgba(239, 68, 68, 0.1);
        }

        .notification-bell:hover {
            background: rgba(255, 255, 255, 0.05);
            color: var(--primary);
        }

        @media (max-width: 768px) {
            #notification-dropdown {
                width: calc(100vw - 2rem) !important;
                right: -50px !important;
            }
        }

        select option {
            background-color: #1e293b;
            color: #ffffff;
        }

        /* Mobile Bottom Navigation Bar Styles - Material 3 Inspired */
        .mobile-bottom-nav {
            display: none;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 80px; /* Taller for M3 */
            background-color: #0c0c0e; 
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            box-shadow: 0 -10px 30px rgba(0, 0, 0, 0.5);
            z-index: 1000;
            align-items: center;
            justify-content: space-around;
            padding: 0 0.8rem;
            padding-bottom: env(safe-area-inset-bottom);
        }

        .mobile-nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            color: var(--text-secondary);
            font-size: 0.72rem;
            font-weight: 600;
            flex: 1;
            height: 100%;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
        }

        .mobile-nav-icon-wrapper {
            position: relative;
            padding: 4px 20px;
            border-radius: 16px;
            margin-bottom: 4px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .mobile-nav-item.active {
            color: #fff;
            font-weight: 800;
        }

        .mobile-nav-item.active .mobile-nav-icon-wrapper {
            background: rgba(249, 115, 22, 0.2);
            color: var(--primary);
        }

        .mobile-nav-item svg {
            width: 24px;
            height: 24px;
            stroke-width: 2;
        }

        .mobile-nav-badge {
            position: absolute;
            top: -2px;
            right: -8px;
            background: var(--danger);
            color: white;
            font-size: 0.65rem;
            font-weight: 900;
            padding: 0 0.4rem;
            border-radius: 10px;
            min-width: 18px;
            height: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid #0c0c0e;
        }

        @media (max-width: 768px) {
            .mobile-bottom-nav {
                display: flex;
            }
            body {
                padding-bottom: 120px !important; /* Compact HP Safe Padding */
            }
            .mobile-logo-container {
                display: flex !important;
            }
            .header h1 {
                display: none !important; /* Hide large page title on mobile to match mockup logo */
            }
        }

        /* Responsive title helper to prevent double titles on desktop */
        .desktop-hidden-title {
            display: none !important;
        }
        @media (max-width: 768px) {
            .desktop-hidden-title {
                display: block !important;
            }
        }

        .mobile-only-header-cart {
            display: none !important;
        }
        @media (max-width: 768px) {
            .mobile-only-header-cart {
                display: flex !important;
            }
        }
    </style>
    @if(Auth::check() && in_array(Auth::user()->role, ['admin', 'employee']))
    <style>
        :root {
            --primary: #0d6efd; /* Solid blue */
            --secondary: #0ea5e9; /* Sky blue */
            --sidebar-text-active: #ffffff;
            --sidebar-active-bg: #0d6efd;
            --primary-glow: rgba(13, 110, 253, 0.10);
            --primary-glow-subtle: rgba(13, 110, 253, 0.03);
        }
        [data-theme="light"] {
            --sidebar-text-active: #ffffff;
            --sidebar-active-bg: #0d6efd;
        }
        [data-theme="dark"] {
            --sidebar-text-active: #ffffff;
            --sidebar-active-bg: #0d6efd;
        }
    </style>
    @endif
    @if(false)
    <style>
        @media (max-width: 768px) {
            body {
                padding-bottom: 0 !important;
            }
            .main-content {
                padding-bottom: 0 !important;
            }
            .mobile-bottom-nav {
                display: none !important;
            }
        }
        @media (max-width: 480px) {
            .main-content {
                padding-bottom: 0 !important;
            }
        }
    </style>
    @endif
    @stack('styles')
</head>
<body>
    <div class="sidebar-backdrop" id="sidebar-backdrop" onclick="toggleMenu()"></div>
    
    <div class="sidebar" id="sidebar">
        <div class="logo">
            @if(Auth::check() && in_array(Auth::user()->role, ['admin', 'employee']))
                CLEANUP<span style="color: #ffc107 !important;"> SHOES</span>
            @else
                CleanUP<span>Shoes</span>
            @endif
        </div>
        <ul class="nav-menu">
            @if(Auth::user()->role == 'employee')
                <li class="nav-item">
                    <a href="{{ route('employee.dashboard') }}" class="nav-link {{ request()->routeIs('employee.dashboard') ? 'active' : '' }}" style="display: flex; align-items: center; gap: 0.8rem;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('employee.orders.index') }}" class="nav-link {{ request()->routeIs('employee.orders.*') ? 'active' : '' }}" style="display: flex; align-items: center; justify-content: space-between;">
                        <div style="display: flex; align-items: center; gap: 0.8rem;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                            <span>Orderan Masuk</span>
                        </div>
                        @php
                            /** @var int $pendingOrdersCount */
                            $pendingOrdersCount = \App\Models\Order::whereStatus('pending')->count();
                        @endphp
                        @if($pendingOrdersCount > 0)
                            <span class="badge-pulse" style="background: #f43f5e; color: #fff; font-size: 0.7rem; font-weight: 800; padding: 0.15rem 0.45rem; border-radius: 20px; min-width: 18px; height: 18px; display: inline-flex; align-items: center; justify-content: center; box-shadow: 0 2px 6px rgba(244, 63, 94, 0.4); margin-left: 0.5rem; line-height: 1;">
                                {{ $pendingOrdersCount }}
                            </span>
                        @endif
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('employee.orders.index', ['delivery' => 1]) }}" class="nav-link {{ request('delivery') == '1' ? 'active' : '' }}" style="display: flex; align-items: center; justify-content: space-between;">
                        <div style="display: flex; align-items: center; gap: 0.8rem;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 18H3a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h3"></path><polyline points="8 6 12 2 16 6"></polyline><path d="M16 2v4"></path><path d="M21 16v2a2 2 0 0 1-2 2h-2"></path><circle cx="17" cy="18" r="2"></circle><circle cx="7" cy="18" r="2"></circle><path d="M12 2h4l4 4v8h-3"></path><path d="M9 18h4"></path></svg>
                            <span>Antar Jemput</span>
                        </div>
                        @php
                            /** @var int $deliveryOrdersCount */
                            $deliveryOrdersCount = \App\Models\Order::whereIsDelivery(1)->whereNotIn('status', ['completed', 'cancelled'])->count();
                        @endphp
                        @if($deliveryOrdersCount > 0)
                            <span style="background: #f59e0b; color: #fff; font-size: 0.7rem; font-weight: 800; padding: 0.15rem 0.45rem; border-radius: 20px; min-width: 18px; height: 18px; display: inline-flex; align-items: center; justify-content: center; margin-left: 0.5rem; line-height: 1;">
                                {{ $deliveryOrdersCount }}
                            </span>
                        @endif
                    </a>
                </li>
                <style>
                    @keyframes pulse-red {
                        0% { box-shadow: 0 0 0 0 rgba(244, 63, 94, 0.7); }
                        70% { box-shadow: 0 0 0 10px rgba(244, 63, 94, 0); }
                        100% { box-shadow: 0 0 0 0 rgba(244, 63, 94, 0); }
                    }
                    .badge-pulse {
                        animation: pulse-red 2s infinite;
                    }
                </style>

                <li class="nav-item">
                    <a href="{{ route('employee.orders.index', ['queue' => 1]) }}" class="nav-link {{ request('queue') == '1' ? 'active' : '' }}" style="display: flex; align-items: center; gap: 0.8rem;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect><line x1="8" y1="21" x2="16" y2="21"></line><line x1="12" y1="17" x2="12" y2="21"></line></svg>
                        <span>Monitor Antrian</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('employee.inventories.index') }}" class="nav-link {{ request()->routeIs('employee.inventories.*') ? 'active' : '' }}" style="display: flex; align-items: center; gap: 0.8rem;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
                        <span>Stok Barang</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link {{ request()->routeIs('employee.reports.*') ? 'active' : '' }}" onclick="toggleSubmenu(event, this)">
                        <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                            <div style="display: flex; align-items: center; gap: 0.8rem;">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>
                                <span>Laporan</span>
                            </div>
                            <svg class="submenu-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="transition: transform 0.3s; transform: {{ request()->routeIs('employee.reports.*') ? 'rotate(180deg)' : 'rotate(0)' }}"><polyline points="6 9 12 15 18 9"></polyline></svg>
                        </div>
                    </a>
                    <ul class="submenu" style="display: {{ request()->routeIs('employee.reports.*') ? 'block' : 'none' }}; list-style: none; padding-left: 1.5rem; margin-top: 0.5rem; margin-bottom: 0.5rem;">
                        <li style="margin-bottom: 0.3rem;"><a href="{{ route('employee.reports.index') }}#ringkasan" class="nav-link" style="font-size: 0.85rem; padding: 0.4rem 1rem; opacity: 0.7; transition: 0.3s;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.7'">Ringkasan Kinerja</a></li>
                        <li style="margin-bottom: 0.3rem;"><a href="{{ route('employee.reports.index') }}#tugas" class="nav-link" style="font-size: 0.85rem; padding: 0.4rem 1rem; opacity: 0.7; transition: 0.3s;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.7'">Orderan Masuk</a></li>
                        <li style="margin-bottom: 0.3rem;"><a href="{{ route('employee.reports.index') }}#riwayat" class="nav-link" style="font-size: 0.85rem; padding: 0.4rem 1rem; opacity: 0.7; transition: 0.3s;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.7'">Riwayat Pekerjaan</a></li>
                        <li style="margin-bottom: 0.3rem;"><a href="{{ route('employee.reports.index') }}#absensi" class="nav-link" style="font-size: 0.85rem; padding: 0.4rem 1rem; opacity: 0.7; transition: 0.3s;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.7'">Rekap Absensi</a></li>
                        <li style="margin-bottom: 0.3rem;"><a href="{{ route('employee.reports.index') }}#rating" class="nav-link" style="font-size: 0.85rem; padding: 0.4rem 1rem; opacity: 0.7; transition: 0.3s;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.7'">Rating Pelanggan</a></li>
                        <li style="margin-bottom: 0.3rem;"><a href="{{ route('employee.reports.index') }}#statistik" class="nav-link" style="font-size: 0.85rem; padding: 0.4rem 1rem; opacity: 0.7; transition: 0.3s;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.7'">Statistik Kerja</a></li>
                        <li style="margin-bottom: 0.3rem;"><a href="{{ route('employee.reports.index') }}#antar_jemput" class="nav-link" style="font-size: 0.85rem; padding: 0.4rem 1rem; opacity: 0.7; transition: 0.3s;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.7'">Antar Jemput</a></li>
                    </ul>
                </li>
            @elseif(Auth::user()->role == 'admin')
                <li class="nav-item">
                    <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" style="display: flex; align-items: center; gap: 0.8rem;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.orders.index') }}" class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}" style="display: flex; align-items: center; justify-content: space-between;">
                        <div style="display: flex; align-items: center; gap: 0.8rem;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                            <span>Pesanan</span>
                        </div>
                        @php
                            /** @var int $adminPendingCount */
                            $adminPendingCount = \App\Models\Order::whereStatus('pending')->count();
                        @endphp
                        @if($adminPendingCount > 0)
                            <span class="badge-pulse" style="background: #f43f5e; color: #fff; font-size: 0.7rem; font-weight: 800; padding: 0.15rem 0.45rem; border-radius: 20px; min-width: 18px; height: 18px; display: inline-flex; align-items: center; justify-content: center; box-shadow: 0 2px 6px rgba(244, 63, 94, 0.4); margin-left: 0.5rem; line-height: 1;">
                                {{ $adminPendingCount }}
                            </span>
                        @endif
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.orders.index', ['delivery' => 1]) }}" class="nav-link {{ request('delivery') == '1' ? 'active' : '' }}" style="display: flex; align-items: center; justify-content: space-between;">
                        <div style="display: flex; align-items: center; gap: 0.8rem;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="3" width="15" height="13"></rect><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon><circle cx="5.5" cy="18.5" r="2.5"></circle><circle cx="18.5" cy="18.5" r="2.5"></circle></svg>
                            <span>Antar Jemput</span>
                        </div>
                        @php
                            /** @var int $adminDeliveryCount */
                            $adminDeliveryCount = \App\Models\Order::whereIsDelivery(1)->whereNotIn('status', ['completed', 'cancelled'])->count();
                        @endphp
                        @if($adminDeliveryCount > 0)
                            <span style="background: #f59e0b; color: #fff; font-size: 0.7rem; font-weight: 800; padding: 0.15rem 0.45rem; border-radius: 20px; min-width: 18px; height: 18px; display: inline-flex; align-items: center; justify-content: center; margin-left: 0.5rem; line-height: 1;">
                                {{ $adminDeliveryCount }}
                            </span>
                        @endif
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.orders.index', ['queue' => 1]) }}" class="nav-link {{ request('queue') == '1' ? 'active' : '' }}" style="display: flex; align-items: center; gap: 0.8rem;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect><line x1="8" y1="21" x2="16" y2="21"></line><line x1="12" y1="17" x2="12" y2="21"></line></svg>
                        <span>Monitor Antrian</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.customers.index') }}" class="nav-link {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}" style="display: flex; align-items: center; gap: 0.8rem;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                        <span>Pelanggan</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.services.index') }}" class="nav-link {{ request()->routeIs('admin.services.*') ? 'active' : '' }}" style="display: flex; align-items: center; gap: 0.8rem;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 2 7 12 12 22 7 12 2"></polygon><polyline points="2 17 12 22 22 17"></polyline><polyline points="2 12 12 17 22 12"></polyline></svg>
                        <span>Layanan</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.inventories.index') }}" class="nav-link {{ request()->routeIs('admin.inventories.*') ? 'active' : '' }}" style="display: flex; align-items: center; gap: 0.8rem;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
                        <span>Stok Barang</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.finances.index') }}" class="nav-link {{ request()->routeIs('admin.finances.*') ? 'active' : '' }}" style="display: flex; align-items: center; gap: 0.8rem;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2" ry="2"></rect><line x1="2" y1="10" x2="22" y2="10"></line></svg>
                        <span>Keuangan</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.reports.index') }}" class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}" style="display: flex; align-items: center; gap: 0.8rem;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>
                        <span>Laporan</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.employees.index') }}" class="nav-link {{ request()->routeIs('admin.employees.*') ? 'active' : '' }}" style="display: flex; align-items: center; gap: 0.8rem;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                        <span>Karyawan</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.settings.index') }}" class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}" style="display: flex; align-items: center; gap: 0.8rem;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                        <span>Pengaturan</span>
                    </a>
                </li>
            @else
                <li class="nav-item">
                    <a href="{{ route('customer.dashboard') }}" class="nav-link {{ request()->routeIs('customer.dashboard') ? 'active' : '' }}" style="display: flex; align-items: center; gap: 0.8rem;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('services.index') }}" class="nav-link {{ request()->routeIs('services.index') ? 'active' : '' }}" style="display: flex; align-items: center; gap: 0.8rem;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 2 7 12 12 22 7 12 2"></polygon><polyline points="2 17 12 22 22 17"></polyline><polyline points="2 12 12 17 22 12"></polyline></svg>
                        <span>Lihat Layanan</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('cart.index') }}" class="nav-link {{ request()->routeIs('cart.index') ? 'active' : '' }}" style="display: flex; align-items: center; justify-content: space-between;">
                        <div style="display: flex; align-items: center; gap: 0.8rem;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
                            <span>Keranjang Saya</span>
                        </div>
                        @if(Session::has('cart') && count(Session::get('cart')) > 0)
                            <span style="background: var(--primary); color: #000; font-size: 0.7rem; font-weight: 800; padding: 0.15rem 0.45rem; border-radius: 20px; min-width: 18px; height: 18px; display: inline-flex; align-items: center; justify-content: center; box-shadow: 0 2px 6px rgba(249, 115, 22, 0.4); margin-left: 0.5rem; line-height: 1;">
                                {{ count(Session::get('cart')) }}
                            </span>
                        @endif
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('orders.my-orders') }}" class="nav-link {{ request()->routeIs('orders.my-orders') ? 'active' : '' }}" style="display: flex; align-items: center; justify-content: space-between;">
                        <div style="display: flex; align-items: center; gap: 0.8rem;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                            <span>Pesanan Saya</span>
                        </div>
                        @php
                            /** @var int|string|null $currentUserId */
                            $currentUserId = Auth::id();
                            /** @var int $custActiveCount */
                            $custActiveCount = \App\Models\Order::whereUserId($currentUserId)->whereNotIn('status', ['completed', 'cancelled'])->count();
                        @endphp
                        @if($custActiveCount > 0)
                            <span style="background: var(--primary); color: #000; font-size: 0.7rem; font-weight: 800; padding: 0.15rem 0.45rem; border-radius: 20px; min-width: 18px; height: 18px; display: inline-flex; align-items: center; justify-content: center; box-shadow: 0 2px 6px rgba(249, 115, 22, 0.4); margin-left: 0.5rem; line-height: 1;">
                                {{ $custActiveCount }}
                            </span>
                        @endif
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('orders.history') }}" class="nav-link {{ request()->routeIs('orders.history') ? 'active' : '' }}" style="display: flex; align-items: center; gap: 0.8rem;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                        <span>Riwayat Pesanan</span>
                    </a>
                </li>
            @endif
        </ul>
        <form method="POST" action="{{ route('logout') }}" style="margin-top: auto;">
            @csrf
            <button type="submit" class="btn-logout">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                Keluar
            </button>
        </form>
    </div>

    <div class="main-content">
        <div class="header">
            <div style="display: flex; align-items: center; gap: 0.75rem; min-width: 0;">
                <button class="menu-toggle" onclick="toggleMenu()">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
                </button>
                <h1 style="font-size: clamp(1.1rem, 4vw, 2rem); font-weight: 800; letter-spacing: -0.5px; color: var(--text); margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">@yield('page_title')</h1>
            </div>
            
            <div style="display: flex; align-items: center; gap: 1rem;">
                <!-- Theme Toggle Button -->
                @if(Auth::check() && in_array(Auth::user()->role, ['admin', 'employee']))
                <button id="theme-toggle" title="Ubah Tema" style="padding: 0.5rem; border-radius: 12px; transition: 0.3s; color: var(--text); background: transparent; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center;" onmouseover="this.style.background='var(--surface-variant)'" onmouseout="this.style.background='transparent'">
                    <svg id="theme-toggle-icon" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <!-- Loaded dynamically via JavaScript -->
                    </svg>
                </button>
                @endif

                <!-- Notification Bell & Dropdown -->
                <div class="notification-container" style="position: relative;">
                    <button id="notification-btn" class="notification-bell" style="position: relative; padding: 0.5rem; border-radius: 12px; transition: 0.3s; color: var(--text); background: transparent; border: none; cursor: pointer;">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                            <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                        </svg>
                        <span id="notification-badge" style="display: none; position: absolute; top: 5px; right: 5px; width: 18px; height: 18px; background: var(--danger); border-radius: 50%; align-items: center; justify-content: center; font-size: 10px; font-weight: 800; border: 2px solid var(--bg);">0</span>
                    </button>

                    <!-- Dropdown Content -->
                    <div id="notification-dropdown" class="glass-card" style="display: none; position: absolute; top: 50px; right: 0; width: 350px; z-index: 1002; padding: 0; overflow: hidden; border: 1px solid rgba(255,255,255,0.1); box-shadow: 0 10px 30px rgba(0,0,0,0.3);">
                        <div style="padding: 1rem; border-bottom: 1px solid rgba(255,255,255,0.05); display: flex; justify-content: space-between; align-items: center; background: rgba(255,255,255,0.02);">
                            <span style="font-weight: 700;">Notifikasi</span>
                            <button onclick="markAllAsRead()" style="font-size: 0.75rem; color: var(--primary); background: transparent; border: none; cursor: pointer;">Tandai semua dibaca</button>
                        </div>
                        <div id="notification-list" style="max-height: 400px; overflow-y: auto;">
                            <!-- List items will be injected here -->
                            <div style="padding: 2rem; text-align: center; opacity: 0.5;">Memuat...</div>
                        </div>
                        <a href="{{ route('notifications.index') }}" style="display: block; padding: 1rem; text-align: center; font-size: 0.85rem; color: var(--text); text-decoration: none; border-top: 1px solid rgba(255,255,255,0.05); background: rgba(255,255,255,0.02); transition: 0.3s;" onmouseover="this.style.background='rgba(255,255,255,0.05)'" onmouseout="this.style.background='rgba(255,255,255,0.02)'">Lihat Semua</a>
                    </div>
                </div>

                @if(Auth::user()->role == 'customer')
                <a href="{{ route('cart.index') }}" class="mobile-only-header-cart" title="Keranjang Saya" style="padding: 0.5rem; border-radius: 12px; transition: 0.3s; color: {{ request()->routeIs('cart.index') ? 'var(--primary)' : 'var(--text)' }}; background: {{ request()->routeIs('cart.index') ? 'rgba(255,255,255,0.05)' : 'transparent' }}; border: none; cursor: pointer; display: none; align-items: center; justify-content: center; text-decoration: none; position: relative;" onmouseover="this.style.background='rgba(255,255,255,0.05)'; this.style.color='var(--primary)'" onmouseout="this.style.background='{{ request()->routeIs('cart.index') ? 'rgba(255,255,255,0.05)' : 'transparent' }}'; this.style.color='{{ request()->routeIs('cart.index') ? 'var(--primary)' : 'var(--text)' }}'">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
                    @if(Session::has('cart') && count(Session::get('cart')) > 0)
                        <span style="position: absolute; top: 2px; right: 2px; background: var(--primary); color: #000; font-size: 0.6rem; font-weight: 900; width: 16px; height: 16px; border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 2.5px solid var(--bg);">
                            {{ count(Session::get('cart')) }}
                        </span>
                    @endif
                </a>
                <a href="{{ route('addresses.index') }}" title="Pengaturan Alamat" style="padding: 0.5rem; border-radius: 12px; transition: 0.3s; color: {{ request()->routeIs('addresses.*') ? 'var(--primary)' : 'var(--text)' }}; background: {{ request()->routeIs('addresses.*') ? 'rgba(255,255,255,0.05)' : 'transparent' }}; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; text-decoration: none;" onmouseover="this.style.background='rgba(255,255,255,0.05)'; this.style.color='var(--primary)'" onmouseout="this.style.background='{{ request()->routeIs('addresses.*') ? 'rgba(255,255,255,0.05)' : 'transparent' }}'; this.style.color='{{ request()->routeIs('addresses.*') ? 'var(--primary)' : 'var(--text)' }}'">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                </a>
                @endif

                <a href="{{ route('profile.edit') }}" class="user-profile" style="text-decoration: none; color: inherit; transition: 0.3s; padding: 0.5rem 0.8rem; border-radius: 16px;" onmouseover="this.style.background='rgba(255,255,255,0.05)'" onmouseout="this.style.background='transparent'">
                    <div class="user-info" style="text-align: right;">
                        <p style="font-weight: 600;">{{ Auth::user()->name }}</p>
                        <p style="font-size: 0.8rem; opacity: 0.6;">
                            @if(Auth::user()->role == 'admin') Admin @elseif(Auth::user()->role == 'employee') Karyawan @else Pelanggan @endif
                        </p>
                    </div>
                    <div class="avatar" style="box-shadow: 0 0 15px rgba(0, 210, 255, 0.2);">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
                </a>
            </div>
        </div>

        @yield('content')
    </div>

    <script>
        function toggleMenu() {
            const sidebar = document.getElementById('sidebar');
            const backdrop = document.getElementById('sidebar-backdrop');
            sidebar.classList.toggle('active');
            backdrop.classList.toggle('active');
        }

        // Notification Logic
        const notificationBtn = document.getElementById('notification-btn');
        const notificationDropdown = document.getElementById('notification-dropdown');
        const notificationList = document.getElementById('notification-list');
        const notificationBadge = document.getElementById('notification-badge');

        notificationBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            notificationDropdown.style.display = notificationDropdown.style.display === 'none' ? 'block' : 'none';
            if (notificationDropdown.style.display === 'block') {
                fetchNotifications();
            }
        });

        document.addEventListener('click', () => {
            notificationDropdown.style.display = 'none';
        });

        notificationDropdown.addEventListener('click', (e) => e.stopPropagation());

        async function fetchNotifications() {
            try {
                const response = await fetch('{{ route('notifications.recent') }}');
                const data = await response.json();
                
                // Update badge
                if (data.unread_count > 0) {
                    notificationBadge.innerText = data.unread_count;
                    notificationBadge.style.display = 'flex';
                } else {
                    notificationBadge.style.display = 'none';
                }

                // Update list
                if (data.recent.length === 0) {
                    notificationList.innerHTML = '<div style="padding: 2rem; text-align: center; opacity: 0.5;">Tidak ada notifikasi</div>';
                } else {
                    notificationList.innerHTML = data.recent.map(n => `
                        <div onclick="markAsRead('${n.id}', '${n.data.url}')" style="padding: 1rem; border-bottom: 1px solid rgba(255,255,255,0.02); cursor: pointer; transition: 0.3s; ${!n.read_at ? 'background: rgba(0, 210, 255, 0.05);' : ''}" onmouseover="this.style.background='rgba(255,255,255,0.05)'" onmouseout="this.style.background='${!n.read_at ? 'rgba(0, 210, 255, 0.05)' : 'transparent'}'">
                            <div style="display: flex; gap: 0.8rem;">
                                <div style="width: 35px; height: 35px; border-radius: 10px; background: rgba(255,255,255,0.05); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                     <span style="color: var(--primary); font-size: 1.2rem;">•</span>
                                </div>
                                <div style="flex-grow: 1;">
                                    <p style="font-weight: 600; font-size: 0.9rem; margin-bottom: 0.2rem;">${n.data.title}</p>
                                    <p style="font-size: 0.8rem; opacity: 0.7; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">${n.data.message}</p>
                                    <p style="font-size: 0.7rem; opacity: 0.4; margin-top: 0.4rem;">${n.created_at}</p>
                                </div>
                            </div>
                        </div>
                    `).join('');
                }
            } catch (error) {
                console.error('Failed to fetch notifications:', error);
            }
        }

        async function markAsRead(id, url) {
            await fetch(`/notifications/${id}/read`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            });
            window.location.href = url;
        }

        async function markAllAsRead() {
            await fetch('{{ route('notifications.markAllAsRead') }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            });
            fetchNotifications();
        }

        // Smart adaptive notification polling
        // - 30s default (vs 3s before = 10x less server load)
        // - 15s when dropdown is open for faster updates
        // - Pauses when tab is hidden (saves resources)
        // - Exponential backoff on errors (prevents hammering downed server)
        const POLL_INTERVAL_NORMAL = 30000;   // 30 detik
        const POLL_INTERVAL_ACTIVE = 15000;   // 15 detik saat dropdown terbuka
        const POLL_MAX_BACKOFF = 120000;       // Max 2 menit saat error
        let pollTimer = null;
        let pollBackoff = 0;
        let lastUnreadCount = -1;

        function getPollingInterval() {
            if (pollBackoff > 0) {
                return Math.min(POLL_INTERVAL_NORMAL * Math.pow(2, pollBackoff), POLL_MAX_BACKOFF);
            }
            return notificationDropdown.style.display === 'block' 
                ? POLL_INTERVAL_ACTIVE 
                : POLL_INTERVAL_NORMAL;
        }

        function scheduleNextPoll() {
            if (pollTimer) clearTimeout(pollTimer);
            pollTimer = setTimeout(async () => {
                await fetchNotifications();
                scheduleNextPoll();
            }, getPollingInterval());
        }

        // Wrap original fetch to support backoff & badge animation
        const _originalFetch = fetchNotifications;
        fetchNotifications = async function() {
            try {
                await _originalFetch();
                pollBackoff = 0; // Reset backoff on success
            } catch (e) {
                pollBackoff = Math.min(pollBackoff + 1, 5);
                console.warn(`Notification poll error (backoff level ${pollBackoff}):`, e);
            }
        };

        // Pause polling when tab is hidden
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                if (pollTimer) clearTimeout(pollTimer);
            } else {
                fetchNotifications();
                scheduleNextPoll();
            }
        });

        // Restart polling rhythm when dropdown toggles
        notificationBtn.addEventListener('click', () => scheduleNextPoll());

        // Initial fetch & start polling
        fetchNotifications();
        scheduleNextPoll();
    </script>

    <!-- Mobile Sticky Bottom Navigation (Android Native style) -->
    @if(Auth::user()->role == 'customer')
    <div class="mobile-bottom-nav">
        <a href="{{ route('customer.dashboard') }}" class="mobile-nav-item {{ request()->routeIs('customer.dashboard') ? 'active' : '' }}">
            <div class="mobile-nav-icon-wrapper">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            </div>
            Utama
        </a>
        <a href="{{ route('services.index') }}" class="mobile-nav-item {{ request()->routeIs('services.index') ? 'active' : '' }}">
            <div class="mobile-nav-icon-wrapper">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
            </div>
            Layanan
        </a>
        <a href="{{ route('orders.my-orders') }}" class="mobile-nav-item {{ request()->routeIs('orders.my-orders') ? 'active' : '' }}">
            <div class="mobile-nav-icon-wrapper">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M16 20V4a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/><rect width="20" height="14" x="2" y="6" rx="2"/></svg>
                @php
                    /** @var int|string|null $currentUserId */
                    $currentUserId = Auth::id();
                    /** @var int $custActiveCount */
                    $custActiveCount = \App\Models\Order::whereUserId($currentUserId)->whereNotIn('status', ['completed', 'cancelled'])->count();
                @endphp
                @if($custActiveCount > 0)
                    <span class="mobile-nav-badge">{{ $custActiveCount }}</span>
                @endif
            </div>
            Pesanan
        </a>
        <a href="{{ route('orders.history') }}" class="mobile-nav-item {{ request()->routeIs('orders.history') ? 'active' : '' }}">
            <div class="mobile-nav-icon-wrapper">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
            </div>
            Riwayat
        </a>
        <a href="{{ route('profile.edit') }}" class="mobile-nav-item {{ request()->routeIs('profile.edit') ? 'active' : '' }}">
            <div class="mobile-nav-icon-wrapper">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            </div>
            Profil
        </a>
    </div>
    @endif

    <script>
        function toggleSubmenu(e, el) {
            e.preventDefault();
            const submenu = el.nextElementSibling;
            const icon = el.querySelector('.submenu-icon');
            if (submenu.style.display === 'none' || !submenu.style.display) {
                submenu.style.display = 'block';
                icon.style.transform = 'rotate(180deg)';
            } else {
                submenu.style.display = 'none';
                icon.style.transform = 'rotate(0deg)';
            }
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Automatic SweetAlert2 confirm wrapper for all views (Customer, Employee, Admin)
        document.addEventListener('DOMContentLoaded', function() {
            // 1. Intercept forms with inline confirm onsubmit
            document.querySelectorAll('form').forEach(form => {
                const onsubmitAttr = form.getAttribute('onsubmit');
                if (onsubmitAttr && onsubmitAttr.includes('confirm(')) {
                    // Extract the confirm message
                    let match = onsubmitAttr.match(/confirm\(['"](.*?)['"]\)/);
                    let message = match ? match[1] : 'Apakah Anda yakin?';
                    
                    // Remove the native onsubmit
                    form.removeAttribute('onsubmit');
                    
                    // Add submit event listener
                    form.addEventListener('submit', function(e) {
                        if (form.dataset.swalConfirmed === 'true') {
                            return true;
                        }
                        e.preventDefault();
                        
                        Swal.fire({
                            title: 'Konfirmasi Tindakan',
                            text: message,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#f97316',
                            cancelButtonColor: '#4b5563',
                            confirmButtonText: 'Ya, Lanjutkan',
                            cancelButtonText: 'Batal',
                            background: '#121214',
                            color: '#fff'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                form.dataset.swalConfirmed = 'true';
                                form.submit();
                            }
                        });
                    });
                }
            });

            // 2. Intercept buttons with inline confirm onclick
            document.querySelectorAll('button[onclick*="confirm("], input[type="submit"][onclick*="confirm("]').forEach(btn => {
                const onclickAttr = btn.getAttribute('onclick');
                if (onclickAttr && onclickAttr.includes('confirm(')) {
                    // Extract the message
                    let match = onclickAttr.match(/confirm\(['"](.*?)['"]\)/);
                    let message = match ? match[1] : 'Apakah Anda yakin?';
                    
                    // Remove the native onclick
                    btn.removeAttribute('onclick');
                    
                    // Add click event listener
                    btn.addEventListener('click', function(e) {
                        e.preventDefault();
                        const form = btn.closest('form');
                        
                        Swal.fire({
                            title: 'Konfirmasi Tindakan',
                            text: message,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#f97316',
                            cancelButtonColor: '#4b5563',
                            confirmButtonText: 'Ya, Lanjutkan',
                            cancelButtonText: 'Batal',
                            background: '#121214',
                            color: '#fff'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                if (form) {
                                    form.dataset.swalConfirmed = 'true';
                                    if (btn.name) {
                                        const hiddenInput = document.createElement('input');
                                        hiddenInput.type = 'hidden';
                                        hiddenInput.name = btn.name;
                                        hiddenInput.value = btn.value;
                                        form.appendChild(hiddenInput);
                                    }
                                    form.submit();
                                }
                            }
                        });
                    });
                }
            });

            // 3. Automatically wrap tables with .table-container for mobile responsiveness
            document.querySelectorAll('table').forEach(table => {
                if (!table.closest('.table-container') && !table.closest('.email-container') && !table.closest('.receipt-box') && !table.closest('table')) {
                    const wrapper = document.createElement('div');
                    wrapper.className = 'table-container';
                    table.parentNode.insertBefore(wrapper, table);
                    wrapper.appendChild(table);
                }
            });
        });
    </script>

    @if(Auth::check() && in_array(Auth::user()->role, ['admin', 'employee']))
    <script>
        // Premium Theme Toggle Script (Admin/Employee Only)
        document.addEventListener('DOMContentLoaded', function() {
            const themeToggleBtn = document.getElementById('theme-toggle');
            const themeToggleIcon = document.getElementById('theme-toggle-icon');
            
            if (!themeToggleBtn && !themeToggleIcon) return;
            
            const moonSvg = `<path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>`;
            const sunSvg = `<circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>`;
            
            function updateThemeIcon(theme) {
                if (themeToggleIcon) {
                    themeToggleIcon.innerHTML = theme === 'dark' ? sunSvg : moonSvg;
                }
            }
            
            const currentTheme = document.documentElement.getAttribute('data-theme') || 'dark';
            updateThemeIcon(currentTheme);
            
            if (themeToggleBtn) {
                themeToggleBtn.addEventListener('click', function() {
                    const activeTheme = document.documentElement.getAttribute('data-theme') || 'dark';
                    const newTheme = activeTheme === 'dark' ? 'light' : 'dark';
                    
                    document.documentElement.setAttribute('data-theme', newTheme);
                    localStorage.setItem('premium-theme', newTheme);
                    updateThemeIcon(newTheme);
                    
                    // Dispatch custom event so that components (like Chart.js in admin.blade.php) can redraw dynamically
                    window.dispatchEvent(new CustomEvent('theme-changed', { detail: { theme: newTheme } }));
                });
            }
        });
    </script>
    @endif
    @stack('scripts')
</body>
</html>
