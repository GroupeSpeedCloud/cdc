<!DOCTYPE html>
<html lang="fr" class="h-full">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Flow – Pilotage financier</title>

  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: { sans: ['Inter', 'system-ui', 'sans-serif'] },
          colors: {
            brand: { 50:'#eff6ff', 100:'#dbeafe', 500:'#3b82f6', 600:'#2563eb', 700:'#1d4ed8' }
          }
        }
      }
    }
  </script>

  <!-- Inter font -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">

  <!-- Material Icons Round -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons+Round">

  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

  <!-- Vue.js 3 -->
  <script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>

  <style>
    body { font-family: 'Inter', system-ui, sans-serif; }
    [v-cloak] { display: none; }
    ::-webkit-scrollbar { width: 5px; height: 5px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    #sidebar { transition: transform .25s cubic-bezier(.4,0,.2,1); }
    #sidebar-overlay {
      display: none; position: fixed; inset: 0;
      background: rgba(0,0,0,.45); z-index: 25;
    }
    #sidebar-overlay.active { display: block; }
    @media (max-width: 1024px) {
      #sidebar { transform: translateX(-100%); position: fixed; }
      #sidebar.open { transform: translateX(0); }
      #main-wrap { margin-left: 0 !important; }
    }
  </style>
</head>
<body class="h-full bg-slate-50" style="font-family:'Inter',system-ui,sans-serif;">
<div id="layout" class="flex h-screen overflow-hidden">
