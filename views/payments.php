<?php require_once __DIR__ . '/partials/header.php'; ?>
<?php require_once __DIR__ . '/partials/sidebar.php'; ?>

<div id="main">
  <header id="topbar">
    <div style="display:flex;align-items:center;min-width:0;">
      <button id="menu-toggle" aria-label="Ouvrir le menu">
        <span class="material-icons">menu</span>
      </button>
      <h1><span class="material-icons" style="vertical-align:middle;margin-right:0.5rem;">payments</span>Analyse des paiements</h1>
    </div>
    <div class="topbar-user">
      <?php if (!empty($user['avatar'])): ?>
        <img src="<?= htmlspecialchars($user['avatar'], ENT_QUOTES, 'UTF-8') ?>" alt="Avatar">
      <?php endif; ?>
      <span><?= htmlspecialchars($user['name'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
    </div>
  </header>

  <div id="content">

    <!-- KPI -->
    <div class="kpi-grid" style="margin-bottom:2rem;">
      <div class="kpi-card">
        <div class="label">Total encaissé</div>
        <div class="value"><?= number_format($totalCollected, 0, ',', ' ') ?> €</div>
      </div>
      <?php foreach ($methodsBreakdown as $mb): ?>
      <div class="kpi-card">
        <div class="label"><?= htmlspecialchars(ucfirst($mb['method']), ENT_QUOTES, 'UTF-8') ?></div>
        <div class="value"><?= number_format((float)$mb['total_amount'], 0, ',', ' ') ?> €</div>
        <div class="sub"><?= (int)$mb['count'] ?> paiements · moy. <?= number_format((float)$mb['avg_amount'], 0, ',', ' ') ?> €</div>
      </div>
      <?php endforeach; ?>
    </div>

    <div class="charts-grid">

      <!-- Methods pie -->
      <div class="card">
        <p class="card-title">
          <span class="material-icons" style="vertical-align:middle;font-size:1rem;">pie_chart</span>
          Répartition par mode de paiement (montant)
        </p>
        <div class="chart-container">
          <canvas id="methodsChart"></canvas>
        </div>
      </div>

      <!-- Frequency distribution -->
      <div class="card">
        <p class="card-title">
          <span class="material-icons" style="vertical-align:middle;font-size:1rem;">bar_chart</span>
          Distribution de fréquence de paiement
        </p>
        <div class="chart-container">
          <canvas id="freqChart"></canvas>
        </div>
      </div>

      <!-- Monthly totals -->
      <div class="card" style="grid-column:1/-1;">
        <p class="card-title">
          <span class="material-icons" style="vertical-align:middle;font-size:1rem;">show_chart</span>
          Encaissements mensuels (12 mois)
        </p>
        <div class="chart-container" style="height:260px;">
          <canvas id="monthlyChart"></canvas>
        </div>
      </div>

    </div>

    <!-- Recent payments table -->
    <div class="card">
      <p class="card-title">
        <span class="material-icons" style="vertical-align:middle;font-size:1rem;">receipt</span>
        Derniers paiements reçus
        <a href="<?= APP_URL ?>/export/csv?type=payments" class="btn btn-outline" style="float:right;font-size:0.8rem;padding:0.25rem 0.75rem;">
          <span class="material-icons" style="font-size:0.875rem;">download</span> CSV
        </a>
      </p>
      <div class="table-scroll">
      <table class="data-table">
        <thead>
          <tr>
            <th>Date</th>
            <th>Tiers</th>
            <th>Facture</th>
            <th>Montant</th>
            <th>Mode</th>
            <th>Libellé</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($recentPayments)): ?>
          <tr><td colspan="6" style="text-align:center;color:#5f6368;padding:2rem;">Aucun paiement enregistré.</td></tr>
          <?php else: ?>
          <?php foreach ($recentPayments as $p): ?>
          <tr>
            <td><?= $p['date_payment'] ? htmlspecialchars(date('d/m/Y', strtotime($p['date_payment'])), ENT_QUOTES, 'UTF-8') : '–' ?></td>
            <td><?= htmlspecialchars($p['tiers_name'] ?? '–', ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars($p['invoice_ref'] ?? '–', ENT_QUOTES, 'UTF-8') ?></td>
            <td style="font-weight:500;"><?= number_format((float)$p['amount'], 2, ',', ' ') ?> €</td>
            <td>
              <?php
                $mColor = ['CB' => 'badge-info', 'virement' => 'badge-success', 'chèque' => 'badge-warning', 'espèces' => 'badge-medium'];
                $mc = $mColor[$p['method']] ?? 'badge-info';
              ?>
              <span class="badge <?= htmlspecialchars($mc, ENT_QUOTES, 'UTF-8') ?>">
                <?= htmlspecialchars(ucfirst($p['method'] ?? 'inconnu'), ENT_QUOTES, 'UTF-8') ?>
              </span>
            </td>
            <td style="font-size:0.8125rem;color:#5f6368;"><?= htmlspecialchars($p['method_label'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
          </tr>
          <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
      </div>
    </div>

  </div>
</div>

<?php
$mLabels = json_encode(array_map('ucfirst', array_column($methodsBreakdown, 'method')));
$mValues = json_encode(array_column($methodsBreakdown, 'total_amount'));
$mCounts = json_encode(array_column($methodsBreakdown, 'count'));

$freqLabels = json_encode(array_map('ucfirst', array_keys($frequencyDist)));
$freqValues = json_encode(array_values($frequencyDist));

$monthLabels = json_encode(array_column($monthlyTotals, 'label'));
$monthValues = json_encode(array_column($monthlyTotals, 'amount'));
?>

<script>
const COLORS = ['#1a73e8','#34a853','#fbbc04','#ea4335','#ab47bc','#00acc1'];

// Methods pie
new Chart(document.getElementById('methodsChart'), {
  type: 'doughnut',
  data: {
    labels: <?= $mLabels ?>,
    datasets: [{
      data: <?= $mValues ?>,
      backgroundColor: COLORS,
      borderWidth: 2,
      borderColor: '#fff'
    }]
  },
  options: {
    responsive: true, maintainAspectRatio: false,
    plugins: {
      legend: { position: 'bottom' },
      tooltip: { callbacks: { label: ctx => ' ' + ctx.label + ': ' + Number(ctx.raw).toLocaleString('fr-FR') + ' €' } }
    }
  }
});

// Frequency bar
new Chart(document.getElementById('freqChart'), {
  type: 'bar',
  data: {
    labels: <?= $freqLabels ?>,
    datasets: [{
      label: 'Nombre de tiers',
      data: <?= $freqValues ?>,
      backgroundColor: COLORS,
      borderRadius: 6,
    }]
  },
  options: {
    responsive: true, maintainAspectRatio: false,
    plugins: { legend: { display: false } },
    scales: { y: { beginAtZero: true, ticks: { precision: 0 } }, x: { grid: { display: false } } }
  }
});

// Monthly line
new Chart(document.getElementById('monthlyChart'), {
  type: 'bar',
  data: {
    labels: <?= $monthLabels ?>,
    datasets: [{
      label: 'Encaissements (€)',
      data: <?= $monthValues ?>,
      backgroundColor: 'rgba(26,115,232,0.75)',
      borderColor: '#1a73e8',
      borderWidth: 1,
      borderRadius: 4,
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
</script>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
