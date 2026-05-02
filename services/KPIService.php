<?php
class KPIService
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = getDB();
    }

    public function getAll(): array
    {
        return [
            'monthly_revenue'    => $this->getMonthlyRevenue(),
            'annual_revenue'     => $this->getAnnualRevenue(),
            'invoice_counts'     => $this->getInvoiceCounts(),
            'average_basket'     => $this->getAverageBasket(),
            'growth_rate'        => $this->getGrowthRate(),
            'revenue_by_tiers'   => $this->getTopTiers(10),
            'revenue_by_product' => $this->getTopProducts(10),
            'revenue_breakdown'  => $this->getRevenueBreakdown(),
            'revenue_evolution'  => $this->getRevenueEvolution(12),
        ];
    }

    public function getMonthlyRevenue(?int $year = null, ?int $month = null): float
    {
        $year  = $year  ?? (int)date('Y');
        $month = $month ?? (int)date('m');

        $stmt = $this->pdo->prepare(
            'SELECT COALESCE(SUM(amount), 0) AS revenue
             FROM payments
             WHERE YEAR(date_payment) = ?
               AND MONTH(date_payment) = ?'
        );
        $stmt->execute([$year, $month]);
        return (float)$stmt->fetchColumn();
    }

    public function getAnnualRevenue(?int $year = null): float
    {
        $year = $year ?? (int)date('Y');
        $stmt = $this->pdo->prepare(
            'SELECT COALESCE(SUM(amount), 0) AS revenue
             FROM payments
             WHERE YEAR(date_payment) = ?'
        );
        $stmt->execute([$year]);
        return (float)$stmt->fetchColumn();
    }

    public function getInvoiceCounts(): array
    {
        $stmt = $this->pdo->query(
            'SELECT
               COUNT(*) AS total,
               SUM(CASE WHEN status = 2 THEN 1 ELSE 0 END) AS paid,
               SUM(CASE WHEN status IN (0,1) AND (date_due IS NULL OR date_due >= CURDATE()) THEN 1 ELSE 0 END) AS unpaid,
               SUM(CASE WHEN is_overdue = 1 THEN 1 ELSE 0 END) AS overdue
             FROM invoices'
        );
        return $stmt->fetch();
    }

    public function getAverageBasket(): float
    {
        $stmt = $this->pdo->query(
            'SELECT COALESCE(AVG(total_ht), 0) FROM invoices WHERE status = 2'
        );
        return (float)$stmt->fetchColumn();
    }

    public function getGrowthRate(): float
    {
        $thisMonth = $this->getMonthlyRevenue();
        $lastMonth = $this->getMonthlyRevenue(
            (int)date('Y', strtotime('-1 month')),
            (int)date('m', strtotime('-1 month'))
        );

        if ($lastMonth == 0) return 0.0;
        return round((($thisMonth - $lastMonth) / $lastMonth) * 100, 2);
    }

    public function getTopTiers(int $limit = 10): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT t.id, t.name, COALESCE(SUM(i.total_ht), 0) AS revenue,
                    COUNT(i.id) AS invoice_count
             FROM tiers t
             LEFT JOIN invoices i ON i.tiers_id = t.id AND i.status = 2
             GROUP BY t.id, t.name
             ORDER BY revenue DESC
             LIMIT ?'
        );
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    public function getTopProducts(int $limit = 10): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT p.id, p.label,
                    COALESCE(SUM(CASE WHEN i.status = 2 THEN il.total_ht ELSE 0 END), 0) AS revenue,
                    COALESCE(SUM(CASE WHEN i.status = 2 THEN il.qty ELSE 0 END), 0) AS qty_sold
             FROM products p
             LEFT JOIN invoice_lines il ON il.product_id = p.id
             LEFT JOIN invoices i ON i.id = il.invoice_id
             GROUP BY p.id, p.label
             ORDER BY revenue DESC
             LIMIT ?'
        );
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    public function getRevenueBreakdown(): array
    {
        $stmt = $this->pdo->query(
            'SELECT p.label, COALESCE(SUM(il.total_ht), 0) AS revenue
             FROM products p
             JOIN invoice_lines il ON il.product_id = p.id
             JOIN invoices i ON i.id = il.invoice_id AND i.status = 2
             GROUP BY p.label
             ORDER BY revenue DESC
             LIMIT 8'
        );
        return $stmt->fetchAll();
    }

    public function getRevenueEvolution(int $months = 12): array
    {
        $data = [];
        for ($i = $months - 1; $i >= 0; $i--) {
            $ts    = strtotime("-$i months");
            $year  = (int)date('Y', $ts);
            $month = (int)date('m', $ts);
            $label = date('M Y', $ts);
            $data[] = [
                'label'   => $label,
                'revenue' => $this->getMonthlyRevenue($year, $month),
            ];
        }
        return $data;
    }

    public function getUnpaidAmount(): float
    {
        $stmt = $this->pdo->query(
            'SELECT COALESCE(SUM(total_ttc), 0) FROM invoices WHERE status IN (0,1)'
        );
        return (float)$stmt->fetchColumn();
    }

    public function getOverdueAmount(): float
    {
        $stmt = $this->pdo->query(
            'SELECT COALESCE(SUM(total_ttc), 0) FROM invoices WHERE is_overdue = 1'
        );
        return (float)$stmt->fetchColumn();
    }

    public function cacheKpi(string $key, $value, string $period = 'current'): void
    {
        $this->pdo->prepare(
            'INSERT INTO kpi_cache (key_name, value, period, calculated_at)
             VALUES (?, ?, ?, NOW())
             ON DUPLICATE KEY UPDATE value=VALUES(value), calculated_at=NOW()'
        )->execute([$key, json_encode($value), $period]);
    }
}
