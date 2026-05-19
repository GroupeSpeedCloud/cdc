@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto py-8">
    <h1 class="text-2xl font-bold mb-6">Ajouter un client</h1>
    <form method="POST" action="{{ route('clients.store') }}" class="bg-card rounded shadow p-6 space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium mb-1">Nom</label>
            <input type="text" name="name" class="w-full rounded bg-zinc-900 border border-zinc-700 p-2" required>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Email</label>
            <input type="email" name="email" class="w-full rounded bg-zinc-900 border border-zinc-700 p-2" required>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Entreprise</label>
            <input type="text" name="company" class="w-full rounded bg-zinc-900 border border-zinc-700 p-2">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Téléphone</label>
            <input type="text" name="phone" class="w-full rounded bg-zinc-900 border border-zinc-700 p-2">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Statut</label>
            <select name="status" class="w-full rounded bg-zinc-900 border border-zinc-700 p-2">
                <option value="actif">Actif</option>
                <option value="inactif">Inactif</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Notes</label>
            <textarea name="notes" class="w-full rounded bg-zinc-900 border border-zinc-700 p-2"></textarea>
        </div>
        <button type="submit" class="bg-primary hover:bg-accent text-white px-4 py-2 rounded">Créer</button>
    </form>
</div>
@endsection
