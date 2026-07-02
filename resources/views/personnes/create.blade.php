@extends('layouts.app')
@section('title', 'Nouvelle personne')
@section('page-title', 'Ajouter une personne')
@section('content')
<div class="card"><div class="card-body">
    <form method="POST" action="{{ route('personnes.store') }}">@csrf
        @include('personnes._form')
    </form>
</div></div>
@endsection
