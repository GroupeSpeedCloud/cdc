<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        @font-face {
            font-family: 'Titillium Web'; font-weight: 400; font-style: normal;
            src: url({{ resource_path('fonts/titillium/TitilliumWeb-Regular.ttf') }}) format('truetype');
        }
        @font-face {
            font-family: 'Titillium Web'; font-weight: 600; font-style: normal;
            src: url({{ resource_path('fonts/titillium/TitilliumWeb-SemiBold.ttf') }}) format('truetype');
        }

        /* ============ Tons Material Design 3 — mêmes rôles que l'application ============ */
        /* Poids de police volontairement limités à 400 (texte) et 600 (repères/légendes) : pas de gras appuyé. */
        @page { margin: 124px 46px 28px; }
        * { box-sizing: border-box; }
        body { font-family: 'Titillium Web', DejaVu Sans, sans-serif; font-size: 10.5px; color: #19181b; margin: 0; line-height: 1.45; font-weight: 400; }
        b, strong { font-weight: 600; }

        .label { font-size: 8px; text-transform: uppercase; letter-spacing: 0.08em; color: #9489a9; font-weight: 600; }

        /* ============ En-tête répété — un seul bloc d'identité légale, pas de répétition ============ */
        .page-head { position: fixed; top: -122px; left: 0; right: 0; height: 114px; border-collapse: collapse; border-spacing: 0; }
        .page-head td { vertical-align: top; }
        .brand-mark { display: inline-block; width: 22px; height: 22px; background: #8a4dfd; color: #fff; border-radius: 6px; text-align: center; line-height: 22px; font-size: 8.5px; font-weight: 600; letter-spacing: 0.2px; vertical-align: middle; }
        .brand-name { display: inline-block; font-size: 14px; font-weight: 600; color: #19181b; letter-spacing: 0.1px; vertical-align: middle; margin-left: 9px; }
        .brand-legal { color: #9489a9; font-size: 8.5px; margin-top: 6px; }
        .doc-label { text-align: right; font-size: 8px; text-transform: uppercase; letter-spacing: 0.16em; color: #8a4dfd; font-weight: 600; }
        .doc-num { text-align: right; color: #592aa9; font-size: 17px; font-weight: 600; letter-spacing: 0.1px; margin-top: 4px; }
        .doc-badges { text-align: right; margin-top: 6px; }
        .badge { display: inline-block; padding: 2px 10px; border-radius: 999px; font-size: 8px; font-weight: 600; letter-spacing: 0.3px; vertical-align: middle; border: 1px solid transparent; margin-left: 6px; }
        .b-valide { background: #eaf7f0; color: #0f5132; border-color: #cdebd9; }
        .b-attente { background: #fdf6e4; color: #7a4e05; border-color: #f6e4b0; }
        .b-refuse { background: #fbeceb; color: #8c1d18; border-color: #f3c8c5; }
        .b-brouillon { background: #f5f4f5; color: #494059; border-color: #e4e1ea; }
        .b-archive { background: #f5f4f5; color: #37353b; border-color: #e4e1ea; }
        .b-nonpayable { background: #ffffff; color: #796b94; border-color: #d8cfe6; }
        .head-rule { height: 1.5px; background: #8a4dfd; margin-top: 12px; }

        /* ============ Pied de page répété — rappel court, le détail légal est dans le corps ============ */
        .page-foot { position: fixed; bottom: -32px; left: 0; right: 0; text-align: center; color: #9489a9; font-size: 7.5px; border-top: 1px solid #e4e1ea; padding-top: 6px; letter-spacing: 0.1px; line-height: 1.5; }
        .page-foot .co { color: #494059; font-weight: 600; }

        /* ============ Bandeau non payable — sobre, contour seulement ============ */
        .notice { border: 1px solid #e4dcf7; background: #faf8fe; border-radius: 10px; padding: 6px 16px; margin: 0 0 8px; text-align: center; }
        .notice-title { color: #592aa9; font-size: 9px; font-weight: 600; letter-spacing: 0.05em; text-transform: uppercase; }
        .notice-text { color: #6a5f80; font-size: 8.5px; margin-top: 2px; }

        /* ============ Émetteur légal — source unique de la mention légale complète ============ */
        .legal { margin: 0 0 8px; padding: 7px 0; border-top: 1px solid #e4e1ea; border-bottom: 1px solid #e4e1ea; }
        .legal-name { font-size: 11.5px; font-weight: 600; color: #19181b; margin: 2px 0 6px; }
        .legal-grid { width: 100%; border-collapse: separate; border-spacing: 0; }
        .legal-grid td { padding: 1px 0; vertical-align: top; border: none; }
        .legal-k { color: #9489a9; font-size: 8.5px; width: 40%; }
        .legal-v { color: #494059; font-size: 9px; }

        /* ============ Parties — texte simple séparé par un filet ============ */
        .parties { width: 100%; border-collapse: separate; border-spacing: 0; margin: 0 0 8px; }
        .parties .cell { vertical-align: top; padding: 0 18px 0 0; }
        .parties .divider { width: 1px; background: #e4e1ea; }
        .parties .cell.second { padding: 0 0 0 18px; }
        .box-name { font-size: 12px; font-weight: 600; color: #19181b; margin: 3px 0 4px; }
        .box-line { color: #6a5f80; font-size: 9px; line-height: 1.5; }

        /* ============ Bandeau infos — filets verticaux, léger accent sur le total ============ */
        .infos { width: 100%; border-collapse: separate; border-spacing: 0; margin: 0 0 8px; border-top: 1px solid #e4e1ea; border-bottom: 1px solid #e4e1ea; }
        .infos .cell { padding: 6px 16px; vertical-align: top; border-left: 1px solid #e4e1ea; }
        .infos .cell:first-child { border-left: none; padding-left: 0; }
        .info-v { font-size: 12px; margin-top: 4px; color: #19181b; }
        .infos .cell.hl { background: #faf8fe; border-left: 1px solid #e4dcf7; }
        .infos .cell.hl .label { color: #8a4dfd; }
        .infos .cell.hl .info-v { color: #592aa9; font-weight: 600; font-size: 13px; }

        .objet { margin: 0 0 8px; }
        .objet-text { font-size: 10.5px; color: #494059; margin-top: 3px; }

        /* ============ Lignes ============ */
        table.lines { width: 100%; border-collapse: collapse; }
        table.lines th { border-bottom: 1px solid #c9c4d4; color: #9489a9; padding: 0 12px 7px; font-size: 8px; text-align: left; font-weight: 600; text-transform: uppercase; letter-spacing: 0.06em; }
        table.lines th:first-child, table.lines td:first-child { padding-left: 0; }
        table.lines th:last-child, table.lines td:last-child { padding-right: 0; }
        table.lines th.r, table.lines td.r { text-align: right; }
        table.lines td { padding: 6px 12px; border-bottom: 1px solid #e4e1ea; }
        .l-title { color: #19181b; font-size: 10.5px; }
        .l-sub { color: #9489a9; font-size: 8.5px; margin-top: 2px; }
        .tag { display: inline-block; padding: 2px 8px; border-radius: 999px; font-size: 8px; font-weight: 600; white-space: nowrap; }
        .num { color: #494059; }
        .l-amount { color: #19181b; }

        /* ============ Bas : mentions + totaux ============ */
        .bottom { width: 100%; border-collapse: collapse; margin-top: 8px; }
        .bottom > td { vertical-align: top; }
        .mentions { width: 50%; padding-right: 24px; }
        .m-block { margin-bottom: 10px; color: #6a5f80; font-size: 9.5px; line-height: 1.55; }
        .m-block .label { display: block; margin-bottom: 3px; }
        .m-block.strong { color: #494059; }

        .tbox { width: 50%; }
        .tbox table { width: 100%; border-collapse: separate; border-spacing: 0; }
        .tbox .row td { padding: 4px 0; font-size: 10.5px; border: none; }
        .tbox .row td.k { color: #6a5f80; }
        .tbox .row td.v { text-align: right; color: #19181b; }
        .tbox .subtotal td { padding-bottom: 7px; border-bottom: 1px solid #e4e1ea; }
        .tbox .grand { margin-top: 8px; background: #faf8fe; border: 1px solid #e4dcf7; border-radius: 12px; padding: 10px 16px; }
        .tbox .grand table { width: 100%; border-collapse: separate; border-spacing: 0; }
        .tbox .grand td { padding: 0; border: none; }
        .tbox .grand td.k { font-size: 10px; color: #592aa9; text-transform: uppercase; letter-spacing: 0.07em; }
        .tbox .grand td.v { text-align: right; font-size: 20px; color: #592aa9; font-weight: 600; }
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
                <div><span class="brand-mark">GSC</span><span class="brand-name">{{ $company['name'] }}</span></div>
                <div class="brand-legal">{{ $company['legal_form'] }} · SIREN {{ $company['siren'] }} · {{ $companyAddress }}</div>
            </td>
            <td style="width:45%;">
                <div class="doc-label">Facture interne</div>
                <div class="doc-num">{{ $document->numero_document }}</div>
                <div class="doc-badges">
                    <span class="badge {{ $badge }}">{{ mb_strtoupper($document->statut) }}</span>
                    <span class="badge b-nonpayable">NON PAYABLE</span>
                </div>
            </td>
        </tr>
        <tr><td colspan="2"><div class="head-rule"></div></td></tr>
    </table>

    <div class="page-foot">
        <span class="co">{{ $company['name'] }}</span> · SIREN {{ $company['siren'] }} · {{ $document->numero_document }} · document interne non payable
    </div>

    <div class="main">
        <div class="notice">
            <div class="notice-title">Document interne — non payable</div>
            <div class="notice-text">Ce document sert exclusivement à l'imputation budgétaire entre services internes de {{ $company['name'] }}. Il ne constitue pas une facture commerciale et n'appelle aucun règlement.</div>
        </div>

        <div class="legal">
            <div class="label">Émetteur légal</div>
            <div class="legal-name">{{ $company['name'] }}</div>
            <table style="width:100%;border-collapse:separate;border-spacing:0;">
                <tr>
                    <td style="width:50%;vertical-align:top;">
                        <table class="legal-grid">
                            <tr><td class="legal-k">Forme juridique</td><td class="legal-v">{{ $company['legal_form'] }}</td></tr>
                            <tr><td class="legal-k">SIREN</td><td class="legal-v">{{ $company['siren'] }}</td></tr>
                            <tr><td class="legal-k">SIRET (siège)</td><td class="legal-v">{{ $company['siret'] }}</td></tr>
                            <tr><td class="legal-k">Code NAF/APE</td><td class="legal-v">{{ $company['naf_code'] }} — {{ $company['naf_label'] }}</td></tr>
                        </table>
                    </td>
                    <td style="width:50%;vertical-align:top;">
                        <table class="legal-grid">
                            <tr><td class="legal-k">N° RNA</td><td class="legal-v">{{ $company['rna'] }}</td></tr>
                            <tr><td class="legal-k">N° TVA intracom.</td><td class="legal-v">Non applicable</td></tr>
                            <tr><td class="legal-k">Adresse du siège</td><td class="legal-v">{{ $companyAddress }}, {{ $company['country'] }}</td></tr>
                            <tr><td class="legal-k">Site web</td><td class="legal-v">{{ $company['website'] }}</td></tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>

        <table class="parties">
            <tr>
                <td class="cell" style="width:50%;">
                    <div class="label">Service émetteur</div>
                    <div class="box-name">{{ $document->serviceEmetteur->name }}</div>
                    <div class="box-line">Code service : {{ $document->serviceEmetteur->code }}</div>
                    <div class="box-line">Demandeur : {{ $document->demandeur->name }}</div>
                </td>
                <td class="divider"></td>
                <td class="cell second" style="width:50%;">
                    <div class="label">Service destinataire</div>
                    <div class="box-name">{{ $document->serviceDestinataire->name }}</div>
                    <div class="box-line">Code service : {{ $document->serviceDestinataire->code }}</div>
                    <div class="box-line">Responsable : {{ $document->serviceDestinataire->manager->name ?? '—' }}</div>
                </td>
            </tr>
        </table>

        <table class="infos">
            <tr>
                <td class="cell" style="width:25%;">
                    <div class="label">Date d'émission</div>
                    <div class="info-v">{{ $document->date_emission->format('d/m/Y') }}</div>
                </td>
                <td class="cell" style="width:25%;">
                    <div class="label">Échéance</div>
                    <div class="info-v">{{ optional($document->date_echeance)->format('d/m/Y') ?? '—' }}</div>
                </td>
                <td class="cell" style="width:25%;">
                    <div class="label">Taux de TVA</div>
                    <div class="info-v">{{ $tauxLabel }} %</div>
                </td>
                <td class="cell hl" style="width:25%;">
                    <div class="label">Total TTC</div>
                    <div class="info-v">{{ $document->montantTtcFormate() }}</div>
                </td>
            </tr>
        </table>

        @if($document->description_globale)
            <div class="objet">
                <div class="label">Objet de la facture</div>
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
                    <td class="r l-amount">{{ number_format($l->montant_ligne, 2, ',', ' ') }} €</td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <table class="bottom">
            <tr>
                <td class="mentions">
                    @if($document->notes)
                        <div class="m-block">
                            <span class="label">Notes</span>
                            {{ $document->notes }}
                        </div>
                    @endif
                    @if($document->validateur)
                        <div class="m-block">
                            <span class="label">Validation</span>
                            Traitée par {{ $document->validateur->name }} le {{ optional($document->date_validation)->format('d/m/Y à H:i') }}.
                            @if($document->statut === 'Refusé' && $document->motif_refus)
                                <br><span style="color:#8c1d18;">Motif du refus :</span> {{ $document->motif_refus }}
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
                    <div class="grand">
                        <table>
                            <tr><td class="k">Total TTC</td><td class="v">{{ $document->montantTtcFormate() }}</td></tr>
                        </table>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
