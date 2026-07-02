<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Personne extends Model
{
    protected $fillable = [
        'user_id', 'service_id', 'nom', 'tarif_horaire_par_defaut',
    ];

    protected $casts = [
        'tarif_horaire_par_defaut' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function lignes()
    {
        return $this->hasMany(LigneDocument::class);
    }

    public function nomAffiche(): string
    {
        return $this->user?->name ?? $this->nom ?? 'Personne #'.$this->id;
    }
}
