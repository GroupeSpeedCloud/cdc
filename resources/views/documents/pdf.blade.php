<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 150px 46px 92px; }
        * { box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10.5px; color: #2b3040; margin: 0; line-height: 1.45; }

        /* ============ En-tête répété ============ */
        .page-head { position: fixed; top: -118px; left: 0; right: 0; height: 114px; }
        .page-head td { vertical-align: middle; }
        .brand-name { font-size: 16px; font-weight: bold; color: #1a1d29; letter-spacing: 0.3px; }
        .brand-legal { color: #9aa0b3; font-size: 8.5px; margin-top: 1px; }
        .brand-sub { color: #9aa0b3; font-size: 9px; }
        .doc-title { font-size: 21px; font-weight: bold; text-align: right; color: #8a4dfd; letter-spacing: 1px; }
        .doc-num { text-align: right; color: #7a8096; font-size: 10.5px; margin-top: 4px; }
        .badge { display: inline-block; padding: 3px 12px; border-radius: 20px; font-size: 8.5px; font-weight: bold; letter-spacing: 0.5px; }
        .b-valide { background: #e7f7ef; color: #16794c; }
        .b-attente { background: #fdf4e3; color: #92600a; }
        .b-refuse { background: #fdeaea; color: #b42318; }
        .b-brouillon { background: #eef0f4; color: #667085; }
        .b-archive { background: #eaeaf0; color: #4a4a68; }
        .b-nonpayable { background: #eef0f4; color: #4a5062; }
        .head-rule { height: 3px; background: #8a4dfd; border-radius: 3px; margin-top: 10px; }
        .head-legal { color: #aeb3c1; font-size: 7.5px; margin-top: 6px; letter-spacing: 0.1px; }

        /* ============ Pied de page répété ============ */
        .page-foot { position: fixed; bottom: -66px; left: 0; right: 0; text-align: center; color: #aeb3c1; font-size: 7.5px; border-top: 1px solid #edeff4; padding-top: 9px; letter-spacing: 0.2px; line-height: 1.6; }
        .page-foot strong { color: #7a8096; }

        /* ============ Bandeau non payable ============ */
        .notice { background: #f4f1fd; border: 1px solid #ddd4fb; border-radius: 8px; padding: 5px 16px; margin: 0 0 8px; text-align: center; }
        .notice-title { color: #5c3ac9; font-size: 9.5px; font-weight: bold; letter-spacing: 0.06em; text-transform: uppercase; }
        .notice-text { color: #6a5aa8; font-size: 8.5px; margin-top: 1px; }

        /* ============ Bloc émetteur légal ============ */
        .legal { width: 100%; border-collapse: separate; border-spacing: 0; background: #f8f9fc; border: 1px solid #ebeef5; border-radius: 9px; margin: 0 0 8px; }
        .legal td { padding: 8px 16px; vertical-align: top; border: none; }
        .legal .box-label { margin-bottom: 4px; }
        .legal-name { font-size: 12px; font-weight: bold; color: #1a1d29; margin-bottom: 2px; }
        .legal-grid { width: 100%; border-collapse: separate; border-spacing: 0; }
        .legal-grid td { padding: 1px 0; vertical-align: top; border: none; }
        .legal-k { color: #9aa0b3; font-size: 8.5px; width: 42%; }
        .legal-v { color: #4a5062; font-size: 9px; font-weight: bold; }

        /* ============ Parties ============ */
        .parties { width: 100%; border-collapse: separate; border-spacing: 0; margin: 0 0 8px; }
        .parties .card { vertical-align: top; background: #f8f9fc; border: 1px solid #ebeef5; border-radius: 9px; padding: 9px 16px; }
        .parties .gap { width: 20px; }
        .box-label { font-size: 8px; text-transform: uppercase; letter-spacing: 0.09em; color: #9aa0b3; margin-bottom: 4px; font-weight: bold; }
        .box-name { font-size: 13px; font-weight: bold; color: #1a1d29; margin-bottom: 3px; }
        .box-line { color: #7a8096; font-size: 9.5px; line-height: 1.45; }

        /* ============ Bandeau infos ============ */
        .infos { width: 100%; border-collapse: separate; border-spacing: 0; margin: 0 0 6px; }
        .infos .cell { background: #fbfbfe; border: 1px solid #eef0f6; border-radius: 8px; padding: 6px 13px; vertical-align: top; }
        .infos .gap { width: 12px; }
        .infos .cell.hl { background: #8a4dfd; border-color: #8a4dfd; }
        .info-k { font-size: 8px; text-transform: uppercase; letter-spacing: 0.06em; color: #9aa0b3; font-weight: bold; margin-bottom: 3px; }
        .info-v { font-size: 12.5px; font-weight: bold; color: #2b3040; }
        .infos .cell.hl .info-k { color: #cdccff; }
        .infos .cell.hl .info-v { color: #fff; }

        .objet { margin: 8px 2px 2px; }
        .objet-label { font-size: 8px; text-transform: uppercase; letter-spacing: 0.07em; color: #9aa0b3; font-weight: bold; margin-bottom: 3px; }
        .objet-text { font-size: 10.5px; color: #4a5062; }

        /* ============ Lignes ============ */
        table.lines { width: 100%; border-collapse: collapse; margin-top: 8px; }
        table.lines th { background: #2b3040; color: #fff; padding: 8px 12px; font-size: 8.5px; text-align: left; font-weight: bold; text-transform: uppercase; letter-spacing: 0.05em; }
        table.lines th:first-child { border-radius: 7px 0 0 0; }
        table.lines th:last-child { border-radius: 0 7px 0 0; }
        table.lines th.r, table.lines td.r { text-align: right; }
        table.lines td { padding: 9px 12px; border-bottom: 1px solid #edeff4; }
        table.lines tbody tr:last-child td { border-bottom: 2px solid #dfe2ec; }
        .l-title { font-weight: bold; color: #1a1d29; font-size: 10.5px; }
        .l-sub { color: #9aa0b3; font-size: 8.5px; margin-top: 2px; }
        .tag { display: inline-block; padding: 3px 9px; border-radius: 20px; font-size: 8px; font-weight: bold; white-space: nowrap; }
        .num { color: #4a5062; }

        /* ============ Bas : mentions + totaux ============ */
        .bottom { width: 100%; border-collapse: collapse; margin-top: 12px; }
        .bottom > td { vertical-align: top; }
        .mentions { width: 50%; padding-right: 24px; }
        .m-label { font-size: 8px; text-transform: uppercase; letter-spacing: 0.07em; color: #9aa0b3; font-weight: bold; margin-bottom: 4px; }
        .m-block { margin-bottom: 9px; color: #7a8096; font-size: 9.5px; line-height: 1.5; }
        .m-block.strong { color: #4a5062; font-weight: bold; }

        .tbox { width: 50%; }
        .tbox table { width: 100%; border-collapse: separate; border-spacing: 0; }
        .tbox .row td { padding: 8px 14px; font-size: 11px; border: none; }
        .tbox .row td.k { color: #7a8096; }
        .tbox .row td.v { text-align: right; font-weight: bold; color: #2b3040; }
        .tbox .subtotal td { border-bottom: 1px solid #edeff4; }
        .tbox .grand-wrap { margin-top: 6px; background: #8a4dfd; border-radius: 9px; }
        .tbox .grand-wrap table { width: 100%; border-collapse: separate; border-spacing: 0; }
        .tbox .grand-wrap td { padding: 13px 16px; border: none; color: #fff; }
        .tbox .grand-wrap td.k { font-size: 11px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.06em; }
        .tbox .grand-wrap td.v { text-align: right; font-size: 18px; font-weight: bold; }
        .tbox .foot-note { font-size: 8px; color: #aeb3c1; text-align: right; padding: 6px 14px 0; }
    </style>
</head>
<body>
    @php
        $company = config('company');
        $badge = match($document->statut) {
            'Validé' => 'b-valide', 'En attente de validation' => 'b-attente',
            'Refusé' => 'b-refuse', 'Archivé' => 'b-archive', default => 'b-brouillon',
        };
        $tauxLabel = rtrim(rtrim(number_format($document->taux_tva, 2, ',', ' '), '0'), ',');
        $companyAddress = $company['address_line'].', '.$company['postal_code'].' '.$company['city'];
    @endphp

    <table class="page-head">
        <tr>
            <td style="width:55%;">
                <div class="brand-name">{{ $company['name'] }}</div>
                <div class="brand-legal">{{ $company['legal_form'] }} · SIREN {{ $company['siren'] }} · SIRET {{ $company['siret'] }}</div>
                <div class="brand-sub">{{ $companyAddress }} · {{ $company['website'] }}</div>
            </td>
            <td style="width:45%;">
                <div class="doc-title">FACTURE INTERNE</div>
                <div class="doc-num">{{ $document->numero_document }} &nbsp;·&nbsp; <span class="badge {{ $badge }}">{{ mb_strtoupper($document->statut) }}</span> &nbsp;<span class="badge b-nonpayable">NON PAYABLE</span></div>
            </td>
        </tr>
        <tr><td colspan="2"><div class="head-rule"></div></td></tr>
        <tr>
            <td colspan="2">
                <div class="head-legal">
                    {{ $company['name'] }} · {{ $company['legal_form'] }} · NAF/APE {{ $company['naf_code'] }} ({{ $company['naf_label'] }}) · RNA {{ $company['rna'] }} · TVA non applicable
                </div>
            </td>
        </tr>
    </table>

    <div class="page-foot">
        <strong>{{ $company['name'] }}</strong> · {{ $company['legal_form'] }} · SIREN {{ $company['siren'] }} · SIRET {{ $company['siret'] }} · {{ $companyAddress }} · {{ $company['website'] }}<br>
        Document de facturation interne {{ $document->numero_document }} — non payable, aucun règlement n'est dû — montants exprimés en euros — généré le {{ now()->format('d/m/Y à H:i') }}
    </div>

    <main>
        <div class="notice">
            <div class="notice-title">Document interne — non payable</div>
            <div class="notice-text">Ce document sert exclusivement à l'imputation budgétaire entre services internes de {{ $company['name'] }}. Il ne constitue pas une facture commerciale et n'appelle aucun règlement.</div>
        </div>

        <table class="legal">
            <tr>
                <td>
                    <div class="box-label">Émetteur légal</div>
                    <div class="legal-name">{{ $company['name'] }}</div>
                    <table class="legal-grid">
                        <tr><td class="legal-k">Forme juridique</td><td class="legal-v">{{ $company['legal_form'] }}</td></tr>
                        <tr><td class="legal-k">SIREN</td><td class="legal-v">{{ $company['siren'] }}</td></tr>
                        <tr><td class="legal-k">SIRET (siège)</td><td class="legal-v">{{ $company['siret'] }}</td></tr>
                        <tr><td class="legal-k">Code NAF/APE</td><td class="legal-v">{{ $company['naf_code'] }} — {{ $company['naf_label'] }}</td></tr>
                    </table>
                </td>
                <td>
                    <div class="box-label">&nbsp;</div>
                    <table class="legal-grid" style="margin-top:20px;">
                        <tr><td class="legal-k">N° RNA</td><td class="legal-v">{{ $company['rna'] }}</td></tr>
                        <tr><td class="legal-k">N° TVA intracom.</td><td class="legal-v">Non applicable</td></tr>
                        <tr><td class="legal-k">Adresse du siège</td><td class="legal-v">{{ $companyAddress }}, {{ $company['country'] }}</td></tr>
                        <tr><td class="legal-k">Site web</td><td class="legal-v">{{ $company['website'] }}</td></tr>
                    </table>
                </td>
            </tr>
        </table>

        <table class="parties">
            <tr>
                <td class="card" style="width:50%;">
                    <div class="box-label">Service émetteur</div>
                    <div class="box-name">{{ $document->serviceEmetteur->name }}</div>
                    <div class="box-line">Code service : {{ $document->serviceEmetteur->code }}</div>
                    <div class="box-line">Demandeur : {{ $document->demandeur->name }}</div>
                </td>
                <td class="gap"></td>
                <td class="card" style="width:50%;">
                    <div class="box-label">Service destinataire</div>
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
                        <div class="m-block strong">Document interne à {{ $company['name'] }} — non payable, à imputer sur le budget du service destinataire.</div>
                    @endif
                </td>
                <td class="tbox">
                    <table>
                        <tr class="row"><td class="k">Sous-total HT</td><td class="v">{{ $document->montantHtFormate() }}</td></tr>
                        <tr class="row subtotal"><td class="k">TVA ({{ $tauxLabel }} %)</td><td class="v">{{ $document->montantTvaFormate() }}</td></tr>
                    </table>
                    <div class="grand-wrap">
                        <table>
                            <tr><td class="k">Total TTC</td><td class="v">{{ $document->montantTtcFormate() }}</td></tr>
                        </table>
                    </div>
                    <div class="foot-note">Montant non exigible — usage interne uniquement</div>
                </td>
            </tr>
        </table>
    </main>
</body>
</html>
