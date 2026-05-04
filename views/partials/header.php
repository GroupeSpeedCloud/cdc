<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Flow – Tableau de bord stratégique</title>

  <!-- Material Web Components -->
  <script type="importmap">
    { "imports": { "@material/web/": "https://esm.run/@material/web/" } }
  </script>
  <script type="module">
    import '@material/web/all.js';
  </script>

  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <!-- Google Fonts: Roboto -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap">
  <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">

  <style>
    :root {
      --primary:      #1a73e8;
      --primary-dark: #1558b0;
      --surface:      #ffffff;
      --background:   #f8f9fa;
      --on-surface:   #202124;
      --outline:      #dadce0;
      --error:        #d93025;
      --warning:      #f9ab00;
      --success:      #1e8e3e;
      --sidebar-w:    260px;
    }

    *, *::before, *::after { box-sizing: border-box; }

    body {
      margin: 0;
      font-family: 'Roboto', sans-serif;
      background: var(--background);
      color: var(--on-surface);
      display: flex;
      min-height: 100vh;
    }

    /* Sidebar */
    #sidebar {
      width: var(--sidebar-w);
      background: var(--surface);
      border-right: 1px solid var(--outline);
      display: flex;
      flex-direction: column;
      position: fixed;
      top: 0; left: 0; bottom: 0;
      z-index: 100;
      overflow-y: auto;
    }

    /* Main content */
    #main {
      margin-left: var(--sidebar-w);
      flex: 1;
      display: flex;
      flex-direction: column;
      min-height: 100vh;
    }

    /* Top bar */
    #topbar {
      background: var(--surface);
      border-bottom: 1px solid var(--outline);
      padding: 0 1.5rem;
      height: 64px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 0.5rem;
      position: sticky;
      top: 0;
      z-index: 50;
    }

    #topbar h1 {
      font-size: 1.25rem;
      font-weight: 500;
      margin: 0;
      color: var(--primary);
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .topbar-user {
      display: flex;
      align-items: center;
      gap: 0.75rem;
    }

    .topbar-user img {
      width: 36px;
      height: 36px;
      border-radius: 50%;
      object-fit: cover;
    }

    .topbar-user span {
      font-size: 0.875rem;
      font-weight: 500;
    }

    /* Page content */
    #content {
      padding: 2rem;
      flex: 1;
    }

    /* Cards */
    .card {
      background: var(--surface);
      border-radius: 12px;
      border: 1px solid var(--outline);
      padding: 1.5rem;
      margin-bottom: 1.5rem;
    }

    .card-title {
      font-size: 1rem;
      font-weight: 500;
      color: var(--on-surface);
      margin: 0 0 1rem 0;
    }

    /* KPI grid */
    .kpi-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
      gap: 1rem;
      margin-bottom: 2rem;
    }

    .kpi-card {
      background: var(--surface);
      border-radius: 12px;
      border: 1px solid var(--outline);
      padding: 1.25rem 1.5rem;
      display: flex;
      flex-direction: column;
      gap: 0.25rem;
    }

    .kpi-card .label {
      font-size: 0.8125rem;
      color: #5f6368;
      font-weight: 500;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .kpi-card .value {
      font-size: 1.875rem;
      font-weight: 700;
      color: var(--primary);
      line-height: 1.2;
    }

    .kpi-card .sub {
      font-size: 0.8125rem;
      color: #5f6368;
    }

    .kpi-card.danger  .value { color: var(--error); }
    .kpi-card.warning .value { color: var(--warning); }
    .kpi-card.success .value { color: var(--success); }

    /* Charts grid */
    .charts-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(440px, 1fr));
      gap: 1.5rem;
      margin-bottom: 2rem;
    }

    .chart-container {
      position: relative;
      height: 280px;
    }

    /* Tables */
    .data-table {
      width: 100%;
      border-collapse: collapse;
      font-size: 0.875rem;
    }

    .data-table th {
      text-align: left;
      padding: 0.625rem 1rem;
      background: #f8f9fa;
      font-weight: 500;
      color: #5f6368;
      border-bottom: 1px solid var(--outline);
    }

    .data-table td {
      padding: 0.75rem 1rem;
      border-bottom: 1px solid var(--outline);
    }

    .data-table tr:last-child td { border-bottom: none; }
    .data-table tr:hover td { background: #f8f9fa; }

    /* Risk badges */
    .badge {
      display: inline-flex;
      align-items: center;
      padding: 0.25rem 0.75rem;
      border-radius: 100px;
      font-size: 0.75rem;
      font-weight: 500;
    }
    .badge-low     { background: #e6f4ea; color: #1e8e3e; }
    .badge-medium  { background: #fef7e0; color: #f9ab00; }
    .badge-high    { background: #fce8e6; color: #d93025; }
    .badge-info    { background: #e8f0fe; color: #1a73e8; }
    .badge-success { background: #e6f4ea; color: #1e8e3e; }
    .badge-warning { background: #fef7e0; color: #f9ab00; }
    .badge-danger  { background: #fce8e6; color: #d93025; }

    /* Alert boxes */
    .alert {
      display: flex;
      align-items: flex-start;
      gap: 0.75rem;
      padding: 0.875rem 1rem;
      border-radius: 8px;
      margin-bottom: 0.75rem;
      font-size: 0.875rem;
    }
    .alert-info    { background: #e8f0fe; color: #1a73e8; border-left: 3px solid #1a73e8; }
    .alert-warning { background: #fef7e0; color: #f9ab00; border-left: 3px solid #f9ab00; }
    .alert-danger  { background: #fce8e6; color: #d93025; border-left: 3px solid #d93025; }
    .alert-success { background: #e6f4ea; color: #1e8e3e; border-left: 3px solid #1e8e3e; }

    /* Search bar */
    .search-bar {
      display: flex;
      align-items: center;
      gap: 1rem;
      margin-bottom: 1.5rem;
      flex-wrap: wrap;
    }

    .search-input {
      flex: 1;
      min-width: 200px;
      padding: 0.625rem 1rem;
      border: 1px solid var(--outline);
      border-radius: 8px;
      font-size: 0.875rem;
      font-family: inherit;
      outline: none;
      transition: border-color 0.2s;
    }

    .search-input:focus { border-color: var(--primary); }

    /* Buttons */
    .btn {
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      padding: 0.625rem 1.25rem;
      border-radius: 8px;
      border: none;
      cursor: pointer;
      font-size: 0.875rem;
      font-family: inherit;
      font-weight: 500;
      text-decoration: none;
      transition: opacity 0.15s, box-shadow 0.15s;
    }

    .btn:hover { opacity: 0.9; box-shadow: 0 2px 8px rgba(0,0,0,0.15); }

    .btn-primary { background: var(--primary); color: #fff; }
    .btn-outline { background: transparent; color: var(--primary); border: 1px solid var(--primary); }
    .btn-danger  { background: var(--error); color: #fff; }

    /* Pagination */
    .pagination {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      margin-top: 1.5rem;
      flex-wrap: wrap;
    }

    .pagination a, .pagination span {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 36px;
      height: 36px;
      border-radius: 6px;
      border: 1px solid var(--outline);
      text-decoration: none;
      color: var(--on-surface);
      font-size: 0.875rem;
      transition: background 0.15s;
    }

    .pagination a:hover  { background: #f0f4ff; }
    .pagination span.current { background: var(--primary); color: #fff; border-color: var(--primary); }

    /* Logout form */
    .logout-form { margin: 0; }

    /* Mobile menu toggle button */
    #menu-toggle {
      display: none;
      align-items: center;
      justify-content: center;
      background: none;
      border: none;
      cursor: pointer;
      padding: 0.5rem;
      border-radius: 8px;
      color: var(--on-surface);
      margin-right: 0.5rem;
      flex-shrink: 0;
    }
    #menu-toggle .material-icons { font-size: 1.5rem; }
    #menu-toggle:hover { background: var(--background); }

    /* Sidebar overlay */
    #sidebar-overlay {
      display: none;
      position: fixed;
      inset: 0;
      background: rgba(0,0,0,0.5);
      z-index: 99;
    }
    #sidebar-overlay.active { display: block; }

    /* Sidebar close button (mobile only) */
    #sidebar-close {
      display: none;
      align-items: center;
      justify-content: center;
      background: none;
      border: none;
      cursor: pointer;
      padding: 0.375rem;
      border-radius: 6px;
      color: var(--on-surface);
      flex-shrink: 0;
    }
    #sidebar-close:hover { background: var(--background); }

    /* Two-column responsive grid utility */
    .grid-cols-2 {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 1.5rem;
      align-items: start;
    }

    /* Scrollable table wrapper */
    .table-scroll {
      overflow-x: auto;
      -webkit-overflow-scrolling: touch;
    }

    /* Responsive */
    @media (max-width: 768px) {
      #menu-toggle  { display: flex; }
      #sidebar-close { display: flex; }

      #sidebar {
        width: var(--sidebar-w);
        transform: translateX(-100%);
        transition: transform 0.25s ease;
        overflow: hidden;
      }
      #sidebar.open {
        transform: translateX(0);
        overflow-y: auto;
      }

      #main    { margin-left: 0; }
      #content { padding: 1rem; }

      .charts-grid { grid-template-columns: 1fr; }
      .grid-cols-2 { grid-template-columns: 1fr; }

      #topbar h1 { font-size: 1rem; }
      .topbar-user > span { display: none; }

      /* Cards containing tables: allow horizontal scroll */
      .card.table-card { overflow-x: auto; }
    }

    @media (max-width: 480px) {
      #content { padding: 0.75rem; }
      .kpi-card .value { font-size: 1.5rem; }
      .btn { padding: 0.5rem 0.875rem; font-size: 0.8125rem; }
    }
  </style>
</head>
<body>
