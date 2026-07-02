<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentInterne extends Model
{
    protected $table = 'documents_internes';

    protected $fillable = [
        'numero_document', 'service_emetteur_id', 'service_destinataire_id',
        'date_emission', 'description_globale', 'montant_total_ht', 'statut',
        'demandeur_id', 'validateur_id', 'date_validation', 'motif_refus',
    ];

    protected $casts = [
        'date_emission' => 'date',
        'date_validation' => 'datetime',
        'montant_total_ht' => 'decimal:2',
    ];

    public const STATUT_BROUILLON = 'Brouillon';

    public const STATUT_EN_ATTENTE = 'En attente de validation';

    public const STATUT_VALIDE = 'Validé';

    public const STATUT_REFUSE = 'Refusé';

    public const STATUT_ARCHIVE = 'Archivé';

    public function serviceEmetteur()
    {
        return $this->belongsTo(Service::class, 'service_emetteur_id');
    }

    public function serviceDestinataire()
    {
        return $this->belongsTo(Service::class, 'service_destinataire_id');
    }

    public function demandeur()
    {
        return $this->belongsTo(User::class, 'demandeur_id');
    }

    public function validateur()
    {
        return $this->belongsTo(User::class, 'validateur_id');
    }

    public function lignes()
    {
        return $this->hasMany(LigneDocument::class);
    }

    public function recalculerTotal(): void
    {
        $this->montant_total_ht = $this->lignes()->sum('montant_ligne');
        $this->save();
    }

    /** Génère un numéro unique du type INT-2026-001. */
    public static function genererNumero(): string
    {
        $annee = now()->year;
        $prefixe = 'INT-'.$annee.'-';
        $dernier = static::where('numero_document', 'like', $prefixe.'%')
            ->orderByDesc('numero_document')
            ->value('numero_document');
        $sequence = $dernier ? ((int) substr($dernier, strlen($prefixe))) + 1 : 1;

        return $prefixe.str_pad((string) $sequence, 3, '0', STR_PAD_LEFT);
    }

    public function statutBadgeClass(): string
    {
        return match ($this->statut) {
            self::STATUT_BROUILLON => 'bg-secondary',
            self::STATUT_EN_ATTENTE => 'bg-warning text-dark',
            self::STATUT_VALIDE => 'bg-success',
            self::STATUT_REFUSE => 'bg-danger',
            self::STATUT_ARCHIVE => 'bg-dark border',
            default => 'bg-secondary',
        };
    }
}
