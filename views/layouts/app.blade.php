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
    <script>
    document.documentElement.setAttribute('data-bs-theme', localStorage.getItem('theme') || 'light');
    </script>

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
            --navbar-height: 68px;
            --border: #e2e8f0;
            --text: #1e2a3e;
            --muted: #64748b;
            --primary: #0f766e;
            --primary-tint: #e6f4f1;
            --blue-light:rgb(18, 171, 241);
            --yellow:rgb(243, 227, 3);
            --bg: #f5f7fa;
            --surface: #ffffff;
            --hover: #f1f5f9;
        }

        [data-bs-theme="dark"] {
            --bg: #0f1a1a;
            --surface: #162323;
            --border: #2a3b3b;
            --text: #e5eeee;
            --muted: #94a8a8;
            --hover: #1e3030;
            --primary-tint: #12332f;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
        }

        /* ===== NAVBAR ===== */
        .navbar-app {
            position: sticky;
            top: 0;
            z-index: 1030;
            min-height: var(--navbar-height);
            background: var(--surface);
            border-bottom: 1px solid var(--border);
        }

        /* Tailwind (CDN di atas) juga punya class "collapse" yang menyembunyikan elemen.
           Baris ini mengembalikan menu navbar Bootstrap supaya tetap tampil. */
        .navbar-collapse.collapse { visibility: visible; }

        .navbar-brand {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 1.5rem;
        }

        .text-brand {
            font-family: 'SF Pro Rounded', 'Nunito', 'Quicksand', 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            color:var(--yellow);
            font-weight:bold;
        }
        .text-brand > span {
            color:var(--blue-light);
        }
        .logoNav {
            width: 48px;
            height: auto;
            aspect-ratio: 1 / .8;
            object-fit: cover;
            object-position: top;
            flex-shrink: 0;
        }

        .navbar-app .nav-link {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 14px !important;
            border-radius: 10px;
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--muted);
        }
        .navbar-app .nav-link:hover { background: var(--hover); color: var(--text); }
        .navbar-app .nav-link.active { background: var(--primary-tint); color: var(--primary); }

        .navbar-app .dropdown-item.active,
        .navbar-app .dropdown-item:active { background: var(--primary-tint); color: var(--primary); }

        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
        }
        .btn-primary:hover { background-color: #115e59; border-color: #115e59; }

        .navbar-icon-btn {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            border: 1px solid var(--border);
            background: var(--surface);
            color: var(--muted);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }
        .navbar-icon-btn:hover { background: var(--hover); color: var(--primary); }
        .navbar-icon-btn .badge-dot {
            position: absolute;
            top: 8px;
            right: 8px;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #ef4444;
            border: 2px solid white;
        }

        .navbar-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #f59e0b;
            color: #1f1400;
            font-weight: 700;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* ===== JUDUL HALAMAN (di bawah navbar) ===== */
        .page-header {
            max-width: 1400px;
            margin: 0 auto;
            padding: 24px 28px 8px;
        }
        .page-title {
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--text);
            margin: 0;
        }
        .page-breadcrumb {
            font-size: 0.85rem;
            color: #94a3b8;
        }
        .page-breadcrumb a { color: var(--muted); text-decoration: none; }
        .page-breadcrumb a:hover { color: var(--primary); }

        .swal2-container {
            z-index: 99999 !important;
        }

        /* ===== MAIN CONTENT ===== */
        .main-wrapper {
            min-height: calc(100vh - var(--navbar-height));
        }

        .main-content {
            max-width: 1400px;
            margin: 0 auto;
        }

        .card {
            background: var(--surface);
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
            border: 1px solid var(--border);
        }

        .card-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.25rem;
        }

        /* ===== MOBILE / TABLET (<=992px) ===== */
        @media (max-width: 992px) {
            .navbar-app .navbar-collapse { padding: 8px 0 12px; }
            .page-header { padding: 16px 16px 4px; }
            .main-content { padding: 16px; }
        }
    </style>

    @stack('styles')
</head>
<body>

    <!-- ========== NAVBAR ========= -->
    <nav class="navbar navbar-expand-lg navbar-app">
        <div class="container-fluid px-4">
            <!-- Brand -->
            <a href="/home" class="navbar-brand">
                <img class="logoNav" src="{{ asset('img/logo.webp') }}" alt="Logo">
                <span class="text-brand"><span>My</span>Serti</span>
            </a>

            <!-- Tombol menu (mobile) -->
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu" aria-controls="navbarMenu" aria-expanded="false" aria-label="Buka menu">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarMenu">
                <!-- Menu kiri -->
                <ul class="navbar-nav me-auto gap-1">
                    <li class="nav-item">
                        <a href="/home" class="nav-link {{ request()->is('home') || request()->is('/') ? 'active' : '' }}">
                            <i class="fas fa-home"></i> Home
                        </a>
                    </li>
                    @if(auth()->check())
                    <li class="nav-item">
                        <a href="/dashboard" class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}">
                            <i class="fas fa-gauge"></i> Dashboard
                        </a>
                    </li>

                    <li class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle {{ request()->is('organisasi*') ? 'active' : '' }}" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-building"></i> Organisasi
                        </a>
                        <ul class="dropdown-menu shadow-sm">
                            <li><a class="dropdown-item {{ request()->is('organisasi') ? 'active' : '' }}" href="/organisasi">Daftar Organisasi</a></li>
                            <li><a class="dropdown-item {{ request()->is('organisasi/member') ? 'active' : '' }}" href="/organisasi/member">Member</a></li>
                        </ul>
                    </li>

                    <li class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle {{ request()->is('sertifikat*') ? 'active' : '' }}" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-certificate"></i> Sertifikat
                        </a>
                        <ul class="dropdown-menu shadow-sm">
                            <li><a class="dropdown-item {{ request()->is('sertifikat/template') ? 'active' : '' }}" href="/sertifikat/template">Template</a></li>
                            <li><a class="dropdown-item {{ request()->is('sertifikat/generate') ? 'active' : '' }}" href="/sertifikat/generate">Generate</a></li>
                            <li><a class="dropdown-item {{ request()->is('sertifikat/penerima') ? 'active' : '' }}" href="/sertifikat/penerima">Penerima</a></li>
                        </ul>
                    </li>

                    <li class="nav-item">
                        <a href="/users" class="nav-link {{ request()->is('users*') ? 'active' : '' }}">
                            <i class="fas fa-users"></i> Pengguna
                        </a>
                    </li>
                    @endif
                </ul>

                <!-- Menu kanan -->
                <div class="d-flex align-items-center gap-2">
                    <div class="mx-3 form-check form-switch">
                    <input class="form-check-input" type="checkbox" role="switch" id="themeSwitcher" onchange="updateTheme(this)">
                    <label class="form-check-label text-body" for="themeSwitcher">light</label>
                    </div>
                    @if(auth()->check())
                    <button class="navbar-icon-btn" title="Notifikasi">
                        <i class="far fa-bell"></i>
                        <span class="badge-dot"></span>
                    </button>

                    <div class="dropdown">
                        <button class="btn btn-link text-decoration-none text-dark d-flex align-items-center gap-2 fw-semibold p-0" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="navbar-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                            <span>{{ auth()->user()->name }}</span>
                            <i class="fas fa-chevron-down" style="font-size:0.7rem;color:#94a3b8;"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                            <li><span class="dropdown-item-text small text-muted">{{ auth()->user()->email }}</span></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="/pengaturan"><i class="fas fa-cog me-2"></i>Pengaturan</a></li>
                            <li><a class="dropdown-item text-danger" href="/logout"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                        </ul>
                    </div>
                    @else
                    <a href="/login" class="btn btn-light border">Masuk</a>
                    <a href="/register" class="btn btn-primary">Daftar</a>
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <!-- ========== MAIN AREA ========= -->
    <div class="main-wrapper">

        <!-- ===== JUDUL HALAMAN ===== -->
      @unless(request()->is('home') || request()->is('/'))
        <div class="page-header">
            <h1 class="page-title">@yield('page-title', 'Dashboard')</h1>
            <div class="page-breadcrumb">
                <a href="/home">Home</a> <span>/</span> <span>@yield('breadcrumb', 'Dashboard')</span>
            </div>
        </div>
        @endunless

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
function updateTheme(checkbox) {
        const isChecked = checkbox.checked; 
        const htmlElement = document.documentElement;
        const theme = isChecked ? 'dark' : 'light';
        htmlElement.setAttribute('data-bs-theme', theme);
        const label = document.querySelector('label[for="themeSwitcher"]');
        label.textContent = theme;
        localStorage.setItem('theme', theme);
    }
    document.addEventListener('DOMContentLoaded', () => {
        const savedTheme = localStorage.getItem('theme') || 'light';
        const htmlElement = document.documentElement;
        const checkbox = document.getElementById('themeSwitcher');
        const label = document.querySelector('label[for="themeSwitcher"]');
        htmlElement.setAttribute('data-bs-theme', savedTheme);
        checkbox.checked = savedTheme === 'dark';
        label.textContent = savedTheme;
    });
</script>

    @stack('scripts')
</body>
</html>
