@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Painel do Coordenador</h1>
        <p>Bem-vindo, {{ Auth::user()->name }}!</p>
    </div>
@endsection