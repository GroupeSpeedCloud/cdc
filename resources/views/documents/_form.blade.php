@php
    $doc = $document ?? null;
    $existingLignes = old('lignes', $doc ? $doc->lignes->map(fn($l) => [
        'description_ligne' => $l->description_ligne,
        'type_prestation' => $l->type_prestation,
        'personne_id' => $l->personne_id,
        'description_achat' => $l->description_achat,
        'quantite' => $l->quantite,
        'tarif_unitaire' => $l->tarif_unitaire,
    ])->toArray() : []);
@endphp

<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Service émetteur</label>
        <select name="service_emetteur_id" class="form-select" required>
            <option value="">—</option>
            @foreach($services as $s)
                <option value="{{ $s->id }}" @selected(old('service_emetteur_id', $doc?->service_emetteur_id) == $s->id)>{{ $s->name }} ({{ $s->code }})</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label">Service destinataire</label>
        <select name="service_destinataire_id" class="form-select" required>
            <option value="">—</option>
            @foreach($services as $s)
                <option value="{{ $s->id }}" @selected(old('service_destinataire_id', $doc?->service_destinataire_id) == $s->id)>{{ $s->name }} ({{ $s->code }})</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label">Date d'émission</label>
        <input type="date" name="date_emission" class="form-control" value="{{ old('date_emission', optional($doc?->date_emission)->format('Y-m-d') ?? now()->format('Y-m-d')) }}" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">Date d'échéance <span class="text-secondary">(optionnel)</span></label>
        <input type="date" name="date_echeance" class="form-control" value="{{ old('date_echeance', optional($doc?->date_echeance)->format('Y-m-d')) }}">
    </div>
    <div class="col-md-4">
        <label class="form-label">Taux de TVA (%)</label>
        <input type="number" step="0.01" min="0" max="100" name="taux_tva" id="tauxTva" class="form-control" value="{{ old('taux_tva', $doc?->taux_tva ?? 20) }}" oninput="total()" required>
    </div>
    <div class="col-md-8">
        <label class="form-label">Objet / description</label>
        <textarea name="description_globale" class="form-control" rows="2">{{ old('description_globale', $doc?->description_globale) }}</textarea>
    </div>
    <div class="col-md-4">
        <label class="form-label">Notes <span class="text-secondary">(bas de facture)</span></label>
        <textarea name="notes" class="form-control" rows="2">{{ old('notes', $doc?->notes) }}</textarea>
    </div>
</div>

<hr class="my-4">
<div class="d-flex justify-content-between align-items-center mb-2">
    <h6 class="mb-0">Lignes de la facture</h6>
    <button type="button" class="btn btn-sm btn-outline-primary" onclick="addLigne()"><i class="bi bi-plus"></i> Ajouter une ligne</button>
</div>

<div class="table-responsive">
    <table class="table align-middle" id="lignesTable">
        <thead><tr>
            <th style="width:24%">Description</th><th style="width:15%">Type</th>
            <th style="width:23%">Personne / Achat</th><th style="width:11%">Quantité</th>
            <th style="width:12%">P.U. HT</th><th style="width:12%" class="text-end">Montant HT</th><th></th>
        </tr></thead>
        <tbody id="lignesBody"></tbody>
    </table>
</div>

<div class="row justify-content-end mt-2">
    <div class="col-md-5 col-lg-4">
        <div class="card">
            <div class="card-body py-2">
                <div class="d-flex justify-content-between py-1"><span class="text-secondary">Total HT</span><strong id="totalHt">0,00 €</strong></div>
                <div class="d-flex justify-content-between py-1"><span class="text-secondary">TVA (<span id="tvaLabel">20</span> %)</span><strong id="totalTva">0,00 €</strong></div>
                <div class="d-flex justify-content-between py-1 border-top pt-2"><span class="fw-bold">Total TTC</span><strong class="fs-5 text-primary" id="totalTtc">0,00 €</strong></div>
            </div>
        </div>
    </div>
</div>

<div class="d-flex gap-2 mt-3">
    <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Enregistrer (brouillon)</button>
    <a href="{{ route('documents.index') }}" class="btn btn-outline-secondary">Annuler</a>
</div>

@push('scripts')
<script>
const PERSONNES = {!! json_encode($personnes->map(fn($p) => ['id' => $p->id, 'nom' => $p->nomAffiche(), 'tarif' => (float)$p->tarif_horaire_par_defaut])->values()) !!};
const EXISTING = {!! json_encode($existingLignes) !!};
let ligneIdx = 0;
const fmt = n => n.toLocaleString('fr-FR', {minimumFractionDigits:2, maximumFractionDigits:2}) + ' €';

function personneOptions(selected) {
    let html = '<option value="">—</option>';
    PERSONNES.forEach(p => {
        html += `<option value="${p.id}" data-tarif="${p.tarif}" ${p.id == selected ? 'selected' : ''}>${p.nom}</option>`;
    });
    return html;
}

function addLigne(data = {}) {
    const i = ligneIdx++;
    const isTemps = (data.type_prestation ?? 'Temps Interne') === 'Temps Interne';
    const tr = document.createElement('tr');
    tr.innerHTML = `
        <td><input name="lignes[${i}][description_ligne]" class="form-control form-control-sm" value="${(data.description_ligne ?? '').replace(/"/g,'&quot;')}" required></td>
        <td>
            <select name="lignes[${i}][type_prestation]" class="form-select form-select-sm" onchange="toggleType(this, ${i})">
                <option value="Temps Interne" ${isTemps ? 'selected' : ''}>Temps Interne</option>
                <option value="Achat Externe" ${!isTemps ? 'selected' : ''}>Achat Externe</option>
            </select>
        </td>
        <td>
            <select name="lignes[${i}][personne_id]" class="form-select form-select-sm cell-personne" onchange="fillTarif(this, ${i})" style="display:${isTemps ? 'block':'none'}">${personneOptions(data.personne_id)}</select>
            <input name="lignes[${i}][description_achat]" class="form-control form-control-sm cell-achat" placeholder="Description achat" value="${(data.description_achat ?? '').replace(/"/g,'&quot;')}" style="display:${isTemps ? 'none':'block'}">
        </td>
        <td><input name="lignes[${i}][quantite]" type="number" step="0.01" min="0" class="form-control form-control-sm cell-qte" value="${data.quantite ?? 0}" oninput="calc(${i})" required></td>
        <td><input name="lignes[${i}][tarif_unitaire]" type="number" step="0.01" min="0" class="form-control form-control-sm cell-tarif" value="${data.tarif_unitaire ?? 0}" oninput="calc(${i})" required></td>
        <td class="text-end cell-montant">0,00 €</td>
        <td><button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('tr').remove();total()"><i class="bi bi-trash"></i></button></td>
    `;
    tr.dataset.idx = i;
    document.getElementById('lignesBody').appendChild(tr);
    calc(i);
}

function rowByIdx(i){ return document.querySelector(`tr[data-idx="${i}"]`); }

function toggleType(sel, i) {
    const tr = rowByIdx(i);
    const isTemps = sel.value === 'Temps Interne';
    tr.querySelector('.cell-personne').style.display = isTemps ? 'block' : 'none';
    tr.querySelector('.cell-achat').style.display = isTemps ? 'none' : 'block';
}

function fillTarif(sel, i) {
    const opt = sel.options[sel.selectedIndex];
    const tarif = opt.getAttribute('data-tarif');
    if (tarif) rowByIdx(i).querySelector('.cell-tarif').value = tarif;
    calc(i);
}

function calc(i) {
    const tr = rowByIdx(i);
    const q = parseFloat(tr.querySelector('.cell-qte').value) || 0;
    const t = parseFloat(tr.querySelector('.cell-tarif').value) || 0;
    tr.querySelector('.cell-montant').textContent = fmt(q*t);
    total();
}

function total() {
    let ht = 0;
    document.querySelectorAll('#lignesBody tr').forEach(tr => {
        const q = parseFloat(tr.querySelector('.cell-qte').value) || 0;
        const t = parseFloat(tr.querySelector('.cell-tarif').value) || 0;
        ht += q*t;
    });
    const taux = parseFloat(document.getElementById('tauxTva').value) || 0;
    const tva = ht * taux / 100;
    document.getElementById('totalHt').textContent = fmt(ht);
    document.getElementById('totalTva').textContent = fmt(tva);
    document.getElementById('totalTtc').textContent = fmt(ht + tva);
    document.getElementById('tvaLabel').textContent = (Math.round(taux*100)/100).toString().replace('.', ',');
}

if (EXISTING.length) { EXISTING.forEach(l => addLigne(l)); } else { addLigne(); }
total();
</script>
@endpush
