<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MonthlyRevenue extends Model
{
    protected $fillable = ['project_id', 'year', 'month', 'amount', 'notes'];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function scopeForMonth(Builder $query, int $year, int $month): Builder
    {
        return $query->where('year', $year)->where('month', $month);
    }
}
