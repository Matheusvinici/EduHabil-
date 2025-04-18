@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0">
                    <i class="fas fa-chart-bar"></i> Desempenho dos Alunos - SAEB
                </h5>
            </div>
            <a href="{{ route('respostas_simulados.aplicador.select') }}" class="btn btn-info btn-sm fw-bold text-primary">
                <i class="fas fa-plus-circle me-1"></i> Aplicar Novo Simulado
            </a>
        </div>
        
        <div class="card-body">
            <!-- Filtros de Pesquisa -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <form method="GET" action="">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input type="text" name="search_aluno" class="form-control" 
                                   placeholder="Pesquisar por aluno..." value="{{ request('search_aluno') }}">
                            <button class="btn btn-primary" type="submit">Buscar</button>
                        </div>
                    </form>
                </div>
                <div class="col-md-6">
                    <form method="GET" action="">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input type="text" name="search_simulado" class="form-control" 
                                   placeholder="Pesquisar por simulado..." value="{{ request('search_simulado') }}">
                            <button class="btn btn-primary" type="submit">Buscar</button>
                        </div>
                    </form>
                </div>
            </div>
            
            @if($estatisticas->isEmpty())
                <div class="alert alert-info text-center py-4">
                    <i class="fas fa-info-circle fa-2x mb-3"></i>
                    <h5>Nenhum resultado encontrado</h5>
                    <p class="mb-0">Você ainda não aplicou nenhum simulado ou não há respostas registradas.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Aluno</th>
                                <th>Escola</th>
                                <th>Simulado</th>
                                <th class="text-center">% Acertos</th>
                                <th class="text-center">Média (Peso)</th>
                                <th class="text-center">Nota TRI</th>
                                <th class="text-center">Data</th>
                                <th class="text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($estatisticas as $est)
                            <tr>
                                <td class="fw-semibold">{{ $est['aluno'] }}</td>
                                <td>{{ $est['escola'] }}</td>
                                <td>{{ $est['simulado'] }}</td>
                                
                                <td class="text-center">
                                    <span class="badge" style="background-color: {{ $est['desempenho_class'] == 'success' ? '#28a745' : ($est['desempenho_class'] == 'warning' ? '#ffc107' : '#dc3545') }}; color: white; padding: 6px 10px; border-radius: 6px;">
                                        {{ number_format($est['porcentagem'], 1) }}%
                                    </span>
                                </td>

                                <td class="text-center">
                                    <span class="badge bg-primary text-white px-3 py-2 rounded-pill">
                                        {{ number_format($est['media'], 1) }}
                                    </span>
                                </td>
                                
                                <td class="text-center">
                                    <span class="badge bg-success text-white px-3 py-2 rounded-pill">
                                        {{ number_format($est['tri_score'], 1) }}
                                    </span>
                                </td>
                                
                                <td class="text-center">{{ \Carbon\Carbon::parse($est['data'])->format('d/m/Y H:i') }}</td>
                                
                                <td class="text-center">
                            <div class="d-flex justify-content-center gap-2">
                                <!-- Botão Ver -->
                                <a href="{{ route('respostas_simulados.aplicador.show', [$est['simulado_id'], $est['user_id']]) }}" 
                                class="btn btn-sm btn-primary">
                                    Ver
                                </a>
                                
                                <!-- Botão Editar -->
                                <a href="{{ route('respostas_simulados.aplicador.edit', [$est['simulado_id'], $est['user_id']]) }}" 
                                class="btn btn-sm btn-warning">
                                    Editar
                                </a>
                                
                                <!-- Botão Excluir -->
                                <form action="{{ route('respostas_simulados.aplicador.destroy', [$est['simulado_id'], $est['user_id']]) }}" 
                                    method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-sm btn-danger btn-delete">
                                        Excluir
                                    </button>
                                </form>
                            </div>
                        </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Paginação -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $pagination->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal de confirmação para exclusão -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Confirmar Exclusão</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja excluir permanentemente estas respostas?</p>
                <p class="fw-bold">Esta ação não pode ser desfeita.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteButton">Excluir</button>
            </div>
        </div>
    </div>
</div>

<style>
    .table-hover tbody tr:hover {
        background-color: rgba(74, 144, 226, 0.1);
    }
    .badge {
        min-width: 70px;
    }
    .btn-sm {
        min-width: 80px;
    }
    @media (max-width: 768px) {
        .table-responsive {
            font-size: 0.85rem;
        }
        .btn-sm {
            min-width: auto;
            padding: 0.25rem 0.5rem;
        }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Configura o modal de exclusão
    const deleteModal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));
    const deleteButtons = document.querySelectorAll('.btn-delete');
    
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const form = this.closest('form');
            
            document.getElementById('confirmDeleteButton').onclick = function() {
                form.submit();
            };
            
            deleteModal.show();
        });
    });
});
</script>
@endsection