@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Nova Avaliação</h4>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('avaliacoes.store') }}">
                @csrf

                {{-- Dados gerais --}}
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Tutor:</label>
                        <select name="tutor_id" class="form-select" required>
                            <option value="">Selecione</option>
                            @foreach($tutores as $tutor)
                                <option value="{{ $tutor->id }}">{{ $tutor->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Escola:</label>
                        <select name="escola_id" class="form-select" required>
                            <option value="">Selecione</option>
                            @foreach($escolas as $escola)
                                <option value="{{ $escola->id }}">{{ $escola->nome }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Data da Visita:</label>
                        <input type="date" name="data_visita" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Observações:</label>
                        <textarea name="observacoes" class="form-control" rows="1"></textarea>
                    </div>
                </div>

                {{-- Avaliação por critério --}}
                <hr>
                <h5 class="mb-3">Avaliação por Critério</h5>

                <div class="table-responsive">
                    <table class="table table-sm table-bordered align-middle text-center">
                        <thead class="table-light">
                            <tr>
                                <th class="text-start" style="width: 45%">Categoria / Descrição</th>
                                @for($i = 0; $i <= 5; $i++)
                                    <th style="width: 9%">{{ $i }}</th>
                                @endfor
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($criterios as $criterio)
                                <tr>
                                    <td class="text-start">
                                        <strong>{{ $criterio->categoria ?? 'Critério' }}</strong><br>
                                        <small class="text-muted">{{ $criterio->descricao }}</small>
                                    </td>
                                    @for($i = 0; $i <= 5; $i++)
                                        <td>
                                            <input type="radio" name="avaliacoes[{{ $criterio->id }}]" value="{{ $i }}" class="form-check-input nota" required>
                                        </td>
                                    @endfor
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3 text-end">
                    <h6 class="text-muted">Média Calculada:</h6>
                    <h4 class="fw-bold text-primary" id="mediaSpan">-</h4>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                    <button type="submit" class="btn btn-success px-4">Salvar Avaliação</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function calcularMedia() {
        let total = 0;
        let count = 0;

        document.querySelectorAll('.nota:checked').forEach(el => {
            const val = parseFloat(el.value);
            if (!isNaN(val)) {
                total += val;
                count++;
            }
        });

        const media = count > 0 ? (total / count).toFixed(2) : '-';
        document.getElementById('mediaSpan').textContent = media;
    }

    document.querySelectorAll('.nota').forEach(el => {
        el.addEventListener('change', calcularMedia);
    });
</script>
@endsection
