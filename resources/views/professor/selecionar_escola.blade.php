@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Selecionar Escola</div>

                <div class="card-body">
                    <p>Por favor, selecione a escola com a qual deseja trabalhar:</p>
                    
                    <form method="POST" action="{{ route('professor.definir-escola') }}">
                        @csrf
                        
                        <div class="form-group">
                            <label for="escola_id">Escola</label>
                            <select name="escola_id" id="escola_id" class="form-control" required>
                                @foreach($escolas as $escola)
                                    <option value="{{ $escola->id }}">{{ $escola->nome }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="form-group mt-3">
                            <button type="submit" class="btn btn-primary">
                                Confirmar Escola
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection