@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto py-8">
    <h1 class="text-2xl font-bold mb-6">Ajouter un service</h1>
    <form method="POST" action="{{ route('services.store') }}" class="bg-card rounded shadow p-6 space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium mb-1">Nom</label>
            <input type="text" name="name" class="w-full rounded bg-zinc-900 border border-zinc-700 p-2" required>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Type</label>
            <select name="type" class="w-full rounded bg-zinc-900 border border-zinc-700 p-2">
                <option value="mensuel">Mensuel</option>
                <option value="annuel">Annuel</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Prix (€)</label>
            <input type="number" step="0.01" name="price" class="w-full rounded bg-zinc-900 border border-zinc-700 p-2" required>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Coût mensuel (€)</label>
            <input type="number" step="0.01" name="monthly_cost" class="w-full rounded bg-zinc-900 border border-zinc-700 p-2" required>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Coût annuel (€)</label>
            <input type="number" step="0.01" name="annual_cost" class="w-full rounded bg-zinc-900 border border-zinc-700 p-2" required>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Statut</label>
            <select name="status" class="w-full rounded bg-zinc-900 border border-zinc-700 p-2">
                <option value="actif">Actif</option>
                <option value="inactif">Inactif</option>
            </select>
        </div>
        <button type="submit" class="bg-primary hover:bg-accent text-white px-4 py-2 rounded">Créer</button>
    </form>
</div>
@endsection
