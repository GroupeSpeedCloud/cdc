<?php require_once __DIR__ . '/partials/header.php'; ?>
<?php require_once __DIR__ . '/partials/sidebar.php'; ?>

<?php
$recurrenceLabels = ['monthly'=>'Mensuelle','annual'=>'Annuelle','one_time'=>'Ponctuelle'];
$recurrenceBadge  = ['monthly'=>'bg-blue-100 text-blue-700','annual'=>'bg-purple-100 text-purple-700','one_time'=>'bg-slate-100 text-slate-600'];
$csrf = htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8');
$editExpense = $editExpense ?? null;
?>

<div id="main-wrap" class="flex-1 flex flex-col overflow-hidden ml-64">
  <header class="bg-white border-b border-slate-200 px-6 h-16 flex items-center justify-between flex-shrink-0 sticky top-0 z-20">
    <div class="flex items-center gap-3">
      <button id="menu-toggle" class="lg:hidden p-2 rounded-lg text-slate-500 hover:bg-slate-100">
        <span class="material-icons-round">menu</span>
      </button>
      <span class="material-icons-round text-blue-600 text-2xl">account_balance_wallet</span>
      <h1 class="text-xl font-semibold text-slate-900">Dépenses</h1>
    </div>
    <div class="flex items-center gap-3">
      <?php if (!empty($user['avatar'])): ?>
      <img src="<?= htmlspecialchars($user['avatar'], ENT_QUOTES, 'UTF-8') ?>" class="w-9 h-9 rounded-full object-cover">
      <?php endif; ?>
      <span class="text-sm font-medium text-slate-700 hidden sm:block"><?= htmlspecialchars($user['name'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
    </div>
  </header>

  <main class="flex-1 overflow-y-auto p-6 space-y-5" id="expenses-page" v-cloak>

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

    <!-- KPI grid -->
    <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-6 gap-4">
      <?php
      $kpiRows = [
        ['Revenus (mois)',   $revenueMonth,   'emerald'],
        ['Dépenses (mois)',  $monthlyTotal,   'red'],
        ['Profit (mois)',    $profitMonth,    $profitMonth>=0?'emerald':'red'],
        ['Revenus (année)',  $revenueYear,    'emerald'],
        ['Dépenses (année)', $annualTotal,    'red'],
        ['Profit (année)',   $profitYear,     $profitYear>=0?'emerald':'red'],
      ];
      foreach ($kpiRows as [$label,$val,$color]): ?>
      <div class="bg-white border <?= $color==='emerald'?'border-emerald-100':($color==='red'?'border-red-100':'border-slate-200') ?> rounded-xl p-4 shadow-sm">
        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide"><?= $label ?></p>
        <p class="text-xl font-bold <?= $color==='emerald'?'text-emerald-700':($color==='red'?'text-red-600':'text-slate-900') ?> mt-1">
          <?= number_format((float)$val, 0, ',', ' ') ?> €
        </p>
      </div>
      <?php endforeach; ?>
    </div>

    <!-- Edit form (if editing) -->
    <?php if ($editExpense): ?>
    <div class="bg-amber-50 border border-amber-200 rounded-xl p-5 shadow-sm">
      <p class="text-sm font-semibold text-amber-900 mb-4 flex items-center gap-2">
        <span class="material-icons-round text-amber-600 text-base">edit</span>
        Modifier la dépense
      </p>
      <form method="POST" action="<?= APP_URL ?>/expenses/update/<?= (int)$editExpense['id'] ?>"
            class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
        <div>
          <label class="block text-xs font-medium text-slate-600 mb-1">Libellé *</label>
          <input type="text" name="label" value="<?= htmlspecialchars($editExpense['label'], ENT_QUOTES, 'UTF-8') ?>" required
                 class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
        </div>
        <div>
          <label class="block text-xs font-medium text-slate-600 mb-1">Montant (€) *</label>
          <input type="number" name="amount" step="0.01" min="0.01" value="<?= (float)$editExpense['amount'] ?>" required
                 class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
        </div>
        <div>
          <label class="block text-xs font-medium text-slate-600 mb-1">Catégorie</label>
          <input type="text" name="category" value="<?= htmlspecialchars($editExpense['category'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="ex: Logiciel, Hébergement…"
                 class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
        </div>
        <div>
          <label class="block text-xs font-medium text-slate-600 mb-1">Récurrence</label>
          <select name="recurrence" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 bg-white">
            <?php foreach ($recurrenceLabels as $k => $v): ?>
            <option value="<?= $k ?>" <?= $editExpense['recurrence']===$k?'selected':'' ?>><?= $v ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label class="block text-xs font-medium text-slate-600 mb-1">Date (optionnel)</label>
          <input type="date" name="expense_date" value="<?= htmlspecialchars($editExpense['expense_date'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                 class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
        </div>
        <div class="xl:col-span-3">
          <label class="block text-xs font-medium text-slate-600 mb-1">Note</label>
          <input type="text" name="note" value="<?= htmlspecialchars($editExpense['note'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="Détail ou commentaire…"
                 class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
        </div>
        <div class="sm:col-span-2 xl:col-span-4 flex gap-3">
          <button type="submit" class="inline-flex items-center gap-1.5 px-5 py-2 bg-amber-600 text-white text-sm font-medium rounded-lg hover:bg-amber-700 transition-colors">
            <span class="material-icons-round text-base">save</span> Enregistrer
          </button>
          <a href="<?= APP_URL ?>/expenses" class="inline-flex items-center gap-1.5 px-4 py-2 border border-slate-300 text-slate-700 text-sm font-medium rounded-lg hover:bg-slate-50 transition-colors">Annuler</a>
        </div>
      </form>
    </div>
    <?php endif; ?>

    <!-- Add form (toggle) -->
    <div v-show="showAdd" class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm">
      <p class="text-sm font-semibold text-slate-900 mb-4 flex items-center gap-2">
        <span class="material-icons-round text-blue-500 text-base">add_circle</span>
        Ajouter une dépense
      </p>
      <form method="POST" action="<?= APP_URL ?>/expenses/store"
            class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
        <div>
          <label class="block text-xs font-medium text-slate-600 mb-1">Libellé *</label>
          <input type="text" name="label" placeholder="ex: Abonnement OVH" required
                 class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
          <label class="block text-xs font-medium text-slate-600 mb-1">Montant (€) *</label>
          <input type="number" name="amount" step="0.01" min="0.01" placeholder="0.00" required
                 class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
          <label class="block text-xs font-medium text-slate-600 mb-1">Catégorie</label>
          <input type="text" name="category" placeholder="ex: Logiciel, Hébergement…"
                 class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
          <label class="block text-xs font-medium text-slate-600 mb-1">Récurrence</label>
          <select name="recurrence" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
            <option value="monthly">Mensuelle</option>
            <option value="annual">Annuelle</option>
            <option value="one_time">Ponctuelle</option>
          </select>
        </div>
        <div>
          <label class="block text-xs font-medium text-slate-600 mb-1">Date (optionnel)</label>
          <input type="date" name="expense_date"
                 class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div class="xl:col-span-3">
          <label class="block text-xs font-medium text-slate-600 mb-1">Note</label>
          <input type="text" name="note" placeholder="Détail ou commentaire…"
                 class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div class="sm:col-span-2 xl:col-span-4 flex gap-3">
          <button type="submit" class="inline-flex items-center gap-1.5 px-5 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
            <span class="material-icons-round text-base">save</span> Ajouter
          </button>
          <button type="button" @click="showAdd=false" class="inline-flex items-center gap-1.5 px-4 py-2 border border-slate-300 text-slate-700 text-sm font-medium rounded-lg hover:bg-slate-50 transition-colors">Annuler</button>
        </div>
      </form>
    </div>

    <!-- Toolbar -->
    <div class="flex flex-wrap items-center gap-3">
      <button @click="showAdd = !showAdd"
              class="inline-flex items-center gap-1.5 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
        <span class="material-icons-round text-base">add</span> Nouvelle dépense
      </button>
      <a href="<?= APP_URL ?>/export/csv?type=expenses"
         class="inline-flex items-center gap-1.5 px-4 py-2 border border-slate-300 text-slate-600 text-sm font-medium rounded-lg hover:bg-slate-50 transition-colors">
        <span class="material-icons-round text-base">download</span> CSV
      </a>
    </div>

    <!-- By category -->
    <?php if (!empty($byCategory)): ?>
    <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm">
      <p class="text-sm font-semibold text-slate-900 mb-3 flex items-center gap-2">
        <span class="material-icons-round text-slate-500 text-base">category</span>
        Répartition par catégorie (ce mois)
      </p>
      <div class="flex flex-wrap gap-3">
        <?php foreach ($byCategory as $cat): ?>
        <span class="inline-flex items-center gap-2 px-3 py-1.5 bg-slate-100 text-slate-700 text-xs font-medium rounded-full">
          <?= htmlspecialchars($cat['category'] ?? 'Sans catégorie', ENT_QUOTES, 'UTF-8') ?>
          <span class="font-bold"><?= number_format((float)$cat['monthly_total'], 0, ',', ' ') ?> €</span>
        </span>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>

    <!-- Table -->
    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm">
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-slate-50 border-b border-slate-200">
            <tr>
              <?php foreach(['Libellé','Montant','Catégorie','Récurrence','Date','Note','Actions'] as $h): ?>
              <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide whitespace-nowrap"><?= $h ?></th>
              <?php endforeach; ?>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <?php if (empty($expenses)): ?>
            <tr><td colspan="7" class="px-4 py-10 text-center text-sm text-slate-400">Aucune dépense.</td></tr>
            <?php else: ?>
            <?php foreach ($expenses as $exp): ?>
            <tr class="hover:bg-slate-50 transition-colors">
              <td class="px-4 py-3.5 text-sm font-semibold text-slate-900"><?= htmlspecialchars($exp['label'], ENT_QUOTES, 'UTF-8') ?></td>
              <td class="px-4 py-3.5 text-sm text-red-600 font-semibold whitespace-nowrap"><?= number_format((float)$exp['amount'], 2, ',', ' ') ?> €</td>
              <td class="px-4 py-3.5 text-sm text-slate-600"><?= htmlspecialchars($exp['category'] ?? '–', ENT_QUOTES, 'UTF-8') ?></td>
              <td class="px-4 py-3.5">
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium <?= $recurrenceBadge[$exp['recurrence']] ?? 'bg-slate-100 text-slate-600' ?>">
                  <?= $recurrenceLabels[$exp['recurrence']] ?? $exp['recurrence'] ?>
                </span>
              </td>
              <td class="px-4 py-3.5 text-sm text-slate-600 whitespace-nowrap"><?= !empty($exp['expense_date']) ? date('d/m/Y', strtotime($exp['expense_date'])) : '–' ?></td>
              <td class="px-4 py-3.5 text-xs text-slate-400 max-w-[160px] truncate"><?= htmlspecialchars($exp['note'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
              <td class="px-4 py-3.5">
                <div class="flex items-center gap-1.5">
                  <a href="<?= APP_URL ?>/expenses?edit=<?= (int)$exp['id'] ?>"
                     class="inline-flex items-center gap-1 px-2.5 py-1.5 border border-slate-200 text-slate-500 text-xs font-medium rounded-lg hover:bg-slate-50 transition-colors">
                    <span class="material-icons-round text-xs">edit</span>
                  </a>
                  <button @click="confirmDelete={id:<?= (int)$exp['id'] ?>,label:'<?= htmlspecialchars(addslashes($exp['label']), ENT_QUOTES, 'UTF-8') ?>'}"
                          class="inline-flex items-center gap-1 px-2.5 py-1.5 border border-red-200 text-red-500 text-xs font-medium rounded-lg hover:bg-red-50 transition-colors">
                    <span class="material-icons-round text-xs">delete</span>
                  </button>
                </div>
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
            <h3 class="text-base font-semibold text-slate-900">Supprimer la dépense ?</h3>
            <p class="text-sm text-slate-500">{{ confirmDelete.label }}</p>
          </div>
        </div>
        <div class="flex gap-3">
          <button @click="confirmDelete=null" class="flex-1 px-4 py-2 border border-slate-300 text-slate-700 text-sm font-medium rounded-xl hover:bg-slate-50">Annuler</button>
          <form :action="'<?= APP_URL ?>/expenses/delete/' + confirmDelete.id" method="POST" class="flex-1">
            <input type="hidden" name="csrf_token" :value="csrf">
            <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-xl hover:bg-red-700">Supprimer</button>
          </form>
        </div>
      </div>
    </div>

  </main>
</div>

<script>
const { createApp, ref } = Vue;
createApp({
  setup() {
    const showAdd       = ref(<?= $editExpense ? 'false' : 'false' ?>);
    const confirmDelete = ref(null);
    const csrf          = '<?= $csrf ?>';
    return { showAdd, confirmDelete, csrf };
  }
}).mount('#expenses-page');
</script>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
