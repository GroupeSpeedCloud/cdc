<?php
require_once __DIR__ . '/BaseModel.php';

class Tiers extends BaseModel
{
    protected string $table = 'tiers';

    public function getAll(): array
    {
        return $this->query('SELECT * FROM tiers ORDER BY name');
    }

    public function find(int $id): ?array
    {
        return $this->findById($id);
    }

    public function getWithStats(int $limit = 100, int $offset = 0, string $search = ''): array
    {
        $params = [];
        $where  = '';

        if ($search) {
            $where    = 'WHERE t.name LIKE ? OR t.email LIKE ?';
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        $params[] = $limit;
        $params[] = $offset;

        return $this->query(
            "SELECT t.*,
                    COALESCE(SUM(CASE WHEN i.status = 2 THEN i.total_ht END), 0) AS revenue,
                    COUNT(DISTINCT i.id) AS invoice_count,
                    SUM(CASE WHEN i.is_overdue = 1 THEN 1 ELSE 0 END) AS overdue_count,
                    MAX(i.date_invoice) AS last_invoice_date
             FROM tiers t
             LEFT JOIN invoices i ON i.tiers_id = t.id
             $where
             GROUP BY t.id
             ORDER BY revenue DESC
             LIMIT ? OFFSET ?",
            $params
        );
    }

    public function getWithRiskScore(int $limit = 100, string $level = ''): array
    {
        $params = [];
        $where  = '';

        if ($level) {
            $where    = 'WHERE t.risk_level = ?';
            $params[] = $level;
        }

        $params[] = $limit;

        return $this->query(
            "SELECT t.*, COALESCE(SUM(CASE WHEN i.status = 2 THEN i.total_ht END), 0) AS revenue
             FROM tiers t
             LEFT JOIN invoices i ON i.tiers_id = t.id
             $where
             GROUP BY t.id
             ORDER BY t.risk_score DESC
             LIMIT ?",
            $params
        );
    }

    public function getDetail(int $id): ?array
    {
        return $this->queryOne(
            'SELECT t.*,
                    COALESCE(SUM(CASE WHEN i.status = 2 THEN i.total_ht END), 0) AS revenue_paid,
                    COUNT(DISTINCT i.id) AS invoice_count,
                    SUM(CASE WHEN i.is_overdue = 1 THEN 1 ELSE 0 END) AS overdue_count,
                    MIN(i.date_invoice) AS first_invoice_date,
                    MAX(i.date_invoice) AS last_invoice_date
             FROM tiers t
             LEFT JOIN invoices i ON i.tiers_id = t.id
             WHERE t.id = ?
             GROUP BY t.id',
            [$id]
        );
    }

    public function getHighRiskCount(): int
    {
        return (int)$this->queryScalar(
            "SELECT COUNT(*) FROM tiers WHERE risk_level = 'high'"
        );
    }

    public function getActiveCount(): int
    {
        return (int)$this->queryScalar(
            "SELECT COUNT(*) FROM tiers WHERE is_active = 1"
        );
    }

    public function getRevenueHistory(int $tiersId, int $months = 12): array
    {
        // Utiliser payments si disponibles, sinon invoices
        $countStmt = $this->pdo->prepare('SELECT COUNT(*) FROM payments WHERE tiers_id = ? AND date_payment IS NOT NULL');
        $countStmt->execute([$tiersId]);
        $usePayments = (int)$countStmt->fetchColumn() > 0;

        $data = [];
        for ($i = $months - 1; $i >= 0; $i--) {
            $ts    = strtotime("-$i months");
            $year  = (int)date('Y', $ts);
            $month = (int)date('m', $ts);
            $label = date('M Y', $ts);

            if ($usePayments) {
                $rev = $this->queryScalar(
                    'SELECT COALESCE(SUM(amount), 0) FROM payments
                     WHERE tiers_id = ?
                       AND YEAR(date_payment) = ? AND MONTH(date_payment) = ?',
                    [$tiersId, $year, $month]
                );
            } else {
                $rev = $this->queryScalar(
                    'SELECT COALESCE(SUM(total_ht), 0) FROM invoices
                     WHERE tiers_id = ? AND status = 2
                       AND YEAR(date_invoice) = ? AND MONTH(date_invoice) = ?',
                    [$tiersId, $year, $month]
                );
            }
            $data[] = ['label' => $label, 'revenue' => (float)$rev];
        }
        return $data;
    }
}
