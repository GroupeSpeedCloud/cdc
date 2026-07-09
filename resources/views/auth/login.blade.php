<!DOCTYPE html>
<html lang="fr" data-bs-theme="{{ request()->cookie('theme', 'dark') }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion — Groupe Speed Cloud</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Titillium+Web:ital,wght@0,300;0,400;0,600;0,700;0,900;1,400&display=swap" rel="stylesheet">
    <style>
        :root {
            --font: 'Titillium Web', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            --brand: #8a4dfd; --brand-dark: #592aa9; --brand-container-light: #dfd1fa; --brand-on-container-light: #14082b;
            --err: #b3261e;
        }
        [data-bs-theme="dark"] {
            --bg: #0f0e10; --surface: #19181b; --surface-2: #1e1d20;
            --outline: #494059; --outline-variant: #37353b;
            --text: #e5e4e7; --text-2: #c9c4d4; --text-3: #9489a9;
            --primary: #bea0f8; --on-primary: #270c5a; --primary-container: #390e8b; --on-primary-container: #dfd1fa;
            --err-container: #8c1d18; --on-err-container: #f9dedc;
            --glow: rgba(190,160,248,.18);
        }
        [data-bs-theme="light"] {
            --bg: #fcfcfd; --surface: #ffffff; --surface-2: #f5f4f5;
            --outline: #c9c4d4; --outline-variant: #e4e1ea;
            --text: #19181b; --text-2: #494051; --text-3: #796b94;
            --primary: #8a4dfd; --on-primary: #ffffff; --primary-container: #dfd1fa; --on-primary-container: #14082b;
            --err-container: #f9dedc; --on-err-container: #410e0b;
            --glow: rgba(138,77,253,.14);
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            background: var(--bg); color: var(--text); font-family: var(--font);
            min-height: 100vh; display: flex; align-items: center; justify-content: center;
        }
        .login-bg {
            position: fixed; inset: 0;
            background: radial-gradient(ellipse 70% 45% at 50% -10%, var(--glow), transparent);
            pointer-events: none;
        }
        .login-wrap { width: 100%; max-width: 420px; padding: 24px; position: relative; z-index: 1; }
        .login-logo { display: flex; flex-direction: column; align-items: center; margin-bottom: 32px; gap: 14px; }
        .logo-mark {
            width: 56px; height: 56px; border-radius: 16px; background: var(--primary); color: var(--on-primary);
            display: flex; align-items: center; justify-content: center; font-weight: 900; font-size: 20px; letter-spacing: -.02em;
            box-shadow: 0 8px 24px var(--glow);
        }
        .logo-name { font-size: 21px; font-weight: 700; letter-spacing: -0.02em; color: var(--text); text-align: center; }
        .login-card { background: var(--surface); border: 1px solid var(--outline-variant); border-radius: 24px; padding: 34px; }
        .login-title { font-size: 20px; font-weight: 700; letter-spacing: -0.01em; color: var(--text); margin-bottom: 6px; }
        .login-subtitle { font-size: 13.5px; color: var(--text-3); margin-bottom: 28px; line-height: 1.55; }
        .login-subtitle strong { color: var(--text-2); font-weight: 600; }
        .error-msg {
            background: var(--err-container); color: var(--on-err-container); border-radius: 12px;
            padding: 12px 14px; font-size: 13px; display: flex; align-items: center; gap: 8px; margin-bottom: 20px; font-weight: 500;
        }
        .btn-google {
            display: flex; align-items: center; justify-content: center; gap: 12px; width: 100%;
            background: var(--surface); border: 1px solid var(--outline); border-radius: 999px;
            padding: 13px 20px; color: var(--text); font-size: 14.5px; font-weight: 600; font-family: inherit;
            cursor: pointer; text-decoration: none; transition: background .15s, border-color .15s, box-shadow .15s;
        }
        .btn-google:hover { background: var(--surface-2); border-color: var(--text-3); box-shadow: 0 2px 8px var(--glow); }
        .login-footer { margin-top: 22px; text-align: center; font-size: 11.5px; color: var(--text-3); }
    </style>
</head>
<body>
<div class="login-bg"></div>
<div class="login-wrap">
    <div class="login-logo">
        <span class="logo-mark">GSC</span>
        <span class="logo-name">Groupe Speed Cloud</span>
    </div>

    <div class="login-card">
        <div class="login-title">Connexion</div>
        <div class="login-subtitle">Réservé aux membres <strong>@groupe-speed.cloud</strong></div>

        @if(session('error'))
            <div class="error-msg">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                {{ session('error') }}
            </div>
        @endif
        @if(isset($error) && $error)
            <div class="error-msg">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                {{ $error }}
            </div>
        @endif

        <a href="/login?force=1" class="btn-google">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
            </svg>
            Continuer avec Google
        </a>
    </div>

    <div class="login-footer">Groupe Speed Cloud — Facturation interne</div>
</div>
</body>
</html>
