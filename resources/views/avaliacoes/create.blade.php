@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card shadow rounded-4">
        <div class="card-header bg-primary text-white rounded-top-4">
            <h4 class="mb-0"><i class="fas fa-clipboard-list me-2"></i> Registrar Visita</h4>
        </div>

        <div class="card-body">
            <form action="{{ route('avaliacoes.store') }}" method="POST">
                @csrf

                <div class="row">
                    {{-- Coluna esquerda --}}
                    <div class="col-md-6">
                        {{-- Tutor --}}
                        <div class="form-group mb-3">
                            <label class="fw-bold">Tutor</label>
                            @if(auth()->user()->role === 'tutor')
                                <input type="hidden" name="tutor_id" value="{{ auth()->id() }}">
                                <div class="form-control-plaintext">{{ auth()->user()->name }}</div>
                            @else
                                <select name="tutor_id" class="form-control" required>
                                    <option value="" disabled selected>Selecione o tutor</option>
                                    @foreach($tutores as $tutor)
                                        <option value="{{ $tutor->id }}">{{ $tutor->name }}</option>
                                    @endforeach
                                </select>
                            @endif
                        </div>

                        {{-- Data da Visita --}}
                        <div class="form-group mb-3">
                            <label class="fw-bold">Data da Visita</label>
                            <input type="date" name="data_visita" class="form-control" required>
                        </div>
                    </div>

                    {{-- Coluna direita --}}
                    <div class="col-md-6">
                        {{-- Escola com busca --}}
                        <div class="form-group mb-3">
                            <label class="fw-bold">Escola</label>
                            <select name="escola_id" id="escolaSelect" class="form-control" required>
                                <option value="" disabled selected>Selecione ou busque a escola</option>
                                @foreach($escolas as $escola)
                                    <option value="{{ $escola->id }}">{{ $escola->nome }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="text-end mt-4">
                    <button type="submit" class="btn btn-success px-4">
                        <i class="fas fa-save me-1"></i> Salvar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- Select2 CSS e JS para busca com autocomplete -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function () {
        $('#escolaSelect').select2({
            placeholder: "Busque por nome da escola",
            width: '100%',
            language: "pt-BR"
        });
    });
</script>
@endsection
