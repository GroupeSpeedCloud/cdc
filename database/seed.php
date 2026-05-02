<?php
/**
 * Seeder – génère des données de démonstration réalistes.
 * Usage : php database/seed.php
 */

define('ROOT', dirname(__DIR__));
require_once ROOT . '/config/app.php';
require_once ROOT . '/config/database.php';

$pdo = getDB();

echo "=== Flow Seeder ===\n\n";

// ──────────────────────────────────────────
// 1. Admin user
// ──────────────────────────────────────────
$adminEmail = explode(',', AUTHORIZED_USERS)[0];
$pdo->exec("DELETE FROM users");
$pdo->prepare(
    "INSERT INTO users (google_id, email, name, avatar, created_at, last_login)
     VALUES ('admin_seed', ?, 'Admin Flow', '', NOW(), NOW())"
)->execute([trim($adminEmail)]);
echo "✓ Utilisateur admin créé : $adminEmail\n";

// ──────────────────────────────────────────
// 2. Produits (30)
// ──────────────────────────────────────────
$pdo->exec("DELETE FROM invoice_lines");
$pdo->exec("DELETE FROM payments");
$pdo->exec("DELETE FROM invoices");
$pdo->exec("DELETE FROM products");
$pdo->exec("DELETE FROM tiers");

$productDefs = [
    ['FORM-BASE',   'Formation de base',          450.00, 1],
    ['FORM-ADV',    'Formation avancée',           890.00, 1],
    ['FORM-MGMT',   'Formation management',        1200.00, 1],
    ['FORM-DIG',    'Formation digital',           750.00, 1],
    ['FORM-RH',     'Formation RH',                680.00, 1],
    ['CONS-STRAT',  'Conseil stratégique',         2500.00, 1],
    ['CONS-ORG',    'Conseil organisationnel',     1800.00, 1],
    ['CONS-DIG',    'Conseil digital',             2200.00, 1],
    ['AUDIT-FIN',   'Audit financier',             3500.00, 1],
    ['AUDIT-RH',    'Audit RH',                    2800.00, 1],
    ['ATELIER-1',   'Atelier team building',       1500.00, 1],
    ['ATELIER-2',   'Atelier créativité',          1200.00, 1],
    ['ATELIER-3',   'Atelier communication',       900.00, 1],
    ['COACH-IND',   'Coaching individuel (10h)',   1100.00, 1],
    ['COACH-GRP',   'Coaching de groupe',          2400.00, 1],
    ['COACH-EXEC',  'Coaching executive',          3200.00, 1],
    ['EVAL-360',    'Évaluation 360°',             800.00, 1],
    ['BILAN-COMP',  'Bilan de compétences',        1600.00, 1],
    ['VAE',         'Accompagnement VAE',          1400.00, 1],
    ['OUTPLACE',    'Outplacement',                4500.00, 1],
    ['E-LEARN-1',   'Module e-learning (licence)', 350.00, 0],
    ['E-LEARN-PKG', 'Pack e-learning 5 modules',  1500.00, 0],
    ['SUPPORT-ANL', 'Support annuel',              600.00, 1],
    ['MATERIAL',    'Supports pédagogiques',       120.00, 0],
    ['LIVRET',      'Livret participant',          25.00, 0],
    ['CERT-CPF',    'Certification CPF',           450.00, 1],
    ['INTRA-1J',    'Formation intra 1 jour',      1800.00, 1],
    ['INTRA-2J',    'Formation intra 2 jours',     3200.00, 1],
    ['INTER-1J',    'Formation inter 1 jour',      650.00, 1],
    ['INTER-2J',    'Formation inter 2 jours',     1100.00, 1],
];

$productIds = [];
$stmt = $pdo->prepare(
    "INSERT INTO products (dolibarr_id, ref, label, price, type, created_at, updated_at)
     VALUES (?, ?, ?, ?, ?, NOW(), NOW())"
);
foreach ($productDefs as $i => [$ref, $label, $price, $type]) {
    $stmt->execute([$i + 1, $ref, $label, $price, $type]);
    $productIds[] = $pdo->lastInsertId();
}
echo "✓ " . count($productIds) . " produits créés\n";

// ──────────────────────────────────────────
// 3. Tiers (50) avec profils de risque variés
// ──────────────────────────────────────────
$companies = [
    'Accenture France', 'BNP Paribas', 'Cap Gemini', 'Danone', 'EDF',
    'Fnac Darty', 'Groupama', 'Hermès', 'ING Direct', 'Jardins de France',
    'KPMG Advisory', 'L\'Oréal', 'Michelin', 'Nokia France', 'Orange SA',
    'Peugeot SA', 'Quechua Sport', 'Renault Group', 'Société Générale', 'Total Energies',
    'Ubisoft', 'Vinci Construction', 'Wendy\'s France', 'Xerox France', 'Yves Rocher',
    'Zara France', 'Alstom', 'Bouygues Telecom', 'Crédit Agricole', 'Dassault Systèmes',
    'Engie', 'Fnac Pro', 'Groupe ADP', 'HSBC France', 'IBM France',
    'JC Decaux', 'Kering Group', 'Legrand', 'Maif', 'Natixis',
    'Oci France', 'Pernod Ricard', 'Qonto', 'Randstad France', 'Safran',
    'Thales Group', 'Unilever France', 'Verizon FR', 'Worldline', 'Zeiss France',
];

$riskProfiles = [];
// 10 high-risk, 15 medium-risk, 25 low-risk
for ($i = 0; $i < 50; $i++) {
    if ($i < 10) $riskProfiles[] = 'high';
    elseif ($i < 25) $riskProfiles[] = 'medium';
    else $riskProfiles[] = 'low';
}

$tiersIds = [];
$stmt = $pdo->prepare(
    "INSERT INTO tiers (dolibarr_id, name, email, phone, address, is_active, risk_score, risk_level, created_at, updated_at)
     VALUES (?, ?, ?, ?, ?, 1, ?, ?, NOW(), NOW())"
);

foreach ($companies as $i => $name) {
    $slug  = strtolower(preg_replace('/[^a-z0-9]/i', '', $name));
    $email = "contact@{$slug}.fr";
    $phone = '0' . rand(1, 9) . str_pad((string)rand(0, 99999999), 8, '0', STR_PAD_LEFT);
    $level = $riskProfiles[$i];
    $score = match ($level) {
        'high'   => rand(60, 95),
        'medium' => rand(30, 59),
        'low'    => rand(0, 29),
    };
    $stmt->execute([$i + 1, $name, $email, $phone, rand(1, 99) . ' rue Example, Paris', $score, $level]);
    $tiersIds[] = $pdo->lastInsertId();
}
echo "✓ " . count($tiersIds) . " tiers créés\n";

// ──────────────────────────────────────────
// 4. Factures (200) + lignes + paiements
// ──────────────────────────────────────────
$methods     = ['CB', 'virement', 'chèque', 'espèces'];
$methodWeights = [20, 50, 20, 10]; // virement dominant

function weightedRandom(array $items, array $weights): string {
    $totalWeight = array_sum($weights);
    $rand        = rand(1, $totalWeight);
    $cumulative  = 0;
    foreach ($items as $i => $item) {
        $cumulative += $weights[$i];
        if ($rand <= $cumulative) return $item;
    }
    return $items[0];
}

$invoiceStmt = $pdo->prepare(
    "INSERT INTO invoices (dolibarr_id, ref, tiers_id, date_invoice, date_due, date_paid, total_ht, total_ttc, status, is_overdue, created_at, updated_at)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())"
);
$lineStmt = $pdo->prepare(
    "INSERT INTO invoice_lines (invoice_id, product_id, description, qty, unit_price, total_ht)
     VALUES (?, ?, ?, ?, ?, ?)"
);
$payStmt = $pdo->prepare(
    "INSERT INTO payments (dolibarr_id, invoice_id, tiers_id, amount, date_payment, method, method_label, created_at)
     VALUES (?, ?, ?, ?, ?, ?, ?, NOW())"
);

$invoiceCount = 0;
$paymentCount = 0;
$today = date('Y-m-d');

for ($inv = 1; $inv <= 200; $inv++) {
    // Pick a tiers (bias towards first 25 for more history)
    $tiersIdx = ($inv <= 120) ? array_rand(array_slice($tiersIds, 0, 25)) : array_rand($tiersIds);
    $tiersId  = ($inv <= 120) ? $tiersIds[$tiersIdx] : $tiersIds[$tiersIdx];

    // Date: spread over last 18 months with seasonality (Q1/Q4 busier)
    $monthsBack  = rand(0, 17);
    $monthOffset = date('n', strtotime("-$monthsBack months"));
    // Seasonality bonus: Q1 (Jan-Mar) and Q4 (Oct-Dec)
    $seasonal = in_array($monthOffset, [1, 2, 3, 10, 11, 12]);
    if ($seasonal && rand(0, 1) && $inv <= 150) {
        // Extra invoice in busy periods
    }
    $invoiceDate = date('Y-m-d', strtotime("-$monthsBack months -" . rand(0, 27) . " days"));
    $dueDate     = date('Y-m-d', strtotime($invoiceDate . " +30 days"));

    // Determine status
    $tIdx    = array_search($tiersId, $tiersIds);
    $profile = $riskProfiles[$tIdx] ?? 'low';

    $isOverdue = 0;
    $status    = 2; // paid by default
    $datePaid  = null;

    if ($profile === 'high') {
        // High-risk: some overdue, some paid late
        $r = rand(0, 10);
        if ($r <= 3) {
            $status   = 1; // unpaid
            $isOverdue = ($dueDate < $today) ? 1 : 0;
            $datePaid = null;
        } elseif ($r <= 7) {
            // Paid late (15-90 days after due)
            $status   = 2;
            $datePaid = date('Y-m-d', strtotime($dueDate . " +" . rand(15, 90) . " days"));
        } else {
            $status   = 2;
            $datePaid = date('Y-m-d', strtotime($invoiceDate . " +" . rand(5, 25) . " days"));
        }
    } elseif ($profile === 'medium') {
        $r = rand(0, 10);
        if ($r <= 1) {
            $status   = 1;
            $isOverdue = ($dueDate < $today) ? 1 : 0;
        } elseif ($r <= 4) {
            $status   = 2;
            $datePaid = date('Y-m-d', strtotime($dueDate . " +" . rand(1, 30) . " days"));
        } else {
            $status   = 2;
            $datePaid = date('Y-m-d', strtotime($invoiceDate . " +" . rand(5, 28) . " days"));
        }
    } else {
        // Low risk: mostly paid on time
        if (rand(0, 20) === 0) {
            $status = 1;
            $isOverdue = ($dueDate < $today) ? 1 : 0;
        } else {
            $status   = 2;
            $datePaid = date('Y-m-d', strtotime($invoiceDate . " +" . rand(5, 28) . " days"));
        }
    }

    // Random 1-3 product lines
    $lineCount  = rand(1, 3);
    $totalHt    = 0.0;
    $linesData  = [];
    for ($l = 0; $l < $lineCount; $l++) {
        $productId = $productIds[array_rand($productIds)];
        // Get price from productDefs
        $pIdx     = array_search($productId, $productIds);
        $basePrice = $productDefs[$pIdx][2] ?? 500.0;
        $qty      = rand(1, 5);
        $price    = $basePrice * (0.85 + lcg_value() * 0.3); // ±15%
        $lht      = round($qty * $price, 2);
        $totalHt += $lht;
        $linesData[] = [$productId, $qty, round($price, 2), $lht];
    }
    $totalTtc = round($totalHt * 1.20, 2);

    $ref = 'INV-' . date('Y', strtotime($invoiceDate)) . '-' . str_pad((string)$inv, 5, '0', STR_PAD_LEFT);

    $invoiceStmt->execute([
        $inv, $ref, $tiersId, $invoiceDate, $dueDate, $datePaid,
        round($totalHt, 2), $totalTtc, $status, $isOverdue,
    ]);
    $invoiceId = $pdo->lastInsertId();
    $invoiceCount++;

    // Insert lines
    foreach ($linesData as [$pid, $qty, $up, $lht]) {
        $lineStmt->execute([$invoiceId, $pid, '', $qty, $up, $lht]);
    }

    // Insert payment if paid
    if ($status === 2 && $datePaid) {
        // High-risk clients: favour chèque/espèces
        if ($profile === 'high') {
            $methodWeightsLocal = [10, 30, 40, 20];
        } elseif ($profile === 'medium') {
            $methodWeightsLocal = [20, 50, 25, 5];
        } else {
            $methodWeightsLocal = [20, 55, 20, 5];
        }
        $method = weightedRandom($methods, $methodWeightsLocal);
        $payStmt->execute([
            $inv, $invoiceId, $tiersId, round($totalHt, 2), $datePaid,
            $method, $method,
        ]);
        $paymentCount++;
    }
}

echo "✓ $invoiceCount factures créées\n";
echo "✓ $paymentCount paiements créés\n";

// ──────────────────────────────────────────
// 5. Set is_overdue flag (cleanup)
// ──────────────────────────────────────────
$pdo->exec(
    "UPDATE invoices SET is_overdue = 1
     WHERE status IN (0,1) AND date_due IS NOT NULL AND date_due < CURDATE()"
);
echo "✓ Drapeaux is_overdue mis à jour\n";

// ──────────────────────────────────────────
// Done
// ──────────────────────────────────────────
echo "\n=== Seeder terminé avec succès ! ===\n";
echo "Vous pouvez vous connecter avec : $adminEmail\n";
