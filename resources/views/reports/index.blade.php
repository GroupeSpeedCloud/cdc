@extends('layouts.app')
@section('title', 'Rapport annuel')
@section('page-title', 'Rapport annuel')

@section('content')
@php
$monthNames = ['', 'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
@endphp

<div class="flex items-center gap-4 mb-6">
    <form method="GET" class="flex items-center gap-2">
        <label class="text-sm text-zinc-400">Année :</label>
        <select name="year" onchange="this.form.submit()" class="input w-auto">
            @foreach($years as $y)
                <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>{{ $y }}</option>
            @endforeach
        </select>
    </form>
</div>

<!-- Summary KPIs -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
    <div class="kpi-card">
        <p class="text-xs text-zinc-500 uppercase tracking-wider mb-1">Revenus totaux {{ $year }}</p>
        <p class="text-xl font-bold text-white">{{ number_format($summary['total_revenue'], 2, ',', ' ') }} €</p>
    </div>
    <div class="kpi-card">
        <p class="text-xs text-zinc-500 uppercase tracking-wider mb-1">Dépenses totales {{ $year }}</p>
        <p class="text-xl font-bold text-white">{{ number_format($summary['total_expenses'], 2, ',', ' ') }} €</p>
    </div>
    <div class="kpi-card">
        <p class="text-xs text-zinc-500 uppercase tracking-wider mb-1">Profit net {{ $year }}</p>
        <p class="text-xl font-bold {{ $summary['total_profit'] >= 0 ? 'text-green-400' : 'text-red-400' }}">
            {{ $summary['total_profit'] >= 0 ? '+' : '' }}{{ number_format($summary['total_profit'], 2, ',', ' ') }} €
        </p>
    </div>
    <div class="kpi-card">
        <p class="text-xs text-zinc-500 uppercase tracking-wider mb-1">Marge moyenne</p>
        <p class="text-xl font-bold text-white">{{ $summary['avg_margin'] }}%</p>
    </div>
</div>

<!-- Monthly table -->
<div class="card overflow-hidden p-0">
    <table class="w-full text-sm">
        <thead class="bg-zinc-800/50">
            <tr>
                <th class="text-left px-4 py-3 text-xs font-medium text-zinc-500 uppercase">Mois</th>
                <th class="text-right px-4 py-3 text-xs font-medium text-zinc-500 uppercase">Revenus</th>
                <th class="text-right px-4 py-3 text-xs font-medium text-zinc-500 uppercase">Dépenses</th>
                <th class="text-right px-4 py-3 text-xs font-medium text-zinc-500 uppercase">Profit net</th>
                <th class="text-right px-4 py-3 text-xs font-medium text-zinc-500 uppercase">Marge</th>
                <th class="text-center px-4 py-3 text-xs font-medium text-zinc-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($summary['months'] as $m => $data)
            <tr class="table-row">
                <td class="px-4 py-3 text-white font-medium">{{ $monthNames[$m] }} {{ $year }}</td>
                <td class="px-4 py-3 text-right">
                    @if($data['revenue'] > 0)
                        <span class="text-green-400">{{ number_format($data['revenue'], 2, ',', ' ') }} €</span>
                    @else
                        <span class="text-zinc-600">—</span>
                    @endif
                </td>
                <td class="px-4 py-3 text-right">
                    @if($data['expenses'] > 0)
                        <span class="text-red-400">{{ number_format($data['expenses'], 2, ',', ' ') }} €</span>
                    @else
                        <span class="text-zinc-600">—</span>
                    @endif
                </td>
                <td class="px-4 py-3 text-right">
                    @if($data['revenue'] > 0 || $data['expenses'] > 0)
                        <span class="{{ $data['profit'] >= 0 ? 'text-green-400' : 'text-red-400' }} font-medium">
                            {{ $data['profit'] >= 0 ? '+' : '' }}{{ number_format($data['profit'], 2, ',', ' ') }} €
                        </span>
                    @else
                        <span class="text-zinc-600">—</span>
                    @endif
                </td>
                <td class="px-4 py-3 text-right">
                    @if($data['revenue'] > 0)
                        <span class="{{ $data['margin'] >= 0 ? 'text-green-400' : 'text-red-400' }}">{{ $data['margin'] }}%</span>
                    @else
                        <span class="text-zinc-600">—</span>
                    @endif
                </td>
                <td class="px-4 py-3 text-center">
                    <a href="{{ route('revenues.edit', [$year, $m]) }}" class="text-xs text-indigo-400 hover:text-indigo-300">Revenus</a>
                    <span class="text-zinc-700 mx-1">|</span>
                    <a href="{{ route('expenses.override', [$year, $m]) }}" class="text-xs text-zinc-400 hover:text-zinc-300">Dépenses</a>
                </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot class="bg-zinc-800/30 border-t border-zinc-700">
            <tr>
                <td class="px-4 py-3 text-xs font-bold text-zinc-300 uppercase">Total {{ $year }}</td>
                <td class="px-4 py-3 text-right font-bold text-green-400">{{ number_format($summary['total_revenue'], 2, ',', ' ') }} €</td>
                <td class="px-4 py-3 text-right font-bold text-red-400">{{ number_format($summary['total_expenses'], 2, ',', ' ') }} €</td>
                <td class="px-4 py-3 text-right font-bold {{ $summary['total_profit'] >= 0 ? 'text-green-400' : 'text-red-400' }}">
                    {{ $summary['total_profit'] >= 0 ? '+' : '' }}{{ number_format($summary['total_profit'], 2, ',', ' ') }} €
                </td>
                <td class="px-4 py-3 text-right font-bold text-white">{{ $summary['avg_margin'] }}%</td>
                <td></td>
            </tr>
        </tfoot>
    </table>
</div>
@endsection
