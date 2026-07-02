<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #222; }
        h1 { font-size: 20px; margin: 0; }
        .muted { color: #777; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #ccc; padding: 6px 8px; text-align: left; }
        th { background: #f2f2f2; }
        .text-right { text-align: right; }
        .header { border-bottom: 2px solid #6366f1; padding-bottom: 10px; margin-bottom: 16px; }
        .grid td { border: none; padding: 2px 4px; }
        .total-row td { font-weight: bold; background: #f8f8f8; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Document Interne</h1>
        <div class="muted">{{ $document->numero_document }} — {{ $document->statut }}</div>
    </div>

    <table class="grid">
        <tr>
            <td><strong>Émetteur :</strong> {{ $document->serviceEmetteur->name }} ({{ $document->serviceEmetteur->code }})</td>
            <td><strong>Destinataire :</strong> {{ $document->serviceDestinataire->name }} ({{ $document->serviceDestinataire->code }})</td>
        </tr>
        <tr>
            <td><strong>Date d'émission :</strong> {{ $document->date_emission->format('d/m/Y') }}</td>
            <td><strong>Demandeur :</strong> {{ $document->demandeur->name }}</td>
        </tr>
    </table>

    @if($document->description_globale)
        <p><strong>Description :</strong> {{ $document->description_globale }}</p>
    @endif

    <table>
        <thead><tr>
            <th>Description</th><th>Type</th><th>Détail</th>
            <th class="text-right">Qté</th><th class="text-right">Tarif</th><th class="text-right">Montant HT</th>
        </tr></thead>
        <tbody>
        @foreach($document->lignes as $l)
            <tr>
                <td>{{ $l->description_ligne }}</td>
                <td>{{ $l->type_prestation }}</td>
                <td>{{ $l->type_prestation === 'Temps Interne' ? ($l->personne?->nomAffiche() ?? '-') : $l->description_achat }}</td>
                <td class="text-right">{{ number_format($l->quantite, 2, ',', ' ') }}</td>
                <td class="text-right">{{ number_format($l->tarif_unitaire, 2, ',', ' ') }} €</td>
                <td class="text-right">{{ number_format($l->montant_ligne, 2, ',', ' ') }} €</td>
            </tr>
        @endforeach
            <tr class="total-row">
                <td colspan="5" class="text-right">Total HT</td>
                <td class="text-right">{{ number_format($document->montant_total_ht, 2, ',', ' ') }} €</td>
            </tr>
        </tbody>
    </table>

    @if($document->validateur)
        <p class="muted">Validé/traité par {{ $document->validateur->name }} le {{ optional($document->date_validation)->format('d/m/Y H:i') }}</p>
    @endif
</body>
</html>
