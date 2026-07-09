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

    /**
     * Valide un document : passe en « Validé » et transfère le montant HT
     * entre les deux services comme un virement bancaire — le destinataire
     * paie (débit), l'émetteur est crédité du même montant (crédit).
     */
    public function valider(DocumentInterne $document, User $validateur): void
    {
        DB::transaction(function () use ($document, $validateur) {
            $document->update([
                'statut' => DocumentInterne::STATUT_VALIDE,
                'validateur_id' => $validateur->id,
                'date_validation' => now(),
            ]);

            // Le transfert se fait sur le montant HT.
            $montant = (float) $document->montant_total_ht;
            $annee = $document->date_emission->year;

            // Débit : le service destinataire paie la facture.
            $destinataire = $document->serviceDestinataire()->lockForUpdate()->first();
            $destinataire->budget_restant = (float) $destinataire->budget_restant - $montant;
            $destinataire->save();

            $budgetDestinataire = $destinataire->budgetPour($annee);
            if ($budgetDestinataire) {
                $budgetDestinataire->montant_depense = (float) $budgetDestinataire->montant_depense + $montant;
                $budgetDestinataire->save();
            }

            // Crédit : le service émetteur est payé pour la prestation facturée.
            $emetteur = $document->serviceEmetteur()->lockForUpdate()->first();
            $emetteur->budget_restant = (float) $emetteur->budget_restant + $montant;
            $emetteur->save();

            $budgetEmetteur = $emetteur->budgetPour($annee);
            if ($budgetEmetteur) {
                $budgetEmetteur->montant_credite = (float) $budgetEmetteur->montant_credite + $montant;
                $budgetEmetteur->save();
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
