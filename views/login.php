<!DOCTYPE html>
<html lang="fr" class="h-full">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Flow – Connexion</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
  <style>body{font-family:'Inter',system-ui,sans-serif;}</style>
</head>
<body class="h-full bg-gradient-to-br from-slate-900 via-blue-950 to-slate-900 flex items-center justify-center p-4">

  <div class="w-full max-w-sm">
    <!-- Card -->
    <div class="bg-white rounded-2xl shadow-2xl p-8">

      <!-- Logo -->
      <div class="flex flex-col items-center mb-8">
        <div class="w-14 h-14 rounded-2xl bg-blue-600 flex items-center justify-center mb-4 shadow-lg">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14H9V8h2v8zm4 0h-2V8h2v8z"/>
          </svg>
        </div>
        <h1 class="text-2xl font-bold text-slate-900">Flow</h1>
        <p class="text-sm text-slate-500 mt-1">Pilotage financier · Groupe Speed Cloud</p>
      </div>

      <!-- Error -->
      <?php if (!empty($_GET['error'])): ?>
      <div class="flex items-start gap-3 bg-red-50 border border-red-200 text-red-700 rounded-xl p-3.5 mb-6 text-sm">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
        </svg>
        <?= htmlspecialchars($_GET['error'], ENT_QUOTES, 'UTF-8') ?>
      </div>
      <?php endif; ?>

      <!-- Google button -->
      <a href="<?= APP_URL ?>/auth/google"
         class="flex items-center justify-center gap-3 w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-slate-700 font-medium text-sm hover:border-blue-400 hover:bg-blue-50 transition-all duration-200 group">
        <svg class="w-5 h-5 flex-shrink-0" viewBox="0 0 24 24">
          <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
          <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
          <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
          <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
        </svg>
        <span>Connexion avec Google</span>
      </a>

      <p class="text-center text-xs text-slate-400 mt-6">
        Accès réservé aux comptes <strong>@groupe-speed.cloud</strong>
      </p>
    </div>

    <p class="text-center text-slate-500 text-xs mt-6">Flow v2 · <?= date('Y') ?></p>
  </div>

</body>
</html>
