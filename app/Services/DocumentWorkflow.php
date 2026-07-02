<?php

namespace App\Services;

use App\Models\AppNotification;
use App\Models\DocumentInterne;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Centralise le cycle de vie d'un document interne (facture interne) :
 * soumission, validation, refus et archivage — avec les notifications
 * et la mise à jour budgétaire associées.
 */
class DocumentWorkflow
{
    /** Soumet un brouillon/refusé pour validation et notifie le manager destinataire. */
    public function soumettre(DocumentInterne $document): void
    {
        $document->update([
            'statut' => DocumentInterne::STATUT_EN_ATTENTE,
            'motif_refus' => null,
        ]);

        $manager = $document->serviceDestinataire->manager;
        if ($manager) {
            AppNotification::notifier(
                $manager->id,
                "Facture {$document->numero_document} à valider ({$document->montantTtcFormate()}).",
                'demande',
                $document->id
            );
        }
    }

    /** Valide un document : passe en « Validé », déduit le budget et notifie le demandeur. */
    public function valider(DocumentInterne $document, User $validateur): void
    {
        DB::transaction(function () use ($document, $validateur) {
            $document->update([
                'statut' => DocumentInterne::STATUT_VALIDE,
                'validateur_id' => $validateur->id,
                'date_validation' => now(),
            ]);

            // La consommation budgétaire se fait sur le montant HT.
            $service = $document->serviceDestinataire()->lockForUpdate()->first();
            $service->budget_restant = (float) $service->budget_restant - (float) $document->montant_total_ht;
            $service->save();

            $budget = $service->budgetPour($document->date_emission->year);
            if ($budget) {
                $budget->montant_depense = (float) $budget->montant_depense + (float) $document->montant_total_ht;
                $budget->save();
            }
        });

        AppNotification::notifier(
            $document->demandeur_id,
            "Votre facture {$document->numero_document} a été validée.",
            'validation',
            $document->id
        );
    }

    /** Refuse un document avec un motif et notifie le demandeur. */
    public function refuser(DocumentInterne $document, User $validateur, string $motif): void
    {
        $document->update([
            'statut' => DocumentInterne::STATUT_REFUSE,
            'validateur_id' => $validateur->id,
            'date_validation' => now(),
            'motif_refus' => $motif,
        ]);

        AppNotification::notifier(
            $document->demandeur_id,
            "Votre facture {$document->numero_document} a été refusée : {$motif}",
            'refus',
            $document->id
        );
    }

    /** Archive un document validé. */
    public function archiver(DocumentInterne $document): void
    {
        $document->update(['statut' => DocumentInterne::STATUT_ARCHIVE]);
    }
}
