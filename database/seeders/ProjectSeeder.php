<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $projects = [
            ['name' => "Festival d'été", 'color' => '#f59e0b', 'description' => 'Organisation annuelle du festival culturel estival', 'status' => 'active'],
            ['name' => 'Atelier numérique', 'color' => '#6366f1', 'description' => 'Ateliers de formation au numérique pour le public', 'status' => 'active'],
            ['name' => 'Projet formation', 'color' => '#10b981', 'description' => 'Programme de formations professionnelles', 'status' => 'active'],
            ['name' => 'Événements culturels', 'color' => '#f43f5e', 'description' => 'Organisation d\'événements culturels tout au long de l\'année', 'status' => 'active'],
            ['name' => 'Projet communication', 'color' => '#3b82f6', 'description' => 'Stratégie et actions de communication de l\'association', 'status' => 'active'],
        ];

        foreach ($projects as $project) {
            Project::create($project);
        }
    }
}
