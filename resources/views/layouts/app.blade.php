<!DOCTYPE html>
<html lang="fr" data-bs-theme="{{ request()->cookie('theme', 'dark') }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'CDC') — Documents Internes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <style>
        :root { --cdc-accent: #6366f1; }
        body { min-height: 100vh; }
        .cdc-sidebar {
            width: 240px; position: fixed; top: 0; bottom: 0; left: 0;
            background: var(--bs-body-bg); border-right: 1px solid var(--bs-border-color);
            display: flex; flex-direction: column; padding: 1rem .75rem; z-index: 1040;
            transition: transform .2s;
        }
        .cdc-brand { display:flex; align-items:center; gap:.6rem; font-weight:700; font-size:1.15rem; padding:.25rem .5rem 1rem; }
        .cdc-brand-icon { width:32px; height:32px; border-radius:8px; background:var(--cdc-accent); color:#fff; display:flex; align-items:center; justify-content:center; }
        .cdc-nav-label { font-size:.7rem; text-transform:uppercase; letter-spacing:.08em; color:var(--bs-secondary-color); padding:.75rem .5rem .25rem; }
        .cdc-link { display:flex; align-items:center; gap:.6rem; padding:.5rem .6rem; border-radius:8px; color:var(--bs-body-color); text-decoration:none; font-size:.9rem; margin-bottom:1px; }
        .cdc-link:hover { background:var(--bs-secondary-bg); }
        .cdc-link.active { background:var(--cdc-accent); color:#fff; }
        .cdc-link i { width:18px; text-align:center; }
        .cdc-main { margin-left:240px; min-height:100vh; display:flex; flex-direction:column; }
        .cdc-topbar { position:sticky; top:0; z-index:1030; height:56px; display:flex; align-items:center; justify-content:space-between; padding:0 1.25rem; background:var(--bs-body-bg); border-bottom:1px solid var(--bs-border-color); }
        .cdc-content { padding:1.5rem; flex:1; }
        .cdc-footer-user { margin-top:auto; border-top:1px solid var(--bs-border-color); padding-top:.75rem; }
        @media (max-width: 768px) {
            .cdc-sidebar { transform:translateX(-100%); }
            .cdc-sidebar.open { transform:translateX(0); }
            .cdc-main { margin-left:0; }
        }
    </style>
    @stack('head')
</head>
<body>
@auth
<aside class="cdc-sidebar" id="cdcSidebar">
    <div class="cdc-brand">
        <span class="cdc-brand-icon"><i class="bi bi-file-earmark-text"></i></span>
        <span>CDC</span>
    </div>
    <nav class="flex-grow-1 overflow-auto">
        <a href="{{ route('dashboard') }}" class="cdc-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"><i class="bi bi-grid-1x2"></i> Tableau de bord</a>
        <a href="{{ route('documents.index') }}" class="cdc-link {{ request()->routeIs('documents.*') ? 'active' : '' }}"><i class="bi bi-file-earmark-text"></i> Documents</a>

        @if(auth()->user()->isAdmin() || auth()->user()->isManager())
        <div class="cdc-nav-label">Gestion</div>
        <a href="{{ route('personnes.index') }}" class="cdc-link {{ request()->routeIs('personnes.*') ? 'active' : '' }}"><i class="bi bi-people"></i> Personnes</a>
        <a href="{{ route('reports.index') }}" class="cdc-link {{ request()->routeIs('reports.*') ? 'active' : '' }}"><i class="bi bi-bar-chart"></i> Rapports</a>
        @endif

        @if(auth()->user()->isAdmin())
        <div class="cdc-nav-label">Administration</div>
        <a href="{{ route('services.index') }}" class="cdc-link {{ request()->routeIs('services.*') ? 'active' : '' }}"><i class="bi bi-diagram-3"></i> Services</a>
        <a href="{{ route('budgets.index') }}" class="cdc-link {{ request()->routeIs('budgets.*') ? 'active' : '' }}"><i class="bi bi-wallet2"></i> Budgets</a>
        <a href="{{ route('users.index') }}" class="cdc-link {{ request()->routeIs('users.*') ? 'active' : '' }}"><i class="bi bi-person-badge"></i> Utilisateurs</a>
        @endif

        @if(strtolower(auth()->user()->email) === strtolower(config('services.auth.super_admin')))
        <a href="{{ route('admin.whitelist') }}" class="cdc-link {{ request()->routeIs('admin.*') ? 'active' : '' }}"><i class="bi bi-shield-lock"></i> Accès</a>
        @endif
    </nav>

    <div class="cdc-footer-user">
        <div class="d-flex align-items-center gap-2 px-2 mb-2">
            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width:34px;height:34px;flex-shrink:0;">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div class="overflow-hidden">
                <div class="small fw-semibold text-truncate">{{ auth()->user()->name }}</div>
                <div class="text-secondary text-truncate" style="font-size:.72rem;">
                    <span class="badge bg-secondary text-capitalize">{{ auth()->user()->role }}</span>
                </div>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-sm btn-outline-danger w-100"><i class="bi bi-box-arrow-right"></i> Déconnexion</button>
        </form>
    </div>
</aside>

<div class="cdc-main">
    <header class="cdc-topbar">
        <div class="d-flex align-items-center gap-2">
            <button class="btn btn-sm btn-outline-secondary d-md-none" onclick="document.getElementById('cdcSidebar').classList.toggle('open')"><i class="bi bi-list"></i></button>
            <span class="fw-semibold">@yield('page-title', 'Tableau de bord')</span>
        </div>
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-sm btn-outline-secondary" id="themeToggle" title="Changer de thème"><i class="bi bi-moon-stars"></i></button>

            <div class="dropdown">
                <button class="btn btn-sm btn-outline-secondary position-relative" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-bell"></i>
                    @if($topbarNonLues > 0)
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">{{ $topbarNonLues }}</span>
                    @endif
                </button>
                <div class="dropdown-menu dropdown-menu-end p-0" style="width:320px;max-height:420px;overflow:auto;">
                    <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
                        <strong class="small">Notifications</strong>
                        @if($topbarNonLues > 0)
                        <form method="POST" action="{{ route('notifications.toutLire') }}" class="m-0">@csrf
                            <button class="btn btn-link btn-sm p-0 text-decoration-none">Tout lire</button>
                        </form>
                        @endif
                    </div>
                    @forelse($topbarNotifications as $notif)
                        <a href="{{ route('notifications.lire', $notif) }}" class="dropdown-item small py-2 border-bottom {{ $notif->lu ? '' : 'fw-semibold' }}" style="white-space:normal;">
                            <div class="d-flex gap-2">
                                <i class="bi {{ $notif->type === 'validation' ? 'bi-check-circle text-success' : ($notif->type === 'refus' ? 'bi-x-circle text-danger' : 'bi-info-circle text-primary') }}"></i>
                                <div>
                                    {{ $notif->message }}
                                    <div class="text-secondary" style="font-size:.7rem;">{{ $notif->created_at->diffForHumans() }}</div>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="text-center text-secondary small py-4">Aucune notification</div>
                    @endforelse
                    <a href="{{ route('notifications.index') }}" class="dropdown-item text-center small py-2">Voir tout</a>
                </div>
            </div>
        </div>
    </header>

    @if(session('success') || session('error') || $errors->any())
    <div class="px-4 pt-3">
        @if(session('success'))<div class="alert alert-success py-2 mb-2"><i class="bi bi-check-circle"></i> {{ session('success') }}</div>@endif
        @if(session('error'))<div class="alert alert-danger py-2 mb-2"><i class="bi bi-exclamation-triangle"></i> {{ session('error') }}</div>@endif
        @if($errors->any())
        <div class="alert alert-danger py-2 mb-2">
            <ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
        @endif
    </div>
    @endif

    <main class="cdc-content">
        @yield('content')
    </main>
</div>
@endauth

@guest
    @yield('content')
@endguest

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Dark mode commutable, persisté via cookie.
    (function () {
        const html = document.documentElement;
        const btn = document.getElementById('themeToggle');
        function setTheme(t) {
            html.setAttribute('data-bs-theme', t);
            document.cookie = 'theme=' + t + ';path=/;max-age=31536000';
            if (btn) btn.innerHTML = t === 'dark' ? '<i class="bi bi-sun"></i>' : '<i class="bi bi-moon-stars"></i>';
        }
        setTheme(html.getAttribute('data-bs-theme') || 'dark');
        btn?.addEventListener('click', () => setTheme(html.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark'));
    })();
</script>
@stack('scripts')
</body>
</html>
