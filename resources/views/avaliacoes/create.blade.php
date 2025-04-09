@extends('layouts.app')

@section('content')
    <h1>Registrar Avaliação</h1>
    <form action="{{ route('avaliacoes.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label>Tutor</label>
            <select name="tutor_id" class="form-control" required>
                @foreach($tutores as $tutor)
                    <option value="{{ $tutor->id }}">{{ $tutor->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label>Escola</label>
            <select name="escola_id" class="form-control" required>
                @foreach($escolas as $escola)
                    <option value="{{ $escola->id }}">{{ $escola->nome }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label>Data da Visita</label>
            <input type="date" name="data_visita" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Salvar</button>
    </form>
@endsection