<?php
class ForecastService
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = getDB();
    }

    public function getMonthlyRevenues(int $months = 18): array
    {
        $data = [];
        for ($i = $months - 1; $i >= 0; $i--) {
            $ts    = strtotime("-$i months");
            $year  = (int)date('Y', $ts);
            $month = (int)date('m', $ts);

            $stmt = $this->pdo->prepare(
                'SELECT COALESCE(SUM(total_ht), 0) FROM invoices
                 WHERE status = 2 AND YEAR(date_invoice) = ? AND MONTH(date_invoice) = ?'
            );
            $stmt->execute([$year, $month]);
            $data[] = [
                'label'   => date('M Y', $ts),
                'year'    => $year,
                'month'   => $month,
                'revenue' => (float)$stmt->fetchColumn(),
            ];
        }
        return $data;
    }

    public function getMovingAverage(array $revenues, int $window = 3): array
    {
        $result = [];
        $values = array_column($revenues, 'revenue');

        for ($i = 0; $i < count($values); $i++) {
            if ($i < $window - 1) {
                $result[] = null;
            } else {
                $slice    = array_slice($values, $i - $window + 1, $window);
                $result[] = array_sum($slice) / $window;
            }
        }
        return $result;
    }

    public function getProjections(array $revenues, int $months = 12): array
    {
        $values = array_filter(array_column($revenues, 'revenue'), fn($v) => $v > 0);
        if (empty($values)) {
            return array_fill(0, $months, 0.0);
        }

        // Linear regression
        $n  = count($values);
        $xs = range(0, $n - 1);
        $sumX  = array_sum($xs);
        $sumY  = array_sum($values);
        $sumXY = 0;
        $sumX2 = 0;

        foreach ($xs as $i => $x) {
            $sumXY += $x * array_values($values)[$i];
            $sumX2 += $x * $x;
        }

        $slope     = ($n * $sumXY - $sumX * $sumY) / max(1, ($n * $sumX2 - $sumX * $sumX));
        $intercept = ($sumY - $slope * $sumX) / $n;

        $projections = [];
        $labels      = [];
        for ($i = 1; $i <= $months; $i++) {
            $ts = strtotime("+$i months");
            $projectedValue = $intercept + $slope * ($n + $i - 1);
            $projections[]  = max(0, round($projectedValue, 2));
            $labels[]       = date('M Y', $ts);
        }

        return ['values' => $projections, 'labels' => $labels];
    }

    public function getTrendIndicator(array $revenues): string
    {
        $values = array_column($revenues, 'revenue');
        $recent = array_slice($values, -3);

        if (count($recent) < 2) return 'stable';

        $first = array_shift($recent);
        $last  = end($recent);

        if ($last > $first * 1.05)  return 'up';
        if ($last < $first * 0.95)  return 'down';
        return 'stable';
    }

    public function getFinancialHealthScore(): float
    {
        $pdo = $this->pdo;

        // Revenue trend
        $revenues = $this->getMonthlyRevenues(6);
        $trend    = $this->getTrendIndicator($revenues);
        $trendScore = match ($trend) {
            'up'     => 100,
            'stable' => 60,
            'down'   => 20,
        };

        // Paid ratio
        $stmt = $pdo->query(
            'SELECT
               COUNT(*) AS total,
               SUM(CASE WHEN status = 2 THEN 1 ELSE 0 END) AS paid
             FROM invoices'
        );
        $counts    = $stmt->fetch();
        $paidRatio = $counts['total'] > 0 ? $counts['paid'] / $counts['total'] : 1;
        $paidScore = $paidRatio * 100;

        // Overdue ratio (negative)
        $stmt = $pdo->query('SELECT COUNT(*) FROM invoices WHERE is_overdue = 1');
        $overdueCount = (int)$stmt->fetchColumn();
        $overdueScore = max(0, 100 - $overdueCount * 10);

        return round(($trendScore * 0.4 + $paidScore * 0.4 + $overdueScore * 0.2), 1);
    }

    public function getAllProjections(): array
    {
        $revenues = $this->getMonthlyRevenues(18);
        $ma3  = $this->getMovingAverage($revenues, 3);
        $ma6  = $this->getMovingAverage($revenues, 6);
        $proj3  = $this->getProjections($revenues, 3);
        $proj6  = $this->getProjections($revenues, 6);
        $proj12 = $this->getProjections($revenues, 12);

        return [
            'historical'  => $revenues,
            'ma3'         => $ma3,
            'ma6'         => $ma6,
            'proj3'       => $proj3,
            'proj6'       => $proj6,
            'proj12'      => $proj12,
            'trend'       => $this->getTrendIndicator($revenues),
            'health'      => $this->getFinancialHealthScore(),
        ];
    }
}
