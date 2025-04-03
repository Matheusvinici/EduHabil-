@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="card shadow-lg p-4">
        <h2 class="mb-4 text-primary">Editar Aluno</h2>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('turmas.update', $aluno->id) }}" method="POST" class="needs-validation" novalidate>
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="name" class="form-label fw-bold">Nome do Aluno</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $aluno->name) }}" required>
            </div>

            <div class="mb-3">
                <label for="deficiencia" class="form-label fw-bold">Deficiência</label>
                <label for="deficiencia" class="form-label">Deficiência:</label>
                    <select name="deficiencia" id="deficiencia" class="form-select">
                        <option value="">Nenhuma</option>
                        @foreach (App\Enums\Deficiencia::cases() as $deficiencia)
                            <option value="{{ $deficiencia->value }}" {{ old('deficiencia', $user->deficiencia) == $deficiencia->value ? 'selected' : '' }}>
                                {{ $deficiencia->label() }}
                            </option>
                        @endforeach
                    </select>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Salvar</button>
                <a href="{{ route('turmas.index') }}" class="btn btn-secondary">Voltar</a>
            </div>
        </form>
    </div>
</div>
@endsection