@php $s = $service ?? null; @endphp
<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Nom du service</label>
        <input name="name" class="form-control" value="{{ old('name', $s?->name) }}" required>
    </div>
    <div class="col-md-3">
        <label class="form-label">Code</label>
        <input name="code" class="form-control text-uppercase" value="{{ old('code', $s?->code) }}" required>
    </div>
    <div class="col-md-3">
        <label class="form-label">Budget annuel (€)</label>
        <input name="budget_annuel_courant" type="number" step="0.01" min="0" class="form-control" value="{{ old('budget_annuel_courant', $s?->budget_annuel_courant ?? 0) }}" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">Responsable (manager)</label>
        <select name="manager_id" class="form-select">
            <option value="">—</option>
            @foreach($managers as $m)
                <option value="{{ $m->id }}" @selected(old('manager_id', $s?->manager_id) == $m->id)>{{ $m->name }} ({{ $m->email }})</option>
            @endforeach
        </select>
    </div>
</div>
<div class="mt-3 d-flex gap-2">
    <button class="btn btn-primary"><i class="bi bi-save"></i> Enregistrer</button>
    <a href="{{ route('services.index') }}" class="btn btn-outline-secondary">Annuler</a>
</div>
