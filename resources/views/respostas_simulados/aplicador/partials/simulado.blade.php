<form id="form-simulado" action="{{ route('respostas_simulados.aplicador.store', $simulado) }}" method="POST">
    @csrf
    <input type="hidden" name="aluno_id" id="input-aluno-id">
    <input type="hidden" name="turma_id" id="input-turma-id">
    
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

@section('scripts')
<script>
// Adicione aqui o JavaScript para navegação entre perguntas e cronômetro
// (similar ao que você já tem na view do aluno)
</script>
@endsection