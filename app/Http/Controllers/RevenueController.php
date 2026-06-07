<?php

namespace App\Http\Controllers;

use App\Models\MonthlyRevenue;
use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RevenueController extends Controller
{
    public function index(Request $request)
    {
        $year = (int) ($request->get('year', Carbon::now()->year));
        $projects = Project::where('status', 'active')->get();

        $grid = [];
        foreach ($projects as $project) {
            $grid[$project->id] = [];
            for ($m = 1; $m <= 12; $m++) {
                $rev = MonthlyRevenue::where('project_id', $project->id)
                    ->where('year', $year)
                    ->where('month', $m)
                    ->first();
                $grid[$project->id][$m] = $rev ? $rev->amount : null;
            }
        }

        $years = range(Carbon::now()->year - 2, Carbon::now()->year + 1);

        return view('revenues.index', compact('projects', 'grid', 'year', 'years'));
    }

    public function edit(int $year, int $month)
    {
        $projects = Project::where('status', 'active')->get();
        $existing = MonthlyRevenue::where('year', $year)
            ->where('month', $month)
            ->get()
            ->keyBy('project_id');

        $monthName = Carbon::createFromDate($year, $month, 1)->translatedFormat('F Y');

        return view('revenues.edit', compact('projects', 'existing', 'year', 'month', 'monthName'));
    }

    public function update(Request $request, int $year, int $month)
    {
        $data = $request->validate([
            'revenues' => 'array',
            'revenues.*' => 'nullable|numeric|min:0',
            'notes' => 'array',
            'notes.*' => 'nullable|string',
        ]);

        foreach ($data['revenues'] ?? [] as $projectId => $amount) {
            if ($amount === null || $amount === '') {
                MonthlyRevenue::where('project_id', $projectId)
                    ->where('year', $year)
                    ->where('month', $month)
                    ->delete();
                continue;
            }

            MonthlyRevenue::updateOrCreate(
                ['project_id' => $projectId, 'year' => $year, 'month' => $month],
                [
                    'amount' => (float) $amount,
                    'notes' => $data['notes'][$projectId] ?? null,
                ]
            );
        }

        return redirect()->route('revenues.index', ['year' => $year])
            ->with('success', 'Revenus enregistrés.');
    }
}
