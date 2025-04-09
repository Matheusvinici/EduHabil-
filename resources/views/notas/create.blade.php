@extends('layouts.app')

@section('content')
    <h1>Adicionar Nota à Avaliação</h1>
    <h3>Escola: {{ $avaliacao->escola?->nome ?? 'Não encontrada' }}</h3>
    <h3>Escola: {{ $avaliacao->tutor?->nome ?? 'Não encontrada' }}</h3>

    <form action="{{ route('notas.store', $avaliacao->id) }}" method="POST">
        @csrf
        <div class="form-group">
            <label>Critério</label>
            <select name="criterio_id" class="form-control" required>
                @foreach($criterios as $criterio)
                    <option value="{{ $criterio->id }}">{{ $criterio->categoria }}: {{ $criterio->descricao }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label>Nota (1-5)</label>
            <select name="nota" class="form-control" required>
                <option value="1">1 - Ruim</option>
                <option value="2">2 - Regular</option>
                <option value="3" selected>3 - Bom</option>
                <option value="4">4 - Muito Bom</option>
                <option value="5">5 - Excelente</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Salvar</button>
    </form>
@endsection