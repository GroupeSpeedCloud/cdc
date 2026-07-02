@extends('layouts.app')
@section('title', $document->numero_document)
@section('page-title', 'Document ' . $document->numero_document)

@php
    $u = auth()->user();
    $estDemandeur = $u->id === $document->demandeur_id;
    $peutEditer = ($estDemandeur || $u->isAdmin()) && in_array($document->statut, ['Brouillon', 'Refusé']);
    $peutSoumettre = $peutEditer && $document->lignes->count() > 0;
    $estValidateur = $u->isAdmin() || $document->serviceDestinataire->manager_id === $u->id;
    $peutValider = $estValidateur && $document->statut === 'En attente de validation';
    $peutArchiver = $u->isAdmin() && $document->statut === 'Validé';
@endphp

@section('content')
<div class="d-flex justify-content-between align-items-start mb-3">
    <div>
        <h4 class="mb-1">{{ $document->numero_document }} <span class="badge {{ $document->statutBadgeClass() }}">{{ $document->statut }}</span></h4>
        <div class="text-secondary small">Créé par {{ $document->demandeur->name }} · {{ $document->created_at->format('d/m/Y H:i') }}</div>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('documents.pdf', $document) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-file-pdf"></i> PDF</a>
        @if($peutEditer)
            <a href="{{ route('documents.edit', $document) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i> Modifier</a>
        @endif
        @if($peutSoumettre)
            <form method="POST" action="{{ route('documents.soumettre', $document) }}">@csrf
                <button class="btn btn-sm btn-primary"><i class="bi bi-send"></i> Soumettre</button>
            </form>
        @endif
        @if($peutValider)
            <form method="POST" action="{{ route('documents.valider', $document) }}">@csrf
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
    <div class="col-md-6"><div class="card"><div class="card-body">
        <div class="row">
            <div class="col-6"><div class="text-secondary small">Émetteur</div><div class="fw-semibold">{{ $document->serviceEmetteur->name }}</div></div>
            <div class="col-6"><div class="text-secondary small">Destinataire</div><div class="fw-semibold">{{ $document->serviceDestinataire->name }}</div></div>
            <div class="col-6 mt-2"><div class="text-secondary small">Date d'émission</div><div>{{ $document->date_emission->format('d/m/Y') }}</div></div>
            <div class="col-6 mt-2"><div class="text-secondary small">Validateur</div><div>{{ $document->validateur->name ?? '—' }}</div></div>
        </div>
    </div></div></div>
    <div class="col-md-6"><div class="card"><div class="card-body">
        <div class="text-secondary small">Description</div>
        <p class="mb-0">{{ $document->description_globale ?: '—' }}</p>
    </div></div></div>
</div>

<div class="card">
    <div class="card-header fw-semibold">Lignes</div>
    <div class="table-responsive">
        <table class="table mb-0 align-middle">
            <thead><tr><th>Description</th><th>Type</th><th>Détail</th><th class="text-end">Qté</th><th class="text-end">Tarif</th><th class="text-end">Montant</th></tr></thead>
            <tbody>
            @foreach($document->lignes as $l)
                <tr>
                    <td>{{ $l->description_ligne }}</td>
                    <td><span class="badge bg-secondary">{{ $l->type_prestation }}</span></td>
                    <td>{{ $l->type_prestation === 'Temps Interne' ? ($l->personne?->nomAffiche() ?? '—') : $l->description_achat }}</td>
                    <td class="text-end">{{ rtrim(rtrim(number_format($l->quantite, 2, ',', ' '), '0'), ',') }}</td>
                    <td class="text-end">{{ number_format($l->tarif_unitaire, 2, ',', ' ') }} €</td>
                    <td class="text-end fw-semibold">{{ number_format($l->montant_ligne, 2, ',', ' ') }} €</td>
                </tr>
            @endforeach
            </tbody>
            <tfoot><tr><td colspan="5" class="text-end fw-semibold">Total HT</td><td class="text-end fw-bold">{{ number_format($document->montant_total_ht, 2, ',', ' ') }} €</td></tr></tfoot>
        </table>
    </div>
</div>

@if($peutValider)
<div class="modal fade" id="refusModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('documents.refuser', $document) }}" class="modal-content">@csrf
            <div class="modal-header"><h5 class="modal-title">Refuser le document</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
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
