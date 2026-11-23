<!DOCTYPE html>
<html lang="zxx">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>MySerti | Create Sertifikat</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <link rel="icon" type="image/svg" href="{{asset('images/Logo MySerti.png')}}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,400;14..32,500;14..32,600;14..32,700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --sidebar-width: 260px;
            --sidebar-collapsed-width: 78px;
            --sidebar-bg: #ffffff;
            --sidebar-border: #e2e8f0;
            --sidebar-text: #617592;
            --sidebar-text-active: #1d46ce;
            --sidebar-hover-bg: #f0f9f4;
            --sidebar-active-bg: #e8f5ee;
            --sidebar-transition: 0.3s cubic-bezier(0.2, 0.9, 0.4, 1.1);
            --topbar-height: 68px;
            --emas-tua:#ca8b16;
            --blue-light:rgb(18, 171, 241);
            --yellow:rgb(243, 227, 3);
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #f5f7fa;
            min-height: 100vh;
            padding-left: var(--sidebar-width);
            transition: padding-left var(--sidebar-transition);
        }

        /* ===== SIDEBAR ===== */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            height: 100dvh;
            background: var(--sidebar-bg);
            border-right: 1px solid var(--sidebar-border);
            display: flex;
            flex-direction: column;
            z-index: 1100;
            transition: width var(--sidebar-transition), transform var(--sidebar-transition);
            overflow: hidden;
        }

        /* Brand */
        .text-brand {
            font-family: 'SF Pro Rounded', 'Nunito', 'Quicksand', 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            color:var(--yellow);
            font-weight:bold;
        }
        .text-brand > span {
            color:var(--blue-light);
        }
        .sidebar-brand {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            padding: 0 16px;
            height: var(--topbar-height);
            flex-shrink: 0;
            border-bottom: 1px solid var(--sidebar-border);
        }

        .sidebar-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 0;
            text-decoration: none;
            color: #1e2a3e;
            font-weight: 700;
        }
        .sidebar-logo > span {
            font-size:2em;
        }

        .logoNav {
            height: 40px;
            width: 40px;         
            object-fit: contain; 
            flex-shrink: 0;
        }

        .sidebar-collapse-btn,
        .sidebar-close-btn {
            background: #f1f5f9;
            border: none;
            color: #2c7a4d;
            width: 32px;
            height: 32px;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.2s;
            flex-shrink: 0;
            align-items: center;
            justify-content: center;
        }
        .sidebar-collapse-btn { display: flex; }
        .sidebar-close-btn    { display: none; font-size: 1rem; }
        .sidebar-collapse-btn:hover,
        .sidebar-close-btn:hover { background: #e2e8f0; }
        .sidebar-collapse-btn i { font-size: 0.85rem; transition: transform 0.3s; }

        /* Nav */
        .sidebar-nav {
            flex: 1;
            min-height: 0;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 12px 0;
        }
        .sidebar-nav::-webkit-scrollbar { width: 6px; }
        .sidebar-nav::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }

        .sidebar-menu { list-style: none; margin: 0; padding: 0 12px; }
        .sidebar-item { margin-bottom: 4px; }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 11px 14px;
            border-radius: 10px;
            text-decoration: none;
            color: var(--sidebar-text);
            font-weight: 500;
            font-size: 0.94rem;
            white-space: nowrap;
            position: relative;
            transition: background 0.2s, color 0.2s;
        }
        .sidebar-link:hover { background: var(--sidebar-hover-bg); color: var(--sidebar-text-active); }

        .sidebar-item.active > .sidebar-link {
            background: var(--sidebar-active-bg);
            color: var(--sidebar-text-active);
            font-weight: 600;
        }
        .sidebar-item.active > .sidebar-link::before {
            content: '';
            position: absolute;
            left: 0;
            top: 20%;
            height: 60%;
            width: 3px;
            background: var(--sidebar-text-active);
            border-radius: 0 3px 3px 0;
        }

        .sidebar-link > i:first-child {
            width: 20px;
            text-align: center;
            font-size: 1.05rem;
            flex-shrink: 0;
        }
        .sidebar-link span { flex: 1; }

        .submenu-arrow { font-size: 0.7rem; transition: transform 0.3s; }
        .sidebar-item.submenu-open > .sidebar-link .submenu-arrow { transform: rotate(180deg); }

        /* Submenu */
        .sidebar-submenu {
            list-style: none;
            padding: 0;
            margin: 0;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
        }
        .sidebar-item.submenu-open .sidebar-submenu { max-height: 300px; padding: 4px 0; }

        .sidebar-submenu li a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 14px 9px 46px;
            text-decoration: none;
            color: #64748b;
            font-size: 0.88rem;
            border-radius: 8px;
            white-space: nowrap;
            transition: background 0.2s, color 0.2s;
        }
        .sidebar-submenu li a:hover { background: var(--sidebar-hover-bg); color: var(--sidebar-text-active); }
        .sidebar-submenu li a i { font-size: 0.4rem; color: #94a3b8; }

        /* Footer */
        .sidebar-footer { padding: 14px 16px; border-top: 1px solid var(--sidebar-border); flex-shrink: 0; }
        .sidebar-user { display: flex; align-items: center; gap: 10px; }
        .sidebar-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: linear-gradient(150deg, #4a87af, #2c547a);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            flex-shrink: 0;
        }
        .sidebar-user-info { overflow: hidden; white-space: nowrap; }
        .sidebar-user-name { font-weight: 600; font-size: 0.9rem; color: #1e2a3e; }
        .sidebar-user-role { font-size: 0.75rem; color: #94a3b8; }

        /* Overlay */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.45);
            backdrop-filter: blur(2px);
            z-index: 1090;
        }

        /* ===== TOPBAR (di dalam area konten) ===== */
        .topbar {
            position: sticky;
            top: 0;
            height: var(--topbar-height);
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--sidebar-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 28px;
            z-index: 900;
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .topbar-title {
            font-size: 1.05rem;
            font-weight: 600;
            color: #1e2a3e;
            margin: 0;
        }

        .topbar-breadcrumb {
            font-size: 0.85rem;
            color: #94a3b8;
        }
        .topbar-breadcrumb a { color: #64748b; text-decoration: none; }
        .topbar-breadcrumb a:hover { color: #2c6e7a; }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .topbar-icon-btn {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: transparent;
            border: 1px solid transparent;
            color: #64748b;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }
        .topbar-icon-btn:hover { background: #f1f5f9; color: #2c7a4d; }

        .topbar-icon-btn .badge-dot {
            position: absolute;
            top: 8px;
            right: 8px;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #ef4444;
            border: 2px solid white;
        }

        .topbar-profile {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 6px 12px 6px 6px;
            border-radius: 40px;
            cursor: pointer;
            transition: background 0.2s;
            border: 1px solid transparent;
        }
        .topbar-profile:hover { background: #f1f5f9; border-color: #e2e8f0; }

        .topbar-profile .sidebar-avatar {
            width: 32px;
            height: 32px;
            font-size: 0.85rem;
        }

        .topbar-profile-info { line-height: 1.2; }
        .topbar-profile-name { font-size: 0.85rem; font-weight: 600; color: #1e2a3e; }
        .topbar-profile-role { font-size: 0.72rem; color: #94a3b8; }

        /* Tombol hamburger (hanya tampil di mobile) */
        .topbar-mobile-toggle {
            display: none;
            background: transparent;
            border: none;
            color: #2c7a4d;
            font-size: 1.25rem;
            cursor: pointer;
            width: 40px;
            height: 40px;
            border-radius: 8px;
        }
        .topbar-mobile-toggle:hover { background: #f1f5f9; }
        .swal2-container {
    z-index: 99999 !important;
}
        /* ===== MAIN CONTENT ===== */
        .main-wrapper {
            min-height: calc(100vh - var(--topbar-height));
        }

        .main-content {
            max-width: 1400px;
            margin: 0 auto;
        }

        .card {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
            border: 1px solid #f1f5f9;
        }

        .card-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.25rem;
        }

        /* ===== DESKTOP: mode collapsed (hanya >992px) ===== */
        @media (min-width: 993px) {
            body.sidebar-collapsed { padding-left: var(--sidebar-collapsed-width); }
            body.sidebar-collapsed .sidebar { width: var(--sidebar-collapsed-width); }

            /* Brand: logo di atas, tombol di bawah, semua center */
            body.sidebar-collapsed .sidebar-brand {
                flex-direction: column;
                justify-content: center;
                gap: 8px;
                height: auto;
                min-height: var(--topbar-height);
                padding: 12px 0;
            }
            body.sidebar-collapsed .sidebar-logo-text { display: none; }
            body.sidebar-collapsed .sidebar-collapse-btn i { transform: rotate(180deg); }

            /* Menu: ikon center */
            body.sidebar-collapsed .sidebar-menu { padding: 0 10px; }
            body.sidebar-collapsed .sidebar-link {
                justify-content: center;
                gap: 0;
                padding: 11px 0;
            }
            body.sidebar-collapsed .sidebar-link span,
            body.sidebar-collapsed .sidebar-link .submenu-arrow { display: none; }
            body.sidebar-collapsed .sidebar-submenu { display: none; }
            body.sidebar-collapsed .sidebar-item.active > .sidebar-link::before { left: -10px; }

            /* Footer */
            body.sidebar-collapsed .sidebar-footer { padding: 14px 0; }
            body.sidebar-collapsed .sidebar-user { justify-content: center; }
            body.sidebar-collapsed .sidebar-user-info { display: none; }
        }

        /* ===== MOBILE / TABLET (<=992px) ===== */
        @media (max-width: 992px) {
            body,
            body.sidebar-collapsed { padding-left: 0; }

            .sidebar {
                width: min(var(--sidebar-width), 85vw);
                transform: translateX(-100%);
                box-shadow: none;
            }
            body.sidebar-open .sidebar {
                transform: translateX(0);
                box-shadow: 4px 0 24px rgba(0, 0, 0, 0.15);
            }
            body.sidebar-open .sidebar-overlay { display: block; }
            body.sidebar-open { overflow: hidden; }

            .sidebar-collapse-btn { display: none; }
            .sidebar-close-btn { display: flex; }

            .topbar-mobile-toggle {
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .topbar { padding: 0 16px; }
            .main-content { padding: 16px; }
        }

        @media (max-width: 576px) {
            .topbar-title { font-size: 0.95rem; }
            .topbar-icon-btn { width: 36px; height: 36px; }
            .topbar-profile { padding: 4px; }
            .topbar-profile-info,
            .topbar-profile > .fa-chevron-down { display: none; }
            .topbar-breadcrumb { display: none; }
        }
    </style>

    @stack('styles')
</head>
<body>

    <!-- ========== SIDEBAR ========= -->
    <aside class="sidebar" id="mainSidebar">
        <!-- Brand -->
        <div class="sidebar-brand">
            <a href="/home" class="sidebar-logo">
                <img class="logoNav rounded-3" src="{{ asset('images/Logo MySerti.png') }}" alt="Logo">
                <span class="text-brand"><span>My</span>Serti</span>
            </a>
            <!-- Tombol collapse (desktop) -->
            <button class="sidebar-collapse-btn" id="sidebarCollapseBtn" title="Collapse">
                <i class="fas fa-angles-left"></i>
            </button>
            <!-- Tombol close (mobile) -->
            <button class="sidebar-close-btn" id="sidebarCloseBtn" title="Tutup">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Menu -->

        <nav class="sidebar-nav">
            <ul class="sidebar-menu">
                <li class="sidebar-item {{ request()->is('home') ? 'active' : '' }}">
                    <a href="/home" class="sidebar-link" data-tooltip="Home">
                        <i class="fas fa-home"></i>
                        <span>Home</span>
                    </a>
                </li>
                @if(auth()->check())
                <li class="sidebar-item {{ request()->is('dashboard') ? 'active' : '' }}">
                    <a href="/dashboard" class="sidebar-link" data-tooltip="Dashboard">
                        <i class="fas fa-gauge"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <li class="sidebar-item {{ request()->is('organisasi*') ? 'active' : '' }}">
                    <a href="#" class="sidebar-link has-submenu" data-tooltip="Organisasi">
                        <i class="fas fa-building"></i>
                        <span>Organisasi</span>
                        <i class="fas fa-chevron-down submenu-arrow"></i>
                    </a>
                    <ul class="sidebar-submenu">
                        <li><a href="/organisasi"><i class="far fa-circle"></i> Daftar Organisasi</a></li>
                        <li><a href="/organisasi/member"><i class="far fa-circle"></i> Member</a></li>
                    </ul>
                </li>

                <li class="sidebar-item {{ request()->is('sertifikat*') ? 'active' : '' }}">
                    <a href="#" class="sidebar-link has-submenu" data-tooltip="Sertifikat">
                        <i class="fas fa-certificate"></i>
                        <span>Sertifikat</span>
                        <i class="fas fa-chevron-down submenu-arrow"></i>
                    </a>
                    <ul class="sidebar-submenu">
                        <li><a href="/sertifikat/template"><i class="far fa-circle"></i> Template</a></li>
                        <li><a href="/sertifikat/generate"><i class="far fa-circle"></i> Generate</a></li>
                        <li><a href="/sertifikat/penerima"><i class="far fa-circle"></i> Penerima</a></li>
                    </ul>
                </li>

                <li class="sidebar-item {{ request()->is('users*') ? 'active' : '' }}">
                    <a href="/users" class="sidebar-link" data-tooltip="Pengguna">
                        <i class="fas fa-users"></i>
                        <span>Pengguna</span>
                    </a>
                </li>

                <li class="sidebar-item {{ request()->is('pengaturan*') ? 'active' : '' }}">
                    <a href="/pengaturan" class="sidebar-link" data-tooltip="Pengaturan">
                        <i class="fas fa-cog"></i>
                        <span>Pengaturan</span>
                    </a>
                </li>
                <li class="sidebar-item {{ request()->is('logout*') ? 'active' : '' }}">
                    <a href="/logout" class="sidebar-link" data-tooltip="Logout">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </a>
                </li>
                @else
                <li class="sidebar-item {{ request()->is('login*') ? 'active' : '' }}">
                    <a href="/login" class="sidebar-link" data-tooltip="Login">
                        <i class="fas fa-right-to-bracket"></i>
                        <span>Login</span>
                    </a>
                </li>
                @endif
            </ul>
        </nav>
         

        <!-- Footer / user info -->
        <div class="sidebar-footer">
            <div class="sidebar-user">
                <div class="sidebar-avatar">A</div>
                <div class="sidebar-user-info">
                    <div class="sidebar-user-name">Admin</div>
                    <div class="sidebar-user-role">Owner</div>
                </div>
            </div>
        </div>
    </aside>

    <!-- Overlay mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- ========== MAIN AREA ========= -->
    <div class="main-wrapper">

        <!-- ===== TOPBAR ===== -->
        <div class="topbar">
            <div class="topbar-left">
                <button class="topbar-mobile-toggle" id="sidebarMobileToggle" aria-label="Buka menu">
                    <i class="fas fa-bars"></i>
                </button>
                <div>
                    <h1 class="topbar-title">@yield('page-title', 'Dashboard')</h1>
                    <div class="topbar-breadcrumb">
                        <a href="/home">Home</a> <span>/</span> <span>@yield('breadcrumb', 'Dashboard')</span>
                    </div>
                </div>
            </div>

            <div class="topbar-right">
                <button class="topbar-icon-btn" title="Notifikasi">
                    <i class="far fa-bell"></i>
                    <span class="badge-dot"></span>
                </button>
                <button class="topbar-icon-btn" title="Bantuan">
                    <i class="far fa-question-circle"></i>
                </button>
                @if(auth()->check())
                <div class="topbar-profile">
                    <div class="sidebar-avatar">A</div>
                    <div class="topbar-profile-info">
                        <div class="topbar-profile-name">{{auth()->user()->name}}</div>
                    </div>
                    <i class="fas fa-chevron-down" style="font-size:0.7rem;color:#94a3b8;margin-left:4px;"></i>
                </div>
                @endif
            </div>
        </div>

        <!-- ===== KONTEN HALAMAN ===== -->
        <main class="main-content">
            @yield('content')
        </main>
    </div>

   @if(session('success'))
<script>
    Swal.fire({
        title: "Berhasil",
        text: @json(session('success')),
        icon: "success",
        confirmButtonText: "Ok"
    });
</script>
@endif
@if(session('error'))
<script>
    Swal.fire({
        title: "Gagal",
        text: @json(session('error')),
        icon: "error",
        confirmButtonText: "Ok"
    });
</script>
@endif

    <script>
        // ===== SIDEBAR =====
        const bodyEl = document.body;
        const isMobile = () => window.innerWidth <= 992;

        const sidebarCollapseBtn  = document.getElementById('sidebarCollapseBtn');
        const sidebarMobileToggle = document.getElementById('sidebarMobileToggle');
        const sidebarCloseBtn     = document.getElementById('sidebarCloseBtn');
        const sidebarOverlay      = document.getElementById('sidebarOverlay');

        const openSidebar  = () => bodyEl.classList.add('sidebar-open');
        const closeSidebar = () => bodyEl.classList.remove('sidebar-open');

        // Restore collapsed state (hanya berpengaruh di desktop berkat CSS)
        if (localStorage.getItem('sidebarCollapsed') === '1') {
            bodyEl.classList.add('sidebar-collapsed');
        }

        // Tooltip native saat collapsed (tidak terpotong overflow)
        document.querySelectorAll('.sidebar-link[data-tooltip]').forEach(link => {
            link.title = link.dataset.tooltip;
        });

        // Collapse (desktop)
        sidebarCollapseBtn?.addEventListener('click', () => {
            bodyEl.classList.toggle('sidebar-collapsed');
            localStorage.setItem('sidebarCollapsed',
                bodyEl.classList.contains('sidebar-collapsed') ? '1' : '0');
        });

        // Off-canvas (mobile)
        sidebarMobileToggle?.addEventListener('click', openSidebar);
        sidebarCloseBtn?.addEventListener('click', closeSidebar);
        sidebarOverlay?.addEventListener('click', closeSidebar);
        document.addEventListener('keydown', e => { if (e.key === 'Escape') closeSidebar(); });
        window.addEventListener('resize', () => { if (!isMobile()) closeSidebar(); });

        // Submenu accordion
        document.querySelectorAll('.sidebar-link.has-submenu').forEach(link => {
            link.addEventListener('click', e => {
                e.preventDefault();

                // Desktop collapsed: expand dulu
                if (!isMobile() && bodyEl.classList.contains('sidebar-collapsed')) {
                    bodyEl.classList.remove('sidebar-collapsed');
                    localStorage.setItem('sidebarCollapsed', '0');
                    return;
                }

                const parent  = link.parentElement;
                const wasOpen = parent.classList.contains('submenu-open');
                document.querySelectorAll('.sidebar-item.submenu-open')
                    .forEach(item => item.classList.remove('submenu-open'));
                if (!wasOpen) parent.classList.add('submenu-open');
            });
        });

        // Auto-open submenu sesuai route aktif
        document.querySelectorAll('.sidebar-item.active').forEach(item => {
            if (item.querySelector('.sidebar-submenu')) item.classList.add('submenu-open');
        });

        // Tutup sidebar mobile setelah klik link biasa
        document.querySelectorAll('.sidebar-link:not(.has-submenu), .sidebar-submenu a').forEach(link => {
            link.addEventListener('click', () => { if (isMobile()) closeSidebar(); });
        });
    </script>

    @stack('scripts')
</body>
</html>
