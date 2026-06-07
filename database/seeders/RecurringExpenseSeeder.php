<?php

namespace Database\Seeders;

use App\Models\RecurringExpense;
use Illuminate\Database\Seeder;

class RecurringExpenseSeeder extends Seeder
{
    public function run(): void
    {
        $startDate = now()->startOfYear()->format('Y-m-d');

        $expenses = [
            [
                'name' => 'Loyer local',
                'category' => 'locaux',
                'amount' => 800.00,
                'start_month' => $startDate,
                'end_month' => null,
                'notes' => 'Loyer mensuel du local associatif',
            ],
            [
                'name' => 'Salaire coordinateur',
                'category' => 'personnel',
                'amount' => 2200.00,
                'start_month' => $startDate,
                'end_month' => null,
                'notes' => 'Salaire brut du coordinateur général',
            ],
            [
                'name' => 'Serveurs et hébergement',
                'category' => 'hebergement',
                'amount' => 150.00,
                'start_month' => $startDate,
                'end_month' => null,
                'notes' => 'Hébergement cloud et serveurs',
            ],
            [
                'name' => 'Logiciels et abonnements',
                'category' => 'infrastructure',
                'amount' => 120.00,
                'start_month' => $startDate,
                'end_month' => null,
                'notes' => 'Suite bureautique, outils collaboratifs',
            ],
            [
                'name' => 'Fournitures bureau',
                'category' => 'autre',
                'amount' => 80.00,
                'start_month' => $startDate,
                'end_month' => null,
                'notes' => 'Papeterie, consommables',
            ],
            [
                'name' => 'Communication digitale',
                'category' => 'marketing',
                'amount' => 200.00,
                'start_month' => $startDate,
                'end_month' => null,
                'notes' => 'Réseaux sociaux, newsletters, publicité',
            ],
        ];

        foreach ($expenses as $expense) {
            RecurringExpense::create($expense);
        }
    }
}
