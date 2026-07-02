@extends('layouts.app')
@section('title', 'Nouveau document')
@section('page-title', 'Nouveau document interne')

@section('content')
<div class="card"><div class="card-body">
    <form method="POST" action="{{ route('documents.store') }}">
        @csrf
        @include('documents._form')
    </form>
</div></div>
@endsection
