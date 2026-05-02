<?php
require_once __DIR__ . '/../models/BaseModel.php';
require_once __DIR__ . '/../models/Invoice.php';
require_once __DIR__ . '/../models/Tiers.php';
require_once __DIR__ . '/../models/Payment.php';
require_once __DIR__ . '/../services/KPIService.php';

class ExportController
{
    public function exportCsv(): void
    {
        $type = $_GET['type'] ?? 'invoices';

        switch ($type) {
            case 'tiers':
                $this->exportTiersCsv();
                break;
            case 'payments':
                $this->exportPaymentsCsv();
                break;
            default:
                $this->exportInvoicesCsv();
        }
    }

    public function exportPdf(): void
    {
        $kpiService = new KPIService();
        $kpis       = $kpiService->getAll();
        $date       = date('d/m/Y H:i');
        $user       = $_SESSION['user'];

        header('Content-Type: text/html; charset=utf-8');
        ?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Rapport Flow – <?= htmlspecialchars($date, ENT_QUOTES, 'UTF-8') ?></title>
<style>
  body { font-family: Arial, sans-serif; font-size: 12px; color: #333; }
  h1   { color: #1a73e8; }
  h2   { color: #333; border-bottom: 1px solid #ccc; padding-bottom: 4px; }
  table { width: 100%; border-collapse: collapse; margin-bottom: 1rem; }
  th, td { border: 1px solid #ccc; padding: 6px 8px; text-align: left; }
  th { background: #f0f4ff; }
  .kpi-grid { display: flex; flex-wrap: wrap; gap: 1rem; margin-bottom: 2rem; }
  .kpi-card { border: 1px solid #ccc; border-radius: 8px; padding: 1rem; min-width: 160px; }
  .kpi-value { font-size: 1.5rem; font-weight: bold; color: #1a73e8; }
  @media print { button { display: none; } }
</style>
</head>
<body>
<button onclick="window.print()" style="margin-bottom:1rem;padding:8px 16px;cursor:pointer;">Imprimer / Exporter PDF</button>
<h1>Rapport de tableau de bord Flow</h1>
<p>Généré le <?= htmlspecialchars($date, ENT_QUOTES, 'UTF-8') ?> par <?= htmlspecialchars($user['name'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>

<h2>Indicateurs clés</h2>
<div class="kpi-grid">
  <div class="kpi-card">
    <div>CA mensuel</div>
    <div class="kpi-value"><?= number_format($kpis['monthly_revenue'], 2, ',', ' ') ?> €</div>
  </div>
  <div class="kpi-card">
    <div>CA annuel</div>
    <div class="kpi-value"><?= number_format($kpis['annual_revenue'], 2, ',', ' ') ?> €</div>
  </div>
  <div class="kpi-card">
    <div>Factures payées</div>
    <div class="kpi-value"><?= (int)($kpis['invoice_counts']['paid'] ?? 0) ?></div>
  </div>
  <div class="kpi-card">
    <div>Factures en retard</div>
    <div class="kpi-value"><?= (int)($kpis['invoice_counts']['overdue'] ?? 0) ?></div>
  </div>
  <div class="kpi-card">
    <div>Panier moyen</div>
    <div class="kpi-value"><?= number_format($kpis['average_basket'], 2, ',', ' ') ?> €</div>
  </div>
  <div class="kpi-card">
    <div>Croissance</div>
    <div class="kpi-value"><?= ($kpis['growth_rate'] >= 0 ? '+' : '') . $kpis['growth_rate'] ?> %</div>
  </div>
</div>

<h2>Top 10 tiers par CA</h2>
<table>
  <thead><tr><th>#</th><th>Tiers</th><th>CA (€)</th><th>Factures</th></tr></thead>
  <tbody>
  <?php foreach ($kpis['revenue_by_tiers'] as $i => $t): ?>
    <tr>
      <td><?= $i + 1 ?></td>
      <td><?= htmlspecialchars($t['name'], ENT_QUOTES, 'UTF-8') ?></td>
      <td><?= number_format((float)$t['revenue'], 2, ',', ' ') ?></td>
      <td><?= (int)$t['invoice_count'] ?></td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>

<h2>Top 10 produits par CA</h2>
<table>
  <thead><tr><th>#</th><th>Produit</th><th>CA (€)</th><th>Qté vendue</th></tr></thead>
  <tbody>
  <?php foreach ($kpis['revenue_by_product'] as $i => $p): ?>
    <tr>
      <td><?= $i + 1 ?></td>
      <td><?= htmlspecialchars($p['label'], ENT_QUOTES, 'UTF-8') ?></td>
      <td><?= number_format((float)$p['revenue'], 2, ',', ' ') ?></td>
      <td><?= number_format((float)$p['qty_sold'], 0, ',', ' ') ?></td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
</body>
</html>
        <?php
    }

    private function exportInvoicesCsv(): void
    {
        $invoiceModel = new Invoice();
        $invoices     = $invoiceModel->getWithTiers(1000);

        $this->outputCsv('invoices', ['Réf', 'Tiers', 'Date', 'Échéance', 'Payé le', 'Total HT', 'Total TTC', 'Statut', 'En retard'], function () use ($invoices) {
            foreach ($invoices as $inv) {
                echo $this->csvRow([
                    $inv['ref'],
                    $inv['tiers_name'] ?? '',
                    $inv['date_invoice'] ?? '',
                    $inv['date_due'] ?? '',
                    $inv['date_paid'] ?? '',
                    number_format((float)$inv['total_ht'], 2, '.', ''),
                    number_format((float)$inv['total_ttc'], 2, '.', ''),
                    $this->statusLabel((int)$inv['status']),
                    $inv['is_overdue'] ? 'Oui' : 'Non',
                ]);
            }
        });
    }

    private function exportTiersCsv(): void
    {
        $tiersModel = new Tiers();
        $tiers      = $tiersModel->getWithStats(1000);

        $this->outputCsv('tiers', ['ID', 'Nom', 'Email', 'Téléphone', 'CA', 'Factures', 'Retards', 'Risque', 'Actif'], function () use ($tiers) {
            foreach ($tiers as $t) {
                echo $this->csvRow([
                    $t['id'],
                    $t['name'],
                    $t['email'] ?? '',
                    $t['phone'] ?? '',
                    number_format((float)$t['revenue'], 2, '.', ''),
                    $t['invoice_count'],
                    $t['overdue_count'],
                    $t['risk_level'] ?? '',
                    $t['is_active'] ? 'Oui' : 'Non',
                ]);
            }
        });
    }

    private function exportPaymentsCsv(): void
    {
        $paymentModel = new Payment();
        $payments     = $paymentModel->getRecentPayments(1000);

        $this->outputCsv('payments', ['Date', 'Tiers', 'Facture', 'Montant', 'Mode', 'Libellé'], function () use ($payments) {
            foreach ($payments as $p) {
                echo $this->csvRow([
                    $p['date_payment'] ?? '',
                    $p['tiers_name'] ?? '',
                    $p['invoice_ref'] ?? '',
                    number_format((float)$p['amount'], 2, '.', ''),
                    $p['method'] ?? '',
                    $p['method_label'] ?? '',
                ]);
            }
        });
    }

    private function outputCsv(string $name, array $headers, callable $rows): void
    {
        $filename = $name . '_' . date('Y-m-d') . '.csv';
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: no-cache, no-store');
        echo "\xEF\xBB\xBF"; // UTF-8 BOM for Excel
        echo $this->csvRow($headers);
        $rows();
        exit;
    }

    private function csvRow(array $fields): string
    {
        $escaped = array_map(function ($f) {
            $f = str_replace('"', '""', (string)$f);
            return '"' . $f . '"';
        }, $fields);
        return implode(',', $escaped) . "\r\n";
    }

    private function statusLabel(int $status): string
    {
        return match ($status) {
            0 => 'Brouillon',
            1 => 'Validée',
            2 => 'Payée',
            3 => 'Abandonnée',
            default => 'Inconnu',
        };
    }
}
