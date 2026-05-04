<?php require_once __DIR__ . '/partials/header.php'; ?>
<?php require_once __DIR__ . '/partials/sidebar.php'; ?>

<?php
$statusLabels = [0=>'Brouillon',1=>'Validée',2=>'Payée',3=>'Abandonnée'];
$statusBadge  = [0=>'bg-slate-100 text-slate-600',1=>'bg-amber-100 text-amber-700',2=>'bg-emerald-100 text-emerald-700',3=>'bg-red-100 text-red-600'];
$csrf = htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8');
?>

<div id="main-wrap" class="flex-1 flex flex-col overflow-hidden ml-64">
  <header class="bg-white border-b border-slate-200 px-6 h-16 flex items-center justify-between flex-shrink-0 sticky top-0 z-20">
    <div class="flex items-center gap-3">
      <button id="menu-toggle" class="lg:hidden p-2 rounded-lg text-slate-500 hover:bg-slate-100">
        <span class="material-icons-round">menu</span>
      </button>
      <span class="material-icons-round text-blue-600 text-2xl">receipt_long</span>
      <h1 class="text-xl font-semibold text-slate-900 font-display">Factures</h1>
    </div>
    <div class="flex items-center gap-3">
      <?php if (!empty($user['avatar'])): ?>
      <img src="<?= htmlspecialchars($user['avatar'], ENT_QUOTES, 'UTF-8') ?>" class="w-9 h-9 rounded-full object-cover">
      <?php endif; ?>
      <span class="text-sm font-medium text-slate-700 hidden sm:block"><?= htmlspecialchars($user['name'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
    </div>
  </header>

  <main class="flex-1 overflow-y-auto p-6 space-y-5" id="invoices-page" v-cloak>

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

    <!-- Toolbar -->
    <div class="flex flex-wrap items-center gap-3">
      <form method="GET" action="<?= APP_URL ?>/invoices" class="flex items-center gap-2 flex-1 min-w-0">
        <div class="relative flex-1 max-w-xs">
          <span class="material-icons-round absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-base pointer-events-none">search</span>
          <input type="text" name="search" value="<?= htmlspecialchars($_GET['search'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                 placeholder="Référence ou client…"
                 class="w-full pl-9 pr-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
        </div>
        <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 bg-slate-700 text-white text-sm font-medium rounded-lg hover:bg-slate-800 transition-colors">
          <span class="material-icons-round text-base">search</span>
        </button>
      </form>
      <button @click="showAdd = !showAdd"
              class="inline-flex items-center gap-1.5 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
        <span class="material-icons-round text-base">add</span> Nouvelle facture
      </button>
    </div>

    <!-- Add form -->
    <div v-show="showAdd" class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm">
      <p class="text-sm font-semibold text-slate-900 mb-4 flex items-center gap-2">
        <span class="material-icons-round text-blue-500 text-base">add_circle</span> Ajouter une facture manuellement
      </p>
      <form method="POST" action="<?= APP_URL ?>/invoices/store"
            class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
        <div>
          <label class="block text-xs font-medium text-slate-600 mb-1">Client</label>
          <select name="tiers_id" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
            <option value="">— Sans client —</option>
            <?php foreach ($tiersAll as $t): ?>
            <option value="<?= (int)$t['id'] ?>"><?= htmlspecialchars($t['name'], ENT_QUOTES, 'UTF-8') ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label class="block text-xs font-medium text-slate-600 mb-1">Description / Service</label>
          <input type="text" name="description" placeholder="ex: Maintenance WordPress"
                 class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
          <label class="block text-xs font-medium text-slate-600 mb-1">Référence</label>
          <input type="text" name="ref" placeholder="Auto-générée si vide"
                 class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
          <label class="block text-xs font-medium text-slate-600 mb-1">Date de facture *</label>
          <input type="date" name="date_invoice" value="<?= date('Y-m-d') ?>" required
                 class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
          <label class="block text-xs font-medium text-slate-600 mb-1">Date d'échéance</label>
          <input type="date" name="date_due"
                 class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
          <label class="block text-xs font-medium text-slate-600 mb-1">Montant HT (€) *</label>
          <input type="number" name="total_ht" step="0.01" min="0.01" placeholder="0.00" required
                 class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
          <label class="block text-xs font-medium text-slate-600 mb-1">Montant TTC (€)</label>
          <input type="number" name="total_ttc" step="0.01" placeholder="HT × 1.20 si vide"
                 class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
          <label class="block text-xs font-medium text-slate-600 mb-1">Statut</label>
          <select name="status" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
            <option value="2" selected>Payée</option>
            <option value="1">Validée</option>
            <option value="0">Brouillon</option>
            <option value="3">Abandonnée</option>
          </select>
        </div>
        <div class="sm:col-span-2 xl:col-span-4 flex gap-3">
          <button type="submit" class="inline-flex items-center gap-1.5 px-5 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
            <span class="material-icons-round text-base">save</span> Créer la facture
          </button>
          <button type="button" @click="showAdd=false" class="inline-flex items-center gap-1.5 px-4 py-2 border border-slate-300 text-slate-700 text-sm font-medium rounded-lg hover:bg-slate-50 transition-colors">
            Annuler
          </button>
        </div>
      </form>
    </div>

    <!-- Table -->
    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm">
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-slate-50 border-b border-slate-200">
            <tr>
              <?php foreach(['Référence','Client','Date','Échéance','Total HT','Statut','Actions'] as $h): ?>
              <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide whitespace-nowrap"><?= $h ?></th>
              <?php endforeach; ?>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <?php if (empty($invoices)): ?>
            <tr><td colspan="7" class="px-4 py-10 text-center text-sm text-slate-400">Aucune facture. Ajoutez-en une avec le bouton ci-dessus.</td></tr>
            <?php else: ?>
            <?php foreach ($invoices as $inv): ?>
            <tr class="hover:bg-slate-50 transition-colors">
              <td class="px-4 py-3.5 text-sm font-semibold text-slate-900 whitespace-nowrap"><?= htmlspecialchars($inv['ref'], ENT_QUOTES, 'UTF-8') ?></td>
              <td class="px-4 py-3.5 text-sm text-slate-700 max-w-[160px] truncate"><?= htmlspecialchars($inv['tiers_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
              <td class="px-4 py-3.5 text-sm text-slate-600 whitespace-nowrap"><?= $inv['date_invoice'] ? date('d/m/Y', strtotime($inv['date_invoice'])) : '–' ?></td>
              <td class="px-4 py-3.5 text-sm whitespace-nowrap <?= ($inv['is_overdue'] ? 'text-red-600 font-medium' : 'text-slate-600') ?>">
                <?= $inv['date_due'] ? date('d/m/Y', strtotime($inv['date_due'])) : '–' ?>
                <?php if ($inv['is_overdue']): ?>
                <span class="ml-1 inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-600">Retard</span>
                <?php endif; ?>
              </td>
              <td class="px-4 py-3.5 text-sm font-semibold text-slate-900 whitespace-nowrap"><?= number_format((float)$inv['total_ht'],2,',',' ') ?> €</td>
              <td class="px-4 py-3.5">
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium <?= $statusBadge[$inv['status']] ?? 'bg-slate-100 text-slate-600' ?>">
                  <?= $statusLabels[$inv['status']] ?? '–' ?>
                </span>
              </td>
              <td class="px-4 py-3.5">
                <div class="flex items-center gap-1.5">
                  <?php if ((int)$inv['status'] !== 2): ?>
                  <form method="POST" action="<?= APP_URL ?>/invoices/pay/<?= (int)$inv['id'] ?>">
                    <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
                    <button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1.5 border border-emerald-300 text-emerald-600 text-xs font-medium rounded-lg hover:bg-emerald-50 transition-colors" title="Marquer payée">
                      <span class="material-icons-round text-xs">check_circle</span>
                    </button>
                  </form>
                  <?php endif; ?>
                  <button @click="confirmDelete={id:<?= (int)$inv['id'] ?>,ref:'<?= htmlspecialchars(addslashes($inv['ref']), ENT_QUOTES, 'UTF-8') ?>'}"
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

      <!-- Pagination -->
      <?php if ($pages > 1): ?>
      <div class="px-4 py-3 border-t border-slate-100 flex items-center justify-between">
        <p class="text-xs text-slate-500"><?= $total ?> factures · page <?= $page ?>/<?= $pages ?></p>
        <div class="flex items-center gap-1">
          <?php if ($page > 1): ?>
          <a href="?page=<?= $page-1 ?>&search=<?= urlencode($_GET['search']??'') ?>" class="px-3 py-1.5 text-xs font-medium border border-slate-200 rounded-lg hover:bg-slate-50 text-slate-600">‹</a>
          <?php endif; ?>
          <?php for ($p = max(1,$page-2); $p <= min($pages,$page+2); $p++): ?>
          <a href="?page=<?= $p ?>&search=<?= urlencode($_GET['search']??'') ?>"
             class="px-3 py-1.5 text-xs font-medium rounded-lg <?= $p===$page ? 'bg-blue-600 text-white' : 'border border-slate-200 hover:bg-slate-50 text-slate-600' ?>">
            <?= $p ?>
          </a>
          <?php endfor; ?>
          <?php if ($page < $pages): ?>
          <a href="?page=<?= $page+1 ?>&search=<?= urlencode($_GET['search']??'') ?>" class="px-3 py-1.5 text-xs font-medium border border-slate-200 rounded-lg hover:bg-slate-50 text-slate-600">›</a>
          <?php endif; ?>
        </div>
      </div>
      <?php endif; ?>
    </div>

    <!-- Delete modal -->
    <div v-if="confirmDelete" class="fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4" @click.self="confirmDelete=null">
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6" @click.stop>
        <div class="flex items-center gap-3 mb-4">
          <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center">
            <span class="material-icons-round text-red-600">delete_forever</span>
          </div>
          <div>
            <h3 class="text-base font-semibold text-slate-900">Supprimer la facture ?</h3>
            <p class="text-sm text-slate-500">{{ confirmDelete.ref }}</p>
          </div>
        </div>
        <p class="text-sm text-slate-600 mb-5">Les paiements associés seront également supprimés.</p>
        <div class="flex gap-3">
          <button @click="confirmDelete=null" class="flex-1 px-4 py-2 border border-slate-300 text-slate-700 text-sm font-medium rounded-xl hover:bg-slate-50">Annuler</button>
          <form :action="'<?= APP_URL ?>/invoices/delete/' + confirmDelete.id" method="POST" class="flex-1">
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
    const showAdd       = ref(false);
    const confirmDelete = ref(null);
    const csrf          = '<?= $csrf ?>';
    return { showAdd, confirmDelete, csrf };
  }
}).mount('#invoices-page');
</script>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
