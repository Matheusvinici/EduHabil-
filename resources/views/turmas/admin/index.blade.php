@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Quantitativo de Turmas por Escola</h1>

    <div class="card mt-4">
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Escola</th>
                        <th>Quantidade de Turmas</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($escolas as $escola)
                        <tr>
                            <td>{{ $escola->nome }}</td>
                            <td>{{ $escola->turmas_count }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="text-center">Nenhuma escola encontrada.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Adicionando a paginação -->
            <div class="d-flex justify-content-center mt-4">
                {{ $escolas->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
