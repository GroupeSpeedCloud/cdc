<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'name', 'code', 'manager_id', 'budget_annuel_courant', 'budget_restant',
    ];

    protected $casts = [
        'budget_annuel_courant' => 'decimal:2',
        'budget_restant' => 'decimal:2',
    ];

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function personnes()
    {
        return $this->hasMany(Personne::class);
    }

    public function budgets()
    {
        return $this->hasMany(Budget::class);
    }

    public function budgetPour(int $annee): ?Budget
    {
        return $this->budgets()->where('annee', $annee)->first();
    }

    public function documentsEmis()
    {
        return $this->hasMany(DocumentInterne::class, 'service_emetteur_id');
    }

    public function documentsRecus()
    {
        return $this->hasMany(DocumentInterne::class, 'service_destinataire_id');
    }

    public function pourcentageConsomme(): float
    {
        if ((float) $this->budget_annuel_courant <= 0) {
            return 0;
        }
        $depense = (float) $this->budget_annuel_courant - (float) $this->budget_restant;

        return round($depense / (float) $this->budget_annuel_courant * 100, 1);
    }
}
