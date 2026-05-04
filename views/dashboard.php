<?php require_once __DIR__ . '/partials/header.php'; ?>
<?php require_once __DIR__ . '/partials/sidebar.php'; ?>

<?php
$annual = $kpis['annual_summary'] ?? [];
$year   = (int)($annual['year'] ?? date('Y'));
$alerts = $annual['alerts'] ?? [];

$colorMap = [
  'blue'    => ['bg-blue-50',    'text-blue-600'],
  'sky'     => ['bg-sky-50',     'text-sky-600'],
  'emerald' => ['bg-emerald-50', 'text-emerald-600'],
  'amber'   => ['bg-amber-50',   'text-amber-600'],
  'red'     => ['bg-red-50',     'text-red-600'],
];
?>

<div id="main-wrap" class="flex-1 flex flex-col overflow-hidden ml-64">

  <header class="bg-white/90 border-b border-slate-200 px-6 h-14 flex items-center justify-between flex-shrink-0 sticky top-0 z-20" style="backdrop-filter:blur(10px)">
    <div class="flex items-center gap-2.5">
      <button id="menu-toggle" class="lg:hidden p-1.5 rounded-lg text-slate-400 hover:bg-slate-100">
        <span class="material-icons-round text-xl">menu</span>
      </button>
      <h1 class="text-base font-semibold text-slate-900 font-display">Tableau de bord</h1>
      <span class="text-xs text-slate-400 font-medium"><?= $year ?></span>
    </div>
    <div class="flex items-center gap-2.5">
      <?php if (!empty($user['avatar'])): ?>
      <img src="<?= htmlspecialchars($user['avatar'], ENT_QUOTES, 'UTF-8') ?>" class="w-7 h-7 rounded-full" alt="">
      <?php endif; ?>
      <span class="text-sm font-medium text-slate-600 hidden sm:block"><?= htmlspecialchars($user['name'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
    </div>
  </header>

  <main class="flex-1 overflow-y-auto p-5 space-y-4">

    <?php if (!empty($alerts)): ?>
    <div class="flex flex-wrap gap-2">
      <?php foreach ($alerts as $alert): ?>
      <div class="inline-flex items-center gap-1.5 bg-amber-50 border border-amber-200 text-amber-800 rounded-lg px-3 py-1.5 text-xs font-medium">
        <span class="material-icons-round text-amber-500" style="font-size:14px">warning_amber</span>
        <?= htmlspecialchars($alert, ENT_QUOTES, 'UTF-8') ?>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- Primary KPIs -->
    <div class="grid grid-cols-2 xl:grid-cols-4 gap-3">
      <?php
      $ap = (float)($annual['annual_profit'] ?? 0);
      $mp = (float)($annual['margin_pct']    ?? 0);
      $primary = [
        ['CA annuel encaissé',
          number_format((float)($annual['annual_revenue'] ?? 0), 0, ',', ' ').' €',
          'Encaissé '.$year, 'blue', 'payments'],
        ['Run-rate annuel',
          number_format((float)($annual['run_rate_annual'] ?? 0), 0, ',', ' ').' €',
          'Projection rythme actuel', 'sky', 'trending_up'],
        ['Résultat annuel',
          ($ap >= 0 ? '+' : '').number_format($ap, 0, ',', ' ').' €',
          $ap >= 0 ? 'Rentable' : 'Déficitaire',
          $ap >= 0 ? 'emerald' : 'red', 'account_balance'],
        ['Marge nette',
          ($mp >= 0 ? '+' : '').number_format($mp, 1, ',', '.').' %',
          'Résultat / CA encaissé',
          $mp >= 20 ? 'emerald' : ($mp >= 5 ? 'amber' : 'red'), 'percent'],
      ];
      foreach ($primary as [$label, $val, $sub, $color, $icon]):
        [$ibg, $itxt] = $colorMap[$color];
      ?>
      <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-start justify-between mb-3">
          <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider leading-tight"><?= $label ?></p>
          <span class="<?= $ibg ?> <?= $itxt ?> w-7 h-7 rounded-lg flex items-center justify-center flex-shrink-0">
            <span class="material-icons-round" style="font-size:16px"><?= $icon ?></span>
          </span>
        </div>
        <p class="text-2xl font-bold text-slate-900 leading-none"><?= $val ?></p>
        <p class="text-xs mt-2 <?= $itxt ?> font-medium"><?= htmlspecialchars($sub) ?></p>
      </div>
      <?php endforeach; ?>
    </div>

    <!-- Secondary KPIs -->
    <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-5 gap-3">
      <?php
      $mr  = (float)($annual['monthly_revenue']    ?? 0);
      $mgp = (float)($annual['monthly_growth_pct'] ?? 0);
      $mp2 = (float)($annual['monthly_profit']     ?? 0);
      $ep  = (float)($annual['expense_rate_pct']   ?? 0);
      $pr  = (float)($annual['paid_rate_pct']      ?? 0);
      $od  = (float)($annual['avg_overdue_days']   ?? 0);
      $secondary = [
        ['CA du mois',
          number_format($mr, 0, ',', ' ').' €',
          ($mgp >= 0 ? '↑ ' : '↓ ').number_format(abs($mgp), 1, ',', '.').'% vs mois préc.',
          $mgp >= 0 ? 'text-emerald-600' : 'text-red-500'],
        ['Résultat mois',
          ($mp2 >= 0 ? '+' : '').number_format($mp2, 0, ',', ' ').' €',
          $mp2 >= 0 ? 'Mois rentable' : 'Mois déficitaire',
          $mp2 >= 0 ? 'text-emerald-600' : 'text-red-500'],
        ['Taux de charges',
          number_format($ep, 1, ',', '.').' %',
          'Charges / CA', 'text-amber-600'],
        ['Factures payées',
          number_format($pr, 1, ',', '.').' %',
          (int)($kpis['invoice_counts']['paid'] ?? 0).'/'.(int)($kpis['invoice_counts']['total'] ?? 0).' factures',
          $pr >= 90 ? 'text-emerald-600' : 'text-amber-600'],
        ['Retard moyen',
          number_format($od, 1, ',', '.').' j',
          'Sur factures en retard',
          $od <= 10 ? 'text-emerald-600' : ($od <= 30 ? 'text-amber-600' : 'text-red-500')],
      ];
      foreach ($secondary as [$label, $val, $sub, $subColor]): ?>
      <div class="bg-white border border-slate-100 rounded-xl px-4 py-3 shadow-sm">
        <p class="text-[11px] text-slate-400 font-medium uppercase tracking-wide truncate"><?= $label ?></p>
        <p class="text-xl font-bold text-slate-800 mt-1 leading-none"><?= $val ?></p>
        <p class="text-[11px] mt-1.5 <?= $subColor ?> font-medium"><?= htmlspecialchars($sub) ?></p>
      </div>
      <?php endforeach; ?>
    </div>

    <!-- Charts -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">
      <div class="xl:col-span-2 bg-white border border-slate-200 rounded-xl p-5 shadow-sm">
        <p class="text-sm font-semibold text-slate-700 mb-4 flex items-center gap-2">
          <span class="bg-blue-50 text-blue-600 w-6 h-6 rounded-md flex items-center justify-center">
            <span class="material-icons-round" style="font-size:15px">show_chart</span>
          </span>
          Évolution du CA — 12 mois
        </p>
        <div style="position:relative;height:200px">
          <canvas id="revenueChart"></canvas>
        </div>
      </div>
      <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm">
        <p class="text-sm font-semibold text-slate-700 mb-4 flex items-center gap-2">
          <span class="bg-amber-50 text-amber-600 w-6 h-6 rounded-md flex items-center justify-center">
            <span class="material-icons-round" style="font-size:15px">pie_chart</span>
          </span>
          Dépenses par catégorie
        </p>
        <?php if (!empty($annual['expense_categories'])): ?>
        <div style="position:relative;height:185px">
          <canvas id="expenseChart"></canvas>
        </div>
        <?php else: ?>
        <div class="flex flex-col items-center justify-center h-36 text-slate-300">
          <span class="material-icons-round text-4xl mb-2">receipt_long</span>
          <p class="text-sm">Aucune dépense</p>
        </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Tables -->
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-4">

      <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm">
        <div class="px-4 py-3 border-b border-slate-100 flex items-center gap-2">
          <span class="bg-blue-50 text-blue-600 w-6 h-6 rounded-md flex items-center justify-center">
            <span class="material-icons-round" style="font-size:14px">star</span>
          </span>
          <span class="text-sm font-semibold text-slate-800">Top clients</span>
        </div>
        <table class="w-full text-sm">
          <thead><tr class="bg-slate-50 text-[11px] font-semibold text-slate-400 uppercase tracking-wide">
            <th class="px-4 py-2.5 text-left">Client</th>
            <th class="px-4 py-2.5 text-right">CA</th>
            <th class="px-4 py-2.5 text-right">Part</th>
          </tr></thead>
          <tbody class="divide-y divide-slate-100">
            <?php
            $topTiers = $kpis['top_tiers'] ?? [];
            $totalRev = max(1, (float)($annual['annual_revenue'] ?? 1));
            foreach (array_slice($topTiers, 0, 8) as $i => $t):
              $share = round(((float)$t['revenue'] / $totalRev) * 100, 1);
            ?>
            <tr class="hover:bg-slate-50/60 transition-colors">
              <td class="px-4 py-2.5">
                <div class="flex items-center gap-2">
                  <span class="w-5 h-5 rounded-full bg-blue-600 text-white text-[10px] flex items-center justify-center font-bold flex-shrink-0"><?= $i + 1 ?></span>
                  <a href="<?= APP_URL ?>/tiers/<?= (int)$t['id'] ?>" class="font-medium text-slate-800 hover:text-blue-600 truncate max-w-[150px]">
                    <?= htmlspecialchars($t['name'], ENT_QUOTES, 'UTF-8') ?>
                  </a>
                </div>
              </td>
              <td class="px-4 py-2.5 text-right font-semibold text-slate-800"><?= number_format((float)$t['revenue'], 0, ',', ' ') ?> €</td>
              <td class="px-4 py-2.5 text-right text-slate-400"><?= $share ?>%</td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($topTiers)): ?>
            <tr><td colspan="3" class="px-4 py-5 text-center text-slate-400">Aucune donnée</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm">
        <div class="px-4 py-3 border-b border-slate-100 flex items-center gap-2">
          <span class="bg-emerald-50 text-emerald-600 w-6 h-6 rounded-md flex items-center justify-center">
            <span class="material-icons-round" style="font-size:14px">workspace_premium</span>
          </span>
          <span class="text-sm font-semibold text-slate-800">Top services</span>
        </div>
        <table class="w-full text-sm">
          <thead><tr class="bg-slate-50 text-[11px] font-semibold text-slate-400 uppercase tracking-wide">
            <th class="px-4 py-2.5 text-left">Service</th>
            <th class="px-4 py-2.5 text-right">CA</th>
            <th class="px-4 py-2.5 text-right">Factures</th>
          </tr></thead>
          <tbody class="divide-y divide-slate-100">
            <?php foreach (array_slice($kpis['top_products'] ?? [], 0, 8) as $i => $p): ?>
            <tr class="hover:bg-slate-50/60 transition-colors">
              <td class="px-4 py-2.5">
                <div class="flex items-center gap-2">
                  <span class="w-5 h-5 rounded-full bg-emerald-500 text-white text-[10px] flex items-center justify-center font-bold flex-shrink-0"><?= $i + 1 ?></span>
                  <span class="font-medium text-slate-800 truncate max-w-[150px]"><?= htmlspecialchars($p['name'] ?? ($p['label'] ?? '–'), ENT_QUOTES, 'UTF-8') ?></span>
                </div>
              </td>
              <td class="px-4 py-2.5 text-right font-semibold text-slate-800"><?= number_format((float)($p['revenue'] ?? $p['total'] ?? 0), 0, ',', ' ') ?> €</td>
              <td class="px-4 py-2.5 text-right text-slate-400"><?= (int)($p['count'] ?? 0) ?></td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($kpis['top_products'] ?? [])): ?>
            <tr><td colspan="3" class="px-4 py-5 text-center text-slate-400">Aucune donnée</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

    </div>
  </main>
</div>

<?php
$revLabels = json_encode(array_column($kpis['revenue_evolution'] ?? [], 'month'));
$revValues = json_encode(array_map(fn($r) => (float)($r['revenue'] ?? 0), $kpis['revenue_evolution'] ?? []));
$expCats   = $annual['expense_categories'] ?? [];
$expLabels = json_encode(array_column($expCats, 'category'));
$expValues = json_encode(array_column($expCats, 'monthly_total'));
?>
<script>
(function () {
  const rc = document.getElementById('revenueChart');
  if (rc) new Chart(rc, {
    type: 'line',
    data: {
      labels: <?= $revLabels ?>,
      datasets: [{
        label: 'CA (€)', data: <?= $revValues ?>,
        borderColor: '#3b82f6', backgroundColor: 'rgba(59,130,246,.07)',
        borderWidth: 2, pointBackgroundColor: '#3b82f6', pointRadius: 3,
        tension: 0.4, fill: true
      }]
    },
    options: {
      responsive: true, maintainAspectRatio: false,
      plugins: { legend: { display: false } },
      scales: {
        y: { beginAtZero: true, ticks: { callback: v => v.toLocaleString('fr-FR') + ' €', font: { size: 11 } }, grid: { color: '#f1f5f9' } },
        x: { grid: { display: false }, ticks: { font: { size: 11 } } }
      }
    }
  });

  const ec = document.getElementById('expenseChart');
  if (ec) new Chart(ec, {
    type: 'doughnut',
    data: {
      labels: <?= $expLabels ?>,
      datasets: [{ data: <?= $expValues ?>, backgroundColor: CHART_COLORS, borderWidth: 2, borderColor: '#fff', hoverOffset: 5 }]
    },
    options: {
      responsive: true, maintainAspectRatio: false,
      cutout: '60%',
      plugins: { legend: { position: 'right', labels: { padding: 10, font: { size: 11 }, boxWidth: 10 } } }
    }
  });
}());
</script>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
