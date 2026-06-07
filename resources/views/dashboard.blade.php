@extends('layouts.app')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
@php
    $monthNames = ['', 'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
    $currentMonthName = $monthNames[$kpis['month']] . ' ' . $kpis['year'];
    $firstName = explode(' ', Auth::user()?->name ?? 'Utilisateur')[0];
    $yearPct = round(($kpis['month'] / 12) * 100);
@endphp

{{-- Section 1 — Hero --}}
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

{{-- Section 2 — KPIs du mois --}}
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:16px;margin-bottom:24px;">

    {{-- Revenus --}}
    <div class="kpi-card">
        <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:14px;">
            <span class="kpi-label">Revenus du mois</span>
            <div class="kpi-icon" style="background:rgba(99,102,241,0.12);">
                <svg fill="none" viewBox="0 0 24 24" stroke="#6366f1" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
            </div>
        </div>
        <div class="kpi-value">{{ number_format($kpis['revenue'], 2, ',', ' ') }} <span style="font-size:16px;font-weight:500;color:var(--text-3);">€</span></div>
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
        <div class="kpi-value">{{ number_format($kpis['expenses'], 2, ',', ' ') }} <span style="font-size:16px;font-weight:500;color:var(--text-3);">€</span></div>
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
            {{ $kpis['profit'] >= 0 ? '+' : '' }}{{ number_format($kpis['profit'], 2, ',', ' ') }} <span style="font-size:16px;font-weight:500;opacity:0.7;">€</span>
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

{{-- Section 3 — Vue annuelle --}}
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:24px;">

    {{-- Revenus YTD --}}
    <div style="background:var(--surface);border:1px solid var(--border);border-radius:12px;padding:20px;">
        <div style="font-size:11px;font-weight:600;letter-spacing:0.08em;text-transform:uppercase;color:var(--text-3);margin-bottom:12px;">
            REVENUS YTD
        </div>
        <div style="font-size:26px;font-weight:700;letter-spacing:-0.02em;color:var(--text);">
            {{ number_format($ytd['revenue'], 2, ',', ' ') }} <span style="font-size:15px;font-weight:500;color:var(--text-3);">€</span>
        </div>
        <div style="margin-top:14px;">
            <div style="display:flex;justify-content:space-between;font-size:11px;color:var(--text-3);margin-bottom:6px;">
                <span>Janvier → {{ $monthNames[$kpis['month']] }}</span>
                <span>{{ $yearPct }}% de l'année</span>
            </div>
            <div style="height:4px;background:var(--surface-2);border-radius:2px;">
                <div style="height:4px;background:var(--accent);border-radius:2px;width:{{ $yearPct }}%;"></div>
            </div>
        </div>
        @if($growthTrend != 0)
        <div style="margin-top:12px;display:flex;align-items:center;gap:4px;font-size:11px;{{ $growthTrend >= 0 ? 'color:#10b981' : 'color:#ef4444' }}">
            @if($growthTrend >= 0)
                <svg width="10" height="10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
            @else
                <svg width="10" height="10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
            @endif
            {{ $growthTrend >= 0 ? '+' : '' }}{{ $growthTrend }}% tendance 3 mois
        </div>
        @endif
    </div>

    {{-- Projection annuelle --}}
    <div style="background:var(--surface);border:1px solid var(--border);border-radius:12px;padding:20px;">
        <div style="font-size:11px;font-weight:600;letter-spacing:0.08em;text-transform:uppercase;color:var(--text-3);margin-bottom:12px;">
            PROJECTION ANNUELLE
        </div>
        <div style="font-size:26px;font-weight:700;letter-spacing:-0.02em;color:var(--text);">
            {{ number_format($projection, 2, ',', ' ') }} <span style="font-size:15px;font-weight:500;color:var(--text-3);">€</span>
        </div>
        <div style="font-size:11px;color:var(--text-3);margin-top:10px;display:flex;align-items:center;gap:6px;">
            <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" d="M12 8v4m0 4h.01"/></svg>
            Basé sur la moyenne des 3 derniers mois
        </div>
        <div style="margin-top:12px;font-size:11px;color:var(--text-3);">
            YTD : {{ number_format($ytd['revenue'], 2, ',', ' ') }} € +
            {{ number_format($projection - $ytd['revenue'], 2, ',', ' ') }} € projetés
        </div>
    </div>

    {{-- Top projet --}}
    <div style="background:var(--surface);border:1px solid var(--border);border-radius:12px;padding:20px;">
        <div style="font-size:11px;font-weight:600;letter-spacing:0.08em;text-transform:uppercase;color:var(--text-3);margin-bottom:12px;">
            TOP PROJET CE MOIS
        </div>
        @if($topProject)
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px;">
                <span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:{{ $topProject['project']->color }};flex-shrink:0;"></span>
                <span style="font-size:15px;font-weight:600;color:var(--text);">{{ $topProject['project']->name }}</span>
            </div>
            <div style="font-size:26px;font-weight:700;letter-spacing:-0.02em;color:var(--green);">
                {{ number_format($topProject['revenue'], 2, ',', ' ') }} <span style="font-size:15px;font-weight:500;opacity:0.7;">€</span>
            </div>
            <div style="margin-top:10px;font-size:11px;color:var(--text-3);">
                {{ $kpis['revenue'] > 0 ? round(($topProject['revenue'] / $kpis['revenue']) * 100) : 0 }}% des revenus du mois
            </div>
        @else
            <div style="font-size:13px;color:var(--text-3);margin-top:8px;">Aucune donnée ce mois</div>
        @endif
    </div>
</div>

{{-- Section 4 — Graphiques --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
    <div class="card-flush">
        <div class="card-header">
            <div>
                <div class="card-title">Revenus par projet</div>
                <div class="card-subtitle">12 mois glissants + projection</div>
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

{{-- Section 5 — Répartition + Tableau --}}
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
                    @php $isTop = $topProject && $topProject['project']->id === $item['project']->id && $item['revenue'] > 0; @endphp
                    <tr style="{{ $isTop ? 'background:rgba(16,185,129,0.06);' : '' }}">
                        <td>
                            <div style="display:flex;align-items:center;gap:10px;">
                                <span class="project-dot" style="background-color:{{ $item['project']->color }};"></span>
                                <span style="color:var(--text);font-weight:500;font-size:13px;">{{ $item['project']->name }}</span>
                                @if($isTop)
                                    <span style="font-size:9px;font-weight:600;letter-spacing:0.06em;background:rgba(16,185,129,0.15);color:#10b981;border-radius:4px;padding:2px 6px;text-transform:uppercase;">TOP</span>
                                @endif
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

// Build revenue chart with projection dataset
(function() {
    const base = @json($revenueChart);

    // Add projection dataset: null for past months, projected value for future months
    @php
        $currentMonth = $kpis['month'];
        $currentYear = $kpis['year'];
        // revenueChart has 12 months ending at current month
        // Index of current month in the 12-month window is index 11 (last)
        // Future months of the year need to be appended as labels + data
        $futureMonthNames = [];
        $monthNamesShort = ['', 'Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'];
        for ($m = $currentMonth + 1; $m <= 12; $m++) {
            $futureMonthNames[] = $monthNamesShort[$m] . ' ' . $currentYear;
        }
        $projectionValues = array_values($yearProjection);
    @endphp

    const futureLabels = @json($futureMonthNames);
    const projectionValues = @json($projectionValues);
    const currentMonthIdx = base.labels.length - 1; // last label is current month

    // Extend labels with future months
    const allLabels = [...base.labels, ...futureLabels];

    // Extend each project dataset with nulls for future months
    const extendedDatasets = base.datasets.map(ds => ({
        ...ds,
        data: [...ds.data, ...futureLabels.map(() => null)],
        spanGaps: false,
    }));

    // Build projection dataset: null for past 11 months + last historical as bridge + future values
    const projData = new Array(currentMonthIdx).fill(null);
    // bridge: show projection starting from current month value (average)
    const avgProjection = projectionValues.length > 0 ? projectionValues[0] : null;
    projData.push(avgProjection); // current month position as bridge
    projData.push(...projectionValues);

    extendedDatasets.push({
        label: 'Projection (moy. 3 mois)',
        data: projData,
        borderColor: '#888888',
        backgroundColor: 'rgba(136,136,136,0.05)',
        borderDash: [5, 4],
        borderWidth: 1.5,
        tension: 0,
        fill: false,
        pointRadius: 3,
        pointBackgroundColor: '#888888',
        spanGaps: false,
    });

    new Chart(document.getElementById('revenueChart'), {
        type: 'line',
        data: { labels: allLabels, datasets: extendedDatasets },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { labels: { color: '#888888', font: { size: 11 }, boxWidth: 10, padding: 12 } } },
            scales: chartScales
        }
    });
})();

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
