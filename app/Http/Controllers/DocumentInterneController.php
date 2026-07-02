<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use App\Models\DocumentInterne;
use App\Models\LigneDocument;
use App\Models\Personne;
use App\Models\Service;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DocumentInterneController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = DocumentInterne::with(['serviceEmetteur', 'serviceDestinataire', 'demandeur'])
            ->latest();

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        // Un user simple ne voit que ses propres documents + ceux de son service.
        if (! $user->isAdmin()) {
            $serviceGereId = $user->serviceGere?->id;
            $query->where(function ($q) use ($user, $serviceGereId) {
                $q->where('demandeur_id', $user->id);
                if ($serviceGereId) {
                    $q->orWhere('service_destinataire_id', $serviceGereId)
                        ->orWhere('service_emetteur_id', $serviceGereId);
                }
            });
        }

        $documents = $query->paginate(20)->withQueryString();
        $statuts = [
            DocumentInterne::STATUT_BROUILLON,
            DocumentInterne::STATUT_EN_ATTENTE,
            DocumentInterne::STATUT_VALIDE,
            DocumentInterne::STATUT_REFUSE,
            DocumentInterne::STATUT_ARCHIVE,
        ];

        return view('documents.index', compact('documents', 'statuts'));
    }

    public function create()
    {
        $services = Service::orderBy('name')->get();
        $personnes = Personne::with(['user', 'service'])->get();

        return view('documents.create', compact('services', 'personnes'));
    }

    public function store(Request $request)
    {
        $data = $this->validateDocument($request);
        $document = null;

        DB::transaction(function () use ($request, $data, &$document) {
            $document = DocumentInterne::create([
                'numero_document' => DocumentInterne::genererNumero(),
                'service_emetteur_id' => $data['service_emetteur_id'],
                'service_destinataire_id' => $data['service_destinataire_id'],
                'date_emission' => $data['date_emission'],
                'description_globale' => $data['description_globale'] ?? null,
                'statut' => DocumentInterne::STATUT_BROUILLON,
                'demandeur_id' => Auth::id(),
            ]);
            $this->syncLignes($document, $request->input('lignes', []));
            $document->recalculerTotal();
        });

        return redirect()->route('documents.show', $document)
            ->with('success', "Document {$document->numero_document} créé en brouillon.");
    }

    public function show(DocumentInterne $document)
    {
        $document->load(['lignes.personne.user', 'serviceEmetteur', 'serviceDestinataire', 'demandeur', 'validateur']);

        return view('documents.show', compact('document'));
    }

    public function edit(DocumentInterne $document)
    {
        abort_if(! in_array($document->statut, [DocumentInterne::STATUT_BROUILLON, DocumentInterne::STATUT_REFUSE], true), 403,
            'Seuls les brouillons ou documents refusés peuvent être modifiés.');
        $this->autoriserEdition($document);

        $document->load('lignes');
        $services = Service::orderBy('name')->get();
        $personnes = Personne::with(['user', 'service'])->get();

        return view('documents.edit', compact('document', 'services', 'personnes'));
    }

    public function update(Request $request, DocumentInterne $document)
    {
        abort_if(! in_array($document->statut, [DocumentInterne::STATUT_BROUILLON, DocumentInterne::STATUT_REFUSE], true), 403);
        $this->autoriserEdition($document);
        $data = $this->validateDocument($request);

        DB::transaction(function () use ($request, $data, $document) {
            $document->update([
                'service_emetteur_id' => $data['service_emetteur_id'],
                'service_destinataire_id' => $data['service_destinataire_id'],
                'date_emission' => $data['date_emission'],
                'description_globale' => $data['description_globale'] ?? null,
            ]);
            $document->lignes()->delete();
            $this->syncLignes($document, $request->input('lignes', []));
            $document->recalculerTotal();
        });

        return redirect()->route('documents.show', $document)
            ->with('success', 'Document mis à jour.');
    }

    public function destroy(DocumentInterne $document)
    {
        abort_if($document->statut !== DocumentInterne::STATUT_BROUILLON && ! Auth::user()->isAdmin(), 403);
        $this->autoriserEdition($document);
        $document->delete();

        return redirect()->route('documents.index')->with('success', 'Document supprimé.');
    }

    /** Soumettre un brouillon pour validation. */
    public function soumettre(DocumentInterne $document)
    {
        $this->autoriserEdition($document);
        abort_if(! in_array($document->statut, [DocumentInterne::STATUT_BROUILLON, DocumentInterne::STATUT_REFUSE], true), 403);

        if ($document->lignes()->count() === 0) {
            return back()->with('error', 'Impossible de soumettre un document sans lignes.');
        }

        $document->update([
            'statut' => DocumentInterne::STATUT_EN_ATTENTE,
            'motif_refus' => null,
        ]);

        // Notifier le manager du service destinataire.
        $manager = $document->serviceDestinataire->manager;
        if ($manager) {
            AppNotification::notifier(
                $manager->id,
                "Nouveau document {$document->numero_document} à valider (".number_format($document->montant_total_ht, 2, ',', ' ').' €).',
                'demande',
                $document->id
            );
        }

        return redirect()->route('documents.show', $document)
            ->with('success', 'Document soumis pour validation.');
    }

    /** Valider un document (manager du service destinataire ou admin). */
    public function valider(DocumentInterne $document)
    {
        $this->autoriserValidation($document);
        abort_if($document->statut !== DocumentInterne::STATUT_EN_ATTENTE, 403);

        DB::transaction(function () use ($document) {
            $document->update([
                'statut' => DocumentInterne::STATUT_VALIDE,
                'validateur_id' => Auth::id(),
                'date_validation' => now(),
            ]);

            // Déduire le montant du budget restant du service destinataire.
            $service = $document->serviceDestinataire()->lockForUpdate()->first();
            $service->budget_restant = (float) $service->budget_restant - (float) $document->montant_total_ht;
            $service->save();

            // Historiser sur le budget annuel de l'année d'émission.
            $annee = $document->date_emission->year;
            $budget = $service->budgetPour($annee);
            if ($budget) {
                $budget->montant_depense = (float) $budget->montant_depense + (float) $document->montant_total_ht;
                $budget->save();
            }
        });

        AppNotification::notifier(
            $document->demandeur_id,
            "Votre document {$document->numero_document} a été validé.",
            'validation',
            $document->id
        );

        return redirect()->route('documents.show', $document)
            ->with('success', 'Document validé, budget mis à jour.');
    }

    /** Refuser un document. */
    public function refuser(Request $request, DocumentInterne $document)
    {
        $this->autoriserValidation($document);
        abort_if($document->statut !== DocumentInterne::STATUT_EN_ATTENTE, 403);

        $request->validate([
            'motif_refus' => ['required', 'string', 'min:3'],
        ], [], ['motif_refus' => 'motif de refus']);

        $document->update([
            'statut' => DocumentInterne::STATUT_REFUSE,
            'validateur_id' => Auth::id(),
            'date_validation' => now(),
            'motif_refus' => $request->motif_refus,
        ]);

        AppNotification::notifier(
            $document->demandeur_id,
            "Votre document {$document->numero_document} a été refusé : {$request->motif_refus}",
            'refus',
            $document->id
        );

        return redirect()->route('documents.show', $document)
            ->with('success', 'Document refusé, le demandeur a été notifié.');
    }

    /** Archiver un document validé (admin uniquement). */
    public function archiver(DocumentInterne $document)
    {
        abort_unless(Auth::user()->isAdmin(), 403);
        abort_if($document->statut !== DocumentInterne::STATUT_VALIDE, 403);
        $document->update(['statut' => DocumentInterne::STATUT_ARCHIVE]);

        return back()->with('success', 'Document archivé.');
    }

    /** Export PDF d'un document individuel. */
    public function pdf(DocumentInterne $document)
    {
        $document->load(['lignes.personne.user', 'serviceEmetteur', 'serviceDestinataire', 'demandeur', 'validateur']);
        $pdf = Pdf::loadView('documents.pdf', compact('document'));

        return $pdf->download($document->numero_document.'.pdf');
    }

    // ------------------------------------------------------------------
    // Helpers
    // ------------------------------------------------------------------

    private function validateDocument(Request $request): array
    {
        return $request->validate([
            'service_emetteur_id' => ['required', 'exists:services,id'],
            'service_destinataire_id' => ['required', 'exists:services,id'],
            'date_emission' => ['required', 'date'],
            'description_globale' => ['nullable', 'string'],
            'lignes' => ['nullable', 'array'],
            'lignes.*.description_ligne' => ['required_with:lignes', 'string'],
            'lignes.*.type_prestation' => ['required_with:lignes', 'in:Temps Interne,Achat Externe'],
            'lignes.*.personne_id' => ['nullable', 'exists:personnes,id'],
            'lignes.*.description_achat' => ['nullable', 'string'],
            'lignes.*.quantite' => ['required_with:lignes', 'numeric', 'min:0'],
            'lignes.*.tarif_unitaire' => ['required_with:lignes', 'numeric', 'min:0'],
        ]);
    }

    private function syncLignes(DocumentInterne $document, array $lignes): void
    {
        foreach ($lignes as $ligne) {
            if (empty($ligne['description_ligne'])) {
                continue;
            }
            $isTemps = ($ligne['type_prestation'] ?? '') === LigneDocument::TYPE_TEMPS;
            $document->lignes()->create([
                'description_ligne' => $ligne['description_ligne'],
                'type_prestation' => $ligne['type_prestation'],
                'personne_id' => $isTemps ? ($ligne['personne_id'] ?? null) : null,
                'description_achat' => ! $isTemps ? ($ligne['description_achat'] ?? null) : null,
                'quantite' => $ligne['quantite'] ?? 0,
                'tarif_unitaire' => $ligne['tarif_unitaire'] ?? 0,
            ]);
        }
    }

    /** Le demandeur ou un admin peut éditer/soumettre. */
    private function autoriserEdition(DocumentInterne $document): void
    {
        abort_unless(Auth::id() === $document->demandeur_id || Auth::user()->isAdmin(), 403);
    }

    /** Le manager du service destinataire ou un admin peut valider/refuser. */
    private function autoriserValidation(DocumentInterne $document): void
    {
        $user = Auth::user();
        $estManagerDestinataire = $document->serviceDestinataire->manager_id === $user->id;
        abort_unless($user->isAdmin() || $estManagerDestinataire, 403,
            'Seul le manager du service destinataire ou un administrateur peut valider ce document.');
    }
}
