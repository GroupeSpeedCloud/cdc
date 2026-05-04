<?php require_once __DIR__ . '/partials/header.php'; ?>
<?php require_once __DIR__ . '/partials/sidebar.php'; ?>

<div id="main">
  <header id="topbar">
    <div style="display:flex;align-items:center;min-width:0;">
      <button id="menu-toggle" aria-label="Ouvrir le menu">
        <span class="material-icons">menu</span>
      </button>
      <h1><span class="material-icons" style="vertical-align:middle;margin-right:0.5rem;">groups</span>Tiers</h1>
    </div>
    <div class="topbar-user">
      <?php if (!empty($user['avatar'])): ?>
        <img src="<?= htmlspecialchars($user['avatar'], ENT_QUOTES, 'UTF-8') ?>" alt="Avatar">
      <?php endif; ?>
      <span><?= htmlspecialchars($user['name'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
    </div>
  </header>

  <div id="content">

    <!-- Search / Filter bar -->
    <form method="GET" action="<?= APP_URL ?>/tiers" class="search-bar">
      <input type="text" name="search" class="search-input"
             placeholder="Rechercher par nom ou email…"
             value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?>">

      <select name="level" class="search-input" style="max-width:160px;">
        <option value="">Tous les risques</option>
        <option value="low"    <?= $level === 'low'    ? 'selected' : '' ?>>Faible</option>
        <option value="medium" <?= $level === 'medium' ? 'selected' : '' ?>>Modéré</option>
        <option value="high"   <?= $level === 'high'   ? 'selected' : '' ?>>Élevé</option>
      </select>

      <button type="submit" class="btn btn-primary">
        <span class="material-icons" style="font-size:1rem;">search</span> Filtrer
      </button>

      <a href="<?= APP_URL ?>/export/csv?type=tiers" class="btn btn-outline">
        <span class="material-icons" style="font-size:1rem;">download</span> Export CSV
      </a>
    </form>

    <!-- Summary -->
    <div class="kpi-grid" style="grid-template-columns:repeat(auto-fill,minmax(160px,1fr));margin-bottom:1.5rem;">
      <div class="kpi-card">
        <div class="label">Total</div>
        <div class="value" style="font-size:1.5rem;"><?= $total ?></div>
        <div class="sub">tiers enregistrés</div>
      </div>
      <?php
        $highRisk = array_filter($tiers, fn($t) => ($t['risk_level'] ?? '') === 'high');
        $medRisk  = array_filter($tiers, fn($t) => ($t['risk_level'] ?? '') === 'medium');
      ?>
      <div class="kpi-card danger">
        <div class="label">Risque élevé</div>
        <div class="value" style="font-size:1.5rem;"><?= count($highRisk) ?></div>
        <div class="sub">sur cette page</div>
      </div>
      <div class="kpi-card warning">
        <div class="label">Risque modéré</div>
        <div class="value" style="font-size:1.5rem;"><?= count($medRisk) ?></div>
        <div class="sub">sur cette page</div>
      </div>
    </div>

    <!-- Table -->
    <div class="card" style="padding:0;overflow:hidden;">
      <div class="table-scroll">
      <table class="data-table">
        <thead>
          <tr>
            <th>Tiers</th>
            <th>CA</th>
            <th>Factures</th>
            <th>En retard</th>
            <th>Dernière facture</th>
            <th>Risque</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($tiers)): ?>
          <tr><td colspan="7" style="text-align:center;color:#5f6368;padding:2rem;">Aucun tiers trouvé.</td></tr>
          <?php else: ?>
          <?php foreach ($tiers as $t): ?>
          <tr>
            <td>
              <div style="font-weight:500;"><?= htmlspecialchars($t['name'], ENT_QUOTES, 'UTF-8') ?></div>
              <?php if (!empty($t['email'])): ?>
              <div style="font-size:0.8125rem;color:#5f6368;"><?= htmlspecialchars($t['email'], ENT_QUOTES, 'UTF-8') ?></div>
              <?php endif; ?>
            </td>
            <td style="font-weight:500;"><?= number_format((float)$t['revenue'], 0, ',', ' ') ?> €</td>
            <td><?= (int)$t['invoice_count'] ?></td>
            <td>
              <?php if ((int)$t['overdue_count'] > 0): ?>
                <span class="badge badge-danger"><?= (int)$t['overdue_count'] ?> en retard</span>
              <?php else: ?>
                <span class="badge badge-success">0</span>
              <?php endif; ?>
            </td>
            <td style="font-size:0.875rem;">
              <?= $t['last_invoice_date'] ? htmlspecialchars(date('d/m/Y', strtotime($t['last_invoice_date'])), ENT_QUOTES, 'UTF-8') : '–' ?>
            </td>
            <td>
              <?php
                $level = $t['risk_level'] ?? 'low';
                $score = (int)($t['risk_score'] ?? 0);
                $badgeClass = 'badge-' . $level;
                $labels = ['low' => 'Faible', 'medium' => 'Modéré', 'high' => 'Élevé'];
              ?>
              <span class="badge <?= htmlspecialchars($badgeClass, ENT_QUOTES, 'UTF-8') ?>">
                <?= htmlspecialchars($labels[$level] ?? $level, ENT_QUOTES, 'UTF-8') ?>
                (<?= $score ?>)
              </span>
            </td>
            <td>
              <a href="<?= APP_URL ?>/tiers/<?= (int)$t['id'] ?>" class="btn btn-outline" style="padding:0.375rem 0.875rem;font-size:0.8125rem;">
                <span class="material-icons" style="font-size:0.875rem;">visibility</span> Détail
              </a>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
      </div>
    </div>

    <!-- Pagination -->
    <?php if ($pages > 1): ?>
    <nav class="pagination">
      <?php if ($page > 1): ?>
        <a href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>&level=<?= urlencode($level) ?>">‹</a>
      <?php endif; ?>

      <?php for ($p = max(1, $page - 3); $p <= min($pages, $page + 3); $p++): ?>
        <?php if ($p === $page): ?>
          <span class="current"><?= $p ?></span>
        <?php else: ?>
          <a href="?page=<?= $p ?>&search=<?= urlencode($search) ?>&level=<?= urlencode($level) ?>"><?= $p ?></a>
        <?php endif; ?>
      <?php endfor; ?>

      <?php if ($page < $pages): ?>
        <a href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>&level=<?= urlencode($level) ?>">›</a>
      <?php endif; ?>
    </nav>
    <?php endif; ?>

  </div>
</div>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
