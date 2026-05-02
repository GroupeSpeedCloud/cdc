<?php
require_once __DIR__ . '/BaseModel.php';

class Product extends BaseModel
{
    protected string $table = 'products';

    public function getWithRevenue(int $limit = 50): array
    {
        return $this->query(
            'SELECT p.*,
                    COALESCE(SUM(il.total_ht), 0) AS revenue,
                    COALESCE(SUM(il.qty), 0) AS qty_sold
             FROM products p
             LEFT JOIN invoice_lines il ON il.product_id = p.id
             LEFT JOIN invoices i ON i.id = il.invoice_id AND i.status = 2
             GROUP BY p.id
             ORDER BY revenue DESC
             LIMIT ?',
            [$limit]
        );
    }

    public function findByDolibarrId(int $dolibarrId): ?array
    {
        return $this->queryOne(
            'SELECT * FROM products WHERE dolibarr_id = ?',
            [$dolibarrId]
        );
    }

    public function searchByLabel(string $term): array
    {
        return $this->query(
            'SELECT * FROM products WHERE label LIKE ? OR ref LIKE ? LIMIT 20',
            ["%$term%", "%$term%"]
        );
    }
}
