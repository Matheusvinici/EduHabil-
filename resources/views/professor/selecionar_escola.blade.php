@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Selecionar Escola</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('definir.escola') }}">
                        @csrf

                        <div class="form-group row">
                            <label for="escola_id" class="col-md-4 col-form-label text-md-right">Escola</label>

                            <div class="col-md-6">
                                <select id="escola_id" class="form-control" name="escola_id" required>
                                    <option value="">Selecione uma escola</option>
                                    @foreach($escolas as $escola)
                                        <option value="{{ $escola->id }}">{{ $escola->nome }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    Acessar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection