@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto py-8">
    <h1 class="text-2xl font-bold mb-6">Ajouter une dépense liée à un service</h1>
    <form method="POST" action="{{ route('expenses.store') }}" class="bg-card rounded shadow p-6 space-y-4">
        @csrf
        <input type="hidden" name="service_id" value="{{ request('service_id') }}">
        <div>
            <label class="block text-sm font-medium mb-1">Catégorie</label>
            <input type="text" name="category" class="w-full rounded bg-zinc-900 border border-zinc-700 p-2" required>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Montant (€)</label>
            <input type="number" step="0.01" name="amount" class="w-full rounded bg-zinc-900 border border-zinc-700 p-2" required>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Date</label>
            <input type="date" name="date" class="w-full rounded bg-zinc-900 border border-zinc-700 p-2" required>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Note</label>
            <textarea name="note" class="w-full rounded bg-zinc-900 border border-zinc-700 p-2"></textarea>
        </div>
        <button type="submit" class="bg-primary hover:bg-accent text-white px-4 py-2 rounded">Créer</button>
    </form>
</div>
@endsection
