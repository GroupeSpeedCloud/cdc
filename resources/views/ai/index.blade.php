@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto py-8">
    <h1 class="text-2xl font-bold mb-6">Intelligence Artificielle Financière</h1>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <x-card>
            <form method="POST" action="{{ route('ai.summary') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium mb-1">Résumé IA</label>
                    <textarea name="data" class="w-full rounded bg-card border border-zinc-700 p-2" rows="3" placeholder="Collez vos données financières ou sélectionnez une période..."></textarea>
                </div>
                <button type="submit" class="bg-primary hover:bg-accent text-white px-4 py-2 rounded">Générer le résumé</button>
            </form>
        </x-card>
        <x-card>
            <form method="POST" action="{{ route('ai.analyze') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium mb-1">Analyse IA</label>
                    <textarea name="data" class="w-full rounded bg-card border border-zinc-700 p-2" rows="3" placeholder="Collez vos données ou sélectionnez une période..."></textarea>
                </div>
                <button type="submit" class="bg-primary hover:bg-accent text-white px-4 py-2 rounded">Analyser</button>
            </form>
        </x-card>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <x-card>
            <form method="POST" action="{{ route('ai.anomalies') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium mb-1">Détection d'anomalies</label>
                    <textarea name="data" class="w-full rounded bg-card border border-zinc-700 p-2" rows="3" placeholder="Collez vos données ou sélectionnez une période..."></textarea>
                </div>
                <button type="submit" class="bg-primary hover:bg-accent text-white px-4 py-2 rounded">Détecter</button>
            </form>
        </x-card>
        <x-card>
            <div class="text-zinc-400 text-sm">
                <p>Utilisez l’IA pour générer des prévisions, des résumés, détecter des anomalies et obtenir des conseils personnalisés sur vos finances.</p>
            </div>
        </x-card>
    </div>
</div>
@endsection
