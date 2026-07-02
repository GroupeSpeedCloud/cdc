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
}
