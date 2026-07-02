@extends('layouts.app')
@section('title', 'Rapports')
@section('page-title', 'Rapports')

@section('content')
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            @unless($filtres['restreint'] ?? false)
            <div class="col-md-3">
                <label class="form-label small">Service</label>
                <select name="service_id" class="form-select form-select-sm">
                    <option value="">Tous</option>
                    @foreach($services as $s)
                        <option value="{{ $s->id }}" @selected(($filtres['service_id'] ?? null) == $s->id)>{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>
            @endunless
            <div class="col-md-2">
                <label class="form-label small">Du</label>
                <input type="date" name="date_debut" class="form-control form-control-sm" value="{{ $filtres['date_debut'] }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small">Au</label>
                <input type="date" name="date_fin" class="form-control form-control-sm" value="{{ $filtres['date_fin'] }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small">Statut</label>
                <select name="statut" class="form-select form-select-sm">
                    <option value="">Tous</option>
                    @foreach($statuts as $s)
                        <option value="{{ $s }}" @selected(($filtres['statut'] ?? null) === $s)>{{ $s }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small">Personne</label>
                <select name="personne_id" class="form-select form-select-sm">
                    <option value="">Toutes</option>
                    @foreach($personnes as $p)
                        <option value="{{ $p->id }}" @selected(($filtres['personne_id'] ?? null) == $p->id)>{{ $p->nomAffiche() }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-1 d-flex gap-1">
                <button class="btn btn-sm btn-primary"><i class="bi bi-funnel"></i></button>
            </div>
        </form>
        <div class="mt-2">
            <a href="{{ route('reports.export', request()->query()) }}" class="btn btn-sm btn-outline-success"><i class="bi bi-file-earmark-excel"></i> Exporter en Excel</a>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-7">
        <div class="card h-100">
            <div class="card-header fw-semibold">Coûts par service (émis vs reçus)</div>
            <div class="table-responsive">
                <table class="table mb-0 align-middle">
                    <thead><tr><th>Service</th><th class="text-end">Émis</th><th class="text-end">Reçus</th><th class="text-end">Budget</th><th class="text-end">Dépensé</th><th class="text-end">Restant</th></tr></thead>
                    <tbody>
                    @foreach($coutsParService as $c)
                        <tr>
                            <td>{{ $c['service']->name }}</td>
                            <td class="text-end">{{ number_format($c['emis'], 0, ',', ' ') }} €</td>
                            <td class="text-end">{{ number_format($c['recus'], 0, ',', ' ') }} €</td>
                            <td class="text-end">{{ number_format($c['budget_initial'], 0, ',', ' ') }} €</td>
                            <td class="text-end">{{ number_format($c['depense'], 0, ',', ' ') }} €</td>
                            <td class="text-end">{{ number_format($c['budget_restant'], 0, ',', ' ') }} €</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card mb-3">
            <div class="card-header fw-semibold">Suivi budgétaire</div>
            <div class="card-body"><canvas id="budgetReportChart" height="200"></canvas></div>
        </div>
    </div>
</div>

<div class="card mt-3">
    <div class="card-header fw-semibold">Temps passé par personne</div>
    <div class="table-responsive">
        <table class="table mb-0 align-middle">
            <thead><tr><th>Personne</th><th>Service</th><th class="text-end">Heures</th><th class="text-end">Montant</th></tr></thead>
            <tbody>
            @forelse($tempsParPersonne as $t)
                <tr>
                    <td>{{ $t['personne']?->nomAffiche() ?? '—' }}</td>
                    <td>{{ $t['personne']?->service?->name ?? '—' }}</td>
                    <td class="text-end">{{ number_format($t['heures'], 2, ',', ' ') }} h</td>
                    <td class="text-end">{{ number_format($t['montant'], 2, ',', ' ') }} €</td>
                </tr>
            @empty
                <tr><td colspan="4" class="text-center text-secondary py-4">Aucune donnée</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script>
new Chart(document.getElementById('budgetReportChart'), {
    type: 'bar',
    data: {
        labels: {!! json_encode($coutsParService->pluck('service.name')) !!},
        datasets: [
            { label: 'Budget initial', data: {!! json_encode($coutsParService->pluck('budget_initial')) !!}, backgroundColor: '#6366f1' },
            { label: 'Dépensé', data: {!! json_encode($coutsParService->pluck('depense')) !!}, backgroundColor: '#ef4444' }
        ]
    },
    options: { indexAxis: 'y', scales: { x: { beginAtZero: true } } }
});
</script>
@endpush
@endsection
