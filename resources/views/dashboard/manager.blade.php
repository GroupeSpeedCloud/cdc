@extends('layouts.app')
@section('title', 'Tableau de bord')
@section('page-title', 'Tableau de bord — Manager')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Service : {{ $service?->name ?? 'Aucun service assigné' }}</h4>
    <a href="{{ route('documents.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Créer un document</a>
</div>

<div class="row g-3">
    <div class="col-lg-7">
        <div class="card h-100">
            <div class="card-header fw-semibold"><i class="bi bi-hourglass-split"></i> Documents en attente de ma validation</div>
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead><tr><th>N°</th><th>Émetteur</th><th>Demandeur</th><th>Montant</th><th></th></tr></thead>
                    <tbody>
                    @forelse($enAttente as $doc)
                        <tr>
                            <td>{{ $doc->numero_document }}</td>
                            <td>{{ $doc->serviceEmetteur->name }}</td>
                            <td>{{ $doc->demandeur->name }}</td>
                            <td>{{ number_format($doc->montant_total_ht, 2, ',', ' ') }} €</td>
                            <td><a href="{{ route('documents.show', $doc) }}" class="btn btn-sm btn-primary">Examiner</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-secondary py-4">Aucun document en attente</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="fw-semibold">Budget du service</span>
                @if($service)
                    <a href="{{ route('services.compte', $service) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-bank"></i> Voir le compte</a>
                @endif
            </div>
            <div class="card-body">
                @if($service)
                    <canvas id="budgetChart" height="180"></canvas>
                    <div class="d-flex justify-content-between mt-3 small">
                        <span class="text-secondary">Restant</span>
                        <strong>{{ number_format($service->budget_restant, 2, ',', ' ') }} € / {{ number_format($service->budget_annuel_courant, 2, ',', ' ') }} €</strong>
                    </div>
                    @if($service->excedent() > 0)
                        <div class="d-flex justify-content-between small">
                            <span class="text-secondary">Dont crédité par d'autres services</span>
                            <strong class="text-success">+{{ number_format($service->excedent(), 2, ',', ' ') }} €</strong>
                        </div>
                    @endif
                @else
                    <p class="text-secondary mb-0">Aucun service assigné.</p>
                @endif
            </div>
        </div>
    </div>
</div>

@if($service)
@push('scripts')
<script>
@php
    $initial = (float) $service->budget_annuel_courant;
    $restant = (float) $service->budget_restant;
    $depense = max(0, $initial - $restant);
    $restantDansBudget = max(0, min($restant, $initial));
    $excedent = $service->excedent();
@endphp
new Chart(document.getElementById('budgetChart'), {
    type: 'doughnut',
    data: {
        labels: ['Dépensé', 'Restant', 'Crédité (excédent)'],
        datasets: [{
            data: [{{ $depense }}, {{ $restantDansBudget }}, {{ $excedent }}],
            backgroundColor: ['#ef4444', '#10b981', '#8a4dfd']
        }]
    },
    options: { plugins: { legend: { position: 'bottom' } } }
});
</script>
@endpush
@endif
@endsection
