@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card shadow mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="m-0 font-weight-bold">Aplicar Simulado: {{ $simulado->nome }}</h5>
            <small>Escola: {{ Auth::user()->escola->nome }}</small>
        </div>
        
        <div class="card-body">
        @if(!session('aluno_selecionado'))
<!-- Formulário de seleção do aluno -->
<form method="POST" action="{{ route('respostas_simulados.aplicador.selecionar', $simulado->id) }}">
    @csrf
    
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="turma_id">Turma:</label>
                <select class="form-control" name="turma_id" id="turma_id" required>
                    <option value="">Selecione a turma</option>
                    @foreach($turmas as $turma)
                        <option value="{{ $turma->id }}" {{ old('turma_id') == $turma->id ? 'selected' : '' }}>
                            {{ $turma->nome_turma }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="form-group" id="aluno-container" style="display: none;">
                <label for="aluno_id">Aluno:</label>
                <select class="form-control" name="aluno_id" id="aluno_id" required disabled>
                    <option value="">Selecione o aluno</option>
                </select>
            </div>
        </div>
    </div>
    
    <div class="form-group">
        <label for="raca">Raça/Cor do Aluno:</label>
        <select class="form-control" name="raca" id="raca" required>
            <option value="">Selecione...</option>
            <option value="Branca" {{ old('raca') == 'Branca' ? 'selected' : '' }}>Branca</option>
            <option value="Preta" {{ old('raca') == 'Preta' ? 'selected' : '' }}>Preta</option>
            <option value="Parda" {{ old('raca') == 'Parda' ? 'selected' : '' }}>Parda</option>
            <option value="Amarela" {{ old('raca') == 'Amarela' ? 'selected' : '' }}>Amarela</option>
            <option value="Indígena" {{ old('raca') == 'Indígena' ? 'selected' : '' }}>Indígena</option>
            <option value="Prefiro não informar" {{ old('raca') == 'Prefiro não informar' ? 'selected' : '' }}>Prefiro não informar</option>
        </select>
    </div>
    
    <button type="submit" class="btn btn-primary" id="btn-selecionar" disabled>
        <i class="fas fa-user-check"></i> Selecionar Aluno
    </button>
</form>
@else

<!-- Formulário de respostas -->
<form method="POST" action="{{ route('respostas_simulados.aplicador.store', $simulado->id) }}">
    @csrf
    <input type="hidden" name="aluno_id" value="{{ session('aluno_id') }}">
    <input type="hidden" name="turma_id" value="{{ session('turma_id') }}">
    <input type="hidden" name="raca" value="{{ session('raca') }}">
    
    <!-- Seção de informações do aluno -->
    <div class="alert alert-info mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-user-graduate"></i> <strong>Aluno:</strong> {{ session('aluno_nome') }}
                | <i class="fas fa-school"></i> <strong>Turma:</strong> {{ session('aluno_turma') }}
                | <i class="fas fa-palette"></i> <strong>Raça/Cor:</strong> {{ session('raca') }}
                | <i class="fas fa-user-tie"></i> <strong>Aplicador:</strong> {{ Auth::user()->name }}
            </div>
            <a href="{{ route('respostas_simulados.aplicador.create', $simulado->id) }}" class="btn btn-sm btn-warning">
                <i class="fas fa-sync-alt"></i> Trocar Aluno
            </a>
        </div>
    </div>

    <!-- Lista de perguntas -->
    @foreach($simulado->perguntas as $index => $pergunta)
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Pergunta {{ $index + 1 }}</h5>
            <p class="card-text">{{ $pergunta->enunciado }}</p>

            @if($pergunta->imagem)
                <img src="{{ asset('storage/' . $pergunta->imagem) }}" class="img-fluid mb-3 rounded" style="max-height: 200px;">
            @endif

            <div class="list-group">
                @foreach(['A' => $pergunta->alternativa_a, 'B' => $pergunta->alternativa_b, 
                         'C' => $pergunta->alternativa_c, 'D' => $pergunta->alternativa_d] as $letra => $texto)
                <label class="list-group-item d-flex align-items-center">
                    <input type="radio" name="respostas[{{ $pergunta->id }}]" 
                           value="{{ $letra }}" class="mr-3" required>
                    <strong>{{ $letra }}.</strong> {{ $texto }}
                </label>
                @endforeach
            </div>
        </div>
    </div>
    @endforeach

    <button type="submit" class="btn btn-success btn-block">
        <i class="fas fa-check-circle"></i> Finalizar Aplicação
    </button>
</form>
@endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Função para verificar e habilitar o botão
    function verificarCampos() {
        const turmaVal = $('#turma_id').val();
        const alunoVal = $('#aluno_id').val();
        const racaVal = $('#raca').val();
        
        $('#btn-selecionar').prop('disabled', !(turmaVal && alunoVal && racaVal));
    }

    // Carregar alunos ao selecionar turma
    $('#turma_id').change(function() {
        const turmaId = $(this).val();
        
        if (!turmaId) {
            $('#aluno-container').hide();
            $('#aluno_id').prop('disabled', true).val('');
            verificarCampos();
            return;
        }

        $('#aluno-container').show();
        $('#aluno_id').html('<option value="">Carregando...</option>').prop('disabled', true);

        $.ajax({
            url: `/aplicador/get-alunos/${turmaId}`,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                let options = '<option value="">Selecione o aluno</option>';
                
                if (response && response.length > 0) {
                    response.forEach(aluno => {
                        options += `<option value="${aluno.id}">${aluno.name}</option>`;
                    });
                } else {
                    options = '<option value="">Nenhum aluno encontrado</option>';
                }
                
                $('#aluno_id').html(options).prop('disabled', false);
                verificarCampos();
            },
            error: function() {
                $('#aluno_id').html('<option value="">Erro ao carregar</option>');
            }
        });
    });

    // Monitorar mudanças nos campos
    $('#aluno_id, #raca').change(verificarCampos);

    // Recarregar se houver valores antigos
    @if(old('turma_id'))
        $('#turma_id').val('{{ old('turma_id') }}').trigger('change');
        setTimeout(() => {
            $('#aluno_id').val('{{ old('aluno_id') }}');
            $('#raca').val('{{ old('raca') }}');
            verificarCampos();
        }, 500);
    @endif
});
</script>
@endsection