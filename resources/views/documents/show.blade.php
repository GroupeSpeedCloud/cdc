@extends('layouts.app')
@section('title', $document->numero_document)
@section('page-title', 'Facture ' . $document->numero_document)

@php
    $u = auth()->user();
    $estDemandeur = $u->id === $document->demandeur_id;
    $peutEditer = ($estDemandeur || $u->isAdmin()) && $document->estModifiable();
    $peutSoumettre = $peutEditer && $document->lignes->count() > 0;
    $estValidateur = $u->isAdmin() || $document->serviceDestinataire->manager_id === $u->id;
    $peutValider = $estValidateur && $document->statut === 'En attente de validation';
    $peutArchiver = $u->isAdmin() && $document->statut === 'Validé';
@endphp

@section('content')
<div class="d-flex justify-content-between align-items-start mb-3 flex-wrap gap-2">
    <div>
        <h4 class="mb-1">{{ $document->numero_document }} <span class="badge {{ $document->statutBadgeClass() }}">{{ $document->statut }}</span> <span class="badge bg-light text-secondary border">Non payable · usage interne</span></h4>
        <div class="text-secondary small">Créée par {{ $document->demandeur->name }} · {{ $document->created_at->format('d/m/Y H:i') }}</div>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('documents.apercu', $document) }}" target="_blank" class="btn btn-sm btn-outline-secondary"><i class="bi bi-eye"></i> Aperçu</a>
        <a href="{{ route('documents.pdf', $document) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-download"></i> Télécharger</a>
        @if($peutEditer)
            <a href="{{ route('documents.edit', $document) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i> Modifier</a>
        @endif
        @if($peutSoumettre)
            <form method="POST" action="{{ route('documents.soumettre', $document) }}">@csrf
                <button class="btn btn-sm btn-primary"><i class="bi bi-send"></i> Soumettre</button>
            </form>
        @endif
        @if($peutValider)
            <form method="POST" action="{{ route('documents.valider', $document) }}" onsubmit="return confirm('Valider cette facture ? Le montant HT sera déduit du budget du service destinataire.')">@csrf
                <button class="btn btn-sm btn-success"><i class="bi bi-check-lg"></i> Valider</button>
            </form>
            <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#refusModal"><i class="bi bi-x-lg"></i> Refuser</button>
        @endif
        @if($peutArchiver)
            <form method="POST" action="{{ route('documents.archiver', $document) }}">@csrf
                <button class="btn btn-sm btn-outline-dark"><i class="bi bi-archive"></i> Archiver</button>
            </form>
        @endif
    </div>
</div>

@if($document->statut === 'Refusé' && $document->motif_refus)
<div class="alert alert-danger"><strong>Motif du refus :</strong> {{ $document->motif_refus }}</div>
@endif

<div class="row g-3 mb-3">
    <div class="col-lg-8">
        <div class="card h-100"><div class="card-body">
            <div class="row g-3">
                <div class="col-sm-6">
                    <div class="text-secondary small text-uppercase" style="font-size:.7rem;">Service émetteur</div>
                    <div class="fw-semibold">{{ $document->serviceEmetteur->name }} <span class="badge bg-secondary">{{ $document->serviceEmetteur->code }}</span></div>
                </div>
                <div class="col-sm-6">
                    <div class="text-secondary small text-uppercase" style="font-size:.7rem;">Service destinataire</div>
                    <div class="fw-semibold">{{ $document->serviceDestinataire->name }} <span class="badge bg-secondary">{{ $document->serviceDestinataire->code }}</span></div>
                </div>
                <div class="col-sm-4">
                    <div class="text-secondary small">Date d'émission</div>
                    <div>{{ $document->date_emission->format('d/m/Y') }}</div>
                </div>
                <div class="col-sm-4">
                    <div class="text-secondary small">Échéance</div>
                    <div>{{ optional($document->date_echeance)->format('d/m/Y') ?? '—' }}</div>
                </div>
                <div class="col-sm-4">
                    <div class="text-secondary small">Validateur</div>
                    <div>{{ $document->validateur->name ?? '—' }}</div>
                </div>
                @if($document->description_globale)
                <div class="col-12">
                    <div class="text-secondary small">Objet</div>
                    <div>{{ $document->description_globale }}</div>
                </div>
                @endif
            </div>
        </div></div>
    </div>
    <div class="col-lg-4">
        <div class="card h-100"><div class="card-body d-flex flex-column justify-content-center">
            <div class="d-flex justify-content-between py-1"><span class="text-secondary">Total HT</span><strong>{{ $document->montantHtFormate() }}</strong></div>
            <div class="d-flex justify-content-between py-1"><span class="text-secondary">TVA ({{ rtrim(rtrim(number_format($document->taux_tva, 2, ',', ' '), '0'), ',') }} %)</span><strong>{{ $document->montantTvaFormate() }}</strong></div>
            <div class="d-flex justify-content-between py-2 mt-1 border-top"><span class="fw-bold">Total TTC</span><strong class="fs-4 text-primary">{{ $document->montantTtcFormate() }}</strong></div>
        </div></div>
    </div>
</div>

<div class="card">
    <div class="card-header fw-semibold">Lignes de la facture</div>
    <div class="table-responsive">
        <table class="table mb-0 align-middle">
            <thead><tr><th>Description</th><th>Type</th><th>Détail</th><th class="text-end">Qté</th><th class="text-end">P.U. HT</th><th class="text-end">Montant HT</th></tr></thead>
            <tbody>
            @foreach($document->lignes as $l)
                <tr>
                    <td>{{ $l->description_ligne }}</td>
                    <td><span class="badge rounded-pill" style="{{ $l->typeStyle() }}">{{ $l->type_prestation }}</span></td>
                    <td>{{ $l->detail() ?: '—' }}</td>
                    <td class="text-end">{{ rtrim(rtrim(number_format($l->quantite, 2, ',', ' '), '0'), ',') }}{{ $l->estTemps() ? ' h' : '' }}</td>
                    <td class="text-end">{{ number_format($l->tarif_unitaire, 2, ',', ' ') }} €</td>
                    <td class="text-end fw-semibold">{{ number_format($l->montant_ligne, 2, ',', ' ') }} €</td>
                </tr>
            @endforeach
            </tbody>
            <tfoot>
                <tr><td colspan="5" class="text-end text-secondary">Total HT</td><td class="text-end">{{ $document->montantHtFormate() }}</td></tr>
                <tr><td colspan="5" class="text-end text-secondary">TVA</td><td class="text-end">{{ $document->montantTvaFormate() }}</td></tr>
                <tr><td colspan="5" class="text-end fw-bold">Total TTC</td><td class="text-end fw-bold">{{ $document->montantTtcFormate() }}</td></tr>
            </tfoot>
        </table>
    </div>
</div>

@if($document->notes)
<div class="card mt-3"><div class="card-body">
    <div class="text-secondary small text-uppercase" style="font-size:.7rem;">Notes</div>
    <div>{{ $document->notes }}</div>
</div></div>
@endif

@if($peutValider)
<div class="modal fade" id="refusModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('documents.refuser', $document) }}" class="modal-content">@csrf
            <div class="modal-header"><h5 class="modal-title">Refuser la facture</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <label class="form-label">Motif du refus <span class="text-danger">*</span></label>
                <textarea name="motif_refus" class="form-control" rows="3" required></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="submit" class="btn btn-danger">Confirmer le refus</button>
            </div>
        </form>
    </div>
</div>
@endif
@endsection
