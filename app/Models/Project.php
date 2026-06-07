<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    protected $fillable = ['name', 'description', 'color', 'status'];

    public function monthlyRevenues(): HasMany
    {
        return $this->hasMany(MonthlyRevenue::class);
    }

    public function getRevenueForMonth(int $year, int $month): float
    {
        return (float) $this->monthlyRevenues()
            ->where('year', $year)
            ->where('month', $month)
            ->value('amount') ?? 0.0;
    }

    public function getTotalRevenue(): float
    {
        return (float) $this->monthlyRevenues()->sum('amount');
    }

    public function getAverageMonthlyRevenue(): float
    {
        $count = $this->monthlyRevenues()->count();
        if ($count === 0) {
            return 0.0;
        }
        return $this->getTotalRevenue() / $count;
    }
}
