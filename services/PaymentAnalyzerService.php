<?php
class PaymentAnalyzerService
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = getDB();
    }

    public function detectMethod(string $code, string $label): string
    {
        $codeUpper  = strtoupper(trim($code));
        $labelLower = strtolower(trim($label));

        // Dolibarr type first
        $map = [
            'CB'          => 'CB', 'CARTE'       => 'CB',
            'CREDIT_CARD' => 'CB', 'VIS'          => 'CB',
            'MC'          => 'CB', 'CB2'          => 'CB',
            'VIR'         => 'virement', 'VIREMENT' => 'virement',
            'TRANSFER'    => 'virement', 'TRF'      => 'virement',
            'LIQ'         => 'virement',
            'CHQ'         => 'chèque', 'CHEQUE'    => 'chèque',
            'CHECK'       => 'chèque',
            'ESP'         => 'espèces', 'CASH'     => 'espèces',
            'ESPECES'     => 'espèces',
        ];

        if (isset($map[$codeUpper])) return $map[$codeUpper];

        // Fallback to label keywords
        if (str_contains($labelLower, 'carte') || str_contains($labelLower, ' cb') || str_contains($labelLower, 'visa') || str_contains($labelLower, 'mastercard')) return 'CB';
        if (str_contains($labelLower, 'virement') || str_contains($labelLower, 'transfer')) return 'virement';
        if (str_contains($labelLower, 'chèque') || str_contains($labelLower, 'cheque')) return 'chèque';
        if (str_contains($labelLower, 'espèce') || str_contains($labelLower, 'espece') || str_contains($labelLower, 'cash') || str_contains($labelLower, 'liquide')) return 'espèces';

        return 'inconnu';
    }

    public function getMainMethodByAmount(int $tiersId): string
    {
        $stmt = $this->pdo->prepare(
            'SELECT method, SUM(amount) AS total
             FROM payments WHERE tiers_id = ?
             GROUP BY method ORDER BY total DESC LIMIT 1'
        );
        $stmt->execute([$tiersId]);
        $row = $stmt->fetch();
        return $row ? $row['method'] : 'inconnu';
    }

    public function detectFrequency(int $tiersId): string
    {
        $stmt = $this->pdo->prepare(
            'SELECT date_payment FROM payments
             WHERE tiers_id = ? AND date_payment IS NOT NULL
             ORDER BY date_payment ASC'
        );
        $stmt->execute([$tiersId]);
        $dates = array_column($stmt->fetchAll(), 'date_payment');

        if (count($dates) < 2) return 'irrégulier';

        $intervals = [];
        for ($i = 1; $i < count($dates); $i++) {
            $diff = (strtotime($dates[$i]) - strtotime($dates[$i - 1])) / 86400;
            $intervals[] = $diff;
        }

        $avg = array_sum($intervals) / count($intervals);

        // ±20% tolerance
        if ($avg >= 24 && $avg <= 36)   return 'mensuel';   // ~30d
        if ($avg >= 72 && $avg <= 108)  return 'trimestriel'; // ~90d
        if ($avg >= 292 && $avg <= 438) return 'annuel';    // ~365d

        return 'irrégulier';
    }

    public function getMethodsBreakdown(): array
    {
        $stmt = $this->pdo->query(
            'SELECT method,
                    COUNT(*) AS count,
                    COALESCE(SUM(amount), 0) AS total_amount
             FROM payments
             GROUP BY method
             ORDER BY total_amount DESC'
        );
        return $stmt->fetchAll();
    }

    public function getFrequencyDistribution(): array
    {
        $stmt = $this->pdo->query('SELECT DISTINCT tiers_id FROM payments WHERE tiers_id IS NOT NULL');
        $tiers = array_column($stmt->fetchAll(), 'tiers_id');

        $dist = ['mensuel' => 0, 'trimestriel' => 0, 'annuel' => 0, 'irrégulier' => 0];
        foreach ($tiers as $id) {
            $freq = $this->detectFrequency((int)$id);
            $dist[$freq] = ($dist[$freq] ?? 0) + 1;
        }
        return $dist;
    }

    public function getPaymentHistory(int $tiersId, int $limit = 24): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT p.*, i.ref AS invoice_ref
             FROM payments p
             LEFT JOIN invoices i ON i.id = p.invoice_id
             WHERE p.tiers_id = ?
             ORDER BY p.date_payment DESC
             LIMIT ?'
        );
        $stmt->execute([$tiersId, $limit]);
        return $stmt->fetchAll();
    }

    public function getDelayStats(int $tiersId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT
               COUNT(*) AS total_invoices,
               SUM(CASE WHEN date_paid > date_due THEN 1 ELSE 0 END) AS delayed_count,
               AVG(CASE WHEN date_paid > date_due THEN DATEDIFF(date_paid, date_due) ELSE NULL END) AS avg_delay_days
             FROM invoices
             WHERE tiers_id = ? AND date_due IS NOT NULL AND date_paid IS NOT NULL'
        );
        $stmt->execute([$tiersId]);
        return $stmt->fetch() ?: ['total_invoices' => 0, 'delayed_count' => 0, 'avg_delay_days' => 0];
    }
}
