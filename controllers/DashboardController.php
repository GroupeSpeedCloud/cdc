<?php
require_once __DIR__ . '/../models/BaseModel.php';
require_once __DIR__ . '/../models/Invoice.php';
require_once __DIR__ . '/../models/Tiers.php';
require_once __DIR__ . '/../services/KPIService.php';

class DashboardController
{
    public function index(): void
    {
        $kpiService = new KPIService();
        $kpis       = $kpiService->getAll();
        $year       = (int)date('Y');

        $invoiceCounts = $kpis['invoice_counts'] ?? ['total' => 0, 'paid' => 0, 'overdue' => 0];
        $totalInvoices = (int)($invoiceCounts['total'] ?? 0);
        $paidInvoices  = (int)($invoiceCounts['paid'] ?? 0);
        $overdueInvoices = (int)($invoiceCounts['overdue'] ?? 0);

        $paidRatePct    = $totalInvoices > 0 ? round(($paidInvoices / $totalInvoices) * 100, 1) : 0.0;
        $overdueRatePct = $totalInvoices > 0 ? round(($overdueInvoices / $totalInvoices) * 100, 1) : 0.0;

        $annualRevenue = (float)($kpis['annual_revenue'] ?? 0);
        $monthlyRevenue = (float)($kpis['monthly_revenue'] ?? 0);
        $monthlyGrowthPct = (float)($kpis['growth_rate'] ?? 0);
        $unpaidAmount  = (float)$kpiService->getUnpaidAmount();
        $overdueAmount = (float)$kpiService->getOverdueAmount();

        $annualExpenses = 0.0;
        $monthlyExpenses = 0.0;
        $expenseCategories = [];
        $expensesAvailable = false;

        try {
            require_once __DIR__ . '/../models/Expense.php';
            $expenseModel = new Expense();
            $annualExpenses   = $expenseModel->getAnnualEquivalent();
            $monthlyExpenses  = $expenseModel->getMonthlyEquivalent();
            $expenseCategories = $expenseModel->getByCategory();
            $expensesAvailable = true;
        } catch (Throwable $e) {
            error_log('Dashboard expenses metrics unavailable: ' . $e->getMessage());
        }

        $annualProfit   = $annualRevenue - $annualExpenses;
        $monthlyProfit  = $monthlyRevenue - $monthlyExpenses;
        $marginPct      = $annualRevenue > 0 ? round(($annualProfit / $annualRevenue) * 100, 1) : 0.0;
        $expenseRatePct = $annualRevenue > 0 ? round(($annualExpenses / $annualRevenue) * 100, 1) : 0.0;
        $overdueRiskPct = $annualRevenue > 0 ? round(($overdueAmount / $annualRevenue) * 100, 1) : 0.0;

        $openAmount = $annualRevenue + $unpaidAmount;
        $collectionRateAmountPct = $openAmount > 0 ? round(($annualRevenue / $openAmount) * 100, 1) : 0.0;
        $overdueOnOpenPct = $unpaidAmount > 0 ? round(($overdueAmount / $unpaidAmount) * 100, 1) : 0.0;

        $revenueSeries = array_map(static fn(array $item): float => (float)($item['revenue'] ?? 0), $kpis['revenue_evolution'] ?? []);
        $avgMonthlyRevenue = !empty($revenueSeries) ? array_sum($revenueSeries) / count($revenueSeries) : 0.0;
        $runRateAnnual = $avgMonthlyRevenue * 12;

        $variance = 0.0;
        if (!empty($revenueSeries) && $avgMonthlyRevenue > 0) {
            foreach ($revenueSeries as $value) {
                $variance += (($value - $avgMonthlyRevenue) ** 2);
            }
            $variance /= count($revenueSeries);
        }
        $stdDev = sqrt($variance);
        $volatilityPct = $avgMonthlyRevenue > 0 ? round(($stdDev / $avgMonthlyRevenue) * 100, 1) : 0.0;

        $topTiers = $kpis['revenue_by_tiers'] ?? [];
        $top1Revenue = isset($topTiers[0]) ? (float)($topTiers[0]['revenue'] ?? 0) : 0.0;
        $top3Revenue = 0.0;
        foreach (array_slice($topTiers, 0, 3) as $row) {
            $top3Revenue += (float)($row['revenue'] ?? 0);
        }

        $top1SharePct = $annualRevenue > 0 ? round(($top1Revenue / $annualRevenue) * 100, 1) : 0.0;
        $top3SharePct = $annualRevenue > 0 ? round(($top3Revenue / $annualRevenue) * 100, 1) : 0.0;

        $runwayMonths = null;
        if ($monthlyExpenses > 0 && $monthlyProfit < 0) {
            $runwayMonths = round($annualRevenue / $monthlyExpenses, 1);
        }

        $kpis['annual_summary'] = [
            'year'              => $year,
            'annual_revenue'    => $annualRevenue,
            'annual_expenses'   => $annualExpenses,
            'monthly_expenses'  => $monthlyExpenses,
            'annual_profit'     => $annualProfit,
            'monthly_profit'    => $monthlyProfit,
            'monthly_revenue'   => $monthlyRevenue,
            'monthly_growth_pct'=> $monthlyGrowthPct,
            'margin_pct'        => $marginPct,
            'expense_rate_pct'  => $expenseRatePct,
            'paid_rate_pct'     => $paidRatePct,
            'overdue_rate_pct'  => $overdueRatePct,
            'collection_rate_amount_pct' => $collectionRateAmountPct,
            'overdue_on_open_pct' => $overdueOnOpenPct,
            'avg_monthly_revenue' => $avgMonthlyRevenue,
            'run_rate_annual'  => $runRateAnnual,
            'volatility_pct'   => $volatilityPct,
            'top1_share_pct'   => $top1SharePct,
            'top3_share_pct'   => $top3SharePct,
            'runway_months'    => $runwayMonths,
            'unpaid_amount'     => $unpaidAmount,
            'overdue_amount'    => $overdueAmount,
            'overdue_risk_pct'  => $overdueRiskPct,
            'expenses_available'=> $expensesAvailable,
            'expense_categories'=> $expenseCategories,
        ];

        $user       = $_SESSION['user'];

        require_once __DIR__ . '/../views/dashboard.php';
    }
}
