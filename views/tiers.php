<?php require_once __DIR__ . '/partials/header.php'; ?>
<?php require_once __DIR__ . '/partials/sidebar.php'; ?>

<?php
$editTiersId = isset($_GET['edit']) ? (int)$_GET['edit'] : null;
$editTiers   = null;
if ($editTiersId) {
  foreach ($tiers as $t) {
    if ((int)$t['id'] === $editTiersId) { $editTiers = $t; break; }
  }
}
$riskBadge = [
  'low'    => 'bg-emerald-100 text-emerald-700',
  'medium' => 'bg-amber-100 text-amber-700',
  'high'   => 'bg-red-100 text-red-700',
];
$riskLabel = ['low'=>'Faible','medium'=>'Modéré','high'=>'Élevé'];
$csrf = htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8');
?>

<div id="main-wrap" class="flex-1 flex flex-col overflow-hidden ml-64">
  <header class="bg-white/90 border-b border-slate-200 px-6 h-14 flex items-center justify-between flex-shrink-0 sticky top-0 z-20" style="backdrop-filter:blur(10px)">
    <div class="flex items-center gap-2.5">
      <button id="menu-toggle" class="lg:hidden p-1.5 rounded-lg text-slate-400 hover:bg-slate-100">
        <span class="material-icons-round text-xl">menu</span>
      </button>
      <span class="material-icons-round text-blue-600 text-xl">groups</span>
      <h1 class="text-base font-semibold text-slate-900 font-display">Tiers</h1>
    </div>
    <div class="flex items-center gap-2.5">
      <?php if (!empty($user['avatar'])): ?>
      <img src="<?= htmlspecialchars($user['avatar'], ENT_QUOTES, 'UTF-8') ?>" class="w-7 h-7 rounded-full">
      <?php endif; ?>
      <span class="text-sm font-medium text-slate-600 hidden sm:block"><?= htmlspecialchars($user['name'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
    </div>
  </header>

  <main class="flex-1 overflow-y-auto p-5 space-y-4" id="tiers-page" v-cloak>

    <!-- Flash messages -->
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
      <form method="GET" action="<?= APP_URL ?>/tiers" class="flex items-center gap-2 flex-1 min-w-0">
        <div class="relative flex-1 max-w-xs">
          <span class="material-icons-round absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-base pointer-events-none">search</span>
          <input type="text" name="search" value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?>"
                 placeholder="Rechercher…"
                 class="w-full pl-9 pr-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
        </div>
        <select name="level" class="px-3 py-2 border border-slate-300 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
          <option value="">Tous les risques</option>
          <option value="low"    <?= $level==='low'    ? 'selected':'' ?>>Faible</option>
          <option value="medium" <?= $level==='medium' ? 'selected':'' ?>>Modéré</option>
          <option value="high"   <?= $level==='high'   ? 'selected':'' ?>>Élevé</option>
        </select>
        <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 bg-slate-700 text-white text-sm font-medium rounded-lg hover:bg-slate-800 transition-colors">
          <span class="material-icons-round text-base">filter_list</span> Filtrer
        </button>
      </form>
      <button @click="showAdd = !showAdd"
              class="inline-flex items-center gap-1.5 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
        <span class="material-icons-round text-base">person_add</span> Nouveau tiers
      </button>
      <a href="<?= APP_URL ?>/export/csv?type=tiers" class="inline-flex items-center gap-1.5 px-4 py-2 border border-slate-300 text-slate-700 text-sm font-medium rounded-lg hover:bg-slate-50 transition-colors">
        <span class="material-icons-round text-base">download</span> CSV
      </a>
    </div>

    <!-- Add form (Vue toggle) -->
    <div v-show="showAdd" class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm">
      <p class="text-sm font-semibold text-slate-900 mb-4 flex items-center gap-2">
        <span class="material-icons-round text-blue-500 text-base">person_add</span> Nouveau tiers
      </p>
      <form method="POST" action="<?= APP_URL ?>/tiers/store"
            class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
        <div>
          <label class="block text-xs font-medium text-slate-600 mb-1">Nom *</label>
          <input type="text" name="name" required placeholder="Nom du tiers"
                 class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
          <label class="block text-xs font-medium text-slate-600 mb-1">Email</label>
          <input type="email" name="email" placeholder="contact@example.com"
                 class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
          <label class="block text-xs font-medium text-slate-600 mb-1">Téléphone</label>
          <input type="text" name="phone" placeholder="+33 6 00 00 00 00"
                 class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div class="flex flex-col justify-end gap-2">
          <label class="block text-xs font-medium text-slate-600 mb-1">Adresse</label>
          <input type="text" name="address" placeholder="Ville, adresse…"
                 class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div class="sm:col-span-2 xl:col-span-4 flex gap-3">
          <button type="submit" class="inline-flex items-center gap-1.5 px-5 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
            <span class="material-icons-round text-base">save</span> Créer
          </button>
          <button type="button" @click="showAdd=false" class="inline-flex items-center gap-1.5 px-4 py-2 border border-slate-300 text-slate-700 text-sm font-medium rounded-lg hover:bg-slate-50 transition-colors">
            Annuler
          </button>
        </div>
      </form>
    </div>

    <!-- Edit form (PHP conditional) -->
    <?php if ($editTiers): ?>
    <div class="bg-white border-2 border-blue-500 rounded-xl p-5 shadow-sm">
      <p class="text-sm font-semibold text-slate-900 mb-4 flex items-center gap-2">
        <span class="material-icons-round text-blue-500 text-base">edit</span>
        Modifier : <?= htmlspecialchars($editTiers['name'], ENT_QUOTES, 'UTF-8') ?>
      </p>
      <form method="POST" action="<?= APP_URL ?>/tiers/update/<?= (int)$editTiers['id'] ?>"
            class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
        <div>
          <label class="block text-xs font-medium text-slate-600 mb-1">Nom *</label>
          <input type="text" name="name" required value="<?= htmlspecialchars($editTiers['name'], ENT_QUOTES, 'UTF-8') ?>"
                 class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
          <label class="block text-xs font-medium text-slate-600 mb-1">Email</label>
          <input type="email" name="email" value="<?= htmlspecialchars($editTiers['email']??'', ENT_QUOTES, 'UTF-8') ?>"
                 class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
          <label class="block text-xs font-medium text-slate-600 mb-1">Téléphone</label>
          <input type="text" name="phone" value="<?= htmlspecialchars($editTiers['phone']??'', ENT_QUOTES, 'UTF-8') ?>"
                 class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
          <label class="block text-xs font-medium text-slate-600 mb-1">Adresse</label>
          <input type="text" name="address" value="<?= htmlspecialchars($editTiers['address']??'', ENT_QUOTES, 'UTF-8') ?>"
                 class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div class="sm:col-span-2 xl:col-span-4 flex gap-3">
          <button type="submit" class="inline-flex items-center gap-1.5 px-5 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
            <span class="material-icons-round text-base">save</span> Enregistrer
          </button>
          <a href="<?= APP_URL ?>/tiers?search=<?= urlencode($search) ?>&level=<?= urlencode($level) ?>"
             class="inline-flex items-center gap-1.5 px-4 py-2 border border-slate-300 text-slate-700 text-sm font-medium rounded-lg hover:bg-slate-50 transition-colors">
            Annuler
          </a>
        </div>
      </form>
    </div>
    <?php endif; ?>

    <!-- Summary KPIs -->
    <?php
    $highCount = count(array_filter($tiers, fn($t) => ($t['risk_level']??'') === 'high'));
    $medCount  = count(array_filter($tiers, fn($t) => ($t['risk_level']??'') === 'medium'));
    ?>
    <div class="grid grid-cols-3 gap-3">
      <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm">
        <div class="flex items-start justify-between mb-2">
          <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Total</p>
          <span class="bg-blue-50 text-blue-600 w-6 h-6 rounded-md flex items-center justify-center">
            <span class="material-icons-round" style="font-size:14px">groups</span>
          </span>
        </div>
        <p class="text-2xl font-bold text-slate-900"><?= $total ?></p>
        <p class="text-xs text-slate-400 mt-1">tiers enregistrés</p>
      </div>
      <div class="bg-white border border-amber-100 rounded-xl p-4 shadow-sm">
        <div class="flex items-start justify-between mb-2">
          <p class="text-[11px] font-semibold text-amber-500 uppercase tracking-wider">Risque modéré</p>
          <span class="bg-amber-50 text-amber-600 w-6 h-6 rounded-md flex items-center justify-center">
            <span class="material-icons-round" style="font-size:14px">warning</span>
          </span>
        </div>
        <p class="text-2xl font-bold text-amber-600"><?= $medCount ?></p>
        <p class="text-xs text-slate-400 mt-1">sur cette page</p>
      </div>
      <div class="bg-white border border-red-100 rounded-xl p-4 shadow-sm">
        <div class="flex items-start justify-between mb-2">
          <p class="text-[11px] font-semibold text-red-500 uppercase tracking-wider">Risque élevé</p>
          <span class="bg-red-50 text-red-600 w-6 h-6 rounded-md flex items-center justify-center">
            <span class="material-icons-round" style="font-size:14px">dangerous</span>
          </span>
        </div>
        <p class="text-2xl font-bold text-red-600"><?= $highCount ?></p>
        <p class="text-xs text-slate-400 mt-1">sur cette page</p>
      </div>
    </div>

    <!-- Table -->
    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm">
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-slate-50 border-b border-slate-200">
            <tr>
              <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Tiers</th>
              <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wide">CA</th>
              <th class="px-4 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wide">Factures</th>
              <th class="px-4 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wide">En retard</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Dernière facture</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Risque</th>
              <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wide">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <?php if (empty($tiers)): ?>
            <tr><td colspan="7" class="px-4 py-10 text-center text-sm text-slate-400">Aucun tiers trouvé.</td></tr>
            <?php else: ?>
            <?php foreach ($tiers as $t):
              $rowLevel = $t['risk_level'] ?? 'low';
              $score    = (int)($t['risk_score'] ?? 0);
            ?>
            <tr class="hover:bg-slate-50 transition-colors">
              <td class="px-4 py-3.5">
                <p class="text-sm font-semibold text-slate-900"><?= htmlspecialchars($t['name'], ENT_QUOTES, 'UTF-8') ?></p>
                <?php if (!empty($t['email'])): ?>
                <p class="text-xs text-slate-400 mt-0.5"><?= htmlspecialchars($t['email'], ENT_QUOTES, 'UTF-8') ?></p>
                <?php endif; ?>
              </td>
              <td class="px-4 py-3.5 text-right text-sm font-semibold text-slate-900 whitespace-nowrap">
                <?= number_format((float)$t['revenue'], 0, ',', ' ') ?> €
              </td>
              <td class="px-4 py-3.5 text-center text-sm text-slate-700"><?= (int)$t['invoice_count'] ?></td>
              <td class="px-4 py-3.5 text-center">
                <?php if ((int)$t['overdue_count'] > 0): ?>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">
                  <?= (int)$t['overdue_count'] ?> en retard
                </span>
                <?php else: ?>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">OK</span>
                <?php endif; ?>
              </td>
              <td class="px-4 py-3.5 text-sm text-slate-500 whitespace-nowrap">
                <?= $t['last_invoice_date'] ? date('d/m/Y', strtotime($t['last_invoice_date'])) : '–' ?>
              </td>
              <td class="px-4 py-3.5">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold <?= $riskBadge[$rowLevel] ?? 'bg-slate-100 text-slate-700' ?>">
                  <?= $riskLabel[$rowLevel] ?? $rowLevel ?> (<?= $score ?>)
                </span>
              </td>
              <td class="px-4 py-3.5">
                <div class="flex items-center justify-end gap-1.5">
                  <a href="<?= APP_URL ?>/tiers/<?= (int)$t['id'] ?>"
                     class="inline-flex items-center gap-1 px-2.5 py-1.5 border border-slate-300 text-slate-600 text-xs font-medium rounded-lg hover:bg-slate-50 transition-colors">
                    <span class="material-icons-round text-xs">visibility</span> Voir
                  </a>
                  <a href="<?= APP_URL ?>/tiers?edit=<?= (int)$t['id'] ?>&search=<?= urlencode($search) ?>&level=<?= urlencode($level) ?>"
                     class="inline-flex items-center gap-1 px-2.5 py-1.5 border border-slate-300 text-slate-600 text-xs font-medium rounded-lg hover:bg-slate-50 transition-colors">
                    <span class="material-icons-round text-xs">edit</span>
                  </a>
                  <button @click="confirmDelete={id:<?= (int)$t['id'] ?>,name:'<?= htmlspecialchars(addslashes($t['name']), ENT_QUOTES, 'UTF-8') ?>'}"
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
        <p class="text-xs text-slate-500">Page <?= $page ?> / <?= $pages ?></p>
        <div class="flex items-center gap-1">
          <?php if ($page > 1): ?>
          <a href="?page=<?= $page-1 ?>&search=<?= urlencode($search) ?>&level=<?= urlencode($level) ?>"
             class="px-3 py-1.5 text-xs font-medium border border-slate-200 rounded-lg hover:bg-slate-50 text-slate-600">‹</a>
          <?php endif; ?>
          <?php for ($p = max(1,$page-2); $p <= min($pages,$page+2); $p++): ?>
          <a href="?page=<?= $p ?>&search=<?= urlencode($search) ?>&level=<?= urlencode($level) ?>"
             class="px-3 py-1.5 text-xs font-medium rounded-lg <?= $p===$page ? 'bg-blue-600 text-white' : 'border border-slate-200 hover:bg-slate-50 text-slate-600' ?>">
            <?= $p ?>
          </a>
          <?php endfor; ?>
          <?php if ($page < $pages): ?>
          <a href="?page=<?= $page+1 ?>&search=<?= urlencode($search) ?>&level=<?= urlencode($level) ?>"
             class="px-3 py-1.5 text-xs font-medium border border-slate-200 rounded-lg hover:bg-slate-50 text-slate-600">›</a>
          <?php endif; ?>
        </div>
      </div>
      <?php endif; ?>
    </div>

    <!-- Delete modal -->
    <div v-if="confirmDelete" class="fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4" @click.self="confirmDelete=null">
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6" @click.stop>
        <div class="flex items-center gap-3 mb-4">
          <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
            <span class="material-icons-round text-red-600">delete_forever</span>
          </div>
          <div>
            <h3 class="text-base font-semibold text-slate-900">Supprimer ce tiers ?</h3>
            <p class="text-sm text-slate-500">{{ confirmDelete.name }}</p>
          </div>
        </div>
        <p class="text-sm text-slate-600 mb-5">Les factures et paiements associés seront désassociés mais conservés.</p>
        <div class="flex gap-3">
          <button @click="confirmDelete=null" class="flex-1 px-4 py-2 border border-slate-300 text-slate-700 text-sm font-medium rounded-xl hover:bg-slate-50 transition-colors">
            Annuler
          </button>
          <form :action="'<?= APP_URL ?>/tiers/delete/' + confirmDelete.id" method="POST" class="flex-1">
            <input type="hidden" name="csrf_token" :value="csrf">
            <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-xl hover:bg-red-700 transition-colors">
              Supprimer
            </button>
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
    const showAdd      = ref(<?= $editTiers ? 'false' : 'false' ?>);
    const confirmDelete = ref(null);
    const csrf         = '<?= $csrf ?>';
    return { showAdd, confirmDelete, csrf };
  }
}).mount('#tiers-page');
</script>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
