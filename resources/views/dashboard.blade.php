@extends('layouts.app')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
@php
    $monthNames = ['', 'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
    $currentMonthName = $monthNames[$kpis['month']] . ' ' . $kpis['year'];
@endphp

<div class="mb-6 flex items-center justify-between">
    <p class="text-zinc-400 text-sm">{{ $currentMonthName }}</p>
    <a href="{{ route('revenues.edit', [$kpis['year'], $kpis['month']]) }}" class="btn-primary">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Saisir les revenus
    </a>
</div>

<!-- KPI Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <!-- Revenus -->
    <div class="kpi-card">
        <div class="flex items-center justify-between mb-3">
            <p class="text-xs font-medium text-zinc-500 uppercase tracking-wider">Revenus du mois</p>
            <div class="w-8 h-8 bg-indigo-600/20 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-white">{{ number_format($kpis['revenue'], 2, ',', ' ') }} €</p>
        @if($kpis['revenue_diff_pct'] !== null)
            <p class="text-xs mt-1 {{ $kpis['revenue_diff_pct'] >= 0 ? 'text-green-400' : 'text-red-400' }}">
                {{ $kpis['revenue_diff_pct'] >= 0 ? '+' : '' }}{{ $kpis['revenue_diff_pct'] }}% vs mois préc.
            </p>
        @endif
    </div>

    <!-- Dépenses -->
    <div class="kpi-card">
        <div class="flex items-center justify-between mb-3">
            <p class="text-xs font-medium text-zinc-500 uppercase tracking-wider">Dépenses du mois</p>
            <div class="w-8 h-8 bg-red-600/20 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-white">{{ number_format($kpis['expenses'], 2, ',', ' ') }} €</p>
        <p class="text-xs mt-1 text-zinc-500">Dépenses récurrentes actives</p>
    </div>

    <!-- Profit net -->
    <div class="kpi-card">
        <div class="flex items-center justify-between mb-3">
            <p class="text-xs font-medium text-zinc-500 uppercase tracking-wider">Profit net</p>
            <div class="w-8 h-8 {{ $kpis['profit'] >= 0 ? 'bg-green-600/20' : 'bg-red-600/20' }} rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 {{ $kpis['profit'] >= 0 ? 'text-green-400' : 'text-red-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
        <p class="text-2xl font-bold {{ $kpis['profit'] >= 0 ? 'text-green-400' : 'text-red-400' }}">
            {{ $kpis['profit'] >= 0 ? '+' : '' }}{{ number_format($kpis['profit'], 2, ',', ' ') }} €
        </p>
        <p class="text-xs mt-1 text-zinc-500">Revenus - Dépenses</p>
    </div>

    <!-- Marge -->
    <div class="kpi-card">
        <div class="flex items-center justify-between mb-3">
            <p class="text-xs font-medium text-zinc-500 uppercase tracking-wider">Marge nette</p>
            <div class="w-8 h-8 bg-amber-600/20 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-white">{{ $kpis['margin'] }}%</p>
        <p class="text-xs mt-1 text-zinc-500">Sur les revenus du mois</p>
    </div>
</div>

<!-- Charts row 1 -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <div class="card">
        <h3 class="text-sm font-semibold text-white mb-4">Revenus par projet — 12 mois glissants</h3>
        <canvas id="revenueChart" height="250"></canvas>
    </div>
    <div class="card">
        <h3 class="text-sm font-semibold text-white mb-4">Cashflow — 12 mois glissants</h3>
        <canvas id="cashflowChart" height="250"></canvas>
    </div>
</div>

<!-- Charts row 2 + Table -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="card">
        <h3 class="text-sm font-semibold text-white mb-4">Dépenses par catégorie</h3>
        <canvas id="expensesChart" height="250"></canvas>
    </div>

    <div class="card lg:col-span-2 p-0 overflow-hidden">
        <div class="px-6 py-4 border-b border-zinc-800 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-white">Revenus ce mois — {{ $currentMonthName }}</h3>
            <a href="{{ route('revenues.edit', [$kpis['year'], $kpis['month']]) }}" class="text-xs text-indigo-400 hover:text-indigo-300">Modifier →</a>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-zinc-800/50">
                <tr>
                    <th class="text-left px-4 py-3 text-xs font-medium text-zinc-500 uppercase">Projet</th>
                    <th class="text-right px-4 py-3 text-xs font-medium text-zinc-500 uppercase">Montant</th>
                </tr>
            </thead>
            <tbody>
                @forelse($projects as $item)
                    <tr class="table-row">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full flex-shrink-0" style="background-color: {{ $item['project']->color }}"></span>
                                <span class="text-white font-medium">{{ $item['project']->name }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-right">
                            @if($item['revenue'] > 0)
                                <span class="text-green-400 font-medium">{{ number_format($item['revenue'], 2, ',', ' ') }} €</span>
                            @else
                                <span class="text-zinc-600">—</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="2" class="px-4 py-6 text-center text-zinc-500">Aucun projet actif</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
const chartDefaults = {
    plugins: { legend: { labels: { color: '#a1a1aa', font: { size: 11 } } } },
    scales: {
        x: { grid: { color: '#27272a' }, ticks: { color: '#71717a', font: { size: 10 } } },
        y: { grid: { color: '#27272a' }, ticks: { color: '#71717a', font: { size: 10 } } }
    }
};

// Revenue chart
const revenueData = @json($revenueChart);
new Chart(document.getElementById('revenueChart'), {
    type: 'line',
    data: revenueData,
    options: { ...chartDefaults, responsive: true, maintainAspectRatio: false }
});

// Cashflow chart
const cashflowData = @json($cashflowChart);
new Chart(document.getElementById('cashflowChart'), {
    type: 'bar',
    data: cashflowData,
    options: { ...chartDefaults, responsive: true, maintainAspectRatio: false }
});

// Expenses doughnut
const expensesData = @json($expensesChart);
new Chart(document.getElementById('expensesChart'), {
    type: 'doughnut',
    data: expensesData,
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { position: 'bottom', labels: { color: '#a1a1aa', font: { size: 10 }, padding: 10 } }
        }
    }
});
</script>
@endpush
