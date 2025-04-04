@extends('layouts.app')

@section('content')
<div class="container">
    <div id="modal-inicio" class="modal fade" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Início do Simulado: {{ $simulado->nome }}</h5>
                </div>
                <div class="modal-body">
                    <p>Total de questões: {{ $simulado->perguntas->count() }}</p>
                    
                    @if($simulado->tempo_limite)
                        <div class="alert alert-warning">
                            <strong>Tempo Limite:</strong> {{ $simulado->tempo_limite }} minutos
                        </div>
                    @else
                        <div class="alert alert-info">
                            Este simulado não possui tempo limite.
                        </div>
                    @endif
                    
                    <p>Quando estiver pronto, clique em "Iniciar Simulado" para começar.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="window.location.href='{{ route('respostas_simulados.aluno.index') }}'">
                        Voltar
                    </button>
                    <button type="button" class="btn btn-primary" id="btn-iniciar">
                        Iniciar Simulado
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div id="conteudo-simulado" style="display: none;">
        <h2>Responder Simulado: {{ $simulado->nome }}</h2>
        
        @if($simulado->tempo_limite)
            <div class="card mb-4">
                <div class="card-body bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <strong>Tempo restante:</strong>
                            <span id="tempo-restante" class="font-weight-bold ml-2" style="font-size: 1.5rem;"></span>
                        </div>
                        <div class="text-danger" id="aviso-tempo" style="display: none;">
                            <i class="fas fa-exclamation-triangle"></i> Tempo está acabando!
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <form id="form-simulado" action="{{ route('respostas_simulados.store', $simulado) }}" method="POST">
            @csrf
            <input type="hidden" name="simulado_id" value="{{ $simulado->id }}">
            <input type="hidden" name="tempo_resposta" id="tempo-resposta" value="0">
            
            <!-- Campo de raça -->
            <div class="form-group mb-4">
                <label for="raca">Informe sua raça/cor (opcional):</label>
                <select class="form-control" name="raca" id="raca">
                    <option value="">Selecione...</option>
                    <option value="Branca">Branca</option>
                    <option value="Preta">Preta</option>
                    <option value="Parda">Parda</option>
                    <option value="Amarela">Amarela</option>
                    <option value="Indígena">Indígena</option>
                    <option value="Prefiro não informar">Prefiro não informar</option>
                </select>
            </div>

            <div id="perguntas-container">
                @foreach ($simulado->perguntas as $index => $pergunta)
                    <div class="card mb-3 pergunta" data-index="{{ $index + 1 }}" @if($index > 0) style="display: none;" @endif>
                        <div class="card-body">
                            <h5 class="card-title">Pergunta {{ $index + 1 }}</h5>
                            <p class="card-text">{{ $pergunta->enunciado }}</p>

                            @if($pergunta->imagem)
                                <img src="{{ asset('storage/' . $pergunta->imagem) }}" alt="Imagem da pergunta" style="max-width: 100%;" class="mb-3">
                            @endif

                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="respostas[{{ $pergunta->id }}]" value="A" id="pergunta-{{ $pergunta->id }}-a" required>
                                <label class="form-check-label" for="pergunta-{{ $pergunta->id }}-a">A) {{ $pergunta->alternativa_a }}</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="respostas[{{ $pergunta->id }}]" value="B" id="pergunta-{{ $pergunta->id }}-b">
                                <label class="form-check-label" for="pergunta-{{ $pergunta->id }}-b">B) {{ $pergunta->alternativa_b }}</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="respostas[{{ $pergunta->id }}]" value="C" id="pergunta-{{ $pergunta->id }}-c">
                                <label class="form-check-label" for="pergunta-{{ $pergunta->id }}-c">C) {{ $pergunta->alternativa_c }}</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="respostas[{{ $pergunta->id }}]" value="D" id="pergunta-{{ $pergunta->id }}-d">
                                <label class="form-check-label" for="pergunta-{{ $pergunta->id }}-d">D) {{ $pergunta->alternativa_d }}</label>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="d-flex justify-content-between mt-4">
                <button type="button" class="btn btn-secondary" id="btn-anterior" disabled>Anterior</button>
                <button type="button" class="btn btn-primary" id="btn-proximo">Próxima</button>
                <button type="submit" class="btn btn-success" id="btn-finalizar" style="display: none;">Finalizar Simulado</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Mostra o modal de início
    $('#modal-inicio').modal('show');
    
    const form = document.getElementById('form-simulado');
    const tempoRespostaInput = document.getElementById('tempo-resposta');
    const tempoRestanteDiv = document.getElementById('tempo-restante');
    const avisoTempoDiv = document.getElementById('aviso-tempo');
    const btnIniciar = document.getElementById('btn-iniciar');
    const conteudoSimulado = document.getElementById('conteudo-simulado');
    const btnAnterior = document.getElementById('btn-anterior');
    const btnProximo = document.getElementById('btn-proximo');
    const btnFinalizar = document.getElementById('btn-finalizar');
    const perguntas = document.querySelectorAll('.pergunta');
    
    let tempoDecorrido = 0;
    let timer;
    let perguntaAtual = 0;
    const totalPerguntas = perguntas.length;
    const tempoLimiteMinutos = {{ $simulado->tempo_limite ?? 0 }};
    const tempoLimiteSegundos = tempoLimiteMinutos * 60;
    
    // Configura o modal para não fechar ao clicar fora
    $('#modal-inicio').modal({
        backdrop: 'static',
        keyboard: false
    });
    
    // Inicia o simulado
    btnIniciar.addEventListener('click', function() {
        $('#modal-inicio').modal('hide');
        conteudoSimulado.style.display = 'block';
        iniciarCronometro();
    });
    
    function iniciarCronometro() {
        timer = setInterval(function() {
            tempoDecorrido++;
            tempoRespostaInput.value = tempoDecorrido;
            
            // Atualiza o display do tempo
            if (tempoLimiteMinutos > 0) {
                const tempoRestante = tempoLimiteSegundos - tempoDecorrido;
                
                // Formata o tempo para HH:MM:SS
                const horas = Math.floor(tempoRestante / 3600);
                const minutos = Math.floor((tempoRestante % 3600) / 60);
                const segundos = tempoRestante % 60;
                
                tempoRestanteDiv.textContent = 
                    `${horas.toString().padStart(2, '0')}:${minutos.toString().padStart(2, '0')}:${segundos.toString().padStart(2, '0')}`;
                
                // Mostra aviso quando faltam 5 minutos
                if (tempoRestante <= 300 && tempoRestante > 60) { // 5 minutos = 300 segundos
                    avisoTempoDiv.style.display = 'block';
                    tempoRestanteDiv.style.color = 'orange';
                }
                
                // Muda para vermelho quando faltam menos de 1 minuto
                if (tempoRestante <= 60) {
                    tempoRestanteDiv.style.color = 'red';
                }
                
                // Verifica se o tempo acabou
                if (tempoRestante <= 0) {
                    clearInterval(timer);
                    alert('O tempo acabou! Suas respostas serão enviadas automaticamente.');
                    form.submit();
                }
            }
        }, 1000);
    }
    
    // Navegação entre perguntas
    function mostrarPergunta(index) {
        perguntas.forEach((pergunta, i) => {
            pergunta.style.display = i === index ? 'block' : 'none';
        });
        
        // Atualiza botões de navegação
        btnAnterior.disabled = index === 0;
        btnProximo.style.display = index < totalPerguntas - 1 ? 'block' : 'none';
        btnFinalizar.style.display = index === totalPerguntas - 1 ? 'block' : 'none';
    }
    
    btnAnterior.addEventListener('click', function() {
        if (perguntaAtual > 0) {
            perguntaAtual--;
            mostrarPergunta(perguntaAtual);
        }
    });
    
    btnProximo.addEventListener('click', function() {
        if (perguntaAtual < totalPerguntas - 1) {
            perguntaAtual++;
            mostrarPergunta(perguntaAtual);
        }
    });
    
    // Impede que o usuário saia da página sem confirmar
    window.addEventListener('beforeunload', function(e) {
        if (tempoDecorrido > 0 && (tempoLimiteMinutos === 0 || tempoDecorrido < tempoLimiteSegundos)) {
            e.preventDefault();
            e.returnValue = 'Você está no meio do simulado. Tem certeza que deseja sair?';
            return e.returnValue;
        }
    });
    
    // Mostra a primeira pergunta
    mostrarPergunta(0);
});
</script>
@endsection