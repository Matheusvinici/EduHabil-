@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card border-primary shadow">
        <div class="card-header bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="mb-0">
                    <i class="fas fa-users mr-2"></i>Cadastrar Usuários em Lote
                </h3>
                <a href="{{ route('users.index') }}" class="btn btn-light btn-sm">
                    <i class="fas fa-arrow-left mr-1"></i> Voltar
                </a>
            </div>
        </div>

        <div class="card-body">
            <form action="{{ route('users.store-lote') }}" method="POST" id="userLoteForm" enctype="multipart/form-data">
                @csrf
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="role" class="font-weight-bold">Tipo de Usuário *</label>
                            <select name="role" id="role" class="form-control form-control-lg" required>
                                <option value="">Selecione o tipo</option>
                                <option value="professor">Professor</option>
                                <option value="coordenador">Coordenador</option>
                                <option value="aee">Professor do AEE</option>
                                <option value="gestor">Gestor</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="escola_id" class="font-weight-bold">Escola *</label>
                            <select name="escola_id" id="escola_id" class="form-control form-control-lg" required>
                                <option value="">Selecione a escola</option>
                                @foreach($escolas as $escola)
                                    <option value="{{ $escola->id }}">{{ $escola->nome }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="font-weight-bold">Importar Usuários via Excel</label>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        O arquivo Excel deve conter duas colunas: Nome e Email (com cabeçalho).
                    </div>
                    
                    <div class="custom-file mb-3">
                        <input type="file" class="custom-file-input" id="arquivo_excel" name="arquivo_excel" accept=".xlsx,.xls,.csv" required>
                        <label class="custom-file-label" for="arquivo_excel">Selecione o arquivo Excel</label>
                    </div>
                    
                    <div id="usuarios-excel-container" class="mb-3" style="display: none;">
                        <h5 class="mb-3">Pré-visualização dos Usuários</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Nome</th>
                                        <th>Email</th>
                                        <th>Ação</th>
                                    </tr>
                                </thead>
                                <tbody id="tabela-usuarios-excel">
                                    <!-- Usuários serão adicionados aqui dinamicamente -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <div class="form-group mt-4">
                    <button type="submit" class="btn btn-primary btn-lg" id="submitButton">
                        <i class="fas fa-save mr-1"></i> Cadastrar Usuários
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

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
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
                const jsonData = XLSX.utils.sheet_to_json(firstSheet);
                
                // Limpa a tabela antes de adicionar novos dados
                $('#tabela-usuarios-excel').empty();
                
                // Processa os dados
                const usuarios = [];
                const erros = [];
                
                jsonData.forEach((row, index) => {
                    const nome = row['Nome'] || row['nome'] || row['NOME'] || '';
                    const email = row['Email'] || row['email'] || row['EMAIL'] || '';
                    
                    if (nome.trim()) {
                        if (nome.trim().length < 3) {
                            erros.push(`Linha ${index + 1}: Nome muito curto "${nome}"`);
                        } else if (email && !isValidEmail(email)) {
                            erros.push(`Linha ${index + 1}: Email inválido "${email}"`);
                        } else {
                            usuarios.push({
                                name: nome.trim(),
                                email: email.trim().toLowerCase()
                            });
                        }
                    }
                });
                
                if (erros.length > 0) {
                    $('#errosExcelModalBody').html(`
                        <p>Foram encontrados ${erros.length} erro(s) no arquivo:</p>
                        <ul>
                            ${erros.map(erro => `<li>${erro}</li>`).join('')}
                        </ul>
                        <p>Os usuários com erros foram ignorados.</p>
                    `);
                    $('#errosExcelModal').modal('show');
                }
                
                if (usuarios.length > 0) {
                    usuarios.forEach((usuario, index) => {
                        $('#tabela-usuarios-excel').append(`
                            <tr>
                                <td>${usuario.name}</td>
                                <td>${usuario.email || '(será gerado automaticamente)'}</td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-outline-danger remover-usuario-excel">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </td>
                                <input type="hidden" name="usuarios_excel[${index}][name]" value="${usuario.name}">
                                <input type="hidden" name="usuarios_excel[${index}][email]" value="${usuario.email || ''}">
                            </tr>
                        `);
                    });
                    
                    $('#usuarios-excel-container').show();
                    Swal.fire({
                        title: 'Sucesso!',
                        text: `${usuarios.length} usuário(s) importado(s) com sucesso!`,
                        icon: 'success'
                    });
                } else {
                    $('#usuarios-excel-container').hide();
                    Swal.fire({
                        title: 'Atenção!',
                        text: 'Nenhum usuário válido encontrado no arquivo.',
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
    
    // Remove usuário da tabela de importação
    $(document).on('click', '.remover-usuario-excel', function() {
        $(this).closest('tr').remove();
        
        if ($('#tabela-usuarios-excel tr').length === 0) {
            $('#usuarios-excel-container').hide();
        }
    });
    
    // Mostra o nome do arquivo selecionado
    $('.custom-file-input').on('change', function() {
        let fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').addClass("selected").html(fileName);
    });
    
    // Desabilita o botão durante o processamento
    $('#userLoteForm').on('submit', function() {
        if ($('#tabela-usuarios-excel tr').length === 0) {
            Swal.fire({
                title: 'Atenção!',
                text: 'Nenhum usuário para cadastrar. Por favor, importe um arquivo válido.',
                icon: 'warning'
            });
            return false;
        }
        
        $('#submitButton').prop('disabled', true);
        $('#submitButton').html('<i class="fas fa-spinner fa-spin mr-1"></i> Cadastrando...');
    });
    
    function isValidEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(String(email).toLowerCase());
    }
});
</script>
@endsection