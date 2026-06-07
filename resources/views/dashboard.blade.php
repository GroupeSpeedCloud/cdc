@extends('layouts.app')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
@php
    $monthNames = ['', 'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
    $currentMonthName = $monthNames[$kpis['month']] . ' ' . $kpis['year'];
    $firstName = explode(' ', Auth::user()?->name ?? 'Utilisateur')[0];
@endphp

{{-- Hero header --}}
<div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:28px;gap:16px;flex-wrap:wrap;">
    <div>
        <h1 style="font-size:24px;font-weight:700;letter-spacing:-0.03em;color:var(--text);margin-bottom:4px;">
            Bonjour, {{ $firstName }} 👋
        </h1>
        <p style="font-size:13px;color:var(--text-3);">Vue d'ensemble — {{ $currentMonthName }}</p>
    </div>
    <a href="{{ route('revenues.edit', [$kpis['year'], $kpis['month']]) }}" class="btn btn-primary">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        Saisir les revenus
    </a>
</div>

{{-- KPI Grid --}}
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:16px;margin-bottom:24px;">

    {{-- Revenus --}}
    <div class="kpi-card">
        <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:14px;">
            <span class="kpi-label">Revenus du mois</span>
            <div class="kpi-icon" style="background:rgba(99,102,241,0.12);">
                <svg fill="none" viewBox="0 0 24 24" stroke="#6366f1" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
            </div>
        </div>
        <div class="kpi-value">{{ number_format($kpis['revenue'], 0, ',', ' ') }} <span style="font-size:16px;font-weight:500;color:var(--text-3);">€</span></div>
        @if($kpis['revenue_diff_pct'] !== null)
            <div style="margin-top:10px;display:flex;align-items:center;gap:4px;font-size:12px;{{ $kpis['revenue_diff_pct'] >= 0 ? 'color:#10b981' : 'color:#ef4444' }}">
                @if($kpis['revenue_diff_pct'] >= 0)
                    <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                @else
                    <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                @endif
                {{ $kpis['revenue_diff_pct'] >= 0 ? '+' : '' }}{{ $kpis['revenue_diff_pct'] }}% vs mois préc.
            </div>
        @else
            <div class="kpi-sub">Premier enregistrement</div>
        @endif
    </div>

    {{-- Dépenses --}}
    <div class="kpi-card">
        <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:14px;">
            <span class="kpi-label">Dépenses du mois</span>
            <div class="kpi-icon" style="background:rgba(239,68,68,0.1);">
                <svg fill="none" viewBox="0 0 24 24" stroke="#ef4444" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
        </div>
        <div class="kpi-value">{{ number_format($kpis['expenses'], 0, ',', ' ') }} <span style="font-size:16px;font-weight:500;color:var(--text-3);">€</span></div>
        <div class="kpi-sub">Dépenses récurrentes actives</div>
    </div>

    {{-- Profit --}}
    <div class="kpi-card">
        <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:14px;">
            <span class="kpi-label">Profit net</span>
            <div class="kpi-icon" style="background:{{ $kpis['profit'] >= 0 ? 'rgba(16,185,129,0.1)' : 'rgba(239,68,68,0.1)' }};">
                <svg fill="none" viewBox="0 0 24 24" stroke="{{ $kpis['profit'] >= 0 ? '#10b981' : '#ef4444' }}" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
        <div class="kpi-value" style="color:{{ $kpis['profit'] >= 0 ? 'var(--green)' : 'var(--red)' }}">
            {{ $kpis['profit'] >= 0 ? '+' : '' }}{{ number_format($kpis['profit'], 0, ',', ' ') }} <span style="font-size:16px;font-weight:500;opacity:0.7;">€</span>
        </div>
        <div class="kpi-sub">Revenus − Dépenses</div>
    </div>

    {{-- Marge --}}
    <div class="kpi-card">
        <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:14px;">
            <span class="kpi-label">Marge nette</span>
            <div class="kpi-icon" style="background:rgba(245,158,11,0.1);">
                <svg fill="none" viewBox="0 0 24 24" stroke="#f59e0b" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            </div>
        </div>
        <div class="kpi-value">{{ $kpis['margin'] }}<span style="font-size:18px;font-weight:500;color:var(--text-3);">%</span></div>
        <div class="kpi-sub">Sur les revenus du mois</div>
    </div>
</div>

{{-- Charts row --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
    <div class="card-flush">
        <div class="card-header">
            <div>
                <div class="card-title">Revenus par projet</div>
                <div class="card-subtitle">12 mois glissants</div>
            </div>
        </div>
        <div style="padding:20px;">
            <div class="chart-wrap">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>
    </div>
    <div class="card-flush">
        <div class="card-header">
            <div>
                <div class="card-title">Cashflow</div>
                <div class="card-subtitle">12 mois glissants</div>
            </div>
        </div>
        <div style="padding:20px;">
            <div class="chart-wrap">
                <canvas id="cashflowChart"></canvas>
            </div>
        </div>
    </div>
</div>

{{-- Chart + table row --}}
<div style="display:grid;grid-template-columns:1fr 2fr;gap:16px;">
    <div class="card-flush">
        <div class="card-header">
            <div>
                <div class="card-title">Dépenses</div>
                <div class="card-subtitle">Répartition par catégorie</div>
            </div>
        </div>
        <div style="padding:20px;">
            <div class="chart-wrap">
                <canvas id="expensesChart"></canvas>
            </div>
        </div>
    </div>

    <div class="card-flush">
        <div class="card-header">
            <div>
                <div class="card-title">Revenus ce mois</div>
                <div class="card-subtitle">{{ $currentMonthName }}</div>
            </div>
            <a href="{{ route('revenues.edit', [$kpis['year'], $kpis['month']]) }}" style="font-size:12px;color:var(--accent);text-decoration:none;transition:opacity 0.15s;" onmouseover="this.style.opacity=0.7" onmouseout="this.style.opacity=1">
                Modifier →
            </a>
        </div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Projet</th>
                    <th class="text-right">Montant</th>
                </tr>
            </thead>
            <tbody>
                @forelse($projects as $item)
                    <tr>
                        <td>
                            <div style="display:flex;align-items:center;gap:10px;">
                                <span class="project-dot" style="background-color:{{ $item['project']->color }};"></span>
                                <span style="color:var(--text);font-weight:500;font-size:13px;">{{ $item['project']->name }}</span>
                            </div>
                        </td>
                        <td class="text-right">
                            @if($item['revenue'] > 0)
                                <span style="color:var(--green);font-weight:600;font-size:13px;">{{ number_format($item['revenue'], 2, ',', ' ') }} €</span>
                            @else
                                <span style="color:var(--text-3);">—</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" style="text-align:center;padding:32px 16px;color:var(--text-3);">Aucun projet actif</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
const chartScales = {
    x: {
        grid: { color: 'rgba(255,255,255,0.03)' },
        ticks: { color: '#555555', font: { size: 10, family: '-apple-system,BlinkMacSystemFont,Inter,sans-serif' } },
        border: { color: '#1e1e1e' }
    },
    y: {
        grid: { color: 'rgba(255,255,255,0.03)' },
        ticks: { color: '#555555', font: { size: 10, family: '-apple-system,BlinkMacSystemFont,Inter,sans-serif' } },
        border: { color: '#1e1e1e' }
    }
};

new Chart(document.getElementById('revenueChart'), {
    type: 'line',
    data: @json($revenueChart),
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { labels: { color: '#888888', font: { size: 11 }, boxWidth: 10, padding: 12 } } },
        scales: chartScales
    }
});

new Chart(document.getElementById('cashflowChart'), {
    type: 'bar',
    data: @json($cashflowChart),
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { labels: { color: '#888888', font: { size: 11 }, boxWidth: 10, padding: 12 } } },
        scales: chartScales
    }
});

new Chart(document.getElementById('expensesChart'), {
    type: 'doughnut',
    data: @json($expensesChart),
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { position: 'bottom', labels: { color: '#888888', font: { size: 10 }, padding: 10, boxWidth: 10 } }
        }
    }
});
</script>
@endpush
