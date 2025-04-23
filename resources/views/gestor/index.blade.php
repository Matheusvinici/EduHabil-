@extends('layouts.app')

@section('title', 'Gestão de Provas')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <h2>Gestão de Provas - {{ auth()->user()->escolas->first()->nome }}</h2>
        </div>
    </div>

    @include('provas.coordenador.estatisticas-escola', [
        'escola' => auth()->user()->escolas->first(),
        'totalProvas' => App\Models\Prova::where('escola_id', auth()->user()->escolas->first()->id)->count(),
        'professores' => App\Models\User::whereHas('provas', function($query) {
            $query->where('escola_id', auth()->user()->escolas->first()->id);
        })->withCount(['provas' => function($query) {
            $query->where('escola_id', auth()->user()->escolas->first()->id);
        }])->get(),
        'topDisciplinas' => App\Models\Disciplina::join('provas', 'disciplinas.id', '=', 'provas.disciplina_id')
            ->select('disciplinas.nome', DB::raw('COUNT(*) as total'))
            ->where('provas.escola_id', auth()->user()->escolas->first()->id)
            ->groupBy('disciplinas.id', 'disciplinas.nome')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get(),
        'provasPorAno' => App\Models\Ano::join('provas', 'anos.id', '=', 'provas.ano_id')
            ->select('anos.nome', DB::raw('COUNT(*) as total'))
            ->where('provas.escola_id', auth()->user()->escolas->first()->id)
            ->groupBy('anos.id', 'anos.nome')
            ->orderBy('total', 'desc')
            ->get(),
        'provasRecentes' => App\Models\Prova::with(['ano', 'disciplina', 'professor'])
            ->where('escola_id', auth()->user()->escolas->first()->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
    ])
</div>
@endsection