@extends('layouts.app')
@section('title', 'Tableau de bord')
@section('page-title', 'Tableau de bord')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Bonjour, {{ auth()->user()->name }} 👋</h4>
    <a href="{{ route('documents.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Créer un document</a>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card"><div class="card-body">
            <div class="text-secondary small text-uppercase">Total documents</div>
            <div class="fs-3 fw-bold">{{ $stats['total'] }}</div>
        </div></div>
    </div>
    <div class="col-md-4">
        <div class="card"><div class="card-body">
            <div class="text-secondary small text-uppercase">En attente</div>
            <div class="fs-3 fw-bold text-warning">{{ $stats['en_attente'] }}</div>
        </div></div>
    </div>
    <div class="col-md-4">
        <div class="card"><div class="card-body">
            <div class="text-secondary small text-uppercase">Validés</div>
            <div class="fs-3 fw-bold text-success">{{ $stats['valides'] }}</div>
        </div></div>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-7">
        <div class="card h-100">
            <div class="card-header fw-semibold">Mes 5 derniers documents</div>
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead><tr><th>N°</th><th>Destinataire</th><th>Montant</th><th>Statut</th></tr></thead>
                    <tbody>
                    @forelse($derniers as $doc)
                        <tr onclick="window.location='{{ route('documents.show', $doc) }}'" style="cursor:pointer;">
                            <td>{{ $doc->numero_document }}</td>
                            <td>{{ $doc->serviceDestinataire->name }}</td>
                            <td>{{ number_format($doc->montant_total_ht, 2, ',', ' ') }} €</td>
                            <td><span class="badge {{ $doc->statutBadgeClass() }}">{{ $doc->statut }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-secondary py-4">Aucun document</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card h-100">
            <div class="card-header fw-semibold">Mes brouillons ({{ $brouillons->count() }})</div>
            <div class="list-group list-group-flush">
                @forelse($brouillons as $doc)
                    <a href="{{ route('documents.edit', $doc) }}" class="list-group-item list-group-item-action d-flex justify-content-between">
                        <span>{{ $doc->numero_document }} — {{ $doc->serviceDestinataire->name }}</span>
                        <span class="text-secondary">{{ number_format($doc->montant_total_ht, 0, ',', ' ') }} €</span>
                    </a>
                @empty
                    <div class="text-center text-secondary py-4">Aucun brouillon</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
