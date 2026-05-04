<?php
$currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$navItems = [
  ['path'=>'/',         'icon'=>'space_dashboard',       'label'=>'Tableau de bord'],
  ['path'=>'/tiers',    'icon'=>'groups',                'label'=>'Tiers'],
  ['path'=>'/invoices', 'icon'=>'receipt_long',          'label'=>'Factures'],
  ['path'=>'/payments', 'icon'=>'credit_card',           'label'=>'Paiements'],
  ['path'=>'/forecast', 'icon'=>'trending_up',           'label'=>'Prévisions'],
  ['path'=>'/expenses', 'icon'=>'account_balance_wallet','label'=>'Dépenses'],
];
?>
<aside id="sidebar" class="w-56 flex-shrink-0 flex flex-col z-30 h-screen" style="background:#0f172a; border-right:1px solid rgba(255,255,255,.06);">

  <!-- Brand -->
  <div style="padding:18px 16px 14px; border-bottom:1px solid rgba(255,255,255,.07)">
    <div style="display:flex; align-items:center; gap:10px;">
      <div style="width:32px;height:32px;border-radius:8px;background:linear-gradient(135deg,#38bdf8,#2563eb);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
        <span class="material-icons-round" style="color:#fff;font-size:18px;">water_drop</span>
      </div>
      <div>
        <div style="color:#fff;font-weight:700;font-size:16px;line-height:1;">Flow</div>
        <div style="color:rgba(255,255,255,.4);font-size:11px;margin-top:2px;">Groupe Speed Cloud</div>
      </div>
    </div>
  </div>

  <!-- Nav -->
  <nav style="flex:1;padding:10px 8px;overflow-y:auto;">
    <?php foreach ($navItems as $item):
      $active = ($currentPath === $item['path'])
             || ($item['path'] !== '/' && str_starts_with($currentPath, $item['path']));
    ?>
    <a href="<?= APP_URL . $item['path'] ?>"
       style="display:flex;align-items:center;gap:10px;padding:8px 10px;margin-bottom:2px;border-radius:6px;font-size:13px;font-weight:500;text-decoration:none;transition:background .15s;<?= $active ? 'background:rgba(37,99,235,.9);color:#fff;' : 'color:rgba(255,255,255,.55);' ?>"
       onmouseover="if(!this.dataset.active)this.style.background='rgba(255,255,255,.07)'"
       onmouseout="if(!this.dataset.active)this.style.background=''"
       <?= $active ? 'data-active="1"' : '' ?>>
      <span class="material-icons-round" style="font-size:18px;flex-shrink:0;"><?= htmlspecialchars($item['icon'], ENT_QUOTES, 'UTF-8') ?></span>
      <?= htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8') ?>
    </a>
    <?php endforeach; ?>

    <div style="margin:16px 0 8px;padding:0 10px;font-size:10px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:rgba(255,255,255,.25);">Exports</div>
    <?php foreach ([
      ['/export/csv?type=invoices','download','Factures CSV'],
      ['/export/csv?type=payments','download','Paiements CSV'],
      ['/export/csv?type=tiers',  'download','Tiers CSV'],
    ] as [$href, $icon, $label]): ?>
    <a href="<?= APP_URL . $href ?>"
       style="display:flex;align-items:center;gap:10px;padding:7px 10px;margin-bottom:1px;border-radius:6px;font-size:13px;color:rgba(255,255,255,.45);text-decoration:none;"
       onmouseover="this.style.background='rgba(255,255,255,.06)';this.style.color='rgba(255,255,255,.75)'"
       onmouseout="this.style.background='';this.style.color='rgba(255,255,255,.45)'">
      <span class="material-icons-round" style="font-size:15px;flex-shrink:0;"><?= $icon ?></span>
      <?= $label ?>
    </a>
    <?php endforeach; ?>
  </nav>

  <!-- User -->
  <?php if (!empty($_SESSION['user'])): $u = $_SESSION['user']; ?>
  <div style="padding:12px 12px;border-top:1px solid rgba(255,255,255,.07);">
    <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px;">
      <?php if (!empty($u['avatar'])): ?>
      <img src="<?= htmlspecialchars($u['avatar'], ENT_QUOTES, 'UTF-8') ?>" style="width:30px;height:30px;border-radius:50%;object-fit:cover;flex-shrink:0;">
      <?php else: ?>
      <div style="width:30px;height:30px;border-radius:50%;background:#2563eb;display:flex;align-items:center;justify-content:center;color:#fff;font-size:13px;font-weight:700;flex-shrink:0;">
        <?= strtoupper(substr($u['name'] ?? 'U', 0, 1)) ?>
      </div>
      <?php endif; ?>
      <div style="min-width:0;">
        <div style="font-size:13px;font-weight:600;color:#fff;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?= htmlspecialchars($u['name'] ?? '', ENT_QUOTES, 'UTF-8') ?></div>
        <div style="font-size:11px;color:rgba(255,255,255,.4);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?= htmlspecialchars($u['email'] ?? '', ENT_QUOTES, 'UTF-8') ?></div>
      </div>
    </div>
    <form method="POST" action="<?= APP_URL ?>/logout">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
      <button type="submit" style="display:flex;align-items:center;gap:8px;width:100%;padding:7px 10px;font-size:12px;color:rgba(255,255,255,.45);background:none;border:none;border-radius:6px;cursor:pointer;font-family:inherit;"
              onmouseover="this.style.background='rgba(255,255,255,.07)';this.style.color='rgba(255,255,255,.8)'"
              onmouseout="this.style.background='';this.style.color='rgba(255,255,255,.45)'">
        <span class="material-icons-round" style="font-size:15px;">logout</span> Se déconnecter
      </button>
    </form>
  </div>
  <?php endif; ?>
</aside>

<div id="sidebar-overlay"></div>
