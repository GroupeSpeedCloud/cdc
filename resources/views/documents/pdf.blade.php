<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 110px 42px 90px; }
        * { box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #33384a; margin: 0; }

        /* ---- Header (répété via position fixed) ---- */
        .page-head { position: fixed; top: -78px; left: 0; right: 0; height: 70px; }
        .page-head td { vertical-align: top; }
        .brand { font-size: 24px; font-weight: bold; color: #4f46e5; letter-spacing: -0.5px; }
        .brand-sub { color: #9aa0b3; font-size: 9.5px; margin-top: 3px; }
        .doc-title { font-size: 19px; font-weight: bold; text-align: right; color: #1a1d29; letter-spacing: 0.5px; }
        .doc-num { text-align: right; color: #9aa0b3; font-size: 11px; margin-top: 3px; }
        .badge { display: inline-block; padding: 3px 11px; border-radius: 20px; font-size: 9px; font-weight: bold; letter-spacing: 0.4px; }
        .b-valide { background: #e7f7ef; color: #16794c; }
        .b-attente { background: #fdf4e3; color: #92600a; }
        .b-refuse { background: #fdeaea; color: #b42318; }
        .b-brouillon { background: #eef0f4; color: #667085; }
        .b-archive { background: #eaeaf0; color: #4a4a68; }

        /* ---- Footer (répété) ---- */
        .page-foot { position: fixed; bottom: -66px; left: 0; right: 0; text-align: center; color: #b0b4c0; font-size: 8.5px; border-top: 1px solid #eceef4; padding-top: 8px; }

        /* ---- Parties ---- */
        .parties { width: 100%; border-collapse: separate; border-spacing: 8px 0; margin-bottom: 18px; }
        .parties td { width: 50%; vertical-align: top; background: #f8f9fc; border: 1px solid #edeff5; border-radius: 8px; padding: 13px 15px; }
        .box-label { font-size: 8.5px; text-transform: uppercase; letter-spacing: 0.07em; color: #9aa0b3; margin-bottom: 5px; font-weight: bold; }
        .box-name { font-size: 13px; font-weight: bold; color: #1a1d29; margin-bottom: 3px; }
        .box-line { color: #7a8096; font-size: 10px; line-height: 1.5; }

        /* ---- Bandeau infos ---- */
        .infos { width: 100%; border-collapse: collapse; margin-bottom: 4px; }
        .infos td { padding: 9px 4px; border-bottom: 1px solid #edeff5; }
        .infos .k { font-size: 8.5px; text-transform: uppercase; letter-spacing: 0.05em; color: #9aa0b3; font-weight: bold; }
        .infos .v { font-size: 12px; font-weight: bold; color: #33384a; }

        .objet { margin: 16px 0 4px; font-size: 11px; }
        .objet-label { font-size: 8.5px; text-transform: uppercase; letter-spacing: 0.06em; color: #9aa0b3; font-weight: bold; margin-bottom: 2px; }

        /* ---- Lignes ---- */
        table.lines { width: 100%; border-collapse: collapse; margin-top: 14px; }
        table.lines th { background: #4f46e5; color: #fff; padding: 9px 11px; font-size: 9.5px; text-align: left; font-weight: bold; }
        table.lines th:first-child { border-radius: 6px 0 0 0; }
        table.lines th:last-child { border-radius: 0 6px 0 0; }
        table.lines th.r, table.lines td.r { text-align: right; }
        table.lines td { padding: 9px 11px; border-bottom: 1px solid #edeff5; }
        table.lines tr:nth-child(even) td { background: #fafbfd; }
        .l-title { font-weight: bold; color: #1a1d29; }
        .l-sub { color: #9aa0b3; font-size: 9px; margin-top: 1px; }
        .tag { display: inline-block; padding: 2px 8px; border-radius: 20px; font-size: 8.5px; background: #eef0f6; color: #667085; }

        /* ---- Bas : mentions + totaux (table, pas de float) ---- */
        .bottom { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .bottom > td { vertical-align: top; }
        .mentions { width: 52%; padding-right: 20px; vertical-align: top; }
        .m-label { font-size: 8.5px; text-transform: uppercase; letter-spacing: 0.06em; color: #9aa0b3; font-weight: bold; margin-bottom: 3px; }
        .m-block { margin-bottom: 12px; color: #7a8096; font-size: 10px; line-height: 1.5; }

        .totaux { width: 48%; vertical-align: top; }
        .totaux table { width: 100%; border-collapse: collapse; }
        .totaux td { padding: 7px 12px; font-size: 11px; }
        .totaux .k { color: #7a8096; }
        .totaux .v { text-align: right; font-weight: bold; color: #33384a; }
        .totaux .sep td { border-top: 1px solid #edeff5; }
        .totaux .grand td { background: #4f46e5; color: #fff; font-size: 14px; font-weight: bold; }
        .totaux .grand td:first-child { border-radius: 7px 0 0 7px; }
        .totaux .grand td:last-child { border-radius: 0 7px 7px 0; text-align: right; }
    </style>
</head>
<body>
    @php
        $badge = match($document->statut) {
            'Validé' => 'b-valide', 'En attente de validation' => 'b-attente',
            'Refusé' => 'b-refuse', 'Archivé' => 'b-archive', default => 'b-brouillon',
        };
        $tauxLabel = rtrim(rtrim(number_format($document->taux_tva, 2, ',', ' '), '0'), ',');
    @endphp

    <table class="page-head">
        <tr>
            <td>
                <div class="brand">CDC</div>
                <div class="brand-sub">Facturation interne inter-services</div>
            </td>
            <td>
                <div class="doc-title">FACTURE INTERNE</div>
                <div class="doc-num">{{ $document->numero_document }} &nbsp;·&nbsp; <span class="badge {{ $badge }}">{{ strtoupper($document->statut) }}</span></div>
            </td>
        </tr>
    </table>

    <div class="page-foot">
        CDC — Facturation interne · {{ $document->numero_document }} · Montants en euros · généré le {{ now()->format('d/m/Y à H:i') }}
    </div>

    <main>
        <table class="parties">
            <tr>
                <td>
                    <div class="box-label">Service émetteur</div>
                    <div class="box-name">{{ $document->serviceEmetteur->name }}</div>
                    <div class="box-line">Code {{ $document->serviceEmetteur->code }}</div>
                    <div class="box-line">Demandeur : {{ $document->demandeur->name }}</div>
                </td>
                <td>
                    <div class="box-label">Service destinataire</div>
                    <div class="box-name">{{ $document->serviceDestinataire->name }}</div>
                    <div class="box-line">Code {{ $document->serviceDestinataire->code }}</div>
                    <div class="box-line">Responsable : {{ $document->serviceDestinataire->manager->name ?? '—' }}</div>
                </td>
            </tr>
        </table>

        <table class="infos">
            <tr>
                <td><div class="k">Date d'émission</div></td>
                <td><div class="k">Échéance</div></td>
                <td><div class="k">Taux de TVA</div></td>
                <td style="text-align:right;"><div class="k">Total TTC</div></td>
            </tr>
            <tr>
                <td><div class="v">{{ $document->date_emission->format('d/m/Y') }}</div></td>
                <td><div class="v">{{ optional($document->date_echeance)->format('d/m/Y') ?? '—' }}</div></td>
                <td><div class="v">{{ $tauxLabel }} %</div></td>
                <td style="text-align:right;"><div class="v" style="color:#4f46e5;">{{ $document->montantTtcFormate() }}</div></td>
            </tr>
        </table>

        @if($document->description_globale)
            <div class="objet">
                <div class="objet-label">Objet</div>
                {{ $document->description_globale }}
            </div>
        @endif

        <table class="lines">
            <thead>
                <tr>
                    <th style="width:40%">Description</th>
                    <th style="width:15%">Type</th>
                    <th class="r" style="width:11%">Qté</th>
                    <th class="r" style="width:16%">P.U. HT</th>
                    <th class="r" style="width:18%">Montant HT</th>
                </tr>
            </thead>
            <tbody>
            @foreach($document->lignes as $l)
                @php $detail = $l->type_prestation === 'Temps Interne' ? ($l->personne?->nomAffiche()) : $l->description_achat; @endphp
                <tr>
                    <td>
                        <div class="l-title">{{ $l->description_ligne }}</div>
                        @if($detail)<div class="l-sub">{{ $detail }}</div>@endif
                    </td>
                    <td><span class="tag">{{ $l->type_prestation }}</span></td>
                    <td class="r">{{ rtrim(rtrim(number_format($l->quantite, 2, ',', ' '), '0'), ',') }}{{ $l->type_prestation === 'Temps Interne' ? ' h' : '' }}</td>
                    <td class="r">{{ number_format($l->tarif_unitaire, 2, ',', ' ') }} €</td>
                    <td class="r">{{ number_format($l->montant_ligne, 2, ',', ' ') }} €</td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <table class="bottom">
            <tr>
                <td class="mentions">
                    @if($document->notes)
                        <div class="m-block">
                            <div class="m-label">Notes</div>
                            {{ $document->notes }}
                        </div>
                    @endif
                    @if($document->validateur)
                        <div class="m-block">
                            <div class="m-label">Validation</div>
                            Traitée par {{ $document->validateur->name }} le {{ optional($document->date_validation)->format('d/m/Y à H:i') }}.
                            @if($document->statut === 'Refusé' && $document->motif_refus)
                                <br><strong style="color:#b42318;">Motif du refus :</strong> {{ $document->motif_refus }}
                            @endif
                        </div>
                    @endif
                </td>
                <td class="totaux">
                    <table>
                        <tr><td class="k">Total HT</td><td class="v">{{ $document->montantHtFormate() }}</td></tr>
                        <tr class="sep"><td class="k">TVA ({{ $tauxLabel }} %)</td><td class="v">{{ $document->montantTvaFormate() }}</td></tr>
                        <tr class="grand"><td>Total TTC</td><td>{{ $document->montantTtcFormate() }}</td></tr>
                    </table>
                </td>
            </tr>
        </table>
    </main>
</body>
</html>
