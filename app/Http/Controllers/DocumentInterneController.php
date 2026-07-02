<?php

namespace App\Http\Controllers;

use App\Http\Requests\DocumentInterneRequest;
use App\Models\DocumentInterne;
use App\Models\LigneDocument;
use App\Models\Personne;
use App\Models\Service;
use App\Services\DocumentWorkflow;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DocumentInterneController extends Controller
{
    public function __construct(private DocumentWorkflow $workflow) {}

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

        return view('documents.index', [
            'documents' => $documents,
            'statuts' => $this->statuts(),
        ]);
    }

    public function create()
    {
        return view('documents.create', $this->formData());
    }

    public function store(DocumentInterneRequest $request)
    {
        $document = DB::transaction(function () use ($request) {
            $document = DocumentInterne::create([
                'numero_document' => DocumentInterne::genererNumero(),
                'service_emetteur_id' => $request->service_emetteur_id,
                'service_destinataire_id' => $request->service_destinataire_id,
                'date_emission' => $request->date_emission,
                'date_echeance' => $request->date_echeance,
                'description_globale' => $request->description_globale,
                'notes' => $request->notes,
                'taux_tva' => $request->taux_tva,
                'statut' => DocumentInterne::STATUT_BROUILLON,
                'demandeur_id' => Auth::id(),
            ]);
            $this->syncLignes($document, $request->input('lignes', []));
            $document->recalculerTotal();

            return $document;
        });

        return redirect()->route('documents.show', $document)
            ->with('success', "Facture {$document->numero_document} créée en brouillon.");
    }

    public function show(DocumentInterne $document)
    {
        $document->load(['lignes.personne.user', 'serviceEmetteur', 'serviceDestinataire', 'demandeur', 'validateur']);

        return view('documents.show', compact('document'));
    }

    public function edit(DocumentInterne $document)
    {
        $this->autoriserEdition($document);
        abort_unless($document->estModifiable(), 403, 'Seuls les brouillons ou factures refusées peuvent être modifiés.');

        $document->load('lignes');

        return view('documents.edit', array_merge(['document' => $document], $this->formData()));
    }

    public function update(DocumentInterneRequest $request, DocumentInterne $document)
    {
        $this->autoriserEdition($document);
        abort_unless($document->estModifiable(), 403);

        DB::transaction(function () use ($request, $document) {
            $document->update([
                'service_emetteur_id' => $request->service_emetteur_id,
                'service_destinataire_id' => $request->service_destinataire_id,
                'date_emission' => $request->date_emission,
                'date_echeance' => $request->date_echeance,
                'description_globale' => $request->description_globale,
                'notes' => $request->notes,
                'taux_tva' => $request->taux_tva,
            ]);
            $document->lignes()->delete();
            $this->syncLignes($document, $request->input('lignes', []));
            $document->recalculerTotal();
        });

        return redirect()->route('documents.show', $document)->with('success', 'Facture mise à jour.');
    }

    public function destroy(DocumentInterne $document)
    {
        $this->autoriserEdition($document);
        abort_if($document->statut !== DocumentInterne::STATUT_BROUILLON && ! Auth::user()->isAdmin(), 403);
        $document->delete();

        return redirect()->route('documents.index')->with('success', 'Facture supprimée.');
    }

    public function soumettre(DocumentInterne $document)
    {
        $this->autoriserEdition($document);
        abort_unless($document->estModifiable(), 403);

        if ($document->lignes()->count() === 0) {
            return back()->with('error', 'Impossible de soumettre une facture sans ligne.');
        }

        $this->workflow->soumettre($document);

        return redirect()->route('documents.show', $document)->with('success', 'Facture soumise pour validation.');
    }

    public function valider(DocumentInterne $document)
    {
        $this->autoriserValidation($document);
        abort_if($document->statut !== DocumentInterne::STATUT_EN_ATTENTE, 403);

        $this->workflow->valider($document, Auth::user());

        return redirect()->route('documents.show', $document)->with('success', 'Facture validée, budget mis à jour.');
    }

    public function refuser(Request $request, DocumentInterne $document)
    {
        $this->autoriserValidation($document);
        abort_if($document->statut !== DocumentInterne::STATUT_EN_ATTENTE, 403);

        $validated = $request->validate(
            ['motif_refus' => ['required', 'string', 'min:3']],
            [],
            ['motif_refus' => 'motif de refus']
        );

        $this->workflow->refuser($document, Auth::user(), $validated['motif_refus']);

        return redirect()->route('documents.show', $document)->with('success', 'Facture refusée, le demandeur a été notifié.');
    }

    public function archiver(DocumentInterne $document)
    {
        abort_unless(Auth::user()->isAdmin(), 403);
        abort_if($document->statut !== DocumentInterne::STATUT_VALIDE, 403);

        $this->workflow->archiver($document);

        return back()->with('success', 'Facture archivée.');
    }

    /** Affiche la facture PDF directement dans le navigateur (aperçu). */
    public function apercu(DocumentInterne $document)
    {
        return $this->genererPdf($document)->stream($document->numero_document.'.pdf');
    }

    /** Télécharge la facture au format PDF. */
    public function pdf(DocumentInterne $document)
    {
        return $this->genererPdf($document)->download($document->numero_document.'.pdf');
    }

    private function genererPdf(DocumentInterne $document)
    {
        $document->load(['lignes.personne.user', 'serviceEmetteur', 'serviceDestinataire', 'demandeur', 'validateur']);

        return Pdf::loadView('documents.pdf', compact('document'))->setPaper('a4');
    }

    // ------------------------------------------------------------------
    // Helpers
    // ------------------------------------------------------------------

    private function statuts(): array
    {
        return [
            DocumentInterne::STATUT_BROUILLON,
            DocumentInterne::STATUT_EN_ATTENTE,
            DocumentInterne::STATUT_VALIDE,
            DocumentInterne::STATUT_REFUSE,
            DocumentInterne::STATUT_ARCHIVE,
        ];
    }

    private function formData(): array
    {
        return [
            'services' => Service::orderBy('name')->get(),
            'personnes' => Personne::with(['user', 'service'])->orderBy('nom')->get(),
        ];
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
            'Seul le manager du service destinataire ou un administrateur peut valider cette facture.');
    }
}
