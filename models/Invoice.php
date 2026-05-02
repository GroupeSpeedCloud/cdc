<?php
require_once __DIR__ . '/BaseModel.php';

class Invoice extends BaseModel
{
    protected string $table = 'invoices';

    public function getByTiers(int $tiersId, int $limit = 50): array
    {
        return $this->query(
            'SELECT * FROM invoices WHERE tiers_id = ? ORDER BY date_invoice DESC LIMIT ?',
            [$tiersId, $limit]
        );
    }

    public function getOverdue(): array
    {
        return $this->query(
            'SELECT i.*, t.name AS tiers_name
             FROM invoices i
             LEFT JOIN tiers t ON t.id = i.tiers_id
             WHERE i.is_overdue = 1
             ORDER BY i.date_due ASC'
        );
    }

    public function getStats(): array
    {
        return $this->queryOne(
            'SELECT
               COUNT(*) AS total,
               SUM(CASE WHEN status = 2 THEN 1 ELSE 0 END) AS paid,
               SUM(CASE WHEN status IN (0,1) THEN 1 ELSE 0 END) AS unpaid,
               SUM(CASE WHEN is_overdue = 1 THEN 1 ELSE 0 END) AS overdue,
               COALESCE(SUM(CASE WHEN status = 2 THEN total_ht END), 0) AS revenue_paid,
               COALESCE(SUM(CASE WHEN status IN (0,1) THEN total_ttc END), 0) AS amount_unpaid
             FROM invoices'
        ) ?? [];
    }

    public function getByDateRange(string $from, string $to): array
    {
        return $this->query(
            'SELECT i.*, t.name AS tiers_name
             FROM invoices i
             LEFT JOIN tiers t ON t.id = i.tiers_id
             WHERE i.date_invoice BETWEEN ? AND ?
             ORDER BY i.date_invoice DESC',
            [$from, $to]
        );
    }

    public function getWithTiers(int $limit = 100, int $offset = 0): array
    {
        return $this->query(
            'SELECT i.*, t.name AS tiers_name
             FROM invoices i
             LEFT JOIN tiers t ON t.id = i.tiers_id
             ORDER BY i.date_invoice DESC
             LIMIT ? OFFSET ?',
            [$limit, $offset]
        );
    }

    public function getMonthlyStats(): array
    {
        return $this->query(
            'SELECT
               YEAR(date_invoice) AS year,
               MONTH(date_invoice) AS month,
               COUNT(*) AS count,
               COALESCE(SUM(total_ht), 0) AS revenue
             FROM invoices
             WHERE status = 2
             GROUP BY YEAR(date_invoice), MONTH(date_invoice)
             ORDER BY year DESC, month DESC
             LIMIT 24'
        );
    }

    public function searchByRef(string $ref): array
    {
        return $this->query(
            'SELECT i.*, t.name AS tiers_name
             FROM invoices i
             LEFT JOIN tiers t ON t.id = i.tiers_id
             WHERE i.ref LIKE ?
             ORDER BY i.date_invoice DESC
             LIMIT 50',
            ["%$ref%"]
        );
    }
}
