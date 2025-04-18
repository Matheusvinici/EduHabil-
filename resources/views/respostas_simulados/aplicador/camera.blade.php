@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Modal de Confirmação -->
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="confirmModalLabel">Confirmar Aplicação</h5>
                </div>
                <div class="modal-body">
                    <p>Você está prestes a aplicar o simulado <strong>{{ $simulado->nome }}</strong> para:</p>
                    <ul class="list-group mb-3">
                        <li class="list-group-item">
                            <i class="fas fa-user-graduate me-2"></i>
                            <strong>Aluno:</strong> {{ session('aluno_nome') }}
                        </li>
                        <li class="list-group-item">
                            <i class="fas fa-school me-2"></i>
                            <strong>Turma:</strong> {{ session('aluno_turma') }}
                        </li>
                        <li class="list-group-item">
                            <i class="fas fa-palette me-2"></i>
                            <strong>Raça/Cor:</strong> {{ session('raca') }}
                        </li>
                    </ul>
                    @if($simulado->tempo_limite)
                    <div class="alert alert-warning">
                        <i class="fas fa-clock me-2"></i> Tempo limite: {{ $simulado->tempo_limite }} minutos
                    </div>
                    @endif
                </div>
                <div class="modal-footer">
                    
                    <button type="button" class="btn btn-primary" id="startSimulado">
                        <i class="fas fa-play me-2"></i> Iniciar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Cabeçalho -->
    <div class="card shadow-lg mb-4">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-0">
                    <i class="fas fa-camera"></i> Aplicar Simulado via Gabarito: {{ $simulado->nome }}
                </h4>
            </div>
        </div>
    </div>

    <div class="card shadow-lg">
        <div class="card-body">
            @if(!session('aluno_selecionado'))
            <!-- Formulário de seleção do aluno -->
            <form method="POST" action="{{ route('respostas_simulados.aplicador.selecionar-aluno', $simulado) }}" id="form-selecao">
    @csrf
    
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="form-floating">
                <select class="form-select" name="turma_id" id="turma_id" required>
                    <option value="">Selecione...</option>
                    @foreach($turmas as $turma)
                        <option value="{{ $turma->id }}" {{ old('turma_id') == $turma->id ? 'selected' : '' }}>
                            {{ $turma->nome_turma }}
                        </option>
                    @endforeach
                </select>
                <label for="turma_id">Turma</label>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="form-floating" id="aluno-container" style="display: none;">
                <select class="form-select" name="aluno_id" id="aluno_id" required disabled>
                    <option value="">Selecione...</option>
                </select>
                <label for="aluno_id">Aluno</label>
            </div>
        </div>
    </div>
    
    <div class="form-floating mb-4">
        <select class="form-select" name="raca" id="raca" required>
            <option value="">Selecione...</option>
            <option value="Branca" {{ old('raca') == 'Branca' ? 'selected' : '' }}>Branca</option>
            <option value="Preta" {{ old('raca') == 'Preta' ? 'selected' : '' }}>Preta</option>
            <option value="Parda" {{ old('raca') == 'Parda' ? 'selected' : '' }}>Parda</option>
            <option value="Amarela" {{ old('raca') == 'Amarela' ? 'selected' : '' }}>Amarela</option>
            <option value="Indígena" {{ old('raca') == 'Indígena' ? 'selected' : '' }}>Indígena</option>
            <option value="Prefiro não informar" {{ old('raca') == 'Prefiro não informar' ? 'selected' : '' }}>Prefiro não informar</option>
        </select>
        <label for="raca">Raça/Cor do Aluno</label>
    </div>
    
    <div class="d-grid">
        <button type="submit" class="btn btn-primary btn-lg" id="btn-selecionar" disabled>
            <i class="fas fa-user-check me-2"></i> Selecionar Aluno
        </button>
    </div>
</form>
            @else
            <!-- Área de captura do gabarito -->
            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Capturar Gabarito</h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i> Posicione o gabarito dentro da área demarcada e certifique-se de que está bem iluminado.
                            </div>
                            
                            <div id="cameraContainer" class="border p-2 mb-3" style="position: relative; min-height: 400px;">
                                <!-- Área de visualização da câmera -->
                                <video id="cameraPreview" width="100%" autoplay playsinline style="display: none;"></video>
                                
                                <!-- Overlay com grade para alinhamento -->
                                <div id="alignmentGrid" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none;"></div>
                                
                                <!-- Canvas para captura -->
                                <canvas id="canvas" style="display: none;"></canvas>
                                
                                <!-- Mensagem quando a câmera não está ativa -->
                                <div id="cameraPlaceholder" class="d-flex flex-column align-items-center justify-content-center h-100 text-muted">
                                    <i class="fas fa-camera fa-3x mb-3"></i>
                                    <p>Clique em "Iniciar Câmera" para começar</p>
                                </div>
                            </div>
                            
                            <div class="btn-group w-100" role="group">
                                <button type="button" id="startCameraBtn" class="btn btn-primary">
                                    <i class="fas fa-camera me-2"></i> Iniciar Câmera
                                </button>
                                <button type="button" id="captureBtn" class="btn btn-success" disabled>
                                    <i class="fas fa-camera-retro me-2"></i> Capturar
                                </button>
                                <button type="button" id="retryBtn" class="btn btn-warning" disabled>
                                    <i class="fas fa-redo me-2"></i> Tentar Novamente
                                </button>
                            </div>
                            
                            <!-- Fallback para upload de imagem -->
                            <div class="mt-3" id="uploadFallback" style="display: none;">
                                <label for="imageInput" class="btn btn-outline-secondary w-100">
                                    <i class="fas fa-upload me-2"></i> Enviar Imagem
                                </label>
                                <input type="file" id="imageInput" name="image" accept="image/*" style="display: none;">
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Pré-visualização</h5>
                        </div>
                        <div class="card-body">
                            <div id="noPreview" class="text-center py-5 text-muted">
                                <i class="fas fa-image fa-3x mb-3"></i>
                                <p>Nenhuma imagem capturada</p>
                            </div>
                            <img id="previewImage" src="#" alt="Pré-visualização do Gabarito" style="max-width: 100%; display: none;">
                            
                            <!-- Formulário para envio da imagem -->
                            <form method="POST" action="{{ route('respostas_simulados.aplicador.processar-gabarito', $simulado) }}">
                            @csrf
                            
                            <input type="hidden" name="aluno_id" value="{{ session('aluno_id') }}">
    <input type="hidden" name="turma_id" value="{{ session('turma_id') }}">
    <input type="hidden" name="raca" value="{{ session('raca') }}">
    <input type="hidden" id="processedImage" name="processed_image">
    
    <!-- Adicionei campos ocultos para melhor rastreamento -->
    <input type="hidden" name="simulado_id" value="{{ $simulado->id }}">
    <input type="hidden" name="aplicador_id" value="{{ auth()->id() }}">
    
    <div class="d-grid gap-2 mt-3">
        <button type="submit" class="btn btn-success btn-lg">
            <i class="fas fa-check-circle me-2"></i> Processar Gabarito
        </button>
        
        <!-- Botão opcional para cancelar/voltar -->
        <a href="{{ route('respostas_simulados.aplicador.camera', $simulado) }}" 
           class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i> Voltar
        </a>
    </div>
</form>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Botão para trocar aluno -->
            <div class="d-grid mt-3">
            <a href="{{ route('respostas_simulados.aplicador.camera', ['simulado' => $simulado->id, 'reset' => 1]) }}" 
   class="btn btn-warning btn-lg">
    <i class="fas fa-sync-alt me-2"></i> Trocar Aluno
</a>
            </div>
            @endif
        </div>
    </div>
</div>

<style>
    #alignmentGrid {
        background: linear-gradient(to right, rgba(255,0,0,0.1) 1px, transparent 1px),
                    linear-gradient(to bottom, rgba(255,0,0,0.1) 1px, transparent 1px);
        background-size: {{ $gabaritoConfig['espacamento_horizontal'] }}px {{ $gabaritoConfig['espacamento_vertical'] }}px;
    }
    
    #cameraContainer {
        background-color: #f0f0f0;
        border-radius: 4px;
    }
    
    #cameraPlaceholder {
        background-color: #f8f9fa;
        border: 2px dashed #dee2e6;
        border-radius: 4px;
    }
    
    .hover-highlight:hover {
        background-color: #f8f9fa;
        cursor: pointer;
        transform: translateX(5px);
        transition: all 0.2s ease;
    }
    
    .form-check-input {
        transform: scale(1.2);
        margin-top: 0.1rem;
    }
    
    .card-header {
        font-weight: 600;
    }
    
    .btn-lg {
        padding: 0.75rem 1.5rem;
        font-size: 1.1rem;
    }
    
    @media (max-width: 768px) {
        .card-header h4 {
            font-size: 1.2rem;
        }
        
        .btn-lg {
            width: 100%;
        }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Mostra o modal de confirmação se aluno foi selecionado
    @if(session('aluno_selecionado'))
    const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
    confirmModal.show();
    @endif

    // Elementos da interface
    const video = document.getElementById('cameraPreview');
    const canvas = document.getElementById('canvas');
    const previewImage = document.getElementById('previewImage');
    const noPreview = document.getElementById('noPreview');
    const startCameraBtn = document.getElementById('startCameraBtn');
    const captureBtn = document.getElementById('captureBtn');
    const retryBtn = document.getElementById('retryBtn');
    const imageInput = document.getElementById('imageInput');
    const processedImage = document.getElementById('processedImage');
    const cameraContainer = document.getElementById('cameraContainer');
    const cameraPlaceholder = document.getElementById('cameraPlaceholder');
    const uploadFallback = document.getElementById('uploadFallback');
    const formRespostas = document.getElementById('form-respostas');
    
    // Estado da aplicação
    let stream = null;
    let capturedImage = null;

    // Configura o botão de iniciar no modal
    document.getElementById('startSimulado')?.addEventListener('click', function() {
        confirmModal.hide();
    });

    // Carregar alunos ao selecionar turma - ATUALIZADO
    $('#turma_id').change(function() {
        const turmaId = $(this).val();
        const simuladoId = {{ $simulado->id }};
        
        if (!turmaId) {
            $('#aluno-container').hide();
            $('#aluno_id').prop('disabled', true).val('');
            $('#btn-selecionar').prop('disabled', true);
            return;
        }

        $('#aluno-container').show();
        $('#aluno_id').html('<option value="">Carregando...</option>').prop('disabled', true);

        $.ajax({
            url: "{{ route('respostas_simulados.aplicador.alunos') }}",
            type: 'GET',
            data: {
                turma_id: turmaId,
                simulado_id: simuladoId
            },
            success: function(response) {
                let options = '<option value="">Selecione o aluno</option>';
                
                if (response.length > 0) {
                    response.forEach(aluno => {
                        options += `<option value="${aluno.id}">${aluno.name}</option>`;
                    });
                    $('#aluno_id').html(options).prop('disabled', false);
                } else {
                    options += '<option value="0">Nenhum aluno disponível</option>';
                    $('#aluno_id').html(options).prop('disabled', false);
                }
                
                verificarCampos();
            },
            error: function(xhr) {
                let errorMsg = 'Erro ao carregar alunos';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg += ': ' + xhr.responseJSON.message;
                }
                $('#aluno_id').html(`<option value="">${errorMsg}</option>`);
            }
        });
    });

    // Verificar campos obrigatórios
    function verificarCampos() {
        const turmaVal = $('#turma_id').val();
        const alunoVal = $('#aluno_id').val();
        const racaVal = $('#raca').val();
        
        $('#btn-selecionar').prop('disabled', !(turmaVal && alunoVal && racaVal && alunoVal !== '0'));
    }

    // Monitorar mudanças nos campos
    $('#aluno_id, #raca').change(verificarCampos);

    // Iniciar câmera
    startCameraBtn.addEventListener('click', async function() {
        try {
            stream = await navigator.mediaDevices.getUserMedia({
                video: {
                    facingMode: 'environment', // Usar câmera traseira
                    width: { ideal: 1280 },
                    height: { ideal: 720 }
                },
                audio: false
            });
            
            video.srcObject = stream;
            video.style.display = 'block';
            cameraPlaceholder.style.display = 'none';
            startCameraBtn.disabled = true;
            captureBtn.disabled = false;
            
            // Ajustar o tamanho do canvas para corresponder ao vídeo
            video.addEventListener('loadedmetadata', function() {
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
            });
            
        } catch (error) {
            console.error("Erro ao acessar câmera:", error);
            alert("Não foi possível acessar a câmera. Por favor, verifique as permissões ou use o upload de imagem.");
            
            // Mostrar fallback para upload
            uploadFallback.style.display = 'block';
            startCameraBtn.disabled = true;
            cameraPlaceholder.style.display = 'none';
        }
    });

    // Capturar imagem
    captureBtn.addEventListener('click', function() {
        if (!stream) return;
        
        // Desenhar a imagem atual do vídeo no canvas
        const context = canvas.getContext('2d');
        context.drawImage(video, 0, 0, canvas.width, canvas.height);
        
        // Processar a imagem para melhorar o reconhecimento
        processImageForOCR(canvas);
        
        // Exibir pré-visualização
        capturedImage = canvas.toDataURL('image/jpeg', 0.8);
        previewImage.src = capturedImage;
        previewImage.style.display = 'block';
        noPreview.style.display = 'none';
        
        // Habilitar botões
        retryBtn.disabled = false;
        captureBtn.disabled = true;
        
        // Mostrar formulário para envio
        formRespostas.style.display = 'block';
        
        // Parar a câmera para economizar recursos
        stopCamera();
    });

    // Tentar novamente
    retryBtn.addEventListener('click', function() {
        previewImage.style.display = 'none';
        noPreview.style.display = 'block';
        capturedImage = null;
        formRespostas.style.display = 'none';
        retryBtn.disabled = true;
        
        // Reiniciar a câmera
        startCamera();
    });

    // Processar imagem para OCR
    function processImageForOCR(canvas) {
        const context = canvas.getContext('2d');
        const imageData = context.getImageData(0, 0, canvas.width, canvas.height);
        const data = imageData.data;
        
        // Aplicar filtros para melhorar o reconhecimento
        for (let i = 0; i < data.length; i += 4) {
            // Converter para escala de cinza
            const avg = (data[i] + data[i + 1] + data[i + 2]) / 3;
            
            // Aumentar contraste
            const contrast = 1.5;
            const newVal = ((avg / 255 - 0.5) * contrast + 0.5) * 255;
            
            // Aplicar limiar (threshold)
            const threshold = newVal > 150 ? 255 : 0;
            
            data[i] = data[i + 1] = data[i + 2] = threshold;
        }
        
        context.putImageData(imageData, 0, 0);
        
        // Adicionar a imagem processada ao formulário
        processedImage.value = canvas.toDataURL('image/jpeg', 0.8);
    }

    // Parar a câmera
    function stopCamera() {
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
            stream = null;
            video.style.display = 'none';
            cameraPlaceholder.style.display = 'flex';
        }
    }

    // Upload alternativo de imagem
    imageInput.addEventListener('change', function(e) {
        if (e.target.files && e.target.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(event) {
                previewImage.src = event.target.result;
                previewImage.style.display = 'block';
                noPreview.style.display = 'none';
                
                // Carregar a imagem no canvas para processamento
                const img = new Image();
                img.onload = function() {
                    canvas.width = img.width;
                    canvas.height = img.height;
                    const context = canvas.getContext('2d');
                    context.drawImage(img, 0, 0);
                    
                    // Processar a imagem
                    processImageForOCR(canvas);
                    capturedImage = canvas.toDataURL('image/jpeg', 0.8);
                    
                    // Habilitar botões
                    retryBtn.disabled = false;
                    formRespostas.style.display = 'block';
                };
                img.src = event.target.result;
            };
            
            reader.readAsDataURL(e.target.files[0]);
        }
    });

    // Limpar recursos quando a página for descarregada
    window.addEventListener('beforeunload', function() {
        stopCamera();
    });
});

// No formulário de envio da imagem
document.getElementById('form-respostas').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const form = this;
    const submitBtn = form.querySelector('button[type="submit"]');
    
    // Mostrar loading
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Processando...';

    // Enviar via AJAX
    fetch(form.action, {
        method: 'POST',
        body: new FormData(form),
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => {
        if (response.redirected) {
            window.location.href = response.url;
        } else {
            return response.json().then(data => {
                throw new Error(data.message || 'Erro no processamento');
            });
        }
    })
    .catch(error => {
        alert(error.message);
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-check-circle me-2"></i> Processar Gabarito';
    });
});
</script>
@endsection