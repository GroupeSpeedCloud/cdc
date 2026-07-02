<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 32px 40px; }
        * { box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #2c3038; margin: 0; }
        .accent { color: #4f46e5; }

        .head { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
        .head td { vertical-align: top; }
        .brand { font-size: 22px; font-weight: bold; color: #4f46e5; letter-spacing: -0.5px; }
        .brand-sub { color: #8b90a0; font-size: 10px; margin-top: 2px; }
        .doc-title { font-size: 20px; font-weight: bold; text-align: right; }
        .doc-num { text-align: right; color: #8b90a0; font-size: 12px; margin-top: 2px; }
        .badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 10px; font-weight: bold; margin-top: 6px; }
        .b-valide { background: #e7f7ef; color: #16794c; }
        .b-attente { background: #fdf4e3; color: #92600a; }
        .b-refuse { background: #fdeaea; color: #b42318; }
        .b-brouillon { background: #eef0f4; color: #667085; }
        .b-archive { background: #eaeaf0; color: #4a4a68; }

        .parties { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .parties td { width: 50%; vertical-align: top; padding: 0; }
        .box { border: 1px solid #e5e7ef; border-radius: 8px; padding: 12px 14px; }
        .box + .box { margin-left: 8px; }
        .box-label { font-size: 9px; text-transform: uppercase; letter-spacing: 0.06em; color: #8b90a0; margin-bottom: 4px; font-weight: bold; }
        .box-name { font-size: 13px; font-weight: bold; color: #1a1d24; }
        .box-code { color: #8b90a0; font-size: 10px; }

        .meta { width: 100%; border-collapse: collapse; margin-bottom: 18px; }
        .meta td { border: 1px solid #e5e7ef; padding: 7px 10px; }
        .meta .k { background: #f7f8fb; color: #8b90a0; font-size: 9px; text-transform: uppercase; letter-spacing: 0.05em; width: 16.6%; }

        table.lines { width: 100%; border-collapse: collapse; margin-bottom: 4px; }
        table.lines th { background: #4f46e5; color: #fff; padding: 8px 10px; font-size: 10px; text-align: left; }
        table.lines th.r, table.lines td.r { text-align: right; }
        table.lines td { padding: 8px 10px; border-bottom: 1px solid #eceef3; }
        table.lines tr:nth-child(even) td { background: #fafbfd; }
        .tag { display: inline-block; padding: 1px 7px; border-radius: 4px; font-size: 9px; background: #eef0f4; color: #667085; }

        .totaux { width: 44%; border-collapse: collapse; float: right; margin-top: 10px; }
        .totaux td { padding: 6px 10px; }
        .totaux .k { color: #667085; }
        .totaux .v { text-align: right; font-weight: bold; }
        .totaux .grand { background: #4f46e5; color: #fff; font-size: 13px; border-radius: 6px; }

        .notes { clear: both; margin-top: 30px; border-top: 1px solid #e5e7ef; padding-top: 12px; color: #667085; }
        .notes-label { font-size: 9px; text-transform: uppercase; letter-spacing: 0.06em; color: #8b90a0; font-weight: bold; margin-bottom: 4px; }
        .foot { margin-top: 26px; text-align: center; color: #a4a8b5; font-size: 9px; border-top: 1px solid #eceef3; padding-top: 10px; }
    </style>
</head>
<body>
    @php
        $badge = match($document->statut) {
            'Validé' => 'b-valide', 'En attente de validation' => 'b-attente',
            'Refusé' => 'b-refuse', 'Archivé' => 'b-archive', default => 'b-brouillon',
        };
    @endphp

    <table class="head">
        <tr>
            <td>
                <div class="brand">CDC</div>
                <div class="brand-sub">Facturation interne inter-services</div>
            </td>
            <td>
                <div class="doc-title">FACTURE INTERNE</div>
                <div class="doc-num">{{ $document->numero_document }}</div>
                <div style="text-align:right;"><span class="badge {{ $badge }}">{{ strtoupper($document->statut) }}</span></div>
            </td>
        </tr>
    </table>

    <table class="parties">
        <tr>
            <td style="padding-right:4px;">
                <div class="box">
                    <div class="box-label">Service émetteur</div>
                    <div class="box-name">{{ $document->serviceEmetteur->name }}</div>
                    <div class="box-code">Code {{ $document->serviceEmetteur->code }}</div>
                    <div class="box-code">Demandeur : {{ $document->demandeur->name }}</div>
                </div>
            </td>
            <td style="padding-left:4px;">
                <div class="box">
                    <div class="box-label">Service destinataire</div>
                    <div class="box-name">{{ $document->serviceDestinataire->name }}</div>
                    <div class="box-code">Code {{ $document->serviceDestinataire->code }}</div>
                    <div class="box-code">Responsable : {{ $document->serviceDestinataire->manager->name ?? '—' }}</div>
                </div>
            </td>
        </tr>
    </table>

    <table class="meta">
        <tr>
            <td class="k">Date d'émission</td>
            <td>{{ $document->date_emission->format('d/m/Y') }}</td>
            <td class="k">Échéance</td>
            <td>{{ optional($document->date_echeance)->format('d/m/Y') ?? '—' }}</td>
            <td class="k">Taux TVA</td>
            <td>{{ rtrim(rtrim(number_format($document->taux_tva, 2, ',', ' '), '0'), ',') }} %</td>
        </tr>
    </table>

    @if($document->description_globale)
        <p style="margin:0 0 16px;"><strong>Objet :</strong> {{ $document->description_globale }}</p>
    @endif

    <table class="lines">
        <thead>
            <tr>
                <th style="width:38%">Description</th>
                <th style="width:16%">Type</th>
                <th class="r" style="width:12%">Qté</th>
                <th class="r" style="width:16%">P.U. HT</th>
                <th class="r" style="width:18%">Montant HT</th>
            </tr>
        </thead>
        <tbody>
        @foreach($document->lignes as $l)
            <tr>
                <td>
                    <strong>{{ $l->description_ligne }}</strong>
                    @php $detail = $l->type_prestation === 'Temps Interne' ? ($l->personne?->nomAffiche()) : $l->description_achat; @endphp
                    @if($detail)<br><span style="color:#8b90a0;font-size:9px;">{{ $detail }}</span>@endif
                </td>
                <td><span class="tag">{{ $l->type_prestation }}</span></td>
                <td class="r">{{ rtrim(rtrim(number_format($l->quantite, 2, ',', ' '), '0'), ',') }}{{ $l->type_prestation === 'Temps Interne' ? ' h' : '' }}</td>
                <td class="r">{{ number_format($l->tarif_unitaire, 2, ',', ' ') }} €</td>
                <td class="r">{{ number_format($l->montant_ligne, 2, ',', ' ') }} €</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <table class="totaux">
        <tr><td class="k">Total HT</td><td class="v">{{ $document->montantHtFormate() }}</td></tr>
        <tr><td class="k">TVA ({{ rtrim(rtrim(number_format($document->taux_tva, 2, ',', ' '), '0'), ',') }} %)</td><td class="v">{{ $document->montantTvaFormate() }}</td></tr>
        <tr><td class="k grand" style="padding:8px 10px;">Total TTC</td><td class="v grand" style="padding:8px 10px;">{{ $document->montantTtcFormate() }}</td></tr>
    </table>

    @if($document->notes)
        <div class="notes">
            <div class="notes-label">Notes</div>
            {{ $document->notes }}
        </div>
    @endif

    @if($document->validateur)
        <div class="notes">
            <div class="notes-label">Validation</div>
            Traitée par {{ $document->validateur->name }} le {{ optional($document->date_validation)->format('d/m/Y à H:i') }}.
            @if($document->statut === 'Refusé' && $document->motif_refus)
                <br><strong>Motif du refus :</strong> {{ $document->motif_refus }}
            @endif
        </div>
    @endif

    <div class="foot">
        Document généré le {{ now()->format('d/m/Y à H:i') }} — Facturation interne CDC. Montants exprimés en euros.
    </div>
</body>
</html>
