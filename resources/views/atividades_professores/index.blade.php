@extends('layouts.app')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="m-0 text-primary fw-bold">Atividades dos Professores</h1>
            <a href="{{ route('atividades_professores.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> Cadastrar Nova Atividade
            </a>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover align-middle mb-0">
                        <thead class="table-primary text-center">
                            <tr>
                                <th>Professor(a)</th>
                                <th>Título</th>
                                <th>Disciplina</th>
                                <th>Ano</th>
                                <th>Escola(s)</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($atividadesProfessores as $atividadeProfessor)
                            <tr>
                                <td>{{ $atividadeProfessor->professor->name }}</td>
                                <td>{{ Str::limit($atividadeProfessor->atividade->titulo, 30) }}</td>
                                <td>{{ $atividadeProfessor->atividade->disciplina->nome }}</td>
                                <td>{{ $atividadeProfessor->atividade->ano->nome }}</td>
                                <td>
                                    @if ($atividadeProfessor->professor->escolas->isNotEmpty())
                                        @if ($atividadeProfessor->professor->escolas->count() > 1)
                                            <button type="button" class="btn btn-sm btn-outline-primary escolas-btn" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#escolasModal"
                                                    data-user-name="{{ $atividadeProfessor->professor->name }}"
                                                    data-escolas="{{ $atividadeProfessor->professor->escolas->pluck('nome')->join('<br>') }}">
                                                {{ $atividadeProfessor->professor->escolas->count() }} escolas
                                            </button>
                                        @else
                                            {{ $atividadeProfessor->professor->escolas->first()->nome }}
                                        @endif
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('atividades_professores.show', $atividadeProfessor->id) }}" 
                                           class="btn btn-sm btn-outline-info" title="Visualizar Detalhes">
                                            <i class="fas fa-eye me-1"></i> Ver
                                        </a>
                                        <a href="{{ route('atividades_professores.download', $atividadeProfessor->id) }}" 
                                           class="btn btn-sm btn-outline-primary" title="Download da Atividade">
                                            <i class="fas fa-download me-1"></i> Baixar
                                        </a>
                                        <form action="{{ route('atividades_professores.destroy', $atividadeProfessor->id) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja remover esta atividade?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Excluir">
                                                <i class="fas fa-trash me-1"></i> Excluir
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">Nenhuma atividade encontrada.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white d-flex justify-content-end">
                {{ $atividadesProfessores->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Modal de Escolas -->
<div class="modal fade" id="escolasModal" tabindex="-1" aria-labelledby="escolasModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content border-primary">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="escolasModalLabel">Escolas Vinculadas</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <h6 id="modalUserName" class="fw-bold mb-3"></h6>
                <div id="modalEscolasList" class="ps-2"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const escolasBtns = document.querySelectorAll('.escolas-btn');

    escolasBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            const userName = this.dataset.userName;
            const escolas = this.dataset.escolas;

            document.getElementById('modalUserName').textContent = `Professor(a): ${userName}`;
            document.getElementById('modalEscolasList').innerHTML = escolas;
        });
    });
});
</script>
@endsection
