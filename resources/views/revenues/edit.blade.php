@extends('layouts.app')
@section('title', 'Saisie revenus')
@section('page-title', 'Saisie des revenus')

@section('content')
<div style="max-width:680px;">
    <div style="margin-bottom:24px;display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;">
        <div>
            <h1 style="font-size:20px;font-weight:700;letter-spacing:-0.03em;color:var(--text);margin-bottom:4px;">
                {{ $monthName }}
            </h1>
            <p style="font-size:13px;color:var(--text-3);">Saisissez les revenus de chaque projet pour ce mois.</p>
        </div>
        <div style="display:flex;gap:6px;">
            <a href="{{ route('revenues.edit', [$year, $month > 1 ? $month - 1 : 12]) }}" class="btn btn-secondary" style="padding:8px 12px;">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" width="14" height="14"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                Mois préc.
            </a>
            <a href="{{ route('revenues.edit', [$year, $month < 12 ? $month + 1 : 1]) }}" class="btn btn-secondary" style="padding:8px 12px;">
                Mois suiv.
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" width="14" height="14"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>
    </div>

    <form method="POST" action="{{ route('revenues.update', [$year, $month]) }}">
        @csrf @method('PUT')

        <div class="card-flush" style="margin-bottom:16px;">
            @forelse($projects as $project)
            <div style="display:grid;grid-template-columns:1fr 160px 160px;gap:12px;align-items:center;padding:14px 20px;border-bottom:1px solid var(--border);transition:background 0.15s;" onmouseover="this.style.background='var(--surface-2)'" onmouseout="this.style.background='transparent'">
                <div style="display:flex;align-items:center;gap:10px;">
                    <span style="width:10px;height:10px;border-radius:50%;background-color:{{ $project->color }};flex-shrink:0;display:block;"></span>
                    <span style="font-size:13px;font-weight:600;color:var(--text);">{{ $project->name }}</span>
                </div>
                <div class="amount-input-wrap" style="margin:0;">
                    <input
                        type="number"
                        name="revenues[{{ $project->id }}]"
                        value="{{ old('revenues.' . $project->id, isset($existing[$project->id]) ? $existing[$project->id]->amount : '') }}"
                        step="0.01"
                        min="0"
                        class="amount-input"
                        placeholder="0"
                        style="font-size:15px;font-weight:600;"
                    >
                    <span class="amount-suffix">€</span>
                </div>
                <input
                    type="text"
                    name="notes[{{ $project->id }}]"
                    value="{{ old('notes.' . $project->id, isset($existing[$project->id]) ? $existing[$project->id]->notes : '') }}"
                    class="form-input"
                    placeholder="Notes..."
                    style="font-size:12px;"
                >
            </div>
            @empty
            <div style="text-align:center;padding:48px 16px;color:var(--text-3);">
                Aucun projet actif — <a href="{{ route('projects.create') }}" style="color:var(--accent);text-decoration:none;">Créer un projet</a>
            </div>
            @endforelse
        </div>

        @if($projects->isNotEmpty())
        <div style="display:flex;gap:10px;">
            <button type="submit" class="btn btn-primary" style="flex:1;justify-content:center;padding:12px 20px;font-size:14px;box-shadow:0 0 20px rgba(99,102,241,0.15);">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                Enregistrer
            </button>
            <a href="{{ route('revenues.index') }}" class="btn btn-secondary">Annuler</a>
        </div>
        @endif
    </form>
</div>
@endsection
