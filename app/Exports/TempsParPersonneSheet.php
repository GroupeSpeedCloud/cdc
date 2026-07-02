<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class TempsParPersonneSheet implements FromArray, WithHeadings, WithTitle
{
    public function __construct(private $temps) {}

    public function title(): string
    {
        return 'Temps par personne';
    }

    public function headings(): array
    {
        return ['Personne', 'Service', 'Heures', 'Montant (€)'];
    }

    public function array(): array
    {
        return collect($this->temps)->map(fn ($t) => [
            $t['personne']?->nomAffiche() ?? '—',
            $t['personne']?->service?->name ?? '—',
            $t['heures'],
            $t['montant'],
        ])->toArray();
    }
}
