<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RecurringExpense extends Model
{
    protected $fillable = ['name', 'category', 'amount', 'start_month', 'end_month', 'notes'];

    protected $casts = [
        'start_month' => 'date',
        'end_month' => 'date',
    ];

    public function overrides(): HasMany
    {
        return $this->hasMany(MonthlyExpenseOverride::class);
    }

    public function getAmountForMonth(int $year, int $month): ?float
    {
        $override = $this->overrides()
            ->where('year', $year)
            ->where('month', $month)
            ->first();

        if ($override) {
            // null amount = disabled for this month
            return $override->amount !== null ? (float) $override->amount : null;
        }

        if (!$this->isActiveForMonth($year, $month)) {
            return null;
        }

        return (float) $this->amount;
    }

    public function isActiveForMonth(int $year, int $month): bool
    {
        $override = $this->overrides()
            ->where('year', $year)
            ->where('month', $month)
            ->first();

        if ($override) {
            return $override->amount !== null;
        }

        $date = Carbon::createFromDate($year, $month, 1);
        $start = Carbon::parse($this->start_month)->startOfMonth();

        if ($date->lt($start)) {
            return false;
        }

        if ($this->end_month !== null) {
            $end = Carbon::parse($this->end_month)->endOfMonth();
            if ($date->gt($end)) {
                return false;
            }
        }

        return true;
    }
}
