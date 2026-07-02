<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class RapportExport implements WithMultipleSheets
{
    public function __construct(
        private $coutsParService,
        private $tempsParPersonne
    ) {}

    public function sheets(): array
    {
        return [
            new CoutsParServiceSheet($this->coutsParService),
            new TempsParPersonneSheet($this->tempsParPersonne),
        ];
    }
}
