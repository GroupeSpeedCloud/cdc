<?php

namespace App\Http\Controllers;

use App\Models\MonthlyRevenue;
use App\Models\Project;
use App\Services\FinanceService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function __construct(private FinanceService $finance) {}

    public function index()
    {
        $now = Carbon::now();
        $projects = Project::all()->map(function ($project) use ($now) {
            return [
                'project' => $project,
                'current_revenue' => $project->getRevenueForMonth($now->year, $now->month),
                'total_revenue' => $project->getTotalRevenue(),
            ];
        });
        return view('projects.index', compact('projects'));
    }

    public function create()
    {
        return view('projects.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'color' => 'required|string|regex:/^#[0-9a-fA-F]{6}$/',
            'status' => 'required|in:active,archived',
        ]);

        Project::create($data);
        return redirect()->route('projects.index')->with('success', 'Projet créé avec succès.');
    }

    public function show(Project $project)
    {
        $revenueData = $this->finance->getRevenueChartData(12);

        // Filter to only this project
        $labels = $revenueData['labels'];
        $dataset = collect($revenueData['datasets'])->firstWhere('label', $project->name);

        $revenues = MonthlyRevenue::where('project_id', $project->id)
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->get();

        return view('projects.show', compact('project', 'labels', 'dataset', 'revenues'));
    }

    public function edit(Project $project)
    {
        return view('projects.edit', compact('project'));
    }

    public function update(Request $request, Project $project)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'color' => 'required|string|regex:/^#[0-9a-fA-F]{6}$/',
            'status' => 'required|in:active,archived',
        ]);

        $project->update($data);
        return redirect()->route('projects.index')->with('success', 'Projet mis à jour.');
    }

    public function destroy(Project $project)
    {
        $hasData = $project->monthlyRevenues()->exists();
        if ($hasData) {
            $project->update(['status' => 'archived']);
            return redirect()->route('projects.index')->with('success', 'Projet archivé (des données existent).');
        }
        $project->delete();
        return redirect()->route('projects.index')->with('success', 'Projet supprimé.');
    }
}
