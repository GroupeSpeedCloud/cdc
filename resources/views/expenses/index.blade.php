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
    'locaux' => 'badge-blue',
    'autre' => 'badge-muted',
];
@endphp

<div class="page-header">
    <div class="page-header-left">
        <h1 class="page-title">Dépenses récurrentes</h1>
        <span class="count-badge">{{ $expenses->count() }}</span>
    </div>
    <div style="display:flex;gap:8px;align-items:center;">
        {{-- Monthly total card --}}
        <div style="display:flex;align-items:center;gap:8px;background:rgba(59,130,246,0.08);border:1px solid rgba(59,130,246,0.2);border-radius:8px;padding:6px 14px;">
            <span style="font-size:11px;color:#60a5fa;font-weight:600;text-transform:uppercase;letter-spacing:0.06em;">Ce mois</span>
            <span style="font-size:15px;font-weight:700;color:#93c5fd;">{{ number_format($totalMonthly, 0, ',', ' ') }} €</span>
        </div>
        <a href="{{ route('expenses.override', [$now->year, $now->month]) }}" class="btn btn-secondary">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            Overrides du mois
        </a>
        <a href="{{ route('expenses.create') }}" class="btn btn-primary">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            Nouvelle dépense
        </a>
    </div>
</div>

<div class="card-flush">
    <table class="data-table">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Catégorie</th>
                <th class="text-right">Montant</th>
                <th>Période</th>
                <th class="text-center">Ce mois</th>
                <th class="text-right">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($expenses as $expense)
            @php
                $monthAmount = $expense->getAmountForMonth($now->year, $now->month);
            @endphp
            <tr>
                <td>
                    <span style="font-weight:500;color:var(--text);font-size:13px;">{{ $expense->name }}</span>
                    @if($expense->notes)
                        <p style="font-size:11px;color:var(--text-3);margin-top:2px;">{{ $expense->notes }}</p>
                    @endif
                </td>
                <td>
                    <span class="badge {{ $categoryBadge[$expense->category] ?? 'badge-muted' }}">
                        {{ $categoryLabels[$expense->category] ?? $expense->category }}
                    </span>
                </td>
                <td class="text-right" style="color:var(--text);font-weight:600;">{{ number_format($expense->amount, 2, ',', ' ') }} €</td>
                <td style="font-size:12px;color:var(--text-3);">
                    <div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;">
                        <span>depuis {{ \Carbon\Carbon::parse($expense->start_month)->format('m/Y') }}</span>
                        @if($expense->end_month)
                            <span style="color:var(--text-3);">→ {{ \Carbon\Carbon::parse($expense->end_month)->format('m/Y') }}</span>
                        @else
                            <span style="display:inline-flex;align-items:center;gap:3px;background:rgba(99,102,241,0.1);border:1px solid rgba(99,102,241,0.25);color:#818cf8;font-size:10px;font-weight:600;letter-spacing:0.05em;padding:2px 7px;border-radius:4px;">
                                <svg width="10" height="10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                Annuel
                            </span>
                        @endif
                    </div>
                </td>
                <td class="text-center">
                    @if($monthAmount !== null)
                        <span class="badge badge-green">{{ number_format($monthAmount, 0, ',', ' ') }} €</span>
                    @else
                        <span class="badge badge-muted">Inactif</span>
                    @endif
                </td>
                <td class="text-right">
                    <div style="display:flex;align-items:center;justify-content:flex-end;gap:4px;">
                        <a href="{{ route('expenses.edit', $expense) }}" class="btn btn-ghost" title="Modifier">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </a>
                        <form method="POST" action="{{ route('expenses.destroy', $expense) }}" onsubmit="return confirm('Supprimer cette dépense ?')" style="display:inline;">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-ghost-red" title="Supprimer">
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align:center;padding:48px 16px;color:var(--text-3);">
                    Aucune dépense récurrente — <a href="{{ route('expenses.create') }}" style="color:var(--accent);text-decoration:none;">Créer la première</a>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
