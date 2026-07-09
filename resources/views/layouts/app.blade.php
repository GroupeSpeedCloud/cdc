<!DOCTYPE html>
<html lang="fr" data-bs-theme="{{ request()->cookie('theme', 'dark') }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Groupe Speed Cloud') — Facturation interne</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Titillium+Web:ital,wght@0,300;0,400;0,600;0,700;0,900;1,400&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <style>
        /* ==========================================================================
           SYSTÈME DE DESIGN — Material Design 3 × charte Groupe Speed Cloud
           Palette tonale dérivée de la couleur de marque #8a4dfd.
           ========================================================================== */
        :root {
            --md-font: 'Titillium Web', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;

            /* Rampe tonale "primary" (violet de marque) */
            --p0:#000000; --p10:#14082b; --p17:#210b4b; --p20:#270c5a; --p24:#2f0d6d; --p30:#390e8b;
            --p40:#4b0dbf; --p50:#5b0af5; --p60:#7a35fd; --p70:#9d6cf9; --p80:#bea0f8; --p90:#dfd1fa;
            --p92:#e6dafb; --p94:#ece4fc; --p95:#efe8fc; --p96:#f2edfd; --p98:#f9f6fe; --p99:#fcfbfe;
            --brand:#8a4dfd;

            /* Rampe "neutral" (surfaces) */
            --n0:#000000; --n4:#0a0a0b; --n6:#0f0e10; --n10:#19181b; --n12:#1e1d20; --n17:#2b292e;
            --n20:#323036; --n22:#37353b; --n30:#4b4851; --n40:#64606c; --n50:#7d7887; --n60:#97939f;
            --n80:#cbc9cf; --n87:#dddce0; --n90:#e5e4e7; --n92:#eae9ec; --n94:#efeff1; --n95:#f2f1f3;
            --n96:#f5f4f5; --n98:#fafafa; --n99:#fcfcfd; --n100:#ffffff;

            /* Rampe "neutral-variant" (contours, texte secondaire) */
            --nv30:#494059; --nv50:#796b94; --nv60:#9489a9; --nv80:#c9c4d4; --nv90:#e4e1ea;

            /* Erreur (rôles MD3 standard) */
            --err40:#b3261e; --err80:#f2b8b5; --err-c-light:#f9dedc; --err-on-c-light:#410e0b;
            --err-c-dark:#8c1d18; --err-on-c-dark:#f9dedc;

            --md-shape-xs: 8px; --md-shape-sm: 12px; --md-shape-md: 16px; --md-shape-lg: 20px; --md-shape-full: 999px;
            --md-elev-1: 0 1px 2px rgba(20,8,43,.08), 0 1px 3px 1px rgba(20,8,43,.06);
            --md-elev-2: 0 1px 2px rgba(20,8,43,.10), 0 2px 6px 2px rgba(20,8,43,.08);
            --md-elev-3: 0 4px 8px rgba(20,8,43,.12), 0 1px 3px rgba(20,8,43,.10);
            --md-state-hover: rgba(138,77,253,.08);
            --md-state-focus: rgba(138,77,253,.12);
        }

        /* ----- Rôles sémantiques par thème (pilotent aussi les variables Bootstrap) ----- */
        [data-bs-theme="light"] {
            --md-primary: var(--brand); --md-on-primary: #ffffff;
            --md-primary-text: var(--p40);
            --md-primary-container: var(--p90); --md-on-primary-container: var(--p10);
            --md-secondary-container: var(--nv90); --md-on-secondary-container: var(--nv30);
            --md-surface: var(--n99); --md-surface-dim: var(--n90);
            --md-surface-container-low: var(--n96); --md-surface-container: var(--n94);
            --md-surface-container-high: var(--n92); --md-surface-container-highest: var(--n90);
            --md-on-surface: var(--n10); --md-on-surface-variant: var(--nv30);
            --md-outline: var(--nv50); --md-outline-variant: var(--nv80);
            --md-error: var(--err40); --md-on-error: #ffffff;
            --md-error-container: var(--err-c-light); --md-on-error-container: var(--err-on-c-light);

            --bs-body-bg: var(--md-surface); --bs-body-color: var(--md-on-surface);
            --bs-border-color: var(--md-outline-variant); --bs-border-color-translucent: var(--md-outline-variant);
            --bs-secondary-bg: var(--md-surface-container); --bs-tertiary-bg: var(--md-surface-container-low);
            --bs-secondary-color: var(--md-on-surface-variant); --bs-emphasis-color: var(--md-on-surface);
            --bs-heading-color: var(--md-on-surface);
        }
        [data-bs-theme="dark"] {
            --md-primary: var(--p80); --md-on-primary: var(--p20);
            --md-primary-text: var(--p80);
            --md-primary-container: var(--p30); --md-on-primary-container: var(--p90);
            --md-secondary-container: var(--n30); --md-on-secondary-container: var(--nv90);
            --md-surface: var(--n6); --md-surface-dim: var(--n6);
            --md-surface-container-low: var(--n10); --md-surface-container: var(--n12);
            --md-surface-container-high: var(--n17); --md-surface-container-highest: var(--n20);
            --md-on-surface: var(--n90); --md-on-surface-variant: var(--nv80);
            --md-outline: var(--nv60); --md-outline-variant: var(--nv30);
            --md-error: var(--err80); --md-on-error: var(--err-on-c-dark);
            --md-error-container: var(--err-c-dark); --md-on-error-container: var(--err-on-c-dark);

            --bs-body-bg: var(--md-surface); --bs-body-color: var(--md-on-surface);
            --bs-border-color: var(--md-outline-variant); --bs-border-color-translucent: var(--md-outline-variant);
            --bs-secondary-bg: var(--md-surface-container); --bs-tertiary-bg: var(--md-surface-container-low);
            --bs-secondary-color: var(--md-on-surface-variant); --bs-emphasis-color: var(--md-on-surface);
            --bs-heading-color: var(--md-on-surface);
            --md-elev-1: 0 1px 2px rgba(0,0,0,.35), 0 1px 3px 1px rgba(0,0,0,.25);
            --md-elev-2: 0 1px 2px rgba(0,0,0,.4), 0 2px 6px 2px rgba(0,0,0,.3);
            --md-elev-3: 0 4px 8px rgba(0,0,0,.45), 0 1px 3px rgba(0,0,0,.35);
            --md-state-hover: rgba(190,160,248,.10);
            --md-state-focus: rgba(190,160,248,.16);
        }

        html, body { font-family: var(--md-font); }
        body { min-height: 100vh; letter-spacing: .01em; }
        h1,h2,h3,h4,h5,h6 { font-family: var(--md-font); font-weight: 700; letter-spacing: -.01em; }
        h1 { font-size: 2.1rem; } h2 { font-size: 1.85rem; } h3 { font-size: 1.55rem; }
        h4 { font-size: 1.3rem; font-weight: 600; } h5 { font-size: 1.1rem; font-weight: 600; }
        h6 { font-size: .95rem; font-weight: 700; text-transform: uppercase; letter-spacing: .04em; }
        small, .small { font-size: .8rem; }

        ::selection { background: var(--md-primary-container); color: var(--md-on-primary-container); }
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: var(--md-outline-variant); border-radius: 8px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--md-outline); }

        a { color: var(--md-primary-text); text-decoration: none; }
        a:hover { color: var(--md-primary); text-decoration: underline; }

        /* ----- Boutons : mapping MD3 (Filled / Outlined / Tonal / Text) ----- */
        .btn {
            font-family: var(--md-font); font-weight: 600; border-radius: var(--md-shape-full);
            padding: .5rem 1.1rem; letter-spacing: .01em; transition: box-shadow .15s, background-color .15s, border-color .15s;
        }
        .btn-sm { padding: .3rem .85rem; border-radius: var(--md-shape-full); }
        .btn-lg { padding: .7rem 1.5rem; }
        .btn-primary {
            --bs-btn-bg: var(--md-primary); --bs-btn-border-color: var(--md-primary); --bs-btn-color: var(--md-on-primary);
            --bs-btn-hover-bg: var(--md-primary); --bs-btn-hover-border-color: var(--md-primary); --bs-btn-hover-color: var(--md-on-primary);
            --bs-btn-active-bg: var(--p30); --bs-btn-active-border-color: var(--p30);
            --bs-btn-disabled-bg: var(--md-primary); --bs-btn-disabled-border-color: var(--md-primary);
            box-shadow: none;
        }
        .btn-primary:hover { box-shadow: var(--md-elev-1); filter: brightness(1.06); }
        .btn-outline-primary {
            --bs-btn-color: var(--md-primary-text); --bs-btn-border-color: var(--md-outline);
            --bs-btn-hover-bg: var(--md-primary-container); --bs-btn-hover-color: var(--md-on-primary-container); --bs-btn-hover-border-color: var(--md-outline);
            --bs-btn-active-bg: var(--md-primary-container); --bs-btn-active-color: var(--md-on-primary-container); --bs-btn-active-border-color: var(--md-outline);
        }
        .btn-secondary, .btn-outline-secondary {
            --bs-btn-bg: transparent; --bs-btn-color: var(--md-on-surface-variant); --bs-btn-border-color: var(--md-outline);
            --bs-btn-hover-bg: var(--md-secondary-container); --bs-btn-hover-color: var(--md-on-secondary-container); --bs-btn-hover-border-color: var(--md-outline);
            --bs-btn-active-bg: var(--md-secondary-container); --bs-btn-active-color: var(--md-on-secondary-container);
        }
        .btn-danger {
            --bs-btn-bg: var(--md-error); --bs-btn-border-color: var(--md-error); --bs-btn-color: var(--md-on-error);
            --bs-btn-hover-bg: var(--md-error); --bs-btn-hover-border-color: var(--md-error); --bs-btn-hover-color: var(--md-on-error);
        }
        .btn-outline-danger {
            --bs-btn-color: var(--md-error); --bs-btn-border-color: var(--md-error);
            --bs-btn-hover-bg: var(--md-error-container); --bs-btn-hover-color: var(--md-on-error-container); --bs-btn-hover-border-color: var(--md-error);
        }
        .btn-outline-dark { --bs-btn-color: var(--md-on-surface); --bs-btn-border-color: var(--md-outline); --bs-btn-hover-bg: var(--md-surface-container-high); --bs-btn-hover-color: var(--md-on-surface); --bs-btn-hover-border-color: var(--md-outline); }
        .btn-success { --bs-btn-bg:#146c43; --bs-btn-border-color:#146c43; }
        .btn-link { font-weight: 600; }
        .btn:focus-visible, .form-control:focus-visible, .form-select:focus-visible, a:focus-visible {
            outline: 2px solid var(--md-primary); outline-offset: 2px;
        }

        /* ----- Champs de formulaire : style "outlined" MD3 ----- */
        .form-label { font-weight: 600; font-size: .82rem; color: var(--md-on-surface-variant); margin-bottom: .35rem; }
        .form-control, .form-select {
            font-family: var(--md-font); border-radius: var(--md-shape-xs); border-color: var(--md-outline-variant);
            background-color: var(--md-surface-container-low); color: var(--md-on-surface);
            padding: .55rem .85rem; transition: border-color .15s, box-shadow .15s;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--md-primary); background-color: var(--md-surface-container-low); color: var(--md-on-surface);
            box-shadow: 0 0 0 1px var(--md-primary);
        }
        .form-control::placeholder { color: var(--md-outline); }
        .form-check-input:checked { background-color: var(--md-primary); border-color: var(--md-primary); }
        .form-check-input:focus { border-color: var(--md-primary); box-shadow: 0 0 0 .2rem var(--md-state-focus); }

        /* ----- Cartes : "filled card" MD3 (surface teintée, pas de bordure ni ombre au repos) ----- */
        .card { background-color: var(--md-surface-container-low); border: 1px solid var(--md-outline-variant); border-radius: var(--md-shape-md); box-shadow: none; }
        .card-header, .card-footer { background-color: transparent; border-color: var(--md-outline-variant); font-family: var(--md-font); }
        .card-header { font-weight: 700; }

        /* ----- Barre de nav latérale = "Navigation Drawer" MD3 ----- */
        .cdc-sidebar {
            width: 248px; position: fixed; top: 0; bottom: 0; left: 0;
            background: var(--md-surface-container-low); border-right: 1px solid var(--md-outline-variant);
            display: flex; flex-direction: column; padding: 1rem .75rem; z-index: 1040;
            transition: transform .2s;
        }
        .cdc-brand { display:flex; align-items:center; gap:.55rem; font-weight:800; font-size:1.15rem; color: var(--md-on-surface); padding:.4rem .6rem 1.1rem; letter-spacing: -.01em; }
        .cdc-brand-mark { width:34px; height:34px; border-radius: var(--md-shape-sm); background: var(--md-primary); color: var(--md-on-primary); display:flex; align-items:center; justify-content:center; font-weight:900; flex-shrink:0; font-size: .95rem; }
        .cdc-nav-label { font-size:.68rem; font-weight:700; text-transform:uppercase; letter-spacing:.08em; color:var(--md-on-surface-variant); padding:1rem .8rem .3rem; }
        .cdc-link {
            display:flex; align-items:center; gap:.7rem; padding:.55rem .85rem; border-radius: var(--md-shape-full);
            color: var(--md-on-surface-variant); text-decoration:none; font-size:.88rem; font-weight: 600; margin-bottom:2px;
            transition: background-color .15s, color .15s;
        }
        .cdc-link:hover { background: var(--md-state-hover); color: var(--md-on-surface); text-decoration: none; }
        .cdc-link.active { background: var(--md-primary-container); color: var(--md-on-primary-container); }
        .cdc-link i { width:18px; text-align:center; font-size: 1rem; }
        .cdc-main { margin-left:248px; min-height:100vh; display:flex; flex-direction:column; }
        .cdc-footer-user { margin-top:auto; border-top:1px solid var(--md-outline-variant); padding-top:.85rem; }

        /* ----- Barre supérieure = "Top App Bar" MD3 ----- */
        .cdc-topbar {
            position:sticky; top:0; z-index:1030; height:60px; display:flex; align-items:center; justify-content:space-between;
            padding:0 1.4rem; background: color-mix(in srgb, var(--md-surface) 92%, transparent);
            backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px);
            border-bottom:1px solid var(--md-outline-variant);
        }
        .cdc-topbar .fw-semibold { font-weight: 700 !important; letter-spacing: -.01em; }
        .cdc-content { padding:1.6rem; flex:1; }

        /* ----- Badges = MD3 assist chips ----- */
        .badge { font-family: var(--md-font); font-weight: 700; border-radius: var(--md-shape-full); letter-spacing: .01em; }
        .bg-primary, .text-bg-primary { background-color: var(--md-primary) !important; color: var(--md-on-primary) !important; }
        .bg-secondary, .text-bg-secondary { background-color: var(--md-secondary-container) !important; color: var(--md-on-secondary-container) !important; }
        .bg-success, .text-bg-success { background-color: #146c43 !important; }
        .bg-danger, .text-bg-danger { background-color: var(--md-error) !important; color: var(--md-on-error) !important; }
        .text-primary { color: var(--md-primary-text) !important; }
        .text-secondary { color: var(--md-on-surface-variant) !important; }
        .text-danger { color: var(--md-error) !important; }
        .progress { background-color: var(--md-surface-container-high); border-radius: var(--md-shape-full); }
        .progress-bar { background-color: var(--md-primary); border-radius: var(--md-shape-full); }

        /* ----- Alertes = bandeaux tonaux MD3 ----- */
        .alert { border: none; border-radius: var(--md-shape-sm); font-weight: 500; }
        .alert-success { background: #d3f0e0; color: #0f5132; }
        [data-bs-theme="dark"] .alert-success { background: #143a29; color: #b6f0d0; }
        .alert-danger { background: var(--md-error-container); color: var(--md-on-error-container); }

        /* ----- Tables ----- */
        .table { --bs-table-bg: transparent; --bs-table-color: var(--md-on-surface); color: var(--md-on-surface); }
        .table > thead { color: var(--md-on-surface-variant); }
        .table thead th { font-size: .72rem; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; border-bottom-color: var(--md-outline-variant); background: transparent; }
        .table > :not(caption) > * > * { border-bottom-color: var(--md-outline-variant); background-color: transparent; }
        .table-hover > tbody > tr:hover > * { background-color: var(--md-state-hover); color: var(--md-on-surface); }
        .table-light { --bs-table-bg: var(--md-surface-container); }

        /* ----- Menus déroulants = "Menu" MD3 ----- */
        .dropdown-menu { border: 1px solid var(--md-outline-variant); border-radius: var(--md-shape-sm); box-shadow: var(--md-elev-2); background-color: var(--md-surface-container); }
        .dropdown-item { color: var(--md-on-surface); border-radius: var(--md-shape-xs); margin: 0 .3rem; width: calc(100% - .6rem); }
        .dropdown-item:hover, .dropdown-item:focus { background-color: var(--md-state-hover); color: var(--md-on-surface); }

        /* ----- Modales = "Dialog" MD3 ----- */
        .modal-content { border: none; border-radius: var(--md-shape-lg); box-shadow: var(--md-elev-3); background-color: var(--md-surface-container-high); }
        .modal-header, .modal-footer { border-color: var(--md-outline-variant); }

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
        <span class="cdc-brand-mark">GSC</span>
        <span>Groupe Speed Cloud</span>
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
            <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width:36px;height:36px;flex-shrink:0;background:var(--md-primary-container);color:var(--md-on-primary-container);">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div class="overflow-hidden">
                <div class="small fw-semibold text-truncate">{{ auth()->user()->name }}</div>
                <div class="text-truncate" style="font-size:.72rem;">
                    <span class="badge text-bg-secondary text-capitalize">{{ auth()->user()->role }}</span>
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
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill text-bg-danger">{{ $topbarNonLues }}</span>
                    @endif
                </button>
                <div class="dropdown-menu dropdown-menu-end p-0" style="width:320px;max-height:420px;overflow:auto;">
                    <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom" style="border-color:var(--md-outline-variant)!important;">
                        <strong class="small">Notifications</strong>
                        @if($topbarNonLues > 0)
                        <form method="POST" action="{{ route('notifications.toutLire') }}" class="m-0">@csrf
                            <button class="btn btn-link btn-sm p-0 text-decoration-none">Tout lire</button>
                        </form>
                        @endif
                    </div>
                    @forelse($topbarNotifications as $notif)
                        <a href="{{ route('notifications.lire', $notif) }}" class="dropdown-item small py-2 border-bottom {{ $notif->lu ? '' : 'fw-semibold' }}" style="white-space:normal;border-color:var(--md-outline-variant)!important;">
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
