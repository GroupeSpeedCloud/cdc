<?php

namespace App\Http\Controllers;

use App\Services\FinanceService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct(private FinanceService $finance) {}

    public function index(Request $request)
    {
        $year = (int) ($request->get('year', Carbon::now()->year));
        $years = range(Carbon::now()->year - 3, Carbon::now()->year + 1);
        $summary = $this->finance->getYearSummary($year);

        $chartLabels = ['Jan','Fév','Mar','Avr','Mai','Jun','Jul','Aoû','Sep','Oct','Nov','Déc'];
        $chartRevenues = array_values(array_map(fn($m) => $m['revenue'], $summary['months']));
        $chartExpenses = array_values(array_map(fn($m) => $m['expenses'], $summary['months']));
        $chartProfits  = array_values(array_map(fn($m) => $m['profit'],  $summary['months']));

        return view('reports.index', compact('year', 'years', 'summary', 'chartLabels', 'chartRevenues', 'chartExpenses', 'chartProfits'));
    }
}
