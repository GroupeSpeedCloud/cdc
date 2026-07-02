<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 128px 46px 84px; }
        * { box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10.5px; color: #2b3040; margin: 0; line-height: 1.45; }

        /* ============ En-tête répété ============ */
        .page-head { position: fixed; top: -96px; left: 0; right: 0; height: 92px; }
        .page-head td { vertical-align: middle; }
        .logo { width: 42px; height: 42px; background: #4f46e5; border-radius: 10px; color: #fff; font-size: 16px; font-weight: bold; text-align: center; line-height: 42px; letter-spacing: 0.5px; }
        .brand-name { font-size: 15px; font-weight: bold; color: #1a1d29; letter-spacing: 0.3px; }
        .brand-sub { color: #9aa0b3; font-size: 9px; }
        .doc-title { font-size: 22px; font-weight: bold; text-align: right; color: #4f46e5; letter-spacing: 1px; }
        .doc-num { text-align: right; color: #7a8096; font-size: 10.5px; margin-top: 4px; }
        .badge { display: inline-block; padding: 3px 12px; border-radius: 20px; font-size: 8.5px; font-weight: bold; letter-spacing: 0.5px; }
        .b-valide { background: #e7f7ef; color: #16794c; }
        .b-attente { background: #fdf4e3; color: #92600a; }
        .b-refuse { background: #fdeaea; color: #b42318; }
        .b-brouillon { background: #eef0f4; color: #667085; }
        .b-archive { background: #eaeaf0; color: #4a4a68; }
        .head-rule { height: 3px; background: #4f46e5; border-radius: 3px; margin-top: 12px; }

        /* ============ Pied de page répété ============ */
        .page-foot { position: fixed; bottom: -58px; left: 0; right: 0; text-align: center; color: #aeb3c1; font-size: 8px; border-top: 1px solid #edeff4; padding-top: 9px; letter-spacing: 0.2px; }

        /* ============ Parties ============ */
        .parties { width: 100%; border-collapse: collapse; margin: 4px 0 20px; }
        .parties .card { vertical-align: top; background: #f8f9fc; border: 1px solid #ebeef5; border-radius: 9px; padding: 14px 16px; }
        .parties .gap { width: 20px; }
        .box-label { font-size: 8px; text-transform: uppercase; letter-spacing: 0.09em; color: #9aa0b3; margin-bottom: 6px; font-weight: bold; }
        .box-name { font-size: 13.5px; font-weight: bold; color: #1a1d29; margin-bottom: 4px; }
        .box-line { color: #7a8096; font-size: 9.5px; line-height: 1.6; }

        /* ============ Bandeau infos ============ */
        .infos { width: 100%; border-collapse: collapse; margin: 0 0 6px; }
        .infos .cell { background: #fbfbfe; border: 1px solid #eef0f6; border-radius: 8px; padding: 10px 13px; vertical-align: top; }
        .infos .gap { width: 12px; }
        .infos .cell.hl { background: #4f46e5; border-color: #4f46e5; }
        .info-k { font-size: 8px; text-transform: uppercase; letter-spacing: 0.06em; color: #9aa0b3; font-weight: bold; margin-bottom: 3px; }
        .info-v { font-size: 12.5px; font-weight: bold; color: #2b3040; }
        .infos .cell.hl .info-k { color: #cdccff; }
        .infos .cell.hl .info-v { color: #fff; }

        .objet { margin: 18px 2px 2px; }
        .objet-label { font-size: 8px; text-transform: uppercase; letter-spacing: 0.07em; color: #9aa0b3; font-weight: bold; margin-bottom: 3px; }
        .objet-text { font-size: 10.5px; color: #4a5062; }

        /* ============ Lignes ============ */
        table.lines { width: 100%; border-collapse: collapse; margin-top: 16px; }
        table.lines th { background: #2b3040; color: #fff; padding: 10px 12px; font-size: 8.5px; text-align: left; font-weight: bold; text-transform: uppercase; letter-spacing: 0.05em; }
        table.lines th:first-child { border-radius: 7px 0 0 0; }
        table.lines th:last-child { border-radius: 0 7px 0 0; }
        table.lines th.r, table.lines td.r { text-align: right; }
        table.lines td { padding: 11px 12px; border-bottom: 1px solid #edeff4; }
        table.lines tbody tr:last-child td { border-bottom: 2px solid #dfe2ec; }
        .l-title { font-weight: bold; color: #1a1d29; font-size: 10.5px; }
        .l-sub { color: #9aa0b3; font-size: 8.5px; margin-top: 2px; }
        .tag { display: inline-block; padding: 3px 9px; border-radius: 20px; font-size: 8px; font-weight: bold; white-space: nowrap; }
        .tag-t { background: #eaf1fe; color: #2563eb; }
        .tag-a { background: #fdf1e7; color: #c2650f; }
        .num { color: #4a5062; }

        /* ============ Bas : mentions + totaux ============ */
        .bottom { width: 100%; border-collapse: collapse; margin-top: 18px; }
        .bottom > td { vertical-align: top; }
        .mentions { width: 50%; padding-right: 24px; }
        .m-label { font-size: 8px; text-transform: uppercase; letter-spacing: 0.07em; color: #9aa0b3; font-weight: bold; margin-bottom: 4px; }
        .m-block { margin-bottom: 13px; color: #7a8096; font-size: 9.5px; line-height: 1.55; }

        .tbox { width: 50%; }
        .tbox table { width: 100%; border-collapse: collapse; }
        .tbox .row td { padding: 8px 14px; font-size: 11px; }
        .tbox .row td.k { color: #7a8096; }
        .tbox .row td.v { text-align: right; font-weight: bold; color: #2b3040; }
        .tbox .subtotal td { border-bottom: 1px solid #edeff4; }
        .tbox .grand td { background: #4f46e5; color: #fff; padding: 12px 14px; }
        .tbox .grand td.k { font-size: 11px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.05em; border-radius: 8px 0 0 8px; }
        .tbox .grand td.v { text-align: right; font-size: 16px; font-weight: bold; border-radius: 0 8px 8px 0; }
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
            <td style="width:55%;">
                <table><tr>
                    <td style="width:42px;"><div class="logo">CDC</div></td>
                    <td style="padding-left:11px;">
                        <div class="brand-name">Groupe Speed Cloud</div>
                        <div class="brand-sub">Facturation interne inter-services</div>
                    </td>
                </tr></table>
            </td>
            <td style="width:45%;">
                <div class="doc-title">FACTURE INTERNE</div>
                <div class="doc-num">{{ $document->numero_document }} &nbsp;·&nbsp; <span class="badge {{ $badge }}">{{ strtoupper($document->statut) }}</span></div>
            </td>
        </tr>
        <tr><td colspan="2"><div class="head-rule"></div></td></tr>
    </table>

    <div class="page-foot">
        Groupe Speed Cloud · Facturation interne · {{ $document->numero_document }} · Montants exprimés en euros · document généré le {{ now()->format('d/m/Y à H:i') }}
    </div>

    <main>
        <table class="parties">
            <tr>
                <td class="card" style="width:50%;">
                    <div class="box-label">Émetteur</div>
                    <div class="box-name">{{ $document->serviceEmetteur->name }}</div>
                    <div class="box-line">Code service : {{ $document->serviceEmetteur->code }}</div>
                    <div class="box-line">Demandeur : {{ $document->demandeur->name }}</div>
                </td>
                <td class="gap"></td>
                <td class="card" style="width:50%;">
                    <div class="box-label">Destinataire</div>
                    <div class="box-name">{{ $document->serviceDestinataire->name }}</div>
                    <div class="box-line">Code service : {{ $document->serviceDestinataire->code }}</div>
                    <div class="box-line">Responsable : {{ $document->serviceDestinataire->manager->name ?? '—' }}</div>
                </td>
            </tr>
        </table>

        <table class="infos">
            <tr>
                <td class="cell" style="width:24%;">
                    <div class="info-k">Date d'émission</div>
                    <div class="info-v">{{ $document->date_emission->format('d/m/Y') }}</div>
                </td>
                <td class="gap"></td>
                <td class="cell" style="width:24%;">
                    <div class="info-k">Échéance</div>
                    <div class="info-v">{{ optional($document->date_echeance)->format('d/m/Y') ?? '—' }}</div>
                </td>
                <td class="gap"></td>
                <td class="cell" style="width:24%;">
                    <div class="info-k">Taux de TVA</div>
                    <div class="info-v">{{ $tauxLabel }} %</div>
                </td>
                <td class="gap"></td>
                <td class="cell hl" style="width:24%;">
                    <div class="info-k">Total TTC</div>
                    <div class="info-v">{{ $document->montantTtcFormate() }}</div>
                </td>
            </tr>
        </table>

        @if($document->description_globale)
            <div class="objet">
                <div class="objet-label">Objet de la facture</div>
                <div class="objet-text">{{ $document->description_globale }}</div>
            </div>
        @endif

        <table class="lines">
            <thead>
                <tr>
                    <th style="width:42%">Description</th>
                    <th style="width:14%">Type</th>
                    <th class="r" style="width:11%">Qté</th>
                    <th class="r" style="width:16%">P.U. HT</th>
                    <th class="r" style="width:17%">Montant HT</th>
                </tr>
            </thead>
            <tbody>
            @foreach($document->lignes as $l)
                @php $detail = $l->detail(); @endphp
                <tr>
                    <td>
                        <div class="l-title">{{ $l->description_ligne }}</div>
                        @if($detail)<div class="l-sub">{{ $detail }}</div>@endif
                    </td>
                    <td><span class="tag" style="{{ $l->typeStyle() }}">{{ $l->type_prestation }}</span></td>
                    <td class="r num">{{ rtrim(rtrim(number_format($l->quantite, 2, ',', ' '), '0'), ',') }}{{ $l->estTemps() ? ' h' : '' }}</td>
                    <td class="r num">{{ number_format($l->tarif_unitaire, 2, ',', ' ') }} €</td>
                    <td class="r" style="font-weight:bold;color:#1a1d29;">{{ number_format($l->montant_ligne, 2, ',', ' ') }} €</td>
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
                    @if(! $document->notes && ! $document->validateur)
                        <div class="m-block">Facture interne à imputer sur le budget du service destinataire.</div>
                    @endif
                </td>
                <td class="tbox">
                    <table>
                        <tr class="row"><td class="k">Sous-total HT</td><td class="v">{{ $document->montantHtFormate() }}</td></tr>
                        <tr class="row subtotal"><td class="k">TVA ({{ $tauxLabel }} %)</td><td class="v">{{ $document->montantTvaFormate() }}</td></tr>
                        <tr class="grand"><td class="k">Total TTC</td><td class="v">{{ $document->montantTtcFormate() }}</td></tr>
                    </table>
                </td>
            </tr>
        </table>
    </main>
</body>
</html>
