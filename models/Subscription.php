<?php
require_once __DIR__ . '/BaseModel.php';

class Subscription extends BaseModel
{
    protected string $table = 'subscriptions';

    /** Retourne les abonnements avec nom du tiers + nom du produit */
    public function getAll(int $limit = 200, int $offset = 0, string $search = '', string $recurrence = ''): array
    {
        $where  = ['1=1'];
        $params = [];

        if ($search !== '') {
            $where[]  = '(t.name LIKE ? OR s.label LIKE ? OR p.label LIKE ?)';
            $like     = "%$search%";
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
        }
        if ($recurrence !== '') {
            $where[]  = 's.recurrence = ?';
            $params[] = $recurrence;
        }

        $sql = 'SELECT s.*, t.name AS tiers_name, COALESCE(p.label, s.label) AS product_label,
                       p.ref AS product_ref
                FROM subscriptions s
                JOIN tiers t ON t.id = s.tiers_id
                LEFT JOIN products p ON p.id = s.product_id
                WHERE ' . implode(' AND ', $where) . '
                ORDER BY t.name, s.recurrence
                LIMIT ? OFFSET ?';
        $params[] = $limit;
        $params[] = $offset;
        return $this->query($sql, $params);
    }

    public function countAll(string $search = '', string $recurrence = ''): int
    {
        $where  = ['1=1'];
        $params = [];
        if ($search !== '') {
            $where[]  = '(t.name LIKE ? OR s.label LIKE ? OR p.label LIKE ?)';
            $like     = "%$search%";
            $params[] = $like; $params[] = $like; $params[] = $like;
        }
        if ($recurrence !== '') {
            $where[]  = 's.recurrence = ?';
            $params[] = $recurrence;
        }
        $sql = 'SELECT COUNT(*) FROM subscriptions s
                JOIN tiers t ON t.id = s.tiers_id
                LEFT JOIN products p ON p.id = s.product_id
                WHERE ' . implode(' AND ', $where);
        return (int)$this->queryOne($sql . ' LIMIT 1', $params)['COUNT(*)'];
    }

    /** MRR : valeur mensuelle de tous les abonnements actifs */
    public function getMRR(): float
    {
        $rows = $this->query(
            "SELECT amount, recurrence FROM subscriptions WHERE is_active = 1
             AND (end_date IS NULL OR end_date >= CURDATE())"
        );
        $mrr = 0.0;
        foreach ($rows as $r) {
            $mrr += match($r['recurrence']) {
                'monthly'   => (float)$r['amount'],
                'quarterly' => (float)$r['amount'] / 3,
                'annual'    => (float)$r['amount'] / 12,
                default     => 0.0,
            };
        }
        return round($mrr, 2);
    }

    public function getARR(): float
    {
        return round($this->getMRR() * 12, 2);
    }

    public function countActive(): int
    {
        return (int)$this->queryOne(
            "SELECT COUNT(*) FROM subscriptions
             WHERE is_active = 1 AND (end_date IS NULL OR end_date >= CURDATE())"
        )['COUNT(*)'];
    }

    public function getByTiers(int $tiersId): array
    {
        return $this->query(
            'SELECT s.*, COALESCE(p.label, s.label) AS product_label, p.ref AS product_ref
             FROM subscriptions s
             LEFT JOIN products p ON p.id = s.product_id
             WHERE s.tiers_id = ?
             ORDER BY s.is_active DESC, s.created_at DESC',
            [$tiersId]
        );
    }
}
