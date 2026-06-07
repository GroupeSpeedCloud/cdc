@extends('layouts.app')
@section('title', 'Nouveau projet')
@section('page-title', 'Nouveau projet')

@section('content')
@php
$palette = ['#f59e0b', '#6366f1', '#10b981', '#f43f5e', '#3b82f6', '#8b5cf6', '#06b6d4', '#84cc16'];
@endphp

<div class="max-w-xl">
    <form method="POST" action="{{ route('projects.store') }}" class="space-y-5">
        @csrf

        <div class="card space-y-5">
            <div>
                <label class="label">Nom du projet <span class="text-red-400">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" class="input" placeholder="Ex: Festival d'été" required>
            </div>

            <div>
                <label class="label">Description</label>
                <textarea name="description" rows="3" class="input" placeholder="Description optionnelle du projet">{{ old('description') }}</textarea>
            </div>

            <div>
                <label class="label">Couleur <span class="text-red-400">*</span></label>
                <div class="flex items-center gap-3 flex-wrap">
                    @foreach($palette as $color)
                        <label class="cursor-pointer">
                            <input type="radio" name="color" value="{{ $color }}" class="sr-only" {{ old('color', '#6366f1') === $color ? 'checked' : '' }}>
                            <span class="w-8 h-8 rounded-full block border-2 border-zinc-700 ring-2 ring-transparent ring-offset-2 ring-offset-zinc-900 has-[:checked]:ring-white transition-all" style="background-color: {{ $color }}"></span>
                        </label>
                    @endforeach
                    <input type="color" name="color_custom" value="{{ old('color', '#6366f1') }}" class="w-8 h-8 rounded cursor-pointer bg-zinc-800 border border-zinc-700" title="Couleur personnalisée">
                </div>
                <p class="text-xs text-zinc-500 mt-1">Ou utilisez le sélecteur de couleur personnalisé</p>
            </div>

            <div>
                <label class="label">Statut</label>
                <select name="status" class="input">
                    <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Actif</option>
                    <option value="archived" {{ old('status') === 'archived' ? 'selected' : '' }}>Archivé</option>
                </select>
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="btn-primary">Créer le projet</button>
            <a href="{{ route('projects.index') }}" class="btn-secondary">Annuler</a>
        </div>
    </form>
</div>

<script>
// Sync custom color picker with radio
document.querySelector('input[type=color]').addEventListener('input', function() {
    document.querySelectorAll('input[name=color]').forEach(r => r.checked = false);
    // We need a hidden input or override approach
    this.closest('form').querySelector('input[name=color]') || (() => {
        const hidden = document.createElement('input');
        hidden.type = 'hidden';
        hidden.name = 'color';
        this.closest('form').appendChild(hidden);
    })();
    document.querySelectorAll('input[name=color]').forEach(r => {
        if (r.type === 'hidden') r.value = this.value;
    });
});
</script>
@endsection
