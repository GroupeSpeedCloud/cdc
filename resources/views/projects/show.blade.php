@extends('layouts.app')
@section('title', $project->name)
@section('page-title', $project->name)

@section('content')
@php
$monthNames = ['', 'Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun', 'Jul', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'];
$fullMonthNames = ['', 'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
@endphp

<div class="flex items-center gap-4 mb-6">
    <span class="w-5 h-5 rounded-full border-2 border-zinc-700" style="background-color: {{ $project->color }}"></span>
    @if($project->description)
        <p class="text-zinc-400 text-sm">{{ $project->description }}</p>
    @endif
    <span class="badge {{ $project->status === 'active' ? 'badge-green' : 'badge-zinc' }}">
        {{ $project->status === 'active' ? 'Actif' : 'Archivé' }}
    </span>
    <div class="ml-auto flex gap-2">
        <a href="{{ route('projects.edit', $project) }}" class="btn-secondary text-xs">Modifier</a>
    </div>
</div>

<!-- Stats -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="kpi-card">
        <p class="text-xs text-zinc-500 uppercase tracking-wider mb-1">Total cumulé</p>
        <p class="text-xl font-bold text-white">{{ number_format($project->getTotalRevenue(), 2, ',', ' ') }} €</p>
    </div>
    <div class="kpi-card">
        <p class="text-xs text-zinc-500 uppercase tracking-wider mb-1">Moy. mensuelle</p>
        <p class="text-xl font-bold text-white">{{ number_format($project->getAverageMonthlyRevenue(), 2, ',', ' ') }} €</p>
    </div>
    <div class="kpi-card">
        <p class="text-xs text-zinc-500 uppercase tracking-wider mb-1">Mois avec données</p>
        <p class="text-xl font-bold text-white">{{ $revenues->count() }}</p>
    </div>
</div>

<!-- Chart -->
<div class="card mb-6">
    <h3 class="text-sm font-semibold text-white mb-4">Revenus sur 12 mois glissants</h3>
    <canvas id="projectChart" height="120"></canvas>
</div>

<!-- Revenues table -->
<div class="card overflow-hidden p-0">
    <div class="px-6 py-4 border-b border-zinc-800 flex items-center justify-between">
        <h3 class="text-sm font-semibold text-white">Historique des revenus</h3>
        <a href="{{ route('revenues.index') }}" class="text-xs text-indigo-400 hover:text-indigo-300">Voir tous les revenus →</a>
    </div>
    <table class="w-full text-sm">
        <thead class="bg-zinc-800/50">
            <tr>
                <th class="text-left px-4 py-3 text-xs font-medium text-zinc-500 uppercase">Période</th>
                <th class="text-right px-4 py-3 text-xs font-medium text-zinc-500 uppercase">Montant</th>
                <th class="text-left px-4 py-3 text-xs font-medium text-zinc-500 uppercase">Notes</th>
            </tr>
        </thead>
        <tbody>
            @forelse($revenues as $rev)
            <tr class="table-row">
                <td class="px-4 py-3 text-white">{{ $fullMonthNames[$rev->month] }} {{ $rev->year }}</td>
                <td class="px-4 py-3 text-right text-green-400 font-medium">{{ number_format($rev->amount, 2, ',', ' ') }} €</td>
                <td class="px-4 py-3 text-zinc-400 text-xs">{{ $rev->notes ?? '—' }}</td>
            </tr>
            @empty
            <tr><td colspan="3" class="px-4 py-6 text-center text-zinc-500">Aucun revenu enregistré</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

@push('scripts')
<script>
const labels = @json($labels);
const dataset = @json($dataset);
const chartColor = '{{ $project->color }}';

new Chart(document.getElementById('projectChart'), {
    type: 'line',
    data: {
        labels: labels,
        datasets: [dataset ?? {
            label: '{{ $project->name }}',
            data: labels.map(() => 0),
            borderColor: chartColor,
            backgroundColor: chartColor + '33',
            tension: 0.3,
            fill: true,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            x: { grid: { color: '#27272a' }, ticks: { color: '#71717a', font: { size: 10 } } },
            y: { grid: { color: '#27272a' }, ticks: { color: '#71717a', font: { size: 10 } } }
        }
    }
});
</script>
@endpush
