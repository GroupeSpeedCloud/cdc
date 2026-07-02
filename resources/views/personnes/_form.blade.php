@php $p = $personne ?? null; @endphp
<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Compte utilisateur (optionnel)</label>
        <select name="user_id" class="form-select">
            <option value="">—</option>
            @foreach($users as $u)
                <option value="{{ $u->id }}" @selected(old('user_id', $p?->user_id) == $u->id)>{{ $u->name }} ({{ $u->email }})</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label">Nom (si pas de compte)</label>
        <input name="nom" class="form-control" value="{{ old('nom', $p?->nom) }}">
    </div>
    <div class="col-md-6">
        <label class="form-label">Service</label>
        <select name="service_id" class="form-select">
            <option value="">—</option>
            @foreach($services as $s)
                <option value="{{ $s->id }}" @selected(old('service_id', $p?->service_id) == $s->id)>{{ $s->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label">Tarif horaire par défaut (€)</label>
        <input name="tarif_horaire_par_defaut" type="number" step="0.01" min="0" class="form-control" value="{{ old('tarif_horaire_par_defaut', $p?->tarif_horaire_par_defaut ?? 0) }}" required>
    </div>
</div>
<div class="mt-3 d-flex gap-2">
    <button class="btn btn-primary"><i class="bi bi-save"></i> Enregistrer</button>
    <a href="{{ route('personnes.index') }}" class="btn btn-outline-secondary">Annuler</a>
</div>
