@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Editar Vinculação - Professor/Turma</h2>

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
            <strong>Erro!</strong> Verifique os campos abaixo:<br><br>
            <ul>
                @foreach ($errors->all() as $erro)
                    <li>{{ $erro }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('professor-turma.update', ['professor_id' => $vinculo->professor_id, 'turma_id' => $vinculo->turma_id]) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="escola" class="form-label">Escola:</label>
            <input type="text" class="form-control" value="{{ $vinculo->escola_nome ?? 'Escola não informada' }}" readonly>
        </div>

        <div class="mb-3">
            <label for="professor_id" class="form-label">Professor:</label>
            <select name="professor_id" class="form-select" required>
                <option value="">Selecione um professor</option>
                @foreach($professores as $professor)
                    <option value="{{ $professor->id }}"
                        {{ $professor->id == $vinculo->professor_id ? 'selected' : '' }}>
                        {{ $professor->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="turma_id" class="form-label">Turma:</label>
            <select name="turma_id" class="form-select" required>
                <option value="">Selecione uma turma</option>
                @foreach($turmas as $turma)
                    <option value="{{ $turma->id }}"
                        {{ $turma->id == $vinculo->turma_id ? 'selected' : '' }}>
                        {{ $turma->nome_turma }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="d-flex justify-content-between">
            <a href="{{ route('professor-turma.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Atualizar
            </button>
        </div>
    </form>
</div>
@endsection
