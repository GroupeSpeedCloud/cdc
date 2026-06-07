<?php

namespace App\Services;

use App\Models\MonthlyRevenue;
use App\Models\Project;
use App\Models\RecurringExpense;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class FinanceService
{
    public function getTotalRevenueForMonth(int $year, int $month): float
    {
        return (float) MonthlyRevenue::where('year', $year)->where('month', $month)->sum('amount');
    }

    public function getRevenueByProjectForMonth(int $year, int $month): Collection
    {
        return MonthlyRevenue::with('project')
            ->where('year', $year)
            ->where('month', $month)
            ->get()
            ->keyBy('project_id')
            ->map(fn($r) => (float) $r->amount);
    }

    public function getTotalExpensesForMonth(int $year, int $month): float
    {
        return RecurringExpense::all()->sum(function (RecurringExpense $expense) use ($year, $month) {
            $amount = $expense->getAmountForMonth($year, $month);
            return $amount ?? 0.0;
        });
    }

    public function getExpensesDetailForMonth(int $year, int $month): Collection
    {
        return RecurringExpense::all()->map(function (RecurringExpense $expense) use ($year, $month) {
            $amount = $expense->getAmountForMonth($year, $month);
            return [
                'expense' => $expense,
                'amount' => $amount,
                'active' => $amount !== null,
            ];
        })->filter(fn($item) => $item['active']);
    }

    public function getNetProfitForMonth(int $year, int $month): float
    {
        return $this->getTotalRevenueForMonth($year, $month) - $this->getTotalExpensesForMonth($year, $month);
    }

    public function getMarginForMonth(int $year, int $month): float
    {
        $revenue = $this->getTotalRevenueForMonth($year, $month);
        if ($revenue == 0) {
            return 0.0;
        }
        return round(($this->getNetProfitForMonth($year, $month) / $revenue) * 100, 1);
    }

    public function getCurrentKPIs(): array
    {
        $now = Carbon::now();
        $year = $now->year;
        $month = $now->month;

        $prevMonth = $now->copy()->subMonth();
        $prevYear = $prevMonth->year;
        $prevM = $prevMonth->month;

        $revenue = $this->getTotalRevenueForMonth($year, $month);
        $expenses = $this->getTotalExpensesForMonth($year, $month);
        $profit = $this->getNetProfitForMonth($year, $month);
        $margin = $this->getMarginForMonth($year, $month);

        $prevRevenue = $this->getTotalRevenueForMonth($prevYear, $prevM);
        $revenueDiff = $prevRevenue > 0 ? round((($revenue - $prevRevenue) / $prevRevenue) * 100, 1) : null;

        return [
            'year' => $year,
            'month' => $month,
            'revenue' => $revenue,
            'expenses' => $expenses,
            'profit' => $profit,
            'margin' => $margin,
            'prev_revenue' => $prevRevenue,
            'revenue_diff_pct' => $revenueDiff,
        ];
    }

    public function getRevenueChartData(int $months = 12): array
    {
        $projects = Project::where('status', 'active')->get();
        $labels = [];
        $datasets = [];

        $end = Carbon::now();
        $start = $end->copy()->subMonths($months - 1)->startOfMonth();

        $current = $start->copy();
        while ($current->lte($end)) {
            $labels[] = $current->translatedFormat('M Y');
            $current->addMonth();
        }

        foreach ($projects as $project) {
            $data = [];
            $current = $start->copy();
            while ($current->lte($end)) {
                $data[] = $project->getRevenueForMonth($current->year, $current->month);
                $current->addMonth();
            }
            $datasets[] = [
                'label' => $project->name,
                'data' => $data,
                'borderColor' => $project->color,
                'backgroundColor' => $project->color . '33',
                'tension' => 0.3,
                'fill' => false,
            ];
        }

        return ['labels' => $labels, 'datasets' => $datasets];
    }

    public function getExpensesByCategoryForMonth(int $year, int $month): array
    {
        $categoryLabels = [
            'personnel' => 'Personnel',
            'hebergement' => 'Hébergement',
            'infrastructure' => 'Infrastructure',
            'marketing' => 'Marketing',
            'locaux' => 'Locaux',
            'autre' => 'Autre',
        ];

        $categoryColors = [
            'personnel' => '#6366f1',
            'hebergement' => '#10b981',
            'infrastructure' => '#f59e0b',
            'marketing' => '#f43f5e',
            'locaux' => '#3b82f6',
            'autre' => '#8b5cf6',
        ];

        $totals = [];
        foreach ($categoryLabels as $key => $_) {
            $totals[$key] = 0.0;
        }

        RecurringExpense::all()->each(function (RecurringExpense $expense) use ($year, $month, &$totals) {
            $amount = $expense->getAmountForMonth($year, $month);
            if ($amount !== null) {
                $totals[$expense->category] = ($totals[$expense->category] ?? 0) + $amount;
            }
        });

        $labels = [];
        $data = [];
        $colors = [];

        foreach ($totals as $category => $amount) {
            if ($amount > 0) {
                $labels[] = $categoryLabels[$category] ?? $category;
                $data[] = $amount;
                $colors[] = $categoryColors[$category] ?? '#6366f1';
            }
        }

        return [
            'labels' => $labels,
            'datasets' => [[
                'data' => $data,
                'backgroundColor' => $colors,
                'borderWidth' => 0,
            ]],
        ];
    }

    public function getCashflowChartData(int $months = 12): array
    {
        $labels = [];
        $revenues = [];
        $expenses = [];
        $profits = [];

        $end = Carbon::now();
        $start = $end->copy()->subMonths($months - 1)->startOfMonth();
        $current = $start->copy();

        while ($current->lte($end)) {
            $y = $current->year;
            $m = $current->month;
            $labels[] = $current->translatedFormat('M Y');
            $rev = $this->getTotalRevenueForMonth($y, $m);
            $exp = $this->getTotalExpensesForMonth($y, $m);
            $revenues[] = $rev;
            $expenses[] = $exp;
            $profits[] = $rev - $exp;
            $current->addMonth();
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Revenus',
                    'data' => $revenues,
                    'backgroundColor' => '#6366f133',
                    'borderColor' => '#6366f1',
                    'borderWidth' => 2,
                ],
                [
                    'label' => 'Dépenses',
                    'data' => $expenses,
                    'backgroundColor' => '#f43f5e33',
                    'borderColor' => '#f43f5e',
                    'borderWidth' => 2,
                ],
                [
                    'label' => 'Profit',
                    'data' => $profits,
                    'backgroundColor' => '#10b98133',
                    'borderColor' => '#10b981',
                    'borderWidth' => 2,
                    'type' => 'line',
                    'tension' => 0.3,
                ],
            ],
        ];
    }

    public function getYearSummary(int $year): array
    {
        $months = [];
        $totalRevenue = 0;
        $totalExpenses = 0;
        $totalProfit = 0;

        for ($m = 1; $m <= 12; $m++) {
            $rev = $this->getTotalRevenueForMonth($year, $m);
            $exp = $this->getTotalExpensesForMonth($year, $m);
            $profit = $rev - $exp;
            $margin = $rev > 0 ? round(($profit / $rev) * 100, 1) : 0;

            $months[$m] = [
                'month' => $m,
                'revenue' => $rev,
                'expenses' => $exp,
                'profit' => $profit,
                'margin' => $margin,
            ];

            $totalRevenue += $rev;
            $totalExpenses += $exp;
            $totalProfit += $profit;
        }

        return [
            'year' => $year,
            'months' => $months,
            'total_revenue' => $totalRevenue,
            'total_expenses' => $totalExpenses,
            'total_profit' => $totalProfit,
            'avg_margin' => $totalRevenue > 0 ? round(($totalProfit / $totalRevenue) * 100, 1) : 0,
        ];
    }
}
