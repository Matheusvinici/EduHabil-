@extends('layouts.app')

@section('content')
    <h1>Editar Nota</h1>
    <p><strong>Escola:</strong> {{ $nota->avaliacao->escola->nome }}</p>
    <p><strong>Critério:</strong> {{ $nota->criterio->descricao }}</p>

    <form action="{{ route('notas.update', $nota->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label>Nova Nota (1-5)</label>
            <select name="nota" class="form-control" required>
                @for($i = 1; $i <= 5; $i++)
                    <option value="{{ $i }}" {{ $i == $nota->nota ? 'selected' : '' }}>
                        {{ $i }} - @switch($i)
                            @case(1) Ruim @break
                            @case(2) Regular @break
                            @case(3) Bom @break
                            @case(4) Muito Bom @break
                            @case(5) Excelente @break
                        @endswitch
                    </option>
                @endfor
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Atualizar</button>
        <a href="{{ route('avaliacoes.show', $nota->avaliacao_id) }}" class="btn btn-secondary">Cancelar</a>
    </form>
@endsection