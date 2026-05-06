<?php
require_once __DIR__ . '/BaseModel.php';

class Product extends BaseModel
{
    protected string $table = 'products';

    public function getAll(): array
    {
        return $this->query('SELECT * FROM products ORDER BY label');
    }

    public function find(int $id): ?array
    {
        return $this->findById($id);
    }

    public function getPagedWithSubscriptionStats(string $search, int $limit, int $offset, bool $withSubscriptions): array
    {
        $where  = $search !== '' ? 'WHERE p.label LIKE ? OR p.ref LIKE ?' : '';
        $params = $search !== '' ? ["%$search%", "%$search%"] : [];

        if ($withSubscriptions) {
            $sql = "SELECT p.*,
                           (SELECT COUNT(*) FROM subscriptions s WHERE s.product_id = p.id AND s.is_active = 1) AS sub_count,
                           (SELECT COALESCE(SUM(s.amount),0) FROM subscriptions s WHERE s.product_id = p.id AND s.is_active = 1 AND s.recurrence='monthly') AS mrr_direct
                    FROM products p $where
                    ORDER BY p.label
                    LIMIT ? OFFSET ?";
        } else {
            $sql = "SELECT p.*, 0 AS sub_count, 0 AS mrr_direct
                    FROM products p $where
                    ORDER BY p.label
                    LIMIT ? OFFSET ?";
        }

        return $this->query($sql, array_merge($params, [$limit, $offset]));
    }

    public function countSearch(string $search): int
    {
        $where  = $search !== '' ? 'WHERE label LIKE ? OR ref LIKE ?' : '';
        $params = $search !== '' ? ["%$search%", "%$search%"] : [];
        $row = $this->queryOne("SELECT COUNT(*) AS c FROM products $where LIMIT 1", $params);
        return (int)($row['c'] ?? 0);
    }

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
