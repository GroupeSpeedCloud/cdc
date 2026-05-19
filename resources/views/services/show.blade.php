@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto py-8">
    <h1 class="text-2xl font-bold mb-6">Fiche service : {{ $service->name }}</h1>
    <div class="bg-card rounded shadow p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <div class="mb-2"><span class="font-semibold">Type :</span> {{ ucfirst($service->type) }}</div>
                <div class="mb-2"><span class="font-semibold">Prix :</span> {{ $service->price }} €</div>
                <div class="mb-2"><span class="font-semibold">Statut :</span> <span class="px-2 py-1 rounded text-xs {{ $service->status === 'actif' ? 'bg-green-600' : 'bg-red-600' }}">{{ ucfirst($service->status) }}</span></div>
            </div>
            <div>
                <div class="mb-2"><span class="font-semibold">Coût réel calculé :</span> <span class="font-mono">{{ $real_cost }} €</span></div>
                @if($expenses->isEmpty())
                    <div class="text-red-400 text-sm mt-2">Aucune dépense liée à ce service, le coût ne peut pas être calculé.</div>
                @endif
            </div>
        </div>
        <div class="mt-4">
            <a href="{{ route('services.edit', $service) }}" class="text-primary hover:underline mr-4">Éditer</a>
            <form action="{{ route('services.destroy', $service) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer ce service ?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-red-500 hover:underline">Supprimer</button>
            </form>
        </div>
    </div>
    <div class="bg-card rounded shadow p-6 mb-6">
        <div class="flex justify-between items-center mb-2">
            <div class="font-semibold">Dépenses liées à ce service</div>
            <a href="{{ route('expenses.create', ['service_id' => $service->id]) }}" class="bg-primary hover:bg-accent text-white px-3 py-1 rounded text-sm">Ajouter une dépense</a>
        </div>
        <table class="min-w-full text-sm">
            <thead>
                <tr class="border-b border-zinc-700">
                    <th class="py-2 px-3 text-left">Catégorie</th>
                    <th class="py-2 px-3 text-left">Montant</th>
                    <th class="py-2 px-3 text-left">Date</th>
                    <th class="py-2 px-3 text-left">Note</th>
                </tr>
            </thead>
            <tbody>
                @forelse($expenses as $expense)
                <tr class="border-b border-zinc-800 hover:bg-zinc-800 transition">
                    <td class="py-2 px-3">{{ $expense->category }}</td>
                    <td class="py-2 px-3">{{ $expense->amount }} €</td>
                    <td class="py-2 px-3">{{ $expense->date }}</td>
                    <td class="py-2 px-3">{{ $expense->note }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="py-4 text-center text-zinc-400">Aucune dépense liée à ce service.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
