@extends('layouts.app')
@section('title', 'Modifier personne')
@section('page-title', 'Modifier ' . $personne->nomAffiche())
@section('content')
<div class="card"><div class="card-body">
    <form method="POST" action="{{ route('personnes.update', $personne) }}">@csrf @method('PUT')
        @include('personnes._form')
    </form>
</div></div>
@endsection
