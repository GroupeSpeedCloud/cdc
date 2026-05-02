<?php
class RiskScoringService
{
    private PDO $pdo;
    private PaymentAnalyzerService $analyzer;

    public function __construct()
    {
        $this->pdo      = getDB();
        $this->analyzer = new PaymentAnalyzerService();
    }

    /**
     * Calculate risk score 0-100 for a tiers.
     */
    public function calculateScore(int $tiersId): int
    {
        $score = 0;

        // Delay factors
        $delayStats = $this->analyzer->getDelayStats($tiersId);
        $delayCount = (int)($delayStats['delayed_count'] ?? 0);
        $avgDelay   = (float)($delayStats['avg_delay_days'] ?? 0);

        if ($delayCount >= 2)  $score += 20;
        if ($delayCount >= 5)  $score += 10;
        if ($avgDelay > 45)    $score += 20;
        if ($avgDelay > 90)    $score += 10;

        // Unpaid/overdue invoices
        $overdueStmt = $this->pdo->prepare(
            'SELECT COUNT(*) FROM invoices WHERE tiers_id = ? AND is_overdue = 1'
        );
        $overdueStmt->execute([$tiersId]);
        $overdueCount = (int)$overdueStmt->fetchColumn();

        if ($overdueCount >= 1) $score += 15;
        if ($overdueCount >= 3) $score += 10;

        // Payment method factor
        $mainMethod = $this->analyzer->getMainMethodByAmount($tiersId);
        $score += match ($mainMethod) {
            'CB'       => -5,
            'virement' => -10,
            'chèque'   => 10,
            'espèces'  => 15,
            default    => 0,
        };

        // Payment irregularity
        $freq = $this->analyzer->detectFrequency($tiersId);
        if ($freq === 'irrégulier') $score += 10;

        // Revenue decline over 3 months
        if ($this->hasRevenueDecline($tiersId)) $score += 15;

        return max(0, min(100, $score));
    }

    public function getRiskLevel(int $score): string
    {
        if ($score < 30)  return 'low';
        if ($score < 60)  return 'medium';
        return 'high';
    }

    public function getRiskLabel(string $level): string
    {
        return match ($level) {
            'low'    => 'Faible',
            'medium' => 'Modéré',
            'high'   => 'Élevé',
            default  => 'Inconnu',
        };
    }

    public function updateAllScores(): void
    {
        $stmt = $this->pdo->query('SELECT id FROM tiers');
        $tiers = $stmt->fetchAll(PDO::FETCH_COLUMN);

        foreach ($tiers as $tiersId) {
            $score = $this->calculateScore((int)$tiersId);
            $level = $this->getRiskLevel($score);
            $this->pdo->prepare(
                'UPDATE tiers SET risk_score = ?, risk_level = ? WHERE id = ?'
            )->execute([$score, $level, $tiersId]);
        }
    }

    public function getAtRiskClients(): array
    {
        $stmt = $this->pdo->query(
            'SELECT t.*, t.risk_score, t.risk_level
             FROM tiers t
             WHERE t.risk_level = "high"
             ORDER BY t.risk_score DESC'
        );
        return $stmt->fetchAll();
    }

    public function getDecliningClients(): array
    {
        $stmt = $this->pdo->query('SELECT id, name FROM tiers');
        $tiers  = $stmt->fetchAll();
        $result = [];

        foreach ($tiers as $t) {
            if ($this->hasRevenueDecline((int)$t['id'])) {
                $result[] = $t;
            }
        }
        return $result;
    }

    public function getDormantClients(int $inactiveDays = 180): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT t.id, t.name, MAX(i.date_invoice) AS last_invoice
             FROM tiers t
             LEFT JOIN invoices i ON i.tiers_id = t.id
             GROUP BY t.id, t.name
             HAVING last_invoice IS NULL OR last_invoice < DATE_SUB(CURDATE(), INTERVAL ? DAY)
             ORDER BY last_invoice ASC'
        );
        $stmt->execute([$inactiveDays]);
        return $stmt->fetchAll();
    }

    public function getPaymentAnomalies(): array
    {
        // Clients who switched payment method recently
        $stmt = $this->pdo->query(
            'SELECT tiers_id, method, COUNT(*) AS cnt, MAX(date_payment) AS last_payment
             FROM payments
             WHERE date_payment >= DATE_SUB(CURDATE(), INTERVAL 3 MONTH)
             GROUP BY tiers_id, method
             HAVING cnt = 1'
        );
        return $stmt->fetchAll();
    }

    private function hasRevenueDecline(int $tiersId): bool
    {
        $months = [];
        for ($i = 2; $i >= 0; $i--) {
            $ts    = strtotime("-$i months");
            $year  = (int)date('Y', $ts);
            $month = (int)date('m', $ts);

            $stmt = $this->pdo->prepare(
                'SELECT COALESCE(SUM(total_ht), 0) FROM invoices
                 WHERE tiers_id = ? AND status = 2
                   AND YEAR(date_invoice) = ? AND MONTH(date_invoice) = ?'
            );
            $stmt->execute([$tiersId, $year, $month]);
            $months[] = (float)$stmt->fetchColumn();
        }

        // Decline if each month is strictly less than the previous
        return $months[0] > 0 && $months[1] < $months[0] && $months[2] < $months[1];
    }

    public function getAlertsForTiers(int $tiersId): array
    {
        $alerts = [];

        $delayStats = $this->analyzer->getDelayStats($tiersId);
        if ((int)($delayStats['delayed_count'] ?? 0) >= 2) {
            $alerts[] = ['type' => 'warning', 'message' => 'Au moins 2 paiements en retard détectés.'];
        }
        if ((float)($delayStats['avg_delay_days'] ?? 0) > 45) {
            $avg = round($delayStats['avg_delay_days'], 1);
            $alerts[] = ['type' => 'danger', 'message' => "Délai moyen de paiement élevé : {$avg} jours."];
        }

        $overdueStmt = $this->pdo->prepare('SELECT COUNT(*) FROM invoices WHERE tiers_id = ? AND is_overdue = 1');
        $overdueStmt->execute([$tiersId]);
        if ((int)$overdueStmt->fetchColumn() > 0) {
            $alerts[] = ['type' => 'danger', 'message' => 'Factures impayées en retard.'];
        }

        if ($this->hasRevenueDecline($tiersId)) {
            $alerts[] = ['type' => 'warning', 'message' => 'Baisse de chiffre d\'affaires sur 3 mois consécutifs.'];
        }

        $freq = $this->analyzer->detectFrequency($tiersId);
        if ($freq === 'irrégulier') {
            $alerts[] = ['type' => 'info', 'message' => 'Fréquence de paiement irrégulière.'];
        }

        return $alerts;
    }
}
