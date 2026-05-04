<?php require_once __DIR__ . '/partials/header.php'; ?>
<?php require_once __DIR__ . '/partials/sidebar.php'; ?>

<?php
$methodBadge = ['CB'=>'bg-blue-100 text-blue-700','virement'=>'bg-emerald-100 text-emerald-700','chèque'=>'bg-amber-100 text-amber-700','espèces'=>'bg-purple-100 text-purple-700','inconnu'=>'bg-slate-100 text-slate-600'];
$csrf = htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8');
?>

<div id="main-wrap" class="flex-1 flex flex-col overflow-hidden ml-64">
  <header class="bg-white border-b border-slate-200 px-6 h-16 flex items-center justify-between flex-shrink-0 sticky top-0 z-20">
    <div class="flex items-center gap-3">
      <button id="menu-toggle" class="lg:hidden p-2 rounded-lg text-slate-500 hover:bg-slate-100">
        <span class="material-icons-round">menu</span>
      </button>
      <span class="material-icons-round text-blue-600 text-2xl">payments</span>
      <h1 class="text-xl font-semibold text-slate-900 font-display">Paiements</h1>
    </div>
    <div class="flex items-center gap-3">
      <?php if (!empty($user['avatar'])): ?>
      <img src="<?= htmlspecialchars($user['avatar'], ENT_QUOTES, 'UTF-8') ?>" class="w-9 h-9 rounded-full object-cover">
      <?php endif; ?>
      <span class="text-sm font-medium text-slate-700 hidden sm:block"><?= htmlspecialchars($user['name'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
    </div>
  </header>

  <main class="flex-1 overflow-y-auto p-6 space-y-5" id="payments-page" v-cloak>

    <!-- Flash -->
    <?php if (!empty($_GET['message'])): ?>
    <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-4 py-3 text-sm">
      <span class="material-icons-round text-emerald-500 text-lg">check_circle</span>
      <?= htmlspecialchars($_GET['message'], ENT_QUOTES, 'UTF-8') ?>
    </div>
    <?php endif; ?>
    <?php if (!empty($_GET['error'])): ?>
    <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 text-sm">
      <span class="material-icons-round text-red-500 text-lg">error</span>
      <?= htmlspecialchars($_GET['error'], ENT_QUOTES, 'UTF-8') ?>
    </div>
    <?php endif; ?>

    <!-- Add payment form -->
    <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm">
      <p class="text-sm font-semibold text-slate-900 mb-4 flex items-center gap-2">
        <span class="material-icons-round text-blue-500 text-base">add_circle</span>
        Enregistrer un paiement
      </p>
      <form method="POST" action="<?= APP_URL ?>/payments/store"
            class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-5 gap-4">
        <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
        <div>
          <label class="block text-xs font-medium text-slate-600 mb-1">Client</label>
          <select name="tiers_id" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
            <option value="">— Optionnel —</option>
            <?php foreach ($tiersAll as $t): ?>
            <option value="<?= (int)$t['id'] ?>"><?= htmlspecialchars($t['name'], ENT_QUOTES, 'UTF-8') ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label class="block text-xs font-medium text-slate-600 mb-1">Montant (€) *</label>
          <input type="number" name="amount" step="0.01" min="0.01" placeholder="0.00" required
                 class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
          <label class="block text-xs font-medium text-slate-600 mb-1">Date *</label>
          <input type="date" name="date_payment" value="<?= date('Y-m-d') ?>" required
                 class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
          <label class="block text-xs font-medium text-slate-600 mb-1">Mode</label>
          <select name="method" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
            <option value="virement">Virement</option>
            <option value="CB">CB</option>
            <option value="chèque">Chèque</option>
            <option value="espèces">Espèces</option>
            <option value="inconnu">Inconnu</option>
          </select>
        </div>
        <div class="flex flex-col justify-end">
          <label class="block text-xs font-medium text-slate-600 mb-1">Libellé</label>
          <div class="flex gap-2">
            <input type="text" name="method_label" placeholder="ex: Virt. SEPA"
                   class="flex-1 px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <button type="submit" class="inline-flex items-center gap-1 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors whitespace-nowrap">
              <span class="material-icons-round text-base">save</span>
            </button>
          </div>
        </div>
      </form>
    </div>

    <!-- KPI summary -->
    <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-5 gap-4">
      <div class="xl:col-span-2 bg-white border border-slate-200 rounded-xl p-4 shadow-sm">
        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Total encaissé</p>
        <p class="text-3xl font-bold text-slate-900 mt-1"><?= number_format($totalCollected, 0, ',', ' ') ?> €</p>
      </div>
      <?php foreach (array_slice($methodsBreakdown, 0, 3) as $mb): ?>
      <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm">
        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide"><?= htmlspecialchars(ucfirst($mb['method']), ENT_QUOTES, 'UTF-8') ?></p>
        <p class="text-2xl font-bold text-slate-900 mt-1"><?= number_format((float)$mb['total_amount'], 0, ',', ' ') ?> €</p>
        <p class="text-xs text-slate-500 mt-1"><?= (int)$mb['count'] ?> paiements · moy. <?= number_format((float)$mb['avg_amount'], 0, ',', ' ') ?> €</p>
      </div>
      <?php endforeach; ?>
    </div>

    <!-- Charts -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-5">
      <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm">
        <p class="text-sm font-semibold text-slate-900 mb-4 flex items-center gap-2">
          <span class="material-icons-round text-blue-500 text-base">pie_chart</span>
          Répartition par mode
        </p>
        <div style="position:relative;height:220px;">
          <canvas id="methodsChart"></canvas>
        </div>
      </div>
      <div class="xl:col-span-2 bg-white border border-slate-200 rounded-xl p-5 shadow-sm">
        <p class="text-sm font-semibold text-slate-900 mb-4 flex items-center gap-2">
          <span class="material-icons-round text-emerald-500 text-base">show_chart</span>
          Encaissements mensuels (12 mois)
        </p>
        <div style="position:relative;height:220px;">
          <canvas id="monthlyChart"></canvas>
        </div>
      </div>
    </div>

    <!-- Payments table -->
    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm">
      <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
        <p class="text-sm font-semibold text-slate-900 flex items-center gap-2">
          <span class="material-icons-round text-slate-500 text-base">receipt</span>
          Derniers paiements
        </p>
        <a href="<?= APP_URL ?>/export/csv?type=payments"
           class="inline-flex items-center gap-1 px-3 py-1.5 border border-slate-200 text-slate-600 text-xs font-medium rounded-lg hover:bg-slate-50 transition-colors">
          <span class="material-icons-round text-sm">download</span> CSV
        </a>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-slate-50">
            <tr>
              <?php foreach(['Date','Client','Facture','Montant','Mode','Libellé',''] as $h): ?>
              <th class="px-4 py-2.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide whitespace-nowrap"><?= $h ?></th>
              <?php endforeach; ?>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <?php if (empty($recentPayments)): ?>
            <tr><td colspan="7" class="px-4 py-8 text-center text-sm text-slate-400">Aucun paiement enregistré.</td></tr>
            <?php else: ?>
            <?php foreach ($recentPayments as $p): ?>
            <tr class="hover:bg-slate-50 transition-colors">
              <td class="px-4 py-3 text-sm text-slate-600 whitespace-nowrap"><?= $p['date_payment'] ? date('d/m/Y', strtotime($p['date_payment'])) : '–' ?></td>
              <td class="px-4 py-3 text-sm text-slate-700 max-w-[140px] truncate"><?= htmlspecialchars($p['tiers_name'] ?? '–', ENT_QUOTES, 'UTF-8') ?></td>
              <td class="px-4 py-3 text-sm text-slate-600"><?= htmlspecialchars($p['invoice_ref'] ?? '–', ENT_QUOTES, 'UTF-8') ?></td>
              <td class="px-4 py-3 text-sm font-semibold text-slate-900 whitespace-nowrap"><?= number_format((float)$p['amount'], 2, ',', ' ') ?> €</td>
              <td class="px-4 py-3">
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium <?= $methodBadge[$p['method']] ?? 'bg-slate-100 text-slate-600' ?>">
                  <?= htmlspecialchars(ucfirst($p['method'] ?? 'inconnu'), ENT_QUOTES, 'UTF-8') ?>
                </span>
              </td>
              <td class="px-4 py-3 text-xs text-slate-400 max-w-[120px] truncate"><?= htmlspecialchars($p['method_label'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
              <td class="px-4 py-3">
                <button @click="confirmDelete={id:<?= (int)$p['id'] ?>,amount:'<?= number_format((float)$p['amount'],2,',',' ') ?> €'}"
                        class="inline-flex items-center gap-1 px-2.5 py-1.5 border border-red-200 text-red-500 text-xs font-medium rounded-lg hover:bg-red-50 transition-colors">
                  <span class="material-icons-round text-xs">delete</span>
                </button>
              </td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Delete modal -->
    <div v-if="confirmDelete" class="fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4" @click.self="confirmDelete=null">
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6" @click.stop>
        <div class="flex items-center gap-3 mb-4">
          <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center">
            <span class="material-icons-round text-red-600">delete_forever</span>
          </div>
          <div>
            <h3 class="text-base font-semibold text-slate-900">Supprimer ce paiement ?</h3>
            <p class="text-sm text-slate-500">{{ confirmDelete.amount }}</p>
          </div>
        </div>
        <div class="flex gap-3">
          <button @click="confirmDelete=null" class="flex-1 px-4 py-2 border border-slate-300 text-slate-700 text-sm font-medium rounded-xl hover:bg-slate-50">Annuler</button>
          <form :action="'<?= APP_URL ?>/payments/delete/' + confirmDelete.id" method="POST" class="flex-1">
            <input type="hidden" name="csrf_token" :value="csrf">
            <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-xl hover:bg-red-700">Supprimer</button>
          </form>
        </div>
      </div>
    </div>

  </main>
</div>

<?php
$mLabels  = json_encode(array_map('ucfirst', array_column($methodsBreakdown, 'method')));
$mValues  = json_encode(array_column($methodsBreakdown, 'total_amount'));
$mLabels12 = json_encode(array_column($monthlyTotals, 'label'));
$mValues12 = json_encode(array_column($monthlyTotals, 'amount'));
?>
<script>
const { createApp, ref } = Vue;
createApp({
  setup() {
    const confirmDelete = ref(null);
    const csrf = '<?= $csrf ?>';
    return { confirmDelete, csrf };
  }
}).mount('#payments-page');

// Charts
new Chart(document.getElementById('methodsChart'), {
  type: 'doughnut',
  data: { labels: <?= $mLabels ?>, datasets: [{ data: <?= $mValues ?>, backgroundColor: CHART_COLORS, borderWidth: 2, borderColor: '#fff', hoverOffset: 6 }] },
  options: { responsive: true, maintainAspectRatio: false, cutout: '60%' }
});
new Chart(document.getElementById('monthlyChart'), {
  type: 'bar',
  data: {
    labels: <?= $mLabels12 ?>,
    datasets: [{ label: 'Encaissements (€)', data: <?= $mValues12 ?>, backgroundColor: 'rgba(16,185,129,.8)', borderColor: '#10b981', borderWidth: 1, borderRadius: 5 }]
  },
  options: {
    responsive: true, maintainAspectRatio: false,
    plugins: { legend: { display: false } },
    scales: { y: { beginAtZero: true, ticks: { callback: v => v.toLocaleString('fr-FR') + ' €' }, grid: { color: '#f1f5f9' } }, x: { grid: { display: false } } }
  }
});
</script>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
