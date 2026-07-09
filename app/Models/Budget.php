<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    protected $fillable = [
        'service_id', 'annee', 'montant_initial', 'montant_depense', 'montant_credite',
    ];

    protected $casts = [
        'annee' => 'integer',
        'montant_initial' => 'decimal:2',
        'montant_depense' => 'decimal:2',
        'montant_credite' => 'decimal:2',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    /** Solde de l'année : budget initial + crédits reçus (facturation émise) - dépenses (facturation reçue). */
    public function montantRestant(): float
    {
        return (float) $this->montant_initial + (float) $this->montant_credite - (float) $this->montant_depense;
    }
}
