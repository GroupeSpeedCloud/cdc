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

        return view('reports.index', compact('year', 'years', 'summary'));
    }
}
