@extends('layouts.app')
@section('title', 'Tableau de bord')
@section('page-title', 'Tableau de bord — Admin')

@section('content')
<div class="row g-3 mb-4">
    <div class="col-6 col-md">
        <div class="card"><div class="card-body">
            <div class="text-secondary small text-uppercase">Services</div>
            <div class="fs-4 fw-bold">{{ $stats['services'] }}</div>
        </div></div>
    </div>
    <div class="col-6 col-md">
        <div class="card"><div class="card-body">
            <div class="text-secondary small text-uppercase">Documents</div>
            <div class="fs-4 fw-bold">{{ $stats['documents'] }}</div>
        </div></div>
    </div>
    <div class="col-6 col-md">
        <div class="card"><div class="card-body">
            <div class="text-secondary small text-uppercase">En attente</div>
            <div class="fs-4 fw-bold text-warning">{{ $stats['en_attente'] }}</div>
        </div></div>
    </div>
    <div class="col-6 col-md">
        <div class="card"><div class="card-body">
            <div class="text-secondary small text-uppercase">Budget total</div>
            <div class="fs-4 fw-bold">{{ number_format($stats['budget_total'], 0, ',', ' ') }} €</div>
        </div></div>
    </div>
    <div class="col-6 col-md">
        <div class="card"><div class="card-body">
            <div class="text-secondary small text-uppercase">Budget restant</div>
            <div class="fs-4 fw-bold text-success">{{ number_format($stats['budget_restant'], 0, ',', ' ') }} €</div>
        </div></div>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-7">
        <div class="card h-100">
            <div class="card-header fw-semibold">Budgets des services</div>
            <div class="card-body"><canvas id="servicesChart" height="140"></canvas></div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card h-100">
            <div class="card-header fw-semibold">Dernières activités</div>
            <div class="list-group list-group-flush" style="max-height:340px;overflow:auto;">
                @forelse($activites as $doc)
                    <a href="{{ route('documents.show', $doc) }}" class="list-group-item list-group-item-action">
                        <div class="d-flex justify-content-between">
                            <span class="fw-semibold">{{ $doc->numero_document }}</span>
                            <span class="badge {{ $doc->statutBadgeClass() }}">{{ $doc->statut }}</span>
                        </div>
                        <div class="small text-secondary">{{ $doc->serviceEmetteur->name }} → {{ $doc->serviceDestinataire->name }} · {{ $doc->created_at->diffForHumans() }}</div>
                    </a>
                @empty
                    <div class="text-center text-secondary py-4">Aucune activité</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
new Chart(document.getElementById('servicesChart'), {
    type: 'bar',
    data: {
        labels: {!! json_encode($services->pluck('name')) !!},
        datasets: [
            { label: 'Budget initial', data: {!! json_encode($services->pluck('budget_annuel_courant')) !!}, backgroundColor: '#6366f1' },
            { label: 'Restant', data: {!! json_encode($services->pluck('budget_restant')) !!}, backgroundColor: '#10b981' }
        ]
    },
    options: { responsive: true, scales: { y: { beginAtZero: true } } }
});
</script>
@endpush
@endsection
