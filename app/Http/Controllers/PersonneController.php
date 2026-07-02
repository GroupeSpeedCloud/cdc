<?php

namespace App\Http\Controllers;

use App\Models\Personne;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PersonneController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $query = Personne::with(['user', 'service']);

        // Un manager ne voit que les membres de son service.
        if ($user->isManager() && ! $user->isAdmin()) {
            $serviceId = $user->serviceGere?->id;
            $query->where('service_id', $serviceId);
        }

        $personnes = $query->orderBy('nom')->get();

        return view('personnes.index', compact('personnes'));
    }

    public function create()
    {
        return view('personnes.create', $this->formData());
    }

    public function store(Request $request)
    {
        $data = $this->validatePersonne($request);
        $this->autoriserService($data['service_id'] ?? null);
        Personne::create($data);

        return redirect()->route('personnes.index')->with('success', 'Personne ajoutée.');
    }

    public function edit(Personne $personne)
    {
        $this->autoriserService($personne->service_id);

        return view('personnes.edit', array_merge(['personne' => $personne], $this->formData()));
    }

    public function update(Request $request, Personne $personne)
    {
        $this->autoriserService($personne->service_id);
        $data = $this->validatePersonne($request);
        $this->autoriserService($data['service_id'] ?? null);
        $personne->update($data);

        return redirect()->route('personnes.index')->with('success', 'Personne mise à jour.');
    }

    public function destroy(Personne $personne)
    {
        $this->autoriserService($personne->service_id);
        $personne->delete();

        return redirect()->route('personnes.index')->with('success', 'Personne supprimée.');
    }

    private function formData(): array
    {
        return [
            'services' => Service::orderBy('name')->get(),
            'users' => User::orderBy('name')->get(),
        ];
    }

    private function validatePersonne(Request $request): array
    {
        return $request->validate([
            'user_id' => ['nullable', 'exists:users,id'],
            'service_id' => ['nullable', 'exists:services,id'],
            'nom' => ['nullable', 'string', 'max:255'],
            'tarif_horaire_par_defaut' => ['required', 'numeric', 'min:0'],
        ]);
    }

    /** Un manager ne peut gérer que les personnes de son propre service. */
    private function autoriserService(?int $serviceId): void
    {
        $user = Auth::user();
        if ($user->isAdmin()) {
            return;
        }
        $serviceGereId = $user->serviceGere?->id;
        abort_unless($serviceGereId && $serviceId === $serviceGereId, 403,
            'Vous ne pouvez gérer que les membres de votre service.');
    }
}
