<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion — Flow</title>
    <style>
        :root {
            --bg: #0a0a0a;
            --surface: #111111;
            --surface-2: #1a1a1a;
            --border: #1e1e1e;
            --border-2: #2a2a2a;
            --text: #ffffff;
            --text-2: #888888;
            --text-3: #555555;
            --accent: #6366f1;
            --accent-bg: rgba(99, 102, 241, 0.12);
            --red: #ef4444;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            background: var(--bg);
            color: var(--text);
            font-family: -apple-system, BlinkMacSystemFont, 'Inter', 'Segoe UI', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-bg {
            position: fixed;
            inset: 0;
            background: radial-gradient(ellipse 80% 50% at 50% -20%, rgba(99,102,241,0.08), transparent);
            pointer-events: none;
        }
        .login-wrap {
            width: 100%;
            max-width: 400px;
            padding: 24px;
            position: relative;
            z-index: 1;
        }
        .login-logo {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 32px;
            gap: 12px;
        }
        .logo-icon {
            width: 44px;
            height: 44px;
            background: var(--accent);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 0 24px rgba(99,102,241,0.4);
        }
        .logo-name {
            font-size: 22px;
            font-weight: 700;
            letter-spacing: -0.04em;
            color: var(--text);
        }
        .login-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 32px;
        }
        .login-title {
            font-size: 18px;
            font-weight: 700;
            letter-spacing: -0.02em;
            color: var(--text);
            margin-bottom: 4px;
        }
        .login-subtitle {
            font-size: 13px;
            color: var(--text-3);
            margin-bottom: 28px;
            line-height: 1.5;
        }
        .login-subtitle strong {
            color: var(--text-2);
            font-weight: 500;
        }
        .error-msg {
            background: rgba(239,68,68,0.08);
            border: 1px solid rgba(239,68,68,0.2);
            color: var(--red);
            border-radius: 10px;
            padding: 12px 14px;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 20px;
        }
        .btn-google {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            width: 100%;
            background: var(--surface-2);
            border: 1px solid var(--border-2);
            border-radius: 10px;
            padding: 12px 20px;
            color: var(--text);
            font-size: 14px;
            font-weight: 500;
            font-family: inherit;
            cursor: pointer;
            text-decoration: none;
            transition: background 0.15s, border-color 0.15s;
        }
        .btn-google:hover {
            background: #222222;
            border-color: #333333;
        }
        .login-footer {
            margin-top: 20px;
            text-align: center;
            font-size: 11px;
            color: var(--text-3);
        }
    </style>
</head>
<body>
<div class="login-bg"></div>
<div class="login-wrap">
    <div class="login-logo">
        <div class="logo-icon">
            <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
            </svg>
        </div>
        <span class="logo-name">Flow</span>
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

    <div class="login-footer">Flow — Gestion financière interne</div>
</div>
</body>
</html>
