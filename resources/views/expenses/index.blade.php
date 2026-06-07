@extends('layouts.app')
@section('title', 'Dépenses')
@section('page-title', 'Dépenses récurrentes')

@section('content')
@php
$categoryLabels = [
    'personnel' => 'Personnel',
    'hebergement' => 'Hébergement',
    'infrastructure' => 'Infrastructure',
    'marketing' => 'Marketing',
    'locaux' => 'Locaux',
    'autre' => 'Autre',
];
$categoryBadge = [
    'personnel' => 'badge-indigo',
    'hebergement' => 'badge-green',
    'infrastructure' => 'badge-yellow',
    'marketing' => 'badge-red',
    'locaux' => 'badge-indigo',
    'autre' => 'badge-zinc',
];
@endphp

<div class="flex items-center justify-between mb-6">
    <p class="text-zinc-400 text-sm">
        {{ $expenses->count() }} dépense(s) récurrente(s) —
        Total estimé ce mois : <span class="text-white font-medium">{{ number_format($totalMonthly, 2, ',', ' ') }} €</span>
    </p>
    <div class="flex gap-2">
        <a href="{{ route('expenses.override', [$now->year, $now->month]) }}" class="btn-secondary">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            Overrides du mois
        </a>
        <a href="{{ route('expenses.create') }}" class="btn-primary">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Nouvelle dépense
        </a>
    </div>
</div>

<div class="card overflow-hidden p-0">
    <table class="w-full text-sm">
        <thead class="bg-zinc-800/50">
            <tr>
                <th class="text-left px-4 py-3 text-xs font-medium text-zinc-500 uppercase">Nom</th>
                <th class="text-left px-4 py-3 text-xs font-medium text-zinc-500 uppercase">Catégorie</th>
                <th class="text-right px-4 py-3 text-xs font-medium text-zinc-500 uppercase">Montant</th>
                <th class="text-left px-4 py-3 text-xs font-medium text-zinc-500 uppercase">Période</th>
                <th class="text-center px-4 py-3 text-xs font-medium text-zinc-500 uppercase">Ce mois</th>
                <th class="text-right px-4 py-3 text-xs font-medium text-zinc-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($expenses as $expense)
            @php
                $monthAmount = $expense->getAmountForMonth($now->year, $now->month);
            @endphp
            <tr class="table-row">
                <td class="px-4 py-3">
                    <span class="font-medium text-white">{{ $expense->name }}</span>
                    @if($expense->notes)
                        <p class="text-xs text-zinc-500 mt-0.5">{{ $expense->notes }}</p>
                    @endif
                </td>
                <td class="px-4 py-3">
                    <span class="badge {{ $categoryBadge[$expense->category] ?? 'badge-zinc' }}">
                        {{ $categoryLabels[$expense->category] ?? $expense->category }}
                    </span>
                </td>
                <td class="px-4 py-3 text-right text-white font-medium">{{ number_format($expense->amount, 2, ',', ' ') }} €</td>
                <td class="px-4 py-3 text-zinc-400 text-xs">
                    {{ \Carbon\Carbon::parse($expense->start_month)->format('m/Y') }}
                    @if($expense->end_month)
                        → {{ \Carbon\Carbon::parse($expense->end_month)->format('m/Y') }}
                    @else
                        → ∞
                    @endif
                </td>
                <td class="px-4 py-3 text-center">
                    @if($monthAmount !== null)
                        <span class="badge badge-green">{{ number_format($monthAmount, 0, ',', ' ') }} €</span>
                    @else
                        <span class="badge badge-zinc">Inactif</span>
                    @endif
                </td>
                <td class="px-4 py-3 text-right">
                    <div class="flex items-center justify-end gap-2">
                        <a href="{{ route('expenses.edit', $expense) }}" class="text-xs text-zinc-400 hover:text-white px-2 py-1 rounded hover:bg-zinc-800">Modifier</a>
                        <form method="POST" action="{{ route('expenses.destroy', $expense) }}" onsubmit="return confirm('Supprimer cette dépense ?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-xs text-red-400 hover:text-red-300 px-2 py-1 rounded hover:bg-red-900/20">Supprimer</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="px-4 py-8 text-center text-zinc-500">Aucune dépense récurrente. <a href="{{ route('expenses.create') }}" class="text-indigo-400 hover:underline">Créer la première</a></td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
