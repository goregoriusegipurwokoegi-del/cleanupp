<!DOCTYPE html>
<html lang="id" data-bs-theme="light">
<head>
    <script>
        (function() {
            const theme = localStorage.getItem('employee-theme') || 'light';
            document.documentElement.setAttribute('data-bs-theme', theme);
        })();
    </script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('page_title', 'CleanUP Shoes Karyawan')</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- AdminLTE 4 & Dependencies CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/styles/overlayscrollbars.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@4.0.0/dist/css/adminlte.min.css" />

    <style>
        body {
            font-family: 'Outfit', sans-serif;
        }
        .brand-link {
            text-decoration: none;
        }
        
        /* Premium Sidebar Overrides & Hierarchy Styling */
        .sidebar-wrapper .nav-item > .nav-link {
            transition: all 0.2s ease;
            border-left: 3px solid transparent;
            padding-left: 12px;
        }
        .sidebar-wrapper .nav-item.menu-open > .nav-link.active {
            background-color: rgba(255, 255, 255, 0.05) !important;
            color: #ffffff !important;
            border-left: 3px solid var(--bs-primary) !important;
        }
        .sidebar-wrapper .nav-treeview {
            padding-left: 0.5rem;
            border-left: 1px dashed rgba(255, 255, 255, 0.15);
            margin-left: 1.6rem;
            margin-top: 4px;
            margin-bottom: 8px;
        }
        .sidebar-wrapper .nav-treeview .nav-item {
            margin-bottom: 2px;
        }
        .sidebar-wrapper .nav-treeview .nav-link {
            padding: 5px 15px;
            font-size: 0.88rem;
            border-radius: 6px;
            color: rgba(255, 255, 255, 0.6) !important;
            border-left: none !important;
        }
        .sidebar-wrapper .nav-treeview .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.05);
            color: #ffffff !important;
        }
        .sidebar-wrapper .nav-treeview .nav-link.active {
            background-color: var(--bs-primary) !important;
            color: #ffffff !important;
            font-weight: 600;
        }

        /* Center chevron arrow to the vertical center of the link text */
        .sidebar-wrapper .nav-item > .nav-link .nav-arrow {
            align-self: center !important;
            line-height: 1 !important;
            margin-top: 0 !important;
            margin-bottom: 0 !important;
        }

        /* Badge Custom Colors for Visibility (Light Mode) */
        .badge-success {
            background-color: rgba(21, 128, 61, 0.08) !important;
            color: #15803d !important;
            border: 1px solid rgba(21, 128, 61, 0.2) !important;
        }
        .badge-warning {
            background-color: rgba(217, 119, 6, 0.08) !important;
            color: #b45309 !important;
            border: 1px solid rgba(217, 119, 6, 0.2) !important;
        }

        /* Dark Mode CSS Overrides */
        [data-bs-theme="dark"] .text-dark {
            color: var(--bs-body-color) !important;
        }
        [data-bs-theme="dark"] .text-secondary {
            color: #cbd5e1 !important;
        }
        [data-bs-theme="dark"] .bg-light {
            background-color: var(--bs-tertiary-bg) !important;
        }
        [data-bs-theme="dark"] .table-light {
            --bs-table-bg: var(--bs-tertiary-bg) !important;
            --bs-table-color: var(--bs-body-color) !important;
        }
        [data-bs-theme="dark"] .modal-box {
            background-color: var(--bs-body-bg) !important;
            border-color: rgba(255, 255, 255, 0.1) !important;
        }
        [data-bs-theme="dark"] .badge-success {
            background-color: rgba(52, 211, 153, 0.15) !important;
            color: #34d399 !important;
            border: 1px solid rgba(52, 211, 153, 0.25) !important;
        }
        [data-bs-theme="dark"] .badge-warning {
            background-color: rgba(245, 158, 11, 0.15) !important;
            color: #fbbf24 !important;
            border: 1px solid rgba(245, 158, 11, 0.25) !important;
        }

        /* Dark Mode Button Outline Overrides */
        [data-bs-theme="dark"] .btn-outline-dark {
            color: #f8f9fa !important;
            border-color: rgba(248, 249, 250, 0.35) !important;
        }
        [data-bs-theme="dark"] .btn-outline-dark:hover {
            background-color: #f8f9fa !important;
            color: #212529 !important;
        }
        [data-bs-theme="dark"] .btn-outline-secondary {
            color: #e2e8f0 !important;
            border-color: rgba(226, 232, 240, 0.3) !important;
        }
        [data-bs-theme="dark"] .btn-outline-secondary:hover {
            background-color: rgba(226, 232, 240, 0.1) !important;
            color: #fff !important;
        }
        [data-bs-theme="dark"] .btn-outline-primary {
            color: #60a5fa !important;
            border-color: rgba(96, 165, 250, 0.4) !important;
        }
        [data-bs-theme="dark"] .btn-outline-primary:hover {
            background-color: #60a5fa !important;
            color: #1e1e24 !important;
        }
        [data-bs-theme="dark"] .btn-outline-danger {
            color: #f87171 !important;
            border-color: rgba(248, 113, 113, 0.4) !important;
        }
        [data-bs-theme="dark"] .btn-outline-danger:hover {
            background-color: #ef4444 !important;
            color: #fff !important;
        }
        [data-bs-theme="dark"] .btn-outline-success {
            color: #4ade80 !important;
            border-color: rgba(74, 222, 128, 0.4) !important;
        }
        [data-bs-theme="dark"] .btn-outline-success:hover {
            background-color: #22c55e !important;
            color: #fff !important;
        }
    </style>
    @stack('styles')
</head>
<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <div class="app-wrapper">
        <!-- Top Navbar -->
        <nav class="app-header navbar navbar-expand bg-body shadow-sm">
            <div class="container-fluid">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
                            <i class="bi bi-list"></i>
                        </a>
                    </li>
                </ul>

                <ul class="navbar-nav ms-auto align-items-center">
                    <!-- Theme Toggle Button -->
                    <li class="nav-item me-2">
                        <button id="theme-toggle" class="btn btn-link nav-link p-2" aria-label="Toggle theme" style="border: none; background: transparent; outline: none; cursor: pointer; display: flex; align-items: center;">
                            <i id="theme-toggle-icon" class="bi bi-moon-fill fs-5"></i>
                        </button>
                    </li>
                    <!-- Notification Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link" data-bs-toggle="dropdown" href="#" id="notification-btn">
                            <i class="bi bi-bell-fill fs-5"></i>
                            <span id="notification-badge" class="navbar-badge badge bg-danger" style="display: none; position: absolute; top: 0; right: 0;">0</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end shadow" id="notification-dropdown">
                            <div class="p-3 border-bottom d-flex justify-content-between align-items-center bg-light">
                                <span class="fw-bold">Notifikasi</span>
                                <button onclick="markAllAsRead()" class="btn btn-link p-0 text-decoration-none text-primary" style="font-size: 0.8rem;">Tandai semua dibaca</button>
                            </div>
                            <div id="notification-list" style="max-height: 350px; overflow-y: auto;">
                                <div class="p-4 text-center text-muted">Memuat...</div>
                            </div>
                            <a href="{{ route('notifications.index') }}" class="dropdown-item dropdown-footer text-center py-2 bg-light border-top text-secondary small">Lihat Semua Notifikasi</a>
                        </div>
                    </li>

                    <!-- User Profile Dropdown -->
                    <li class="nav-item dropdown user-menu ms-2">
                        <a href="#" class="nav-link dropdown-toggle d-flex align-items-center" data-bs-toggle="dropdown">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold text-uppercase" style="width: 32px; height: 32px; font-size: 0.95rem;">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                            <span class="d-none d-md-inline ms-2 fw-semibold">{{ Auth::user()->name }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end shadow">
                            <li class="user-header bg-primary text-white text-center p-4">
                                <div class="bg-white text-primary rounded-circle d-inline-flex align-items-center justify-content-center fw-bold text-uppercase fs-2 mb-2" style="width: 70px; height: 70px;">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </div>
                                <p class="mb-0 fw-bold fs-5">{{ Auth::user()->name }}</p>
                                <small class="opacity-75">Staff Karyawan</small>
                            </li>
                            <li class="user-footer p-3 d-flex justify-content-between">
                                <a href="{{ route('profile.edit') }}" class="btn btn-outline-secondary btn-sm px-3">Edit Profil</a>
                                <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-danger btn-sm px-3">Keluar</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Sidebar Navigation -->
        <aside class="app-sidebar bg-dark shadow" data-lte-theme="dark">
            <div class="sidebar-brand d-flex align-items-center p-3 border-bottom border-secondary">
                <a href="{{ route('employee.dashboard') }}" class="brand-link d-flex align-items-center gap-2" style="color: #ffffff !important; text-decoration: none;">
                    <span class="brand-text fw-bold fs-5 text-uppercase" style="color: #ffffff !important;">CleanUP <span class="text-warning" style="color: #ffc107 !important;">Shoes</span></span>
                </a>
            </div>
            <div class="sidebar-wrapper p-2">
                <nav class="mt-2">
                    <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu" data-accordion="false">
                        <li class="nav-item mb-1">
                            <a href="{{ route('employee.dashboard') }}" class="nav-link {{ request()->routeIs('employee.dashboard') ? 'active text-white bg-primary' : 'text-light' }}">
                                <i class="nav-icon bi bi-speedometer2"></i>
                                <p class="ms-2">Dashboard</p>
                            </a>
                        </li>
                        <li class="nav-item mb-1">
                            <a href="{{ route('employee.attendance.index') }}" class="nav-link {{ request()->routeIs('employee.attendance.*') ? 'active text-white bg-primary' : 'text-light' }}">
                                <i class="nav-icon bi bi-clock"></i>
                                <p class="ms-2">Absensi</p>
                            </a>
                        </li>
                        <li class="nav-item mb-1">
                            <a href="{{ route('employee.orders.index') }}" class="nav-link {{ request()->routeIs('employee.orders.*') && !request('delivery') && !request('queue') && !request()->routeIs('employee.orders.scan') ? 'active text-white bg-primary' : 'text-light' }}">
                                <i class="nav-icon bi bi-file-earmark-plus"></i>
                                <p class="ms-2">Orderan Masuk</p>
                            </a>
                        </li>
                        <li class="nav-item mb-1">
                            <a href="{{ route('employee.orders.index', ['delivery' => 1]) }}" class="nav-link {{ request('delivery') == '1' ? 'active text-white bg-primary' : 'text-light' }}">
                                <i class="nav-icon bi bi-truck"></i>
                                <p class="ms-2">Antar Jemput</p>
                            </a>
                        </li>
                        <li class="nav-item mb-1">
                            <a href="{{ route('employee.orders.scan') }}" class="nav-link {{ request()->routeIs('employee.orders.scan') ? 'active text-white bg-primary' : 'text-light' }}">
                                <i class="nav-icon bi bi-qr-code-scan"></i>
                                <p class="ms-2">Scan QR / Cari</p>
                            </a>
                        </li>
                        <li class="nav-item {{ request('queue') == '1' ? 'menu-open' : '' }} mb-1">
                            <a href="#" class="antrian-toggle nav-link {{ request('queue') == '1' ? 'active text-white bg-primary' : 'text-light' }}">
                                <i class="nav-icon bi bi-display"></i>
                                <p class="ms-2 mb-0">Monitor Antrian</p>
                                <i class="nav-arrow bi bi-chevron-right" style="transition: transform 0.3s ease; transform: {{ request('queue') == '1' ? 'rotate(90deg)' : 'rotate(0deg)' }}; font-size: 0.8rem; margin-left: auto;"></i>
                            </a>
                            <ul class="nav nav-treeview" style="display: {{ request('queue') == '1' ? 'block' : 'none' }}; list-style: none;">
                                <li class="nav-item">
                                    <a href="{{ route('employee.orders.index', ['queue' => 1]) }}" class="nav-link {{ request('queue') == '1' && !request('category') ? 'active' : '' }}">
                                        Semua Antrian
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('employee.orders.index', ['queue' => 1, 'category' => 'cleaning']) }}" class="nav-link {{ request('queue') == '1' && request('category') == 'cleaning' ? 'active' : '' }}">
                                        Cuci Sepatu
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('employee.orders.index', ['queue' => 1, 'category' => 'repair']) }}" class="nav-link {{ request('queue') == '1' && request('category') == 'repair' ? 'active' : '' }}">
                                        Reparasi Sepatu
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item mb-1">
                            <a href="{{ route('employee.inventories.index') }}" class="nav-link {{ request()->routeIs('employee.inventories.*') ? 'active text-white bg-primary' : 'text-light' }}">
                                <i class="nav-icon bi bi-box-seam"></i>
                                <p class="ms-2">Stok Barang</p>
                            </a>
                        </li>
                        <li class="nav-item {{ request()->routeIs('employee.reports.*') ? 'menu-open' : '' }}">
                            <a href="#" class="laporan-toggle nav-link {{ request()->routeIs('employee.reports.*') ? 'active text-white bg-primary' : 'text-light' }}">
                                <i class="nav-icon bi bi-bar-chart-line"></i>
                                <p class="ms-2 mb-0">Laporan</p>
                                <i class="nav-arrow bi bi-chevron-right" style="transition: transform 0.3s ease; transform: {{ request()->routeIs('employee.reports.*') ? 'rotate(90deg)' : 'rotate(0deg)' }}; font-size: 0.8rem; margin-left: auto;"></i>
                            </a>
                            <ul class="nav nav-treeview" style="display: {{ request()->routeIs('employee.reports.*') ? 'block' : 'none' }}; list-style: none;">
                                <li class="nav-item">
                                    <a href="{{ route('employee.reports.index') }}#ringkasan" class="nav-link" onclick="window.location.hash = 'ringkasan';">
                                        Ringkasan Kinerja
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('employee.reports.index') }}#tugas" class="nav-link" onclick="window.location.hash = 'tugas';">
                                        Tugas Aktif
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('employee.reports.index') }}#riwayat" class="nav-link" onclick="window.location.hash = 'riwayat';">
                                        Riwayat Kerja
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('employee.reports.index') }}#absensi" class="nav-link" onclick="window.location.hash = 'absensi';">
                                        Rekap Absensi
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('employee.reports.index') }}#rating" class="nav-link" onclick="window.location.hash = 'rating';">
                                        Penilaian Pelanggan
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('employee.reports.index') }}#statistik" class="nav-link" onclick="window.location.hash = 'statistik';">
                                        Statistik Kerja
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('employee.reports.index') }}#antar_jemput" class="nav-link" onclick="window.location.hash = 'antar_jemput';">
                                        Antar Jemput
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </nav>
            </div>
        </aside>

        <!-- Main Wrapper -->
        <main class="app-main py-4">
            <div class="app-content">
                <div class="container-fluid">
                    <h1 class="fw-bold text-dark fs-3 mb-3">@yield('page_title')</h1>
                    @yield('content')
                </div>
            </div>
        </main>
    </div>

    <!-- AdminLTE & Bootstrap JS Dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/browser/overlayscrollbars.browser.es6.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@4.0.0/dist/js/adminlte.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Sidebar Treeview Toggle
        document.addEventListener('DOMContentLoaded', function() {
            // Laporan Toggle
            const laporanToggle = document.querySelector('.laporan-toggle');
            if (laporanToggle) {
                laporanToggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation(); // Prevent propagation to avoid library conflicts
                    
                    const parentLi = this.closest('.nav-item');
                    const submenu = parentLi.querySelector('.nav-treeview');
                    const arrow = this.querySelector('.nav-arrow');
                    
                    if (parentLi.classList.contains('menu-open')) {
                        parentLi.classList.remove('menu-open');
                        if (submenu) submenu.style.display = 'none';
                        if (arrow) arrow.style.transform = 'rotate(0deg)';
                    } else {
                        parentLi.classList.add('menu-open');
                        if (submenu) submenu.style.display = 'block';
                        if (arrow) arrow.style.transform = 'rotate(90deg)';
                    }
                });
            }

            // Monitor Antrian Toggle
            const antrianToggle = document.querySelector('.antrian-toggle');
            if (antrianToggle) {
                antrianToggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation(); // Prevent propagation to avoid library conflicts
                    
                    const parentLi = this.closest('.nav-item');
                    const submenu = parentLi.querySelector('.nav-treeview');
                    const arrow = this.querySelector('.nav-arrow');
                    
                    if (parentLi.classList.contains('menu-open')) {
                        parentLi.classList.remove('menu-open');
                        if (submenu) submenu.style.display = 'none';
                        if (arrow) arrow.style.transform = 'rotate(0deg)';
                    } else {
                        parentLi.classList.add('menu-open');
                        if (submenu) submenu.style.display = 'block';
                        if (arrow) arrow.style.transform = 'rotate(90deg)';
                    }
                });
            }
        });

        // Notification Logic
        const notificationBtn = document.getElementById('notification-btn');
        const notificationDropdown = document.getElementById('notification-dropdown');
        const notificationList = document.getElementById('notification-list');
        const notificationBadge = document.getElementById('notification-badge');

        notificationBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            fetchNotifications();
        });

        async function fetchNotifications() {
            try {
                const response = await fetch('{{ route('notifications.recent') }}');
                const data = await response.json();
                
                if (data.unread_count > 0) {
                    notificationBadge.innerText = data.unread_count;
                    notificationBadge.style.display = 'inline-block';
                } else {
                    notificationBadge.style.display = 'none';
                }

                if (data.recent.length === 0) {
                    notificationList.innerHTML = '<div class="p-4 text-center text-muted">Tidak ada notifikasi</div>';
                } else {
                    notificationList.innerHTML = data.recent.map(n => `
                        <div onclick="markAsRead('${n.id}', '${n.data.url}')" class="p-3 border-bottom" style="cursor: pointer; ${!n.read_at ? 'background-color: rgba(249, 115, 22, 0.03);' : ''}">
                            <div class="d-flex gap-2">
                                <div class="text-warning"><i class="bi bi-circle-fill" style="font-size: 0.6rem;"></i></div>
                                <div>
                                    <div class="fw-bold small text-dark">${n.data.title}</div>
                                    <div class="text-secondary small mt-1" style="font-size: 0.8rem;">${n.data.message}</div>
                                    <div class="text-muted small mt-1" style="font-size: 0.7rem;">${n.created_at}</div>
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

        // Auto polling notifications
        setInterval(fetchNotifications, 30000);
        fetchNotifications();

        // SweetAlert2 confirm handler
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('form').forEach(form => {
                const onsubmitAttr = form.getAttribute('onsubmit');
                if (onsubmitAttr && onsubmitAttr.includes('confirm(')) {
                    let match = onsubmitAttr.match(/confirm\(['"](.*?)['"]\)/);
                    let message = match ? match[1] : 'Apakah Anda yakin?';
                    form.removeAttribute('onsubmit');
                    
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
                            confirmButtonColor: '#0d6efd',
                            cancelButtonColor: '#6c757d',
                            confirmButtonText: 'Ya, Lanjutkan',
                            cancelButtonText: 'Batal'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                form.dataset.swalConfirmed = 'true';
                                form.submit();
                            }
                        });
                    });
                }
            });
        });

        // Theme Toggle Script
        document.addEventListener('DOMContentLoaded', function() {
            const themeToggleBtn = document.getElementById('theme-toggle');
            const themeToggleIcon = document.getElementById('theme-toggle-icon');
            
            function updateThemeIcon(theme) {
                if (theme === 'dark') {
                    themeToggleIcon.className = 'bi bi-sun-fill fs-5';
                } else {
                    themeToggleIcon.className = 'bi bi-moon-fill fs-5';
                }
            }
            
            // Sync initial state of the icon
            const currentTheme = document.documentElement.getAttribute('data-bs-theme') || 'light';
            updateThemeIcon(currentTheme);
            
            if (themeToggleBtn) {
                themeToggleBtn.addEventListener('click', function() {
                    const activeTheme = document.documentElement.getAttribute('data-bs-theme') || 'light';
                    const newTheme = activeTheme === 'dark' ? 'light' : 'dark';
                    
                    document.documentElement.setAttribute('data-bs-theme', newTheme);
                    localStorage.setItem('employee-theme', newTheme);
                    updateThemeIcon(newTheme);
                });
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
