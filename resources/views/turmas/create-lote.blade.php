@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card border-primary shadow">
        <div class="card-header bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="mb-0">
                    <i class="fas fa-file-excel mr-2"></i>Cadastrar Turma em Lote
                </h3>
                <a href="{{ route('turmas.index') }}" class="btn btn-light btn-sm">
                    <i class="fas fa-arrow-left mr-1"></i> Voltar
                </a>
            </div>
        </div>

        <div class="card-body">
            <form action="{{ route('turmas.store-lote') }}" method="POST" id="turmaLoteForm" enctype="multipart/form-data">
                @csrf
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="escola_id" class="font-weight-bold">Escola</label>
                            <select name="escola_id" id="escola_id" class="form-control form-control-lg" required>
                                <option value="">Selecione uma escola</option>
                                @foreach($escolas as $escola)
                                    <option value="{{ $escola->id }}">{{ $escola->nome }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nome_turma" class="font-weight-bold">Nome da Turma</label>
                            <input type="text" name="nome_turma" id="nome_turma" class="form-control form-control-lg" placeholder="Digite o nome da turma" required>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="font-weight-bold">Importar Alunos via Excel</label>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        O arquivo Excel deve conter apenas uma coluna com os nomes dos alunos, sem cabeçalho.
                    </div>
                    
                    <div class="custom-file mb-3">
                        <input type="file" class="custom-file-input" id="arquivo_excel" name="arquivo_excel" accept=".xlsx,.xls,.csv" required>
                        <label class="custom-file-label" for="arquivo_excel">Selecione o arquivo Excel</label>
                    </div>
                    
                    <div id="alunos-excel-container" class="mb-3" style="display: none;">
                        <h5 class="mb-3">Alunos do Arquivo</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Nome do Aluno</th>
                                        <th>Ação</th>
                                    </tr>
                                </thead>
                                <tbody id="tabela-alunos-excel">
                                    <!-- Alunos serão adicionados aqui dinamicamente -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <div class="form-group mt-4">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save mr-1"></i> Cadastrar Turma
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para visualização de erros no Excel -->
<div class="modal fade" id="errosExcelModal" tabindex="-1" role="dialog" aria-labelledby="errosExcelModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title" id="errosExcelModalLabel">Erros no Arquivo Excel</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="errosExcelModalBody">
                <!-- Conteúdo dos erros será inserido aqui -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script>
$(document).ready(function() {
    // Processar arquivo Excel
    $('#arquivo_excel').change(function() {
        const fileInput = $('#arquivo_excel')[0];
        const file = fileInput.files[0];
        
        if (!file) {
            Swal.fire({
                title: 'Atenção!',
                text: 'Por favor, selecione um arquivo Excel primeiro.',
                icon: 'warning'
            });
            return;
        }
        
        const reader = new FileReader();
        reader.onload = function(e) {
            try {
                const data = new Uint8Array(e.target.result);
                const workbook = XLSX.read(data, { type: 'array' });
                const firstSheet = workbook.Sheets[workbook.SheetNames[0]];
                const jsonData = XLSX.utils.sheet_to_json(firstSheet, { header: 1 });
                
                // Limpa a tabela antes de adicionar novos dados
                $('#tabela-alunos-excel').empty();
                
                // Filtra linhas vazias e processa os dados
                const alunos = [];
                const erros = [];
                
                jsonData.forEach((row, index) => {
                    if (index === 0 && row[0] && (row[0].toString().toLowerCase().includes('nome') || 
                                                  row[0].toString().toLowerCase().includes('aluno'))) {
                        return;
                    }
                    
                    if (row[0] && row[0].toString().trim() !== '') {
                        const nomeAluno = row[0].toString().trim();
                        
                        if (nomeAluno.length < 3) {
                            erros.push(`Linha ${index + 1}: Nome muito curto "${nomeAluno}"`);
                        } else if (nomeAluno.length > 255) {
                            erros.push(`Linha ${index + 1}: Nome muito longo "${nomeAluno.substring(0, 20)}..."`);
                        } else {
                            alunos.push(nomeAluno);
                        }
                    }
                });
                
                if (erros.length > 0) {
                    $('#errosExcelModalBody').html(`
                        <p>Foram encontrados ${erros.length} erro(s) no arquivo:</p>
                        <ul>
                            ${erros.map(erro => `<li>${erro}</li>`).join('')}
                        </ul>
                        <p>Os alunos com erros foram ignorados.</p>
                    `);
                    $('#errosExcelModal').modal('show');
                }
                
                if (alunos.length > 0) {
                    alunos.forEach(aluno => {
                        $('#tabela-alunos-excel').append(`
                            <tr>
                                <td>${aluno}</td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-outline-danger remover-aluno-excel">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </td>
                                <input type="hidden" name="alunos_excel[]" value="${aluno}">
                            </tr>
                        `);
                    });
                    
                    $('#alunos-excel-container').show();
                    Swal.fire({
                        title: 'Sucesso!',
                        text: `${alunos.length} aluno(s) importado(s) com sucesso!`,
                        icon: 'success'
                    });
                } else {
                    Swal.fire({
                        title: 'Atenção!',
                        text: 'Nenhum aluno válido encontrado no arquivo.',
                        icon: 'warning'
                    });
                }
            } catch (error) {
                console.error('Erro ao processar arquivo Excel:', error);
                Swal.fire({
                    title: 'Erro!',
                    text: 'Erro ao processar arquivo Excel. Verifique se o formato está correto.',
                    icon: 'error'
                });
            }
        };
        reader.readAsArrayBuffer(file);
    });
    
    // Remove aluno da tabela de importação
    $(document).on('click', '.remover-aluno-excel', function() {
        $(this).closest('tr').remove();
        
        if ($('#tabela-alunos-excel tr').length === 0) {
            $('#alunos-excel-container').hide();
        }
    });
    
    // Mostra o nome do arquivo selecionado
    $('.custom-file-input').on('change', function() {
        let fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').addClass("selected").html(fileName);
    });
});
</script>
@endsection