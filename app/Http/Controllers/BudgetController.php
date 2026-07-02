<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Service;
use Illuminate\Http\Request;

class BudgetController extends Controller
{
    public function index(Request $request)
    {
        $annee = (int) $request->input('annee', now()->year);
        $services = Service::with(['budgets' => fn ($q) => $q->where('annee', $annee)])
            ->orderBy('name')->get();

        $annees = range(now()->year + 1, now()->year - 2);

        return view('budgets.index', compact('services', 'annee', 'annees'));
    }

    /** Alloue / met à jour les budgets pour une année donnée (notamment N+1). */
    public function store(Request $request)
    {
        $data = $request->validate([
            'annee' => ['required', 'integer', 'min:2020', 'max:2100'],
            'budgets' => ['required', 'array'],
            'budgets.*' => ['nullable', 'numeric', 'min:0'],
        ]);

        $annee = (int) $data['annee'];
        $anneeCourante = now()->year;

        foreach ($data['budgets'] as $serviceId => $montant) {
            if ($montant === null || $montant === '') {
                continue;
            }
            $service = Service::find($serviceId);
            if (! $service) {
                continue;
            }

            $budget = Budget::firstOrNew(['service_id' => $service->id, 'annee' => $annee]);
            $delta = (float) $montant - (float) ($budget->montant_initial ?? 0);
            $budget->montant_initial = $montant;
            $budget->montant_depense = $budget->montant_depense ?? 0;
            $budget->save();

            // Si on alloue le budget de l'année courante, refléter sur le service.
            if ($annee === $anneeCourante) {
                $service->budget_annuel_courant = $montant;
                $service->budget_restant = (float) $service->budget_restant + $delta;
                $service->save();
            }
        }

        return redirect()->route('budgets.index', ['annee' => $annee])
            ->with('success', "Budgets de l'année {$annee} enregistrés.");
    }
}
