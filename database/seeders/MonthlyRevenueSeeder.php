<?php

namespace Database\Seeders;

use App\Models\MonthlyRevenue;
use App\Models\Project;
use Illuminate\Database\Seeder;

class MonthlyRevenueSeeder extends Seeder
{
    public function run(): void
    {
        $year = now()->year;
        $currentMonth = now()->month;

        $projects = Project::all();

        // Base amounts per project (monthly average)
        $baseAmounts = [
            "Festival d'été" => [500, 600, 800, 1200, 2500, 4800, 5000, 3200, 1500, 800, 600, 500],
            'Atelier numérique' => [1200, 1100, 1300, 1400, 1350, 1200, 800, 900, 1300, 1400, 1500, 1200],
            'Projet formation' => [2000, 2200, 2100, 1800, 2300, 1900, 1500, 1600, 2200, 2400, 2100, 1900],
            'Événements culturels' => [800, 900, 1200, 1500, 1800, 2200, 2500, 2000, 1600, 1200, 900, 700],
            'Projet communication' => [600, 650, 700, 720, 680, 750, 600, 620, 700, 750, 800, 700],
        ];

        foreach ($projects as $project) {
            $amounts = $baseAmounts[$project->name] ?? array_fill(0, 12, 1000);

            for ($month = 1; $month <= 12; $month++) {
                // Don't seed future months
                if ($month > $currentMonth) {
                    continue;
                }

                // Add some variation ±10%
                $base = $amounts[$month - 1];
                $variation = $base * 0.1 * (($month % 3) - 1); // deterministic variation
                $amount = round($base + $variation, 2);

                MonthlyRevenue::updateOrCreate(
                    ['project_id' => $project->id, 'year' => $year, 'month' => $month],
                    ['amount' => $amount]
                );
            }
        }
    }
}
