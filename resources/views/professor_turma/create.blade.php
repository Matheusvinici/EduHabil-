@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Vincular Professor à Turma - {{ $escola->nome }}</h2>

    {{-- MENSAGEM DE SUCESSO --}}
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    {{-- MENSAGEM DE ERRO --}}
    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    {{-- ERROS DE VALIDAÇÃO --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $erro)
                    <li>{{ $erro }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    
    <form action="{{ route('professor-turma.store') }}" method="POST">
        @csrf
        <input type="hidden" name="escola_id" value="{{ $escola->id }}">
        
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Professor:</label>
                    <select name="professor_id" class="form-control" required>
                        <option value="">Selecione um professor</option>
                        @foreach($professores as $professor)
                            <option value="{{ $professor->id }}">{{ $professor->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="form-group">
                    <label>Turmas Disponíveis:</label>
                    @foreach($turmas as $turma)
                        <div class="form-check">
                            <input type="checkbox" 
                                   name="turmas[]" 
                                   value="{{ $turma->id }}"
                                   id="turma_{{ $turma->id }}"
                                   class="form-check-input">
                            <label for="turma_{{ $turma->id }}" class="form-check-label">
                                {{ $turma->nome_turma }}
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        
        <button type="submit" class="btn btn-primary">Vincular</button>
        <a href="{{ route('professor-turma.index') }}" class="btn btn-secondary">Voltar</a>
    </form>
</div>
@endsection
