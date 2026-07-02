<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    protected $fillable = [
        'service_id', 'annee', 'montant_initial', 'montant_depense',
    ];

    protected $casts = [
        'annee' => 'integer',
        'montant_initial' => 'decimal:2',
        'montant_depense' => 'decimal:2',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function montantRestant(): float
    {
        return (float) $this->montant_initial - (float) $this->montant_depense;
    }
}
