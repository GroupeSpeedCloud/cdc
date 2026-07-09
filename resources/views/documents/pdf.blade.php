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

        /* Palette : tokens MD3 de l'app (seed #8a4dfd). Deux graisses seulement : 400 / 600. */
        @page { margin: 36px 0 46px; }
        body, div, p, table, td, th { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Titillium Web', DejaVu Sans, sans-serif; font-size: 10px; color: #19181b; line-height: 1.5; font-weight: 400; }
        table { border-collapse: collapse; border-spacing: 0; }

        .px { padding-left: 48px; padding-right: 48px; }

        /* ===== Bandeau de marque ===== */
        .masthead { width: 100%; background: #8a4dfd; color: #fff; }
        .masthead td { padding: 24px 48px 22px; vertical-align: middle; }
        .mh-brand { font-size: 17px; font-weight: 600; letter-spacing: 0.2px; color: #fff; }
        .mh-tag { font-size: 8.5px; color: #dfd1fa; margin-top: 2px; letter-spacing: 0.4px; text-transform: uppercase; }
        .mh-doc { text-align: right; }
        .mh-doc-label { font-size: 9px; letter-spacing: 0.2em; text-transform: uppercase; color: #dfd1fa; font-weight: 600; }
        .mh-doc-num { font-size: 22px; font-weight: 600; color: #fff; margin-top: 1px; }

        /* ===== Ligne de statut sous le bandeau ===== */
        .statusbar { width: 100%; background: #f4effd; }
        .statusbar td { padding: 8px 48px; }
        .sb-text { font-size: 8.5px; color: #592aa9; letter-spacing: 0.3px; }
        .sb-text .sep { color: #b39aec; padding: 0 6px; }
        .badge { display: inline-block; padding: 2px 11px; border-radius: 999px; font-size: 8px; font-weight: 600; letter-spacing: 0.4px; }
        .b-valide { background: #d3f0e0; color: #0f5132; }
        .b-attente { background: #fbecc7; color: #7a4e05; }
        .b-refuse { background: #f9dedc; color: #8c1d18; }
        .b-brouillon { background: #e4e1ea; color: #494059; }
        .b-archive { background: #e4e1ea; color: #37353b; }
        .b-np { background: #fff; color: #592aa9; border: 1px solid #d8bdfa; }
        .sb-badges { text-align: right; }

        /* ===== Libellés ===== */
        .k { font-size: 7.5px; text-transform: uppercase; letter-spacing: 0.14em; color: #8a4dfd; font-weight: 600; }

        /* ===== De / À ===== */
        .parties { width: 100%; margin-top: 22px; }
        .parties td { vertical-align: top; }
        .parties .col { width: 44%; }
        .parties .mid { width: 12%; }
        .p-name { font-size: 14px; font-weight: 600; color: #19181b; margin: 5px 0 2px; }
        .p-line { font-size: 9.5px; color: #494059; }
        .p-dim { font-size: 8.5px; color: #9489a9; }

        /* ===== Méta ===== */
        .meta { width: 100%; margin-top: 20px; border-top: 1.2px solid #19181b; border-bottom: 1px solid #e4e1ea; }
        .meta td { padding: 9px 14px 8px; border-right: 1px solid #e4e1ea; }
        .meta td:first-child { padding-left: 0; }
        .meta td:last-child { border-right: none; }
        .meta .v { font-size: 12.5px; color: #19181b; margin-top: 2px; font-weight: 600; }
        .meta .v.accent { color: #592aa9; }

        /* ===== Objet ===== */
        .objet { margin-top: 16px; }
        .objet-text { font-size: 11px; color: #19181b; margin-top: 2px; }

        /* ===== Tableau des lignes ===== */
        .lines { width: 100%; margin-top: 16px; }
        .lines th { font-size: 7.5px; text-transform: uppercase; letter-spacing: 0.14em; color: #8a4dfd; font-weight: 600; text-align: left; padding: 0 12px 6px; border-bottom: 1.2px solid #19181b; }
        .lines th:first-child, .lines td:first-child { padding-left: 0; }
        .lines th:last-child, .lines td:last-child { padding-right: 0; }
        .lines .r { text-align: right; }
        .lines td { padding: 8px 12px; border-bottom: 1px solid #eceaf0; vertical-align: top; }
        .li-t { font-size: 10.5px; color: #19181b; }
        .li-d { font-size: 8.5px; color: #9489a9; margin-top: 1px; }
        .tag { display: inline-block; padding: 2px 9px; border-radius: 999px; font-size: 7.5px; font-weight: 600; white-space: nowrap; }
        .num { color: #494059; font-size: 10px; }
        .amt { color: #19181b; font-size: 10.5px; font-weight: 600; }

        /* ===== Totaux + mentions ===== */
        .closing { width: 100%; margin-top: 4px; }
        .closing td { vertical-align: top; }
        .notes-col { width: 52%; padding-right: 36px; padding-top: 14px; }
        .n-block { margin-bottom: 10px; font-size: 9px; color: #494059; line-height: 1.55; }
        .n-block .k { display: block; margin-bottom: 2px; }
        .totals-col { width: 48%; }
        .totals { width: 100%; }
        .totals td { padding: 7px 0; font-size: 10.5px; border-bottom: 1px solid #eceaf0; }
        .totals .tk { color: #494059; }
        .totals .tv { text-align: right; color: #19181b; }
        .grand { width: 100%; margin-top: -1px; border-top: 1.2px solid #19181b; }
        .grand td { padding: 10px 0 0; }
        .grand .gk { font-size: 9px; text-transform: uppercase; letter-spacing: 0.14em; color: #19181b; font-weight: 600; }
        .grand .gv { text-align: right; font-size: 21px; font-weight: 600; color: #8a4dfd; }

        /* ===== Juridique, une seule fois ===== */
        .legal { margin-top: 26px; border-top: 1px solid #e4e1ea; padding-top: 9px; font-size: 7.5px; color: #9489a9; line-height: 1.65; }
        .legal .co { color: #494059; font-weight: 600; }

        /* ===== Pied de page répété ===== */
        .page-foot { position: fixed; bottom: -34px; left: 48px; right: 48px; font-size: 7.5px; color: #9489a9; border-top: 1px solid #e4e1ea; padding-top: 6px; text-align: center; }
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

    <div class="page-foot">
        {{ $company['name'] }} · SIREN {{ $company['siren'] }} · {{ $document->numero_document }} · document interne non payable
    </div>

    <table class="masthead">
        <tr>
            <td style="width:55%;">
                <div class="mh-brand">{{ $company['name'] }}</div>
                <div class="mh-tag">Facturation interne entre services</div>
            </td>
            <td style="width:45%;" class="mh-doc">
                <div class="mh-doc-label">Facture interne</div>
                <div class="mh-doc-num">{{ $document->numero_document }}</div>
            </td>
        </tr>
    </table>

    <table class="statusbar">
        <tr>
            <td style="width:62%;">
                <span class="sb-text">Document interne — non payable<span class="sep">·</span>imputation budgétaire entre services, aucun règlement n'est dû</span>
            </td>
            <td style="width:38%;" class="sb-badges">
                <span class="badge {{ $badge }}">{{ mb_strtoupper($document->statut) }}</span>
                <span class="badge b-np">NON PAYABLE</span>
            </td>
        </tr>
    </table>

    <div class="px">
        <table class="parties">
            <tr>
                <td class="col">
                    <div class="k">De — service émetteur</div>
                    <div class="p-name">{{ $document->serviceEmetteur->name }}</div>
                    <div class="p-line">{{ $company['name'] }} · code {{ $document->serviceEmetteur->code }}</div>
                    <div class="p-dim">Demandeur : {{ $document->demandeur->name }}</div>
                </td>
                <td class="mid"></td>
                <td class="col">
                    <div class="k">À — service destinataire</div>
                    <div class="p-name">{{ $document->serviceDestinataire->name }}</div>
                    <div class="p-line">{{ $company['name'] }} · code {{ $document->serviceDestinataire->code }}</div>
                    <div class="p-dim">Responsable : {{ $document->serviceDestinataire->manager->name ?? '—' }}</div>
                </td>
            </tr>
        </table>

        <table class="meta">
            <tr>
                <td style="width:25%;">
                    <div class="k">Émission</div>
                    <div class="v">{{ $document->date_emission->format('d/m/Y') }}</div>
                </td>
                <td style="width:25%;">
                    <div class="k">Échéance</div>
                    <div class="v">{{ optional($document->date_echeance)->format('d/m/Y') ?? '—' }}</div>
                </td>
                <td style="width:22%;">
                    <div class="k">TVA</div>
                    <div class="v">{{ $tauxLabel }} %</div>
                </td>
                <td style="width:28%;">
                    <div class="k">Total TTC</div>
                    <div class="v accent">{{ $document->montantTtcFormate() }}</div>
                </td>
            </tr>
        </table>

        @if($document->description_globale)
            <div class="objet">
                <div class="k">Objet</div>
                <div class="objet-text">{{ $document->description_globale }}</div>
            </div>
        @endif

        <table class="lines">
            <thead>
                <tr>
                    <th style="width:44%">Description</th>
                    <th style="width:15%">Type</th>
                    <th class="r" style="width:10%">Qté</th>
                    <th class="r" style="width:15%">P.U. HT</th>
                    <th class="r" style="width:16%">Montant HT</th>
                </tr>
            </thead>
            <tbody>
            @foreach($document->lignes as $l)
                @php $detail = $l->detail(); @endphp
                <tr>
                    <td>
                        <div class="li-t">{{ $l->description_ligne }}</div>
                        @if($detail)<div class="li-d">{{ $detail }}</div>@endif
                    </td>
                    <td><span class="tag" style="{{ $l->typeStyle() }}">{{ $l->type_prestation }}</span></td>
                    <td class="r num">{{ rtrim(rtrim(number_format($l->quantite, 2, ',', ' '), '0'), ',') }}{{ $l->estTemps() ? ' h' : '' }}</td>
                    <td class="r num">{{ number_format($l->tarif_unitaire, 2, ',', ' ') }} €</td>
                    <td class="r amt">{{ number_format($l->montant_ligne, 2, ',', ' ') }} €</td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <table class="closing">
            <tr>
                <td class="notes-col">
                    @if($document->notes)
                        <div class="n-block">
                            <span class="k">Notes</span>
                            {{ $document->notes }}
                        </div>
                    @endif
                    @if($document->validateur)
                        <div class="n-block">
                            <span class="k">Validation</span>
                            Traitée par {{ $document->validateur->name }} le {{ optional($document->date_validation)->format('d/m/Y à H:i') }}.
                            @if($document->statut === 'Refusé' && $document->motif_refus)
                                <br><span style="color:#8c1d18;font-weight:600;">Motif du refus :</span> {{ $document->motif_refus }}
                            @endif
                        </div>
                    @endif
                    @if(! $document->notes && ! $document->validateur)
                        <div class="n-block">
                            <span class="k">Imputation</span>
                            Montant à imputer sur le budget du service destinataire.
                        </div>
                    @endif
                </td>
                <td class="totals-col">
                    <table class="totals">
                        <tr><td class="tk">Sous-total HT</td><td class="tv">{{ $document->montantHtFormate() }}</td></tr>
                        <tr><td class="tk">TVA ({{ $tauxLabel }} %)</td><td class="tv">{{ $document->montantTvaFormate() }}</td></tr>
                    </table>
                    <table class="grand">
                        <tr><td class="gk">Total TTC</td><td class="gv">{{ $document->montantTtcFormate() }}</td></tr>
                    </table>
                </td>
            </tr>
        </table>

        <div class="legal">
            <span class="co">{{ $company['name'] }}</span> — {{ $company['legal_form'] }} · SIREN {{ $company['siren'] }} · SIRET (siège) {{ $company['siret'] }} · NAF/APE {{ $company['naf_code'] }} ({{ $company['naf_label'] }}) · RNA {{ $company['rna'] }} · TVA non applicable · {{ $companyAddress }}, {{ $company['country'] }} · {{ $company['website'] }}<br>
            Document de facturation interne servant exclusivement à l'imputation budgétaire entre services. Il ne constitue pas une facture commerciale et n'appelle aucun règlement. Montants exprimés en euros. Généré le {{ now()->format('d/m/Y à H:i') }}.
        </div>
    </div>
</body>
</html>
