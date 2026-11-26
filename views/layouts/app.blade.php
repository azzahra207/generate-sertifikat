<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('page-title', 'Beranda') | MySerti</title>
    <link rel="icon" href="{{ asset('img/logo.webp') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Great+Vibes&display=swap">
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <script src="{{ asset('js/theme.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        /* kartu di halaman backend (dashboard, organisasi, member) */
        .sk-wrap .card { background: var(--sk-surface); border: 1px solid var(--sk-line); border-radius: 1rem; padding: 1.5rem; margin-bottom: 1rem; box-shadow: none; }
        .sk-pagehead { margin-bottom: 1.25rem; }
        .sk-pagehead h1 { font-size: 1.4rem; font-weight: 800; margin: 0; letter-spacing: -.3px; }
        .sk-crumb { font-size: 13px; color: var(--sk-muted); }
        .sk-crumb a { color: var(--sk-muted); text-decoration: none; }
        .sk-crumb a:hover { color: var(--sk-primary); }
        .sk-topbar .dropdown-item.active, .sk-topbar .dropdown-item:active { background: var(--sk-primary-tint); color: var(--sk-primary); }
    </style>
    @stack('styles')
</head>
<body>

{{-- Menu atas: desain dari app.html + menu dari layout backend --}}
<nav class="navbar navbar-expand-lg sk-topbar">
    <div class="container-xl gap-2">
        <a class="logo me-3" href="{{ url('/') }}"><img src="{{ asset('img/logo.webp') }}" alt=""><b>My<span>Serti</span></b></a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#topnav" aria-controls="topnav" aria-expanded="false" aria-label="Menu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="topnav">
            <div class="navbar-nav gap-1 me-auto py-2 py-lg-0">
                @auth
                    <a class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}" href="{{ url('/dashboard') }}"><i class="bi bi-speedometer2"></i>Dashboard</a>

                    <div class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->is('organisasi*') ? 'active' : '' }}" href="#" data-bs-toggle="dropdown" aria-expanded="false"><i class="bi bi-building"></i>Organisasi</a>
                        <ul class="dropdown-menu shadow-sm">
                            <li><a class="dropdown-item {{ request()->is('organisasi') ? 'active' : '' }}" href="{{ url('/organisasi') }}">Daftar Organisasi</a></li>
                            <li><a class="dropdown-item {{ request()->is('organisasi/member') ? 'active' : '' }}" href="{{ url('/organisasi/member') }}">Member</a></li>
                        </ul>
                    </div>

                    <div class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->is('sertifikat*') ? 'active' : '' }}" href="#" data-bs-toggle="dropdown" aria-expanded="false"><i class="bi bi-award"></i>Sertifikat</a>
                        <ul class="dropdown-menu shadow-sm">
                            <li><a class="dropdown-item {{ request()->is('sertifikat/template') ? 'active' : '' }}" href="{{ url('/sertifikat/template') }}">Template</a></li>
                            <li><a class="dropdown-item {{ request()->is('sertifikat/generate') ? 'active' : '' }}" href="{{ url('/sertifikat/generate') }}">Generate</a></li>
                            <li><a class="dropdown-item {{ request()->is('sertifikat/penerima') ? 'active' : '' }}" href="{{ url('/sertifikat/penerima') }}">Penerima</a></li>
                        </ul>
                    </div>

                    <a class="nav-link {{ request()->is('users*') ? 'active' : '' }}" href="{{ url('/users') }}"><i class="bi bi-people"></i>Pengguna</a>
                @else
                    <a class="nav-link" href="{{ url('/') }}#cara-kerja">Cara Kerja</a>
                    <a class="nav-link" href="{{ url('/') }}#fitur">Fitur</a>
                    <a class="nav-link" href="{{ url('/') }}#faq">Tutorial &amp; FAQ</a>
                @endauth
            </div>

            <div class="d-flex align-items-center gap-2 pb-2 pb-lg-0">
                <div data-theme-toggle></div>
                @auth
                    <div class="dropdown sk-notif">
                        <button class="btn btn-light border btn-sm" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Notifikasi"><i class="bi bi-bell"></i></button>
                        <div class="dropdown-menu dropdown-menu-end shadow p-0">
                            <div class="px-3 py-2 border-bottom fw-bold small">Notifikasi</div>
                            <div class="px-3 py-4 text-center text-muted small">Belum ada notifikasi.</div>
                        </div>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-link text-reset text-decoration-none p-0 d-flex align-items-center gap-2 fw-semibold" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Menu akun">
                            <span class="sk-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                            <span class="d-lg-none d-xl-inline text-truncate" style="max-width:140px">{{ auth()->user()->name }}</span>
                            <i class="bi bi-chevron-down small"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                            <li><span class="dropdown-item-text small text-muted">{{ auth()->user()->email }}</span></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="{{ url('/pengaturan') }}"><i class="bi bi-gear me-2"></i>Pengaturan</a></li>
                            <li><a class="dropdown-item" href="{{ url('/') }}#faq"><i class="bi bi-question-circle me-2"></i>Tutorial</a></li>
                            <li><a class="dropdown-item text-danger" href="{{ url('/logout') }}"><i class="bi bi-box-arrow-right me-2"></i>Keluar</a></li>
                        </ul>
                    </div>
                @else
                    <a class="btn btn-light border" href="{{ route('login') }}">Masuk</a>
                    <a class="btn btn-primary" href="{{ route('register') }}">Daftar</a>
                @endauth
            </div>
        </div>
    </div>
</nav>

@if($__env->hasSection('landing') || $__env->hasSection('editor'))
    {{-- landing (home) dan editor sertifikat mengatur lebarnya sendiri --}}
    @yield('content')
@else
    <main class="sk-main">
        <div class="sk-wrap">
            @hasSection('page-title')
                <div class="sk-pagehead">
                    <h1>@yield('page-title')</h1>
                    <div class="sk-crumb"><a href="{{ url('/') }}">Home</a> / @yield('breadcrumb')</div>
                </div>
            @endif
            @yield('content')
        </div>
    </main>
@endif

<script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

@if(session('success'))
<script>
    Swal.fire({ title: "Berhasil", text: @json(session('success')), icon: "success", confirmButtonText: "Ok" });
</script>
@endif
@if(session('error'))
<script>
    Swal.fire({ title: "Gagal", text: @json(session('error')), icon: "error", confirmButtonText: "Ok" });
</script>
@endif

@stack('scripts')
</body>
</html>
