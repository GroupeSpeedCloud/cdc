<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::with('manager')->orderBy('name')->get();

        return view('services.index', compact('services'));
    }

    public function create()
    {
        $managers = User::orderBy('name')->get();

        return view('services.create', compact('managers'));
    }

    public function store(Request $request)
    {
        $data = $this->validateService($request);
        $data['budget_restant'] = $data['budget_annuel_courant'];
        $service = Service::create($data);
        $this->promouvoirManager($service);

        return redirect()->route('services.index')->with('success', 'Service créé.');
    }

    public function edit(Service $service)
    {
        $managers = User::orderBy('name')->get();

        return view('services.edit', compact('service', 'managers'));
    }

    public function update(Request $request, Service $service)
    {
        $data = $this->validateService($request, $service);

        // Ajuster le budget restant proportionnellement au changement de budget annuel.
        $ancienBudget = (float) $service->budget_annuel_courant;
        $depense = $ancienBudget - (float) $service->budget_restant;
        $data['budget_restant'] = max(0, (float) $data['budget_annuel_courant'] - $depense);

        $service->update($data);
        $this->promouvoirManager($service);

        return redirect()->route('services.index')->with('success', 'Service mis à jour.');
    }

    /** Promeut l'utilisateur responsable au rôle manager (sauf s'il est admin). */
    private function promouvoirManager(Service $service): void
    {
        $manager = $service->manager;
        if ($manager && $manager->role === 'user') {
            $manager->update(['role' => 'manager']);
        }
    }

    public function destroy(Service $service)
    {
        $service->delete();

        return redirect()->route('services.index')->with('success', 'Service supprimé.');
    }

    private function validateService(Request $request, ?Service $service = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:20', 'unique:services,code'.($service ? ','.$service->id : '')],
            'manager_id' => ['nullable', 'exists:users,id'],
            'budget_annuel_courant' => ['required', 'numeric', 'min:0'],
        ]);
    }
}
