<?php

namespace App\Http\Controllers;

use App\Models\DocumentInterne;
use App\Models\Service;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            return $this->admin();
        }
        if ($user->isManager()) {
            return $this->manager();
        }

        return $this->user();
    }

    private function user()
    {
        $user = Auth::user();
        $derniers = DocumentInterne::where('demandeur_id', $user->id)
            ->with(['serviceDestinataire'])
            ->latest()->take(5)->get();
        $brouillons = DocumentInterne::where('demandeur_id', $user->id)
            ->where('statut', DocumentInterne::STATUT_BROUILLON)->get();
        $stats = [
            'total' => DocumentInterne::where('demandeur_id', $user->id)->count(),
            'en_attente' => DocumentInterne::where('demandeur_id', $user->id)->where('statut', DocumentInterne::STATUT_EN_ATTENTE)->count(),
            'valides' => DocumentInterne::where('demandeur_id', $user->id)->where('statut', DocumentInterne::STATUT_VALIDE)->count(),
        ];

        return view('dashboard.user', compact('derniers', 'brouillons', 'stats'));
    }

    private function manager()
    {
        $user = Auth::user();
        $service = $user->serviceGere;

        $enAttente = collect();
        if ($service) {
            $enAttente = DocumentInterne::where('service_destinataire_id', $service->id)
                ->where('statut', DocumentInterne::STATUT_EN_ATTENTE)
                ->with(['serviceEmetteur', 'demandeur'])
                ->latest()->get();
        }

        $mesDerniers = DocumentInterne::where('demandeur_id', $user->id)
            ->with('serviceDestinataire')->latest()->take(5)->get();

        return view('dashboard.manager', compact('service', 'enAttente', 'mesDerniers'));
    }

    private function admin()
    {
        $services = Service::with('manager')->orderBy('name')->get();
        $activites = DocumentInterne::with(['serviceEmetteur', 'serviceDestinataire', 'demandeur'])
            ->latest()->take(10)->get();

        $stats = [
            'services' => $services->count(),
            'documents' => DocumentInterne::count(),
            'en_attente' => DocumentInterne::where('statut', DocumentInterne::STATUT_EN_ATTENTE)->count(),
            'budget_total' => $services->sum('budget_annuel_courant'),
            'budget_restant' => $services->sum('budget_restant'),
        ];

        return view('dashboard.admin', compact('services', 'activites', 'stats'));
    }
}
