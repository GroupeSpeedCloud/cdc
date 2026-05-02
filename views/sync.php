<?php require_once __DIR__ . '/partials/header.php'; ?>
<?php require_once __DIR__ . '/partials/sidebar.php'; ?>

<div id="main">
  <header id="topbar">
    <h1><span class="material-icons" style="vertical-align:middle;margin-right:0.5rem;">sync</span>Synchronisation Dolibarr</h1>
    <div class="topbar-user">
      <?php if (!empty($user['avatar'])): ?>
        <img src="<?= htmlspecialchars($user['avatar'], ENT_QUOTES, 'UTF-8') ?>" alt="Avatar">
      <?php endif; ?>
      <span><?= htmlspecialchars($user['name'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
    </div>
  </header>

  <div id="content">

    <?php if (!empty($_GET['message'])): ?>
    <div class="alert alert-success" style="margin-bottom:1.5rem;">
      <span class="material-icons" style="font-size:1.125rem;">check_circle</span>
      <?= htmlspecialchars($_GET['message'], ENT_QUOTES, 'UTF-8') ?>
    </div>
    <?php endif; ?>

    <?php if (!empty($_GET['error'])): ?>
    <div class="alert alert-danger" style="margin-bottom:1.5rem;">
      <span class="material-icons" style="font-size:1.125rem;">error</span>
      <?= htmlspecialchars($_GET['error'], ENT_QUOTES, 'UTF-8') ?>
    </div>
    <?php endif; ?>

    <?php if (!empty($_GET['warning'])): ?>
    <div class="alert alert-warning" style="margin-bottom:1.5rem;">
      <span class="material-icons" style="font-size:1.125rem;">warning</span>
      <?= htmlspecialchars($_GET['warning'], ENT_QUOTES, 'UTF-8') ?>
    </div>
    <?php endif; ?>

    <!-- Status cards -->
    <div class="kpi-grid" style="margin-bottom:2rem;">
      <?php
        $entities = ['tiers' => 'Tiers', 'services' => 'Services', 'invoices' => 'Factures', 'payments' => 'Paiements', 'kpis' => 'KPI'];
        foreach ($entities as $key => $label):
          $lastSync = $lastSyncs[$key] ?? null;
      ?>
      <div class="kpi-card">
        <div class="label"><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></div>
        <?php if ($lastSync): ?>
          <div class="value" style="font-size:1rem;color:var(--success);">
            <span class="material-icons" style="vertical-align:middle;font-size:1rem;">check_circle</span> Synchronisé
          </div>
          <div class="sub"><?= htmlspecialchars(date('d/m/Y H:i', strtotime($lastSync)), ENT_QUOTES, 'UTF-8') ?></div>
        <?php else: ?>
          <div class="value" style="font-size:1rem;color:#5f6368;">
            <span class="material-icons" style="vertical-align:middle;font-size:1rem;">schedule</span> Jamais
          </div>
          <div class="sub">Aucune sync enregistrée</div>
        <?php endif; ?>
      </div>
      <?php endforeach; ?>
    </div>

    <!-- Force sync -->
    <div class="card" style="margin-bottom:1.5rem;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;">
      <div>
        <h2 style="margin:0 0 0.25rem;font-size:1rem;">Synchronisation complète (force)</h2>
        <p style="margin:0;color:#5f6368;font-size:0.875rem;">
          Déclenche une synchronisation totale depuis Dolibarr. Cela peut prendre plusieurs minutes.
        </p>
      </div>
      <form method="POST" action="<?= APP_URL ?>/sync/force">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        <button type="submit" class="btn btn-primary"
                onclick="return confirm('Lancer une synchronisation complète depuis Dolibarr ?')">
          <span class="material-icons" style="font-size:1rem;">refresh</span> Forcer la synchronisation
        </button>
      </form>
    </div>

    <!-- Logs table -->
    <div class="card" style="padding:0;overflow:hidden;">
      <div style="padding:1rem 1.5rem;border-bottom:1px solid var(--outline);">
        <h2 style="margin:0;font-size:1rem;">
          <span class="material-icons" style="vertical-align:middle;font-size:1rem;">history</span>
          Journal de synchronisation
        </h2>
      </div>
      <table class="data-table">
        <thead>
          <tr>
            <th>Entité</th>
            <th>Statut</th>
            <th>Démarré</th>
            <th>Terminé</th>
            <th>Traités</th>
            <th>Erreurs</th>
            <th>Message</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($logs)): ?>
          <tr>
            <td colspan="7" style="text-align:center;color:#5f6368;padding:2rem;">
              Aucun journal de synchronisation disponible.
            </td>
          </tr>
          <?php else: ?>
          <?php foreach ($logs as $log): ?>
          <tr>
            <td style="font-weight:500;text-transform:capitalize;">
              <?= htmlspecialchars($log['entity_type'], ENT_QUOTES, 'UTF-8') ?>
            </td>
            <td>
              <?php
                $statusClass = match ($log['status']) {
                  'success' => 'badge-success',
                  'error'   => 'badge-danger',
                  'running' => 'badge-info',
                  default   => 'badge-warning',
                };
                $statusLabel = match ($log['status']) {
                  'success' => 'Succès',
                  'error'   => 'Erreur',
                  'running' => 'En cours',
                  default   => htmlspecialchars($log['status'], ENT_QUOTES, 'UTF-8'),
                };
              ?>
              <span class="badge <?= htmlspecialchars($statusClass, ENT_QUOTES, 'UTF-8') ?>">
                <?= htmlspecialchars($statusLabel, ENT_QUOTES, 'UTF-8') ?>
              </span>
            </td>
            <td style="font-size:0.8125rem;">
              <?= $log['started_at'] ? htmlspecialchars(date('d/m/Y H:i:s', strtotime($log['started_at'])), ENT_QUOTES, 'UTF-8') : '–' ?>
            </td>
            <td style="font-size:0.8125rem;">
              <?= $log['completed_at'] ? htmlspecialchars(date('d/m/Y H:i:s', strtotime($log['completed_at'])), ENT_QUOTES, 'UTF-8') : '–' ?>
            </td>
            <td><?= (int)$log['records_processed'] ?></td>
            <td>
              <?php if ((int)$log['records_failed'] > 0): ?>
                <span style="color:var(--error);font-weight:500;"><?= (int)$log['records_failed'] ?></span>
              <?php else: ?>
                <span style="color:var(--success);">0</span>
              <?php endif; ?>
            </td>
            <td style="font-size:0.8125rem;color:#5f6368;max-width:300px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
              <?= htmlspecialchars($log['message'] ?? '', ENT_QUOTES, 'UTF-8') ?>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

  </div>
</div>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
