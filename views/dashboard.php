<?php require_once __DIR__ . '/partials/header.php'; ?>
<?php require_once __DIR__ . '/partials/sidebar.php'; ?>

<div id="main">
  <!-- Top bar -->
  <header id="topbar">
    <h1><span class="material-icons" style="vertical-align:middle;margin-right:0.5rem;">dashboard</span>Tableau de bord</h1>
    <div class="topbar-user">
      <?php if (!empty($user['avatar'])): ?>
        <img src="<?= htmlspecialchars($user['avatar'], ENT_QUOTES, 'UTF-8') ?>" alt="Avatar">
      <?php endif; ?>
      <span><?= htmlspecialchars($user['name'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
    </div>
  </header>

  <div id="content">

    <!-- KPI Cards -->
    <div class="kpi-grid">
      <div class="kpi-card">
        <div class="label">CA du mois</div>
        <div class="value"><?= number_format($kpis['monthly_revenue'], 0, ',', ' ') ?> €</div>
        <div class="sub">
          <?php $gr = $kpis['growth_rate']; ?>
          <?= $gr >= 0 ? '▲' : '▼' ?>
          <span style="color:<?= $gr >= 0 ? 'var(--success)' : 'var(--error)' ?>">
            <?= ($gr >= 0 ? '+' : '') . $gr ?> % vs mois précédent
          </span>
        </div>
      </div>

      <div class="kpi-card">
        <div class="label">CA annuel</div>
        <div class="value"><?= number_format($kpis['annual_revenue'], 0, ',', ' ') ?> €</div>
        <div class="sub">Année <?= date('Y') ?></div>
      </div>

      <div class="kpi-card success">
        <div class="label">Factures payées</div>
        <div class="value"><?= (int)($kpis['invoice_counts']['paid'] ?? 0) ?></div>
        <div class="sub">sur <?= (int)($kpis['invoice_counts']['total'] ?? 0) ?> total</div>
      </div>

      <div class="kpi-card warning">
        <div class="label">Factures impayées</div>
        <div class="value"><?= (int)($kpis['invoice_counts']['unpaid'] ?? 0) ?></div>
        <div class="sub">en attente de règlement</div>
      </div>

      <div class="kpi-card danger">
        <div class="label">Factures en retard</div>
        <div class="value"><?= (int)($kpis['invoice_counts']['overdue'] ?? 0) ?></div>
        <div class="sub">dépassement d'échéance</div>
      </div>

      <div class="kpi-card">
        <div class="label">Panier moyen</div>
        <div class="value"><?= number_format($kpis['average_basket'], 0, ',', ' ') ?> €</div>
        <div class="sub">par facture payée</div>
      </div>
    </div>

    <!-- Charts -->
    <div class="charts-grid">

      <!-- Revenue evolution -->
      <div class="card" style="grid-column: 1/-1;">
        <p class="card-title">
          <span class="material-icons" style="vertical-align:middle;margin-right:0.25rem;font-size:1rem;">show_chart</span>
          Évolution du chiffre d'affaires (12 mois)
        </p>
        <div class="chart-container" style="height:300px;">
          <canvas id="revenueChart"></canvas>
        </div>
      </div>

      <!-- Revenue breakdown pie -->
      <div class="card">
        <p class="card-title">
          <span class="material-icons" style="vertical-align:middle;margin-right:0.25rem;font-size:1rem;">pie_chart</span>
          Répartition CA par produit
        </p>
        <div class="chart-container">
          <canvas id="breakdownChart"></canvas>
        </div>
      </div>

      <!-- Top tiers bar -->
      <div class="card">
        <p class="card-title">
          <span class="material-icons" style="vertical-align:middle;margin-right:0.25rem;font-size:1rem;">bar_chart</span>
          Top 10 tiers par CA
        </p>
        <div class="chart-container">
          <canvas id="tiersChart"></canvas>
        </div>
      </div>

      <!-- Top products bar -->
      <div class="card">
        <p class="card-title">
          <span class="material-icons" style="vertical-align:middle;margin-right:0.25rem;font-size:1rem;">inventory_2</span>
          Top 10 produits par CA
        </p>
        <div class="chart-container">
          <canvas id="productsChart"></canvas>
        </div>
      </div>

    </div>

    <!-- Quick actions -->
    <div class="card" style="display:flex;gap:1rem;flex-wrap:wrap;align-items:center;">
      <a href="<?= APP_URL ?>/tiers" class="btn btn-outline">
        <span class="material-icons" style="font-size:1rem;">groups</span> Voir les tiers
      </a>
      <a href="<?= APP_URL ?>/forecast" class="btn btn-outline">
        <span class="material-icons" style="font-size:1rem;">trending_up</span> Prévisions
      </a>
      <a href="<?= APP_URL ?>/export/pdf" target="_blank" class="btn btn-outline">
        <span class="material-icons" style="font-size:1rem;">picture_as_pdf</span> Exporter rapport
      </a>
    </div>

  </div><!-- #content -->
</div><!-- #main -->

<?php
// JSON encode for JS
$revenueLabels   = json_encode(array_column($kpis['revenue_evolution'], 'label'));
$revenueValues   = json_encode(array_column($kpis['revenue_evolution'], 'revenue'));
$breakdownLabels = json_encode(array_column($kpis['revenue_breakdown'], 'label'));
$breakdownValues = json_encode(array_column($kpis['revenue_breakdown'], 'revenue'));
$tiersLabels     = json_encode(array_column($kpis['revenue_by_tiers'], 'name'));
$tiersValues     = json_encode(array_column($kpis['revenue_by_tiers'], 'revenue'));
$productLabels   = json_encode(array_column($kpis['revenue_by_product'], 'label'));
$productValues   = json_encode(array_column($kpis['revenue_by_product'], 'revenue'));
?>

<script>
const COLORS = [
  '#1a73e8','#34a853','#fbbc04','#ea4335','#ab47bc',
  '#00acc1','#ff7043','#66bb6a','#42a5f5','#ec407a'
];

// Revenue evolution
new Chart(document.getElementById('revenueChart'), {
  type: 'line',
  data: {
    labels: <?= $revenueLabels ?>,
    datasets: [{
      label: 'CA (€)',
      data: <?= $revenueValues ?>,
      borderColor: '#1a73e8',
      backgroundColor: 'rgba(26,115,232,0.08)',
      borderWidth: 2,
      fill: true,
      tension: 0.4,
      pointBackgroundColor: '#1a73e8',
      pointRadius: 4,
    }]
  },
  options: {
    responsive: true, maintainAspectRatio: false,
    plugins: { legend: { display: false } },
    scales: {
      y: {
        beginAtZero: true,
        ticks: { callback: v => v.toLocaleString('fr-FR') + ' €' },
        grid: { color: '#f0f0f0' }
      },
      x: { grid: { display: false } }
    }
  }
});

// Breakdown pie
new Chart(document.getElementById('breakdownChart'), {
  type: 'doughnut',
  data: {
    labels: <?= $breakdownLabels ?>,
    datasets: [{
      data: <?= $breakdownValues ?>,
      backgroundColor: COLORS,
      borderWidth: 2,
      borderColor: '#fff'
    }]
  },
  options: {
    responsive: true, maintainAspectRatio: false,
    plugins: {
      legend: { position: 'bottom', labels: { padding: 12, font: { size: 12 } } },
      tooltip: { callbacks: { label: ctx => ' ' + ctx.label + ': ' + Number(ctx.raw).toLocaleString('fr-FR') + ' €' } }
    }
  }
});

// Top tiers
new Chart(document.getElementById('tiersChart'), {
  type: 'bar',
  data: {
    labels: <?= $tiersLabels ?>,
    datasets: [{
      label: 'CA (€)',
      data: <?= $tiersValues ?>,
      backgroundColor: COLORS,
      borderRadius: 6,
    }]
  },
  options: {
    indexAxis: 'y',
    responsive: true, maintainAspectRatio: false,
    plugins: { legend: { display: false } },
    scales: {
      x: {
        beginAtZero: true,
        ticks: { callback: v => v.toLocaleString('fr-FR') + ' €' }
      },
      y: { grid: { display: false } }
    }
  }
});

// Top products
new Chart(document.getElementById('productsChart'), {
  type: 'bar',
  data: {
    labels: <?= $productLabels ?>,
    datasets: [{
      label: 'CA (€)',
      data: <?= $productValues ?>,
      backgroundColor: COLORS.slice(0).reverse(),
      borderRadius: 6,
    }]
  },
  options: {
    indexAxis: 'y',
    responsive: true, maintainAspectRatio: false,
    plugins: { legend: { display: false } },
    scales: {
      x: {
        beginAtZero: true,
        ticks: { callback: v => v.toLocaleString('fr-FR') + ' €' }
      },
      y: { grid: { display: false } }
    }
  }
});
</script>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
