@extends('layouts.app')
@section('title', 'Modifier ' . $project->name)
@section('page-title', 'Modifier le projet')

@section('content')
@php
$palette = ['#f59e0b', '#6366f1', '#10b981', '#f43f5e', '#3b82f6', '#8b5cf6', '#06b6d4', '#84cc16'];
@endphp

<div class="max-w-xl">
    <form method="POST" action="{{ route('projects.update', $project) }}" class="space-y-5">
        @csrf @method('PUT')

        <div class="card space-y-5">
            <div>
                <label class="label">Nom du projet <span class="text-red-400">*</span></label>
                <input type="text" name="name" value="{{ old('name', $project->name) }}" class="input" required>
            </div>

            <div>
                <label class="label">Description</label>
                <textarea name="description" rows="3" class="input">{{ old('description', $project->description) }}</textarea>
            </div>

            <div>
                <label class="label">Couleur <span class="text-red-400">*</span></label>
                <div class="flex items-center gap-3 flex-wrap">
                    @foreach($palette as $color)
                        <label class="cursor-pointer">
                            <input type="radio" name="color" value="{{ $color }}" class="sr-only" {{ old('color', $project->color) === $color ? 'checked' : '' }}>
                            <span class="w-8 h-8 rounded-full block border-2 border-zinc-700" style="background-color: {{ $color }}"></span>
                        </label>
                    @endforeach
                    <input type="color" id="color_custom" value="{{ old('color', $project->color) }}" class="w-8 h-8 rounded cursor-pointer bg-zinc-800 border border-zinc-700" title="Couleur personnalisée">
                    <input type="hidden" name="color" id="color_hidden" value="{{ old('color', $project->color) }}">
                </div>
            </div>

            <div>
                <label class="label">Statut</label>
                <select name="status" class="input">
                    <option value="active" {{ old('status', $project->status) === 'active' ? 'selected' : '' }}>Actif</option>
                    <option value="archived" {{ old('status', $project->status) === 'archived' ? 'selected' : '' }}>Archivé</option>
                </select>
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="btn-primary">Enregistrer</button>
            <a href="{{ route('projects.index') }}" class="btn-secondary">Annuler</a>
        </div>
    </form>
</div>

<script>
document.querySelectorAll('input[name=color][type=radio]').forEach(r => {
    r.addEventListener('change', function() {
        document.getElementById('color_hidden').value = this.value;
        document.getElementById('color_custom').value = this.value;
    });
});
document.getElementById('color_custom').addEventListener('input', function() {
    document.getElementById('color_hidden').value = this.value;
    document.querySelectorAll('input[name=color][type=radio]').forEach(r => r.checked = false);
});
</script>
@endsection
