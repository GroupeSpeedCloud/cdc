@extends('layouts.app')
@section('title', 'Modifier ' . $project->name)
@section('page-title', 'Modifier le projet')

@section('content')
@php
$palette = ['#f59e0b', '#6366f1', '#10b981', '#f43f5e', '#3b82f6', '#8b5cf6', '#06b6d4', '#84cc16'];
$currentColor = old('color', $project->color);
@endphp

<div style="max-width:600px;">
    <div style="margin-bottom:24px;">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:4px;">
            <span style="width:10px;height:10px;border-radius:50%;background:{{ $project->color }};display:block;"></span>
            <h1 class="page-title">{{ $project->name }}</h1>
        </div>
        <p style="font-size:13px;color:var(--text-3);">Modifiez les informations du projet</p>
    </div>

    <form method="POST" action="{{ route('projects.update', $project) }}">
        @csrf @method('PUT')

        <div class="card" style="border-radius:16px;margin-bottom:16px;">
            <div style="margin-bottom:20px;">
                <label class="form-label">Nom du projet <span style="color:var(--red);">*</span></label>
                <input type="text" name="name" value="{{ old('name', $project->name) }}" class="form-input" required>
            </div>

            <div style="margin-bottom:20px;">
                <label class="form-label">Description</label>
                <textarea name="description" rows="3" class="form-input" style="resize:vertical;">{{ old('description', $project->description) }}</textarea>
            </div>

            <div style="margin-bottom:20px;">
                <label class="form-label">Couleur <span style="color:var(--red);">*</span></label>
                <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-top:4px;">
                    @foreach($palette as $color)
                        <label style="cursor:pointer;" class="swatch-label">
                            <input type="radio" name="color_radio" value="{{ $color }}" class="sr-only swatch-radio" {{ $currentColor === $color ? 'checked' : '' }}>
                            <span class="color-swatch {{ $currentColor === $color ? 'selected' : '' }}" style="background-color:{{ $color }};display:block;" data-color="{{ $color }}"></span>
                        </label>
                    @endforeach
                    <div style="display:flex;align-items:center;gap:8px;margin-left:4px;padding-left:12px;border-left:1px solid var(--border-2);">
                        <label class="form-label" style="margin:0;white-space:nowrap;">Perso.</label>
                        <input type="color" id="color_custom" value="{{ $currentColor }}" style="width:32px;height:32px;border-radius:8px;cursor:pointer;background:var(--surface-2);border:1px solid var(--border-2);padding:2px;">
                    </div>
                </div>
                <input type="hidden" name="color" id="color_final" value="{{ $currentColor }}">
            </div>

            <div>
                <label class="form-label">Statut</label>
                <select name="status" class="form-input">
                    <option value="active" {{ old('status', $project->status) === 'active' ? 'selected' : '' }}>Actif</option>
                    <option value="archived" {{ old('status', $project->status) === 'archived' ? 'selected' : '' }}>Archivé</option>
                </select>
            </div>
        </div>

        <div style="display:flex;gap:10px;">
            <button type="submit" class="btn btn-primary" style="padding:10px 20px;font-size:14px;">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                Enregistrer
            </button>
            <a href="{{ route('projects.index') }}" class="btn btn-secondary">Annuler</a>
        </div>
    </form>
</div>

<script>
(function() {
    const radios = document.querySelectorAll('.swatch-radio');
    const swatches = document.querySelectorAll('.color-swatch');
    const colorFinal = document.getElementById('color_final');
    const colorCustom = document.getElementById('color_custom');

    function updateSelected(val) {
        colorFinal.value = val;
        swatches.forEach(s => {
            s.classList.toggle('selected', s.dataset.color === val);
        });
    }

    radios.forEach(r => {
        r.addEventListener('change', function() {
            updateSelected(this.value);
            colorCustom.value = this.value;
        });
    });

    swatches.forEach(s => {
        s.addEventListener('click', function() {
            const val = this.dataset.color;
            radios.forEach(r => { r.checked = r.value === val; });
            updateSelected(val);
            colorCustom.value = val;
        });
    });

    colorCustom.addEventListener('input', function() {
        radios.forEach(r => r.checked = false);
        colorFinal.value = this.value;
        swatches.forEach(s => s.classList.remove('selected'));
    });
})();
</script>
@endsection
