<?php

namespace App\Http\Controllers;

use App\Models\DocumentInterne;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::with('manager')->orderBy('name')->get();

        return view('services.index', compact('services'));
    }

    /**
     * Relevé de compte du service : solde actuel et historique des
     * mouvements (crédits/débits) issus des factures internes validées,
     * comme un vrai compte bancaire.
     */
    public function compte(Request $request, Service $service)
    {
        $user = Auth::user();
        abort_unless($user->isAdmin() || $service->manager_id === $user->id, 403,
            'Vous ne pouvez consulter que le compte de votre propre service.');

        $operations = DocumentInterne::whereIn('statut', [DocumentInterne::STATUT_VALIDE, DocumentInterne::STATUT_ARCHIVE])
            ->where(function ($q) use ($service) {
                $q->where('service_emetteur_id', $service->id)
                    ->orWhere('service_destinataire_id', $service->id);
            })
            ->with(['serviceEmetteur', 'serviceDestinataire'])
            ->orderByDesc('date_validation')
            ->orderByDesc('id')
            ->get();

        // Le solde actuel du service fait foi ; on reconstitue le solde après
        // chaque mouvement en remontant le temps depuis cette valeur, afin que
        // le relevé reste toujours cohérent avec le compte réel.
        $solde = (float) $service->budget_restant;
        $mouvements = $operations->map(function (DocumentInterne $doc) use ($service, &$solde) {
            $estCredit = $doc->service_emetteur_id === $service->id;
            $montant = (float) $doc->montant_total_ht;

            $ligne = [
                'document' => $doc,
                'date' => $doc->date_validation,
                'contrepartie' => $estCredit ? $doc->serviceDestinataire : $doc->serviceEmetteur,
                'libelle' => $estCredit ? 'Facturation émise' : 'Paiement de facture',
                'credit' => $estCredit ? $montant : null,
                'debit' => $estCredit ? null : $montant,
                'solde_apres' => $solde,
            ];

            $solde -= $estCredit ? $montant : -$montant;

            return $ligne;
        });

        $soldeOuverture = $solde;
        $totalCredits = $mouvements->sum('credit');
        $totalDebits = $mouvements->sum('debit');

        $annees = $mouvements->pluck('date')->filter()->map(fn ($d) => $d->year)->unique()->sortDesc()->values();
        $annee = (int) $request->input('annee', 0);
        if ($annee) {
            $mouvements = $mouvements->filter(fn ($m) => $m['date'] && $m['date']->year === $annee)->values();
        }

        return view('services.compte', compact(
            'service', 'mouvements', 'soldeOuverture', 'totalCredits', 'totalDebits', 'annees', 'annee'
        ));
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
