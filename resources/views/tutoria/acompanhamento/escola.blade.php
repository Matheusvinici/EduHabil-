@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
        </div>
        <div class="card-body">
            <form action="{{ route('tutoria.acompanhamento.store') }}" method="POST">
                @csrf
                <input type="hidden" name="avaliacao_id" value="{{ $avaliacao->id }}">

                <div class="row mb-3">
                <div class="col-md-6">
    <label class="form-label">Critérios com Baixo Desempenho:</label>
    <div class="border p-3" style="max-height: 200px; overflow-y: auto;">
        @if(isset($criteriosBaixos) && $criteriosBaixos->count() > 0)
            @foreach($criteriosBaixos as $criterio)
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="criterios[]" 
                       value="{{ $criterio->id }}" id="criterio{{ $criterio->id }}" checked>
                <label class="form-check-label" for="criterio{{ $criterio->id }}">
                    {{ $criterio->categoria }} - Nota: {{ $criterio->pivot->nota }}
                </label>
            </div>
            @endforeach
        @else
            <p>Não há critérios com baixo desempenho disponíveis.</p>
        @endif
    </div>
</div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Prioridade:</label>
                            <select name="prioridade" class="form-select" required>
                                <option value="alta">Alta</option>
                                <option value="media">Média</option>
                                <option value="baixa">Baixa</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Responsável:</label>
                            <select name="responsavel_id" class="form-select" required>
                                @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Prazo:</label>
                            <input type="date" name="prazo" class="form-control" required 
                                   min="{{ date('Y-m-d') }}">
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Ação de Melhoria:</label>
                    <textarea name="acao_melhoria" class="form-control" rows="3" required></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Observações:</label>
                    <textarea name="observacoes" class="form-control" rows="2"></textarea>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="submit" class="btn btn-success px-4">Salvar Acompanhamento</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection