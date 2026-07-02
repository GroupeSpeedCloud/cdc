<?php

namespace App\Http\Requests;

use App\Models\LigneDocument;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DocumentInterneRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'service_emetteur_id' => ['required', 'exists:services,id'],
            'service_destinataire_id' => ['required', 'different:service_emetteur_id', 'exists:services,id'],
            'date_emission' => ['required', 'date'],
            'date_echeance' => ['nullable', 'date', 'after_or_equal:date_emission'],
            'description_globale' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'taux_tva' => ['required', 'numeric', 'min:0', 'max:100'],
            'lignes' => ['required', 'array', 'min:1'],
            'lignes.*.description_ligne' => ['required', 'string', 'max:255'],
            'lignes.*.type_prestation' => ['required', Rule::in(LigneDocument::TYPES)],
            'lignes.*.personne_id' => ['nullable', 'exists:personnes,id'],
            'lignes.*.description_achat' => ['nullable', 'string', 'max:255'],
            'lignes.*.quantite' => ['required', 'numeric', 'min:0'],
            'lignes.*.tarif_unitaire' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function attributes(): array
    {
        return [
            'service_emetteur_id' => 'service émetteur',
            'service_destinataire_id' => 'service destinataire',
            'date_emission' => "date d'émission",
            'date_echeance' => "date d'échéance",
            'taux_tva' => 'taux de TVA',
            'lignes' => 'lignes',
        ];
    }

    public function messages(): array
    {
        return [
            'service_destinataire_id.different' => 'Le service destinataire doit être différent du service émetteur.',
            'lignes.required' => 'Ajoutez au moins une ligne à la facture.',
            'lignes.min' => 'Ajoutez au moins une ligne à la facture.',
        ];
    }
}
