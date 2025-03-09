@extends('layouts.app')

@section('content')
<div class="card">
<div class="card-header">
        <h3 class="card-title">Listagem de Turmas</h3>
        <div class="card-tools">
            <a href="{{ route('turmas.create') }}" class="btn btn-primary">Nova Turma</a>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nome da Turma</th>
                    <th>Quantidade de Alunos</th>
                    <th>Código da Turma</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($turmas as $turma)
                <tr>
                    <td>{{ $turma->nome_turma }}</td>
                    <td>{{ $turma->quantidade_alunos }}</td>
                    <td>{{ $turma->codigo_turma }}</td>
                    <td>
                      

                        <!-- Modal para gerar códigos adicionais -->
                        <div class="modal fade" id="modalGerarCodigos{{ $turma->id }}" tabindex="-1" role="dialog" aria-labelledby="modalGerarCodigosLabel{{ $turma->id }}" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="modalGerarCodigosLabel{{ $turma->id }}">Gerar Códigos Adicionais</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <form action="{{ route('turmas.gerar-codigos-adicionais', $turma->id) }}" method="POST">
                                            @csrf
                                            <div class="form-group">
                                                <label for="quantidade_adicionais">Quantidade de Códigos Adicionais:</label>
                                                <input type="number" name="quantidade_adicionais" class="form-control" required>
                                            </div>
                                            <button type="submit" class="btn btn-primary">Gerar Códigos</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Link para visualizar a turma -->
                        <a href="{{ route('turmas.show', $turma->id) }}" class="btn btn-sm btn-info">Ver Detalhes</a>

                        <!-- Link para editar a turma -->
                        <a href="{{ route('turmas.edit', $turma->id) }}" class="btn btn-sm btn-warning">Editar</a>

                        <!-- Formulário para excluir a turma -->
                        <form action="{{ route('turmas.destroy', $turma->id) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja excluir esta turma?')">Excluir</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection