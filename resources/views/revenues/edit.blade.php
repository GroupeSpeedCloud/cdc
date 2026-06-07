@extends('layouts.app')
@section('title', 'Saisie revenus')
@section('page-title', 'Saisie des revenus')

@section('content')
<div style="max-width:700px;">
    <div style="margin-bottom:28px;">
        <h1 style="font-size:22px;font-weight:700;letter-spacing:-0.03em;color:var(--text);margin-bottom:4px;">
            Saisie — {{ $monthName }}
        </h1>
        <p style="font-size:13px;color:var(--text-3);">Saisissez les revenus de chaque projet pour ce mois.</p>
    </div>

    <form method="POST" action="{{ route('revenues.update', [$year, $month]) }}">
        @csrf @method('PUT')

        <div style="display:flex;flex-direction:column;gap:12px;margin-bottom:24px;">
            @forelse($projects as $project)
            <div class="card" style="border-radius:14px;">
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px;">
                    <span style="width:12px;height:12px;border-radius:50%;background-color:{{ $project->color }};flex-shrink:0;display:block;"></span>
                    <span style="font-size:14px;font-weight:600;color:var(--text);">{{ $project->name }}</span>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <div>
                        <label class="form-label">Montant</label>
                        <div class="amount-input-wrap">
                            <input
                                type="number"
                                name="revenues[{{ $project->id }}]"
                                value="{{ old('revenues.' . $project->id, isset($existing[$project->id]) ? $existing[$project->id]->amount : '') }}"
                                step="0.01"
                                min="0"
                                class="amount-input"
                                placeholder="0.00"
                                style="font-size:18px;"
                            >
                            <span class="amount-suffix">€</span>
                        </div>
                    </div>
                    <div>
                        <label class="form-label">Notes (optionnel)</label>
                        <input
                            type="text"
                            name="notes[{{ $project->id }}]"
                            value="{{ old('notes.' . $project->id, isset($existing[$project->id]) ? $existing[$project->id]->notes : '') }}"
                            class="form-input"
                            placeholder="Notes..."
                            style="height:46px;"
                        >
                    </div>
                </div>
            </div>
            @empty
            <div class="card" style="text-align:center;color:var(--text-3);">
                Aucun projet actif — <a href="{{ route('projects.create') }}" style="color:var(--accent);text-decoration:none;">Créer un projet</a>
            </div>
            @endforelse
        </div>

        @if($projects->isNotEmpty())
        <div style="display:flex;flex-direction:column;gap:10px;">
            <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:13px 20px;font-size:15px;box-shadow:0 0 20px rgba(99,102,241,0.2);">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                Enregistrer les revenus
            </button>
            <a href="{{ route('revenues.index') }}" class="btn btn-secondary" style="justify-content:center;">Annuler</a>
        </div>
        @endif
    </form>
</div>
@endsection
