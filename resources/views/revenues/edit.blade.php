@extends('layouts.app')
@section('title', 'Saisie revenus')
@section('page-title', 'Saisie des revenus')

@section('content')
<div class="mb-6">
    <h2 class="text-xl font-bold text-white">{{ $monthName }}</h2>
    <p class="text-zinc-400 text-sm mt-1">Saisissez les revenus de chaque projet pour ce mois.</p>
</div>

<form method="POST" action="{{ route('revenues.update', [$year, $month]) }}">
    @csrf @method('PUT')

    <div class="space-y-3 mb-6">
        @forelse($projects as $project)
        <div class="card">
            <div class="flex items-center gap-3 mb-3">
                <span class="w-4 h-4 rounded-full border-2 border-zinc-700 flex-shrink-0" style="background-color: {{ $project->color }}"></span>
                <span class="font-medium text-white">{{ $project->name }}</span>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="label">Montant (€)</label>
                    <input
                        type="number"
                        name="revenues[{{ $project->id }}]"
                        value="{{ old('revenues.' . $project->id, isset($existing[$project->id]) ? $existing[$project->id]->amount : '') }}"
                        step="0.01"
                        min="0"
                        class="input"
                        placeholder="0.00"
                    >
                </div>
                <div>
                    <label class="label">Notes (optionnel)</label>
                    <input
                        type="text"
                        name="notes[{{ $project->id }}]"
                        value="{{ old('notes.' . $project->id, isset($existing[$project->id]) ? $existing[$project->id]->notes : '') }}"
                        class="input"
                        placeholder="Notes..."
                    >
                </div>
            </div>
        </div>
        @empty
        <div class="card text-center text-zinc-500">
            Aucun projet actif. <a href="{{ route('projects.create') }}" class="text-indigo-400 hover:underline">Créer un projet</a>
        </div>
        @endforelse
    </div>

    @if($projects->isNotEmpty())
    <div class="flex gap-3">
        <button type="submit" class="btn-primary">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            Enregistrer
        </button>
        <a href="{{ route('revenues.index') }}" class="btn-secondary">Annuler</a>
    </div>
    @endif
</form>
@endsection
