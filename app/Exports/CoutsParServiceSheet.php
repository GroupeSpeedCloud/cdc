<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class CoutsParServiceSheet implements FromArray, WithHeadings, WithTitle
{
    public function __construct(private $couts) {}

    public function title(): string
    {
        return 'Coûts par service';
    }

    public function headings(): array
    {
        return ['Service', 'Code', 'Émis (€)', 'Reçus (€)', 'Budget initial (€)', 'Dépensé (€)', 'Restant (€)'];
    }

    public function array(): array
    {
        return collect($this->couts)->map(fn ($c) => [
            $c['service']->name,
            $c['service']->code,
            $c['emis'],
            $c['recus'],
            $c['budget_initial'],
            $c['depense'],
            $c['budget_restant'],
        ])->toArray();
    }
}
