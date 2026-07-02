@extends('layouts.app')
@section('title', 'Modifier document')
@section('page-title', 'Modifier ' . $document->numero_document)

@section('content')
<div class="card"><div class="card-body">
    <form method="POST" action="{{ route('documents.update', $document) }}">
        @csrf @method('PUT')
        @include('documents._form')
    </form>
</div></div>
@endsection
