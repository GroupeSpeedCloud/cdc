<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LigneDocument extends Model
{
    protected $table = 'lignes_documents';

    protected $fillable = [
        'document_interne_id', 'description_ligne', 'type_prestation',
        'personne_id', 'description_achat', 'quantite', 'tarif_unitaire', 'montant_ligne',
    ];

    protected $casts = [
        'quantite' => 'decimal:2',
        'tarif_unitaire' => 'decimal:2',
        'montant_ligne' => 'decimal:2',
    ];

    public const TYPE_TEMPS = 'Temps Interne';

    public const TYPE_ACHAT = 'Achat Externe';

    /** Tous les types de prestation / produit disponibles pour une ligne. */
    public const TYPES = [
        'Temps Interne',
        'Prestation externe',
        'Achat Externe',
        'Matériel',
        'Licence / Abonnement',
        'Frais',
    ];

    protected static function booted(): void
    {
        static::saving(function (LigneDocument $ligne) {
            $ligne->montant_ligne = round((float) $ligne->quantite * (float) $ligne->tarif_unitaire, 2);
        });
    }

    public function document()
    {
        return $this->belongsTo(DocumentInterne::class, 'document_interne_id');
    }

    public function personne()
    {
        return $this->belongsTo(Personne::class);
    }

    /** Seul le « Temps Interne » est rattaché à une personne ; les autres à un descriptif. */
    public function estTemps(): bool
    {
        return $this->type_prestation === self::TYPE_TEMPS;
    }

    /** Libellé du détail : personne (temps) ou description d'achat (produit). */
    public function detail(): ?string
    {
        return $this->estTemps() ? $this->personne?->nomAffiche() : $this->description_achat;
    }

    /** Couleurs [fond, texte] associées à un type — cohérentes entre l'écran et le PDF. */
    public static function typeCouleurs(string $type): array
    {
        return match ($type) {
            'Temps Interne' => ['#eaf1fe', '#2563eb'],
            'Prestation externe' => ['#f1ecfe', '#6d28d9'],
            'Achat Externe' => ['#fdf1e7', '#c2650f'],
            'Matériel' => ['#e7f7ef', '#16794c'],
            'Licence / Abonnement' => ['#e6f6fb', '#0e7490'],
            'Frais' => ['#fdeef3', '#be185d'],
            default => ['#eef0f4', '#667085'],
        };
    }

    public function typeStyle(): string
    {
        [$bg, $fg] = self::typeCouleurs($this->type_prestation);

        return "background:{$bg};color:{$fg};";
    }
}
