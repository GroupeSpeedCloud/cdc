<?php require_once __DIR__ . '/partials/header.php'; ?>
<?php require_once __DIR__ . '/partials/sidebar.php'; ?>

<div id="main">
  <header id="topbar">
    <div style="display:flex;align-items:center;min-width:0;">
      <button id="menu-toggle" aria-label="Ouvrir le menu">
        <span class="material-icons">menu</span>
      </button>
      <h1>
        <a href="<?= APP_URL ?>/tiers" style="color:inherit;text-decoration:none;">
          <span class="material-icons" style="vertical-align:middle;font-size:1rem;">arrow_back</span>
        </a>
        &nbsp;<?= htmlspecialchars($tiers['name'], ENT_QUOTES, 'UTF-8') ?>
      </h1>
    </div>
    <div class="topbar-user">
      <?php if (!empty($user['avatar'])): ?>
        <img src="<?= htmlspecialchars($user['avatar'], ENT_QUOTES, 'UTF-8') ?>" alt="Avatar">
      <?php endif; ?>
      <span><?= htmlspecialchars($user['name'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
    </div>
  </header>

  <div id="content">

    <!-- Alerts -->
    <?php if (!empty($alerts)): ?>
    <div style="margin-bottom:1.5rem;">
      <?php foreach ($alerts as $alert): ?>
        <div class="alert alert-<?= htmlspecialchars($alert['type'], ENT_QUOTES, 'UTF-8') ?>">
          <span class="material-icons" style="font-size:1.125rem;flex-shrink:0;">
            <?= $alert['type'] === 'danger' ? 'error_outline' : ($alert['type'] === 'warning' ? 'warning_amber' : 'info') ?>
          </span>
          <?= htmlspecialchars($alert['message'], ENT_QUOTES, 'UTF-8') ?>
        </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- KPI row -->
    <div class="kpi-grid" style="margin-bottom:1.5rem;">
      <div class="kpi-card">
        <div class="label">CA total</div>
        <div class="value"><?= number_format((float)($tiers['revenue_paid'] ?? 0), 0, ',', ' ') ?> €</div>
        <div class="sub"><?= (int)($tiers['invoice_count'] ?? 0) ?> factures</div>
      </div>

      <div class="kpi-card <?= $riskLevel === 'high' ? 'danger' : ($riskLevel === 'medium' ? 'warning' : 'success') ?>">
        <div class="label">Score de risque</div>
        <div class="value"><?= (int)$riskScore ?>/100</div>
        <div class="sub">
          <?= $riskLevel === 'high' ? 'Élevé' : ($riskLevel === 'medium' ? 'Modéré' : 'Faible') ?>
        </div>
      </div>

      <div class="kpi-card <?= (int)($tiers['overdue_count'] ?? 0) > 0 ? 'danger' : '' ?>">
        <div class="label">Factures en retard</div>
        <div class="value"><?= (int)($tiers['overdue_count'] ?? 0) ?></div>
      </div>

      <div class="kpi-card">
        <div class="label">Mode de paiement principal</div>
        <div class="value" style="font-size:1.125rem;"><?= htmlspecialchars(ucfirst($mainMethod), ENT_QUOTES, 'UTF-8') ?></div>
        <div class="sub">Fréquence : <?= htmlspecialchars($frequency, ENT_QUOTES, 'UTF-8') ?></div>
      </div>

      <div class="kpi-card">
        <div class="label">Retards de paiement</div>
        <div class="value"><?= (int)($delayStats['delayed_count'] ?? 0) ?></div>
        <div class="sub">délai moyen : <?= round((float)($delayStats['avg_delay_days'] ?? 0), 1) ?> j</div>
      </div>

      <?php if ($tiers['first_invoice_date']): ?>
      <div class="kpi-card">
        <div class="label">Client depuis</div>
        <div class="value" style="font-size:1.125rem;">
          <?= htmlspecialchars(date('d/m/Y', strtotime($tiers['first_invoice_date'])), ENT_QUOTES, 'UTF-8') ?>
        </div>
      </div>
      <?php endif; ?>
    </div>

    <div class="charts-grid">

      <!-- Revenue history -->
      <div class="card">
        <p class="card-title">
          <span class="material-icons" style="vertical-align:middle;font-size:1rem;">show_chart</span>
          Évolution du CA (12 mois)
        </p>
        <div class="chart-container">
          <canvas id="revenueChart"></canvas>
        </div>
      </div>

      <!-- Tiers info -->
      <div class="card">
        <p class="card-title">
          <span class="material-icons" style="vertical-align:middle;font-size:1rem;">contact_page</span>
          Informations
        </p>
        <table class="data-table">
          <tr>
            <td style="color:#5f6368;width:140px;">Nom</td>
            <td style="font-weight:500;"><?= htmlspecialchars($tiers['name'], ENT_QUOTES, 'UTF-8') ?></td>
          </tr>
          <?php if (!empty($tiers['email'])): ?>
          <tr>
            <td style="color:#5f6368;">Email</td>
            <td><?= htmlspecialchars($tiers['email'], ENT_QUOTES, 'UTF-8') ?></td>
          </tr>
          <?php endif; ?>
          <?php if (!empty($tiers['phone'])): ?>
          <tr>
            <td style="color:#5f6368;">Téléphone</td>
            <td><?= htmlspecialchars($tiers['phone'], ENT_QUOTES, 'UTF-8') ?></td>
          </tr>
          <?php endif; ?>
          <?php if (!empty($tiers['address'])): ?>
          <tr>
            <td style="color:#5f6368;">Adresse</td>
            <td><?= htmlspecialchars($tiers['address'], ENT_QUOTES, 'UTF-8') ?></td>
          </tr>
          <?php endif; ?>
          <tr>
            <td style="color:#5f6368;">Statut</td>
            <td>
              <span class="badge <?= $tiers['is_active'] ? 'badge-success' : 'badge-medium' ?>">
                <?= $tiers['is_active'] ? 'Actif' : 'Inactif' ?>
              </span>
            </td>
          </tr>
        </table>
      </div>

    </div>

    <!-- Invoices table -->
    <div class="card" style="margin-bottom:1.5rem;">
      <p class="card-title">
        <span class="material-icons" style="vertical-align:middle;font-size:1rem;">receipt_long</span>
        Factures récentes
      </p>
      <div class="table-scroll">
      <table class="data-table">
        <thead>
          <tr>
            <th>Référence</th>
            <th>Date</th>
            <th>Échéance</th>
            <th>Payé le</th>
            <th>Total HT</th>
            <th>Statut</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($invoices)): ?>
          <tr><td colspan="6" style="text-align:center;color:#5f6368;">Aucune facture.</td></tr>
          <?php else: ?>
          <?php foreach ($invoices as $inv): ?>
          <tr>
            <td style="font-weight:500;"><?= htmlspecialchars($inv['ref'], ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= $inv['date_invoice'] ? htmlspecialchars(date('d/m/Y', strtotime($inv['date_invoice'])), ENT_QUOTES, 'UTF-8') : '–' ?></td>
            <td><?= $inv['date_due'] ? htmlspecialchars(date('d/m/Y', strtotime($inv['date_due'])), ENT_QUOTES, 'UTF-8') : '–' ?></td>
            <td><?= $inv['date_paid'] ? htmlspecialchars(date('d/m/Y', strtotime($inv['date_paid'])), ENT_QUOTES, 'UTF-8') : '–' ?></td>
            <td><?= number_format((float)$inv['total_ht'], 2, ',', ' ') ?> €</td>
            <td>
              <?php
                $statusMap = [0 => ['Brouillon','badge-info'], 1 => ['Validée','badge-warning'], 2 => ['Payée','badge-success'], 3 => ['Abandonnée','badge-medium']];
                [$slabel, $sclass] = $statusMap[$inv['status']] ?? ['–','badge-info'];
              ?>
              <span class="badge <?= htmlspecialchars($sclass, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($slabel, ENT_QUOTES, 'UTF-8') ?></span>
              <?php if ($inv['is_overdue']): ?>
                <span class="badge badge-danger" style="margin-left:0.25rem;">Retard</span>
              <?php endif; ?>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
      </div>
    </div>

    <!-- Payments table -->
    <div class="card">
      <p class="card-title">
        <span class="material-icons" style="vertical-align:middle;font-size:1rem;">payments</span>
        Historique des paiements
      </p>
      <div class="table-scroll">
      <table class="data-table">
        <thead>
          <tr>
            <th>Date</th>
            <th>Facture</th>
            <th>Montant</th>
            <th>Mode</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($payments)): ?>
          <tr><td colspan="4" style="text-align:center;color:#5f6368;">Aucun paiement.</td></tr>
          <?php else: ?>
          <?php foreach ($payments as $pay): ?>
          <tr>
            <td><?= $pay['date_payment'] ? htmlspecialchars(date('d/m/Y', strtotime($pay['date_payment'])), ENT_QUOTES, 'UTF-8') : '–' ?></td>
            <td><?= htmlspecialchars($pay['invoice_ref'] ?? '–', ENT_QUOTES, 'UTF-8') ?></td>
            <td style="font-weight:500;"><?= number_format((float)$pay['amount'], 2, ',', ' ') ?> €</td>
            <td>
              <?php
                $mColor = ['CB' => 'badge-info', 'virement' => 'badge-success', 'chèque' => 'badge-warning', 'espèces' => 'badge-medium'];
                $mc = $mColor[$pay['method']] ?? 'badge-info';
              ?>
              <span class="badge <?= htmlspecialchars($mc, ENT_QUOTES, 'UTF-8') ?>">
                <?= htmlspecialchars(ucfirst($pay['method'] ?? 'inconnu'), ENT_QUOTES, 'UTF-8') ?>
              </span>
            </td>
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
$revLabels = json_encode(array_column($revenueHist, 'label'));
$revValues = json_encode(array_column($revenueHist, 'revenue'));
?>

<script>
new Chart(document.getElementById('revenueChart'), {
  type: 'bar',
  data: {
    labels: <?= $revLabels ?>,
    datasets: [{
      label: 'CA (€)',
      data: <?= $revValues ?>,
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
