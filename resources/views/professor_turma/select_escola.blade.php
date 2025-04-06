@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Selecione a Escola</h2>
    
    <form action="{{ route('professor-turma.create') }}" method="GET">
        <div class="form-group">
            <label for="escola_id">Escola:</label>
            <select name="escola_id" id="escola_id" class="form-control" required>
                <option value="">Selecione uma escola</option>
                @foreach($escolas as $escola)
                    <option value="{{ $escola->id }}">{{ $escola->nome }}</option>
                @endforeach
            </select>
        </div>
        
        <button type="submit" class="btn btn-primary">Continuar</button>
    </form>
</div>
@endsection