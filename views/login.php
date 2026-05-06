<!DOCTYPE html>
<html lang="fr" class="h-full">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Flow – Connexion</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap">
  <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons+Round">
  <style>
    * { box-sizing: border-box; }
    body {
      font-family: 'Inter', system-ui, sans-serif;
      background: #0f172a;
      margin: 0;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .login-card {
      background: #1e293b;
      border: 1px solid rgba(255,255,255,.08);
      border-radius: 16px;
      padding: 40px 36px;
      width: 100%;
      max-width: 400px;
      box-shadow: 0 25px 50px rgba(0,0,0,.5);
    }

    .btn-google {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 12px;
      width: 100%;
      padding: 13px 20px;
      background: #fff;
      color: #0f172a;
      border: none;
      border-radius: 10px;
      font-family: 'Inter', sans-serif;
      font-size: 14px;
      font-weight: 600;
      cursor: pointer;
      text-decoration: none;
      transition: background .15s, box-shadow .15s, transform .1s;
    }
    .btn-google:hover {
      background: #f1f5f9;
      box-shadow: 0 4px 12px rgba(0,0,0,.2);
      transform: translateY(-1px);
    }
    .btn-google:active { transform: translateY(0); }

    .error-box {
      display: flex;
      align-items: center;
      gap: 8px;
      background: rgba(239,68,68,.1);
      border: 1px solid rgba(239,68,68,.3);
      border-radius: 8px;
      padding: 10px 14px;
      color: #fca5a5;
      font-size: 13px;
      margin-bottom: 20px;
    }

    .divider {
      display: flex;
      align-items: center;
      gap: 12px;
      margin: 24px 0;
      color: rgba(255,255,255,.2);
      font-size: 12px;
    }
    .divider::before, .divider::after {
      content: '';
      flex: 1;
      height: 1px;
      background: rgba(255,255,255,.08);
    }
  </style>
</head>
<body>

  <!-- Background grid pattern -->
  <div style="position:fixed;inset:0;background-image:radial-gradient(circle at 1px 1px, rgba(255,255,255,.04) 1px, transparent 0);background-size:32px 32px;pointer-events:none;"></div>

  <!-- Glow -->
  <div style="position:fixed;top:-200px;left:50%;transform:translateX(-50%);width:600px;height:600px;background:radial-gradient(circle, rgba(37,99,235,.15) 0%, transparent 70%);pointer-events:none;"></div>

  <div class="login-card" style="position:relative;z-index:1;">

    <!-- Brand -->
    <div style="text-align:center;margin-bottom:32px;">
      <div style="display:inline-flex;align-items:center;justify-content:center;width:52px;height:52px;border-radius:14px;background:linear-gradient(135deg,#38bdf8,#2563eb);margin-bottom:16px;box-shadow:0 8px 24px rgba(37,99,235,.4);">
        <span class="material-icons-round" style="color:#fff;font-size:26px;">water_drop</span>
      </div>
      <div style="font-size:24px;font-weight:800;color:#fff;margin-bottom:6px;letter-spacing:-.02em;">Flow</div>
      <div style="font-size:13px;color:rgba(255,255,255,.45);">Pilotage financier · Groupe Speed Cloud</div>
    </div>

    <?php if (!empty($error)): ?>
    <div class="error-box">
      <span class="material-icons-round" style="font-size:16px;color:#ef4444;flex-shrink:0;">error</span>
      <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
    </div>
    <?php endif; ?>

    <div style="margin-bottom:8px;">
      <div style="font-size:15px;font-weight:600;color:#fff;margin-bottom:4px;">Connexion</div>
      <div style="font-size:13px;color:rgba(255,255,255,.4);">Connectez-vous avec votre compte Google autorisé.</div>
    </div>

    <div class="divider">ou</div>

    <a href="<?= APP_URL ?>/auth/google" class="btn-google">
      <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M17.64 9.2c0-.637-.057-1.251-.164-1.84H9v3.481h4.844a4.14 4.14 0 0 1-1.796 2.716v2.259h2.908c1.702-1.567 2.684-3.875 2.684-6.615Z" fill="#4285F4"/>
        <path d="M9 18c2.43 0 4.467-.806 5.956-2.18l-2.908-2.259c-.806.54-1.837.86-3.048.86-2.344 0-4.328-1.584-5.036-3.711H.957v2.332A8.997 8.997 0 0 0 9 18Z" fill="#34A853"/>
        <path d="M3.964 10.71A5.41 5.41 0 0 1 3.682 9c0-.593.102-1.17.282-1.71V4.958H.957A8.996 8.996 0 0 0 0 9c0 1.452.348 2.827.957 4.042l3.007-2.332Z" fill="#FBBC05"/>
        <path d="M9 3.58c1.321 0 2.508.454 3.44 1.345l2.582-2.58C13.463.891 11.426 0 9 0A8.997 8.997 0 0 0 .957 4.958L3.964 7.29C4.672 5.163 6.656 3.58 9 3.58Z" fill="#EA4335"/>
      </svg>
      Continuer avec Google
    </a>

    <div style="text-align:center;margin-top:24px;font-size:12px;color:rgba(255,255,255,.25);">
      Accès réservé aux membres de l'organisation.
    </div>

  </div>

</body>
</html>
