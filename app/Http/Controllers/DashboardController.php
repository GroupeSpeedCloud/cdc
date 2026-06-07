<?php

namespace App\Http\Controllers;

use App\Models\MonthlyRevenue;
use App\Models\Project;
use App\Services\FinanceService;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function __construct(private FinanceService $finance) {}

    public function index()
    {
        $kpis = $this->finance->getCurrentKPIs();
        $revenueChart = $this->finance->getRevenueChartData(12);
        $cashflowChart = $this->finance->getCashflowChartData(12);
        $expensesChart = $this->finance->getExpensesByCategoryForMonth($kpis['year'], $kpis['month']);

        $now = Carbon::now();
        $projects = Project::where('status', 'active')->get()->map(function ($project) use ($now) {
            return [
                'project' => $project,
                'revenue' => $project->getRevenueForMonth($now->year, $now->month),
            ];
        });

        return view('dashboard', compact('kpis', 'revenueChart', 'cashflowChart', 'expensesChart', 'projects'));
    }
}
