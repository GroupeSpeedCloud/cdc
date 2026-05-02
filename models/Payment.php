<?php
require_once __DIR__ . '/BaseModel.php';

class Payment extends BaseModel
{
    protected string $table = 'payments';

    public function getByTiers(int $tiersId, int $limit = 50): array
    {
        return $this->query(
            'SELECT p.*, i.ref AS invoice_ref
             FROM payments p
             LEFT JOIN invoices i ON i.id = p.invoice_id
             WHERE p.tiers_id = ?
             ORDER BY p.date_payment DESC
             LIMIT ?',
            [$tiersId, $limit]
        );
    }

    public function getMethodsBreakdown(): array
    {
        return $this->query(
            'SELECT method,
                    COUNT(*) AS count,
                    COALESCE(SUM(amount), 0) AS total_amount,
                    COALESCE(AVG(amount), 0) AS avg_amount
             FROM payments
             GROUP BY method
             ORDER BY total_amount DESC'
        );
    }

    public function getRecentPayments(int $limit = 20): array
    {
        return $this->query(
            'SELECT p.*, t.name AS tiers_name, i.ref AS invoice_ref
             FROM payments p
             LEFT JOIN tiers t ON t.id = p.tiers_id
             LEFT JOIN invoices i ON i.id = p.invoice_id
             ORDER BY p.date_payment DESC
             LIMIT ?',
            [$limit]
        );
    }

    public function getMonthlyTotals(int $months = 12): array
    {
        $data = [];
        for ($i = $months - 1; $i >= 0; $i--) {
            $ts    = strtotime("-$i months");
            $year  = (int)date('Y', $ts);
            $month = (int)date('m', $ts);

            $total = $this->queryScalar(
                'SELECT COALESCE(SUM(amount), 0) FROM payments
                 WHERE YEAR(date_payment) = ? AND MONTH(date_payment) = ?',
                [$year, $month]
            );
            $data[] = [
                'label'  => date('M Y', $ts),
                'amount' => (float)$total,
            ];
        }
        return $data;
    }

    public function getTotalCollected(): float
    {
        return (float)$this->queryScalar('SELECT COALESCE(SUM(amount), 0) FROM payments');
    }
}
