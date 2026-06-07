@extends('layouts.app')
@section('title', $project->name)
@section('page-title', $project->name)

@section('content')
@php
$monthNames = ['', 'Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun', 'Jul', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'];
$fullMonthNames = ['', 'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
@endphp

{{-- Project header --}}
<div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:28px;gap:16px;flex-wrap:wrap;">
    <div style="display:flex;align-items:center;gap:14px;">
        <div style="width:44px;height:44px;border-radius:12px;background:{{ $project->color }}22;border:2px solid {{ $project->color }}44;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <span style="width:14px;height:14px;border-radius:50%;background:{{ $project->color }};display:block;"></span>
        </div>
        <div>
            <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                <h1 style="font-size:20px;font-weight:700;letter-spacing:-0.02em;color:var(--text);">{{ $project->name }}</h1>
                @if($project->status === 'active')
                    <span class="badge badge-green">Actif</span>
                @else
                    <span class="badge badge-muted">Archivé</span>
                @endif
            </div>
            @if($project->description)
                <p style="font-size:13px;color:var(--text-3);margin-top:3px;">{{ $project->description }}</p>
            @endif
        </div>
    </div>
    <a href="{{ route('projects.edit', $project) }}" class="btn btn-secondary">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
        Modifier
    </a>
</div>

{{-- Stats KPIs --}}
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:16px;margin-bottom:24px;">
    <div class="kpi-card">
        <span class="kpi-label">Total cumulé</span>
        <div class="kpi-value" style="margin-top:10px;">{{ number_format($project->getTotalRevenue(), 0, ',', ' ') }} <span style="font-size:16px;font-weight:500;color:var(--text-3);">€</span></div>
    </div>
    <div class="kpi-card">
        <span class="kpi-label">Moy. mensuelle</span>
        <div class="kpi-value" style="margin-top:10px;">{{ number_format($project->getAverageMonthlyRevenue(), 0, ',', ' ') }} <span style="font-size:16px;font-weight:500;color:var(--text-3);">€</span></div>
    </div>
    <div class="kpi-card">
        <span class="kpi-label">Mois avec données</span>
        <div class="kpi-value" style="margin-top:10px;">{{ $revenues->count() }}</div>
    </div>
</div>

{{-- Chart --}}
<div class="card-flush" style="margin-bottom:16px;">
    <div class="card-header">
        <div>
            <div class="card-title">Revenus — {{ $project->name }}</div>
            <div class="card-subtitle">12 mois glissants</div>
        </div>
    </div>
    <div style="padding:20px;">
        <div class="chart-wrap">
            <canvas id="projectChart"></canvas>
        </div>
    </div>
</div>

{{-- Revenues table --}}
<div class="card-flush">
    <div class="card-header">
        <div>
            <div class="card-title">Historique des revenus</div>
        </div>
        <a href="{{ route('revenues.index') }}" style="font-size:12px;color:var(--accent);text-decoration:none;" onmouseover="this.style.opacity=0.7" onmouseout="this.style.opacity=1">Voir tous →</a>
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Période</th>
                <th class="text-right">Montant</th>
                <th>Notes</th>
            </tr>
        </thead>
        <tbody>
            @forelse($revenues as $rev)
            <tr>
                <td style="color:var(--text);font-weight:500;">{{ $fullMonthNames[$rev->month] }} {{ $rev->year }}</td>
                <td class="text-right" style="color:var(--green);font-weight:600;">{{ number_format($rev->amount, 2, ',', ' ') }} €</td>
                <td style="font-size:12px;color:var(--text-3);">{{ $rev->notes ?? '—' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="3" style="text-align:center;padding:40px 16px;color:var(--text-3);">Aucun revenu enregistré</td>
            </tr>
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
            backgroundColor: chartColor + '22',
            tension: 0.3,
            fill: true,
            pointBackgroundColor: chartColor,
            pointRadius: 3,
            pointHoverRadius: 5,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            x: { grid: { color: 'rgba(255,255,255,0.03)' }, ticks: { color: '#555555', font: { size: 10 } }, border: { color: '#1e1e1e' } },
            y: { grid: { color: 'rgba(255,255,255,0.03)' }, ticks: { color: '#555555', font: { size: 10 } }, border: { color: '#1e1e1e' } }
        }
    }
});
</script>
@endpush
