<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Flow – Connexion</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap">
  <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: 'Roboto', sans-serif;
      background: linear-gradient(135deg, #1a73e8 0%, #0d47a1 100%);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .login-card {
      background: #fff;
      border-radius: 16px;
      box-shadow: 0 8px 40px rgba(0,0,0,0.2);
      padding: 3rem 2.5rem;
      width: 100%;
      max-width: 420px;
      text-align: center;
    }
    .logo {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.75rem;
      margin-bottom: 1.5rem;
    }
    .logo-icon {
      font-size: 3rem;
      color: #1a73e8;
    }
    .logo-text {
      font-size: 2.5rem;
      font-weight: 700;
      color: #1a73e8;
    }
    .subtitle {
      color: #5f6368;
      font-size: 0.9375rem;
      margin-bottom: 2rem;
    }
    .error-box {
      background: #fce8e6;
      color: #d93025;
      border-radius: 8px;
      padding: 0.875rem 1rem;
      margin-bottom: 1.5rem;
      font-size: 0.875rem;
      display: flex;
      align-items: center;
      gap: 0.5rem;
      text-align: left;
    }
    .google-btn {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.875rem;
      background: #fff;
      border: 2px solid #dadce0;
      border-radius: 100px;
      padding: 0.875rem 1.5rem;
      width: 100%;
      cursor: pointer;
      font-size: 1rem;
      font-family: 'Roboto', sans-serif;
      font-weight: 500;
      color: #202124;
      text-decoration: none;
      transition: box-shadow 0.15s, border-color 0.15s;
    }
    .google-btn:hover {
      box-shadow: 0 2px 12px rgba(0,0,0,0.15);
      border-color: #aaa;
    }
    .google-logo {
      width: 22px;
      height: 22px;
    }
    .footer-note {
      margin-top: 2rem;
      font-size: 0.75rem;
      color: #9aa0a6;
    }
    .divider {
      border: none;
      border-top: 1px solid #e0e0e0;
      margin: 1.5rem 0;
    }
    .access-note {
      background: #e8f0fe;
      color: #1a73e8;
      border-radius: 8px;
      padding: 0.75rem 1rem;
      font-size: 0.8125rem;
      text-align: left;
      display: flex;
      align-items: flex-start;
      gap: 0.5rem;
    }
    @media (max-width: 480px) {
      .login-card { padding: 2rem 1.25rem; }
    }
  </style>
</head>
<body>
  <div class="login-card">
    <div class="logo">
      <span class="material-icons logo-icon">water_drop</span>
      <span class="logo-text">Flow</span>
    </div>
    <p class="subtitle">Tableau de bord stratégique – Groupe Speed</p>

    <?php if (!empty($error)): ?>
    <div class="error-box">
      <span class="material-icons" style="font-size:1.125rem;flex-shrink:0;">error_outline</span>
      <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
    </div>
    <?php endif; ?>

    <div class="access-note">
      <span class="material-icons" style="font-size:1.125rem;flex-shrink:0;">info_outline</span>
      <span>Accès réservé aux membres de la direction avec un compte <strong>@groupe-speed.cloud</strong>.</span>
    </div>

    <hr class="divider">

    <a href="<?= APP_URL ?>/auth/google" class="google-btn">
      <svg class="google-logo" viewBox="0 0 24 24">
        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
      </svg>
      Se connecter avec Google
    </a>

    <p class="footer-note">Vos données sont protégées.<br>Seuls les comptes autorisés peuvent accéder à cette application.</p>
  </div>
</body>
</html>
