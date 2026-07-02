<?php

namespace App\Http\Controllers;

use App\Exports\RapportExport;
use App\Models\DocumentInterne;
use App\Models\LigneDocument;
use App\Models\Personne;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $filtres = $this->filtres($request);
        $data = $this->calculer($filtres);

        $services = Service::orderBy('name')->get();
        $personnes = Personne::with('user')->get();
        $statuts = [
            DocumentInterne::STATUT_BROUILLON,
            DocumentInterne::STATUT_EN_ATTENTE,
            DocumentInterne::STATUT_VALIDE,
            DocumentInterne::STATUT_REFUSE,
            DocumentInterne::STATUT_ARCHIVE,
        ];

        return view('reports.index', array_merge($data, [
            'services' => $services,
            'personnes' => $personnes,
            'statuts' => $statuts,
            'filtres' => $filtres,
        ]));
    }

    public function export(Request $request)
    {
        $filtres = $this->filtres($request);
        $data = $this->calculer($filtres);

        return Excel::download(new RapportExport($data['coutsParService'], $data['tempsParPersonne']), 'rapport-cdc.xlsx');
    }

    private function filtres(Request $request): array
    {
        $user = Auth::user();
        $filtres = [
            'service_id' => $request->input('service_id'),
            'date_debut' => $request->input('date_debut'),
            'date_fin' => $request->input('date_fin'),
            'statut' => $request->input('statut'),
            'personne_id' => $request->input('personne_id'),
        ];

        // Un manager est restreint à son propre service.
        if ($user->isManager() && ! $user->isAdmin()) {
            $filtres['service_id'] = $user->serviceGere?->id;
            $filtres['restreint'] = true;
        }

        return $filtres;
    }

    private function baseQuery(array $filtres)
    {
        $query = DocumentInterne::query()->with(['serviceEmetteur', 'serviceDestinataire']);

        if (! empty($filtres['statut'])) {
            $query->where('statut', $filtres['statut']);
        }
        if (! empty($filtres['date_debut'])) {
            $query->whereDate('date_emission', '>=', $filtres['date_debut']);
        }
        if (! empty($filtres['date_fin'])) {
            $query->whereDate('date_emission', '<=', $filtres['date_fin']);
        }
        if (! empty($filtres['service_id'])) {
            $query->where(function ($q) use ($filtres) {
                $q->where('service_emetteur_id', $filtres['service_id'])
                    ->orWhere('service_destinataire_id', $filtres['service_id']);
            });
        }

        return $query;
    }

    private function calculer(array $filtres): array
    {
        $documents = $this->baseQuery($filtres)->get();

        // Coûts par service : émis (en tant qu'émetteur) vs reçus (destinataire).
        $coutsParService = Service::orderBy('name')->get()->map(function ($service) use ($documents) {
            $emis = $documents->where('service_emetteur_id', $service->id)->sum('montant_total_ht');
            $recus = $documents->where('service_destinataire_id', $service->id)->sum('montant_total_ht');

            return [
                'service' => $service,
                'emis' => (float) $emis,
                'recus' => (float) $recus,
                'budget_initial' => (float) $service->budget_annuel_courant,
                'budget_restant' => (float) $service->budget_restant,
                'depense' => (float) $service->budget_annuel_courant - (float) $service->budget_restant,
            ];
        });

        if (! empty($filtres['service_id'])) {
            $coutsParService = $coutsParService->where('service.id', (int) $filtres['service_id'])->values();
        }

        // Analyse du temps passé par personne.
        $ligneQuery = LigneDocument::query()
            ->where('type_prestation', LigneDocument::TYPE_TEMPS)
            ->whereIn('document_interne_id', $documents->pluck('id'));

        if (! empty($filtres['personne_id'])) {
            $ligneQuery->where('personne_id', $filtres['personne_id']);
        }

        $lignes = $ligneQuery->with('personne.user')->get();
        $tempsParPersonne = $lignes->groupBy('personne_id')->map(function ($group) {
            $personne = $group->first()->personne;

            return [
                'personne' => $personne,
                'heures' => (float) $group->sum('quantite'),
                'montant' => (float) $group->sum('montant_ligne'),
            ];
        })->values();

        return compact('coutsParService', 'tempsParPersonne', 'documents');
    }
}
