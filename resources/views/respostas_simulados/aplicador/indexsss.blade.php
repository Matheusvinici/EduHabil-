@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="m-0 font-weight-bold">Simulados Aplicados</h5>
                <a href="{{ route('respostas_simulados.aplicador.select') }}" class="btn btn-light btn-sm">
                    <i class="fas fa-plus-circle mr-1"></i> Aplicar Novo Simulado
                </a>
            </div>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-dark">
                        <tr>
                            <th>Simulado</th>
                            <th>Aluno</th>
                            <th>Data</th>
                            <th>Desempenho</th>
                            <th class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($simulados as $simulado)
                        <tr>
                            <td>{{ $simulado['simulado_nome'] }}</td>
                            <td>{{ $simulado['aluno_nome'] }}</td>
                            <td>{{ $simulado['data_aplicacao']->format('d/m/Y H:i') }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="progress flex-grow-1" style="height: 20px;">
                                        <div class="progress-bar bg-{{ $simulado['desempenho_class'] }}" 
                                            role="progressbar" 
                                            style="width: {{ $simulado['porcentagem'] }}%" 
                                            aria-valuenow="{{ $simulado['porcentagem'] }}" 
                                            aria-valuemin="0" 
                                            aria-valuemax="100">
                                        </div>
                                    </div>
                                    <small class="ml-2 font-weight-bold">
                                        {{ $simulado['acertos'] }}/{{ $simulado['total_questoes'] }} ({{ $simulado['porcentagem'] }}%)
                                    </small>
                                </div>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('respostas_simulados.aplicador.detalhes', $simulado['id']) }}" 
                                   class="btn btn-sm btn-info" title="Detalhes">
                                    <i class="fas fa-search"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- DataTables -->
<link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
<script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>
<script>
    $(document).ready(function() {
        $('#dataTable').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Portuguese-Brasil.json"
            },
            "order": [[2, "desc"]],
            "columnDefs": [
                { "orderable": false, "targets": [4] }
            ]
        });
    });
</script>
@endsection