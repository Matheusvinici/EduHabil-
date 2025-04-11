<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Simulado;
use App\Models\Ano;
use App\Models\Escola;
use App\Models\Disciplina;

use App\Models\Habilidade;
use App\Models\RespostaSimulado;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

use App\Models\Aluno;
use App\Models\User;

use App\Models\Professor;
use PDF;
use Excel;
use App\Exports\RedeMunicipalExport;
use App\Exports\QuestoesExport;
use App\Exports\RelatorioRacaExport;
use App\Exports\DeficienciasExport;


class RelatorioController extends Controller
{
    
    public function estatisticasRede(Request $request)
    {
        // Validação do filtro obrigatório
        $request->validate([
            'simulado_id' => 'nullable|exists:simulados,id'
        ]);
    
        // Dados básicos
        $simulados = Simulado::orderBy('nome')->get();
        $anos = Ano::orderBy('nome')->get();
        $escolas = Escola::orderBy('nome')->get();
    
        // Se não tiver simulado selecionado, retorna apenas os filtros
        if (!$request->simulado_id) {
            return view('relatorios.rede-municipal', compact('simulados', 'anos', 'escolas'));
        }
    
        // Contar o número de usuários com role 'aluno'
     $quantidadeAlunos = User::where('role', 'aluno')->count();

    
        // Query base para respostas
        $queryRespostas = RespostaSimulado::where('simulado_id', $request->simulado_id)
            ->with(['aluno', 'pergunta']);
    
        // Aplicar filtros adicionais
        if ($request->ano_id) {
            $queryAlunos->where('ano_id', $request->ano_id);
            $queryRespostas->whereHas('aluno', fn($q) => $q->where('ano_id', $request->ano_id));
        }
    
        if ($request->escola_id) {
            $queryAlunos->where('escola_id', $request->escola_id);
            $queryRespostas->whereHas('aluno', fn($q) => $q->where('escola_id', $request->escola_id));
        }
    
        if ($request->deficiencia) {
            if ($request->deficiencia === 'ND') {
                $queryAlunos->whereNull('deficiencia');
                $queryRespostas->whereHas('aluno', fn($q) => $q->whereNull('deficiencia'));
            } else {
                $queryAlunos->where('deficiencia', $request->deficiencia);
                $queryRespostas->whereHas('aluno', fn($q) => $q->where('deficiencia', $request->deficiencia));
            }
        }

        $queryAlunos = User::where('role', 'aluno');

    
        // Dados gerais
        $totalAlunos = $queryAlunos->count();
        $alunosAtivos = $queryAlunos->count(); // Considerando que a query já filtra ativos
        $alunosResponderam = $queryRespostas->distinct('user_id')->count('user_id');
    
        // Cálculo das médias por peso
        $mediasPeso = [
            'peso_1' => $this->calcularMediaPorPeso($queryRespostas, 1),
            'peso_2' => $this->calcularMediaPorPeso($queryRespostas, 2),
            'peso_3' => $this->calcularMediaPorPeso($queryRespostas, 3),
            'media_geral' => $this->calcularMediaGeral($queryRespostas)
        ];
    
        // Projeção TRI (simplificada)
        $projecaoTRI = [
            'peso_1' => $mediasPeso['peso_1'] * 1.1,
            'peso_2' => $mediasPeso['peso_2'] * 1.2,
            'peso_3' => $mediasPeso['peso_3'] * 1.3,
            'media_geral' => $mediasPeso['media_geral'] * 1.2
        ];
    
                // Projeção por segmento
        $projecaoSegmento = [
            '1a5' => $this->calcularProjecaoSegmento($queryRespostas, range(1, 5)),
            '6a9' => $this->calcularProjecaoSegmento($queryRespostas, range(6, 9))
        ];

    
        // Estatísticas por escola
        $estatisticasPorEscola = $this->calcularEstatisticasPorEscola($request);
    
        return view('relatorios.rede-municipal', compact(
            'simulados', 'anos', 'escolas',
            'totalAlunos', 'alunosAtivos', 'alunosResponderam',
            'mediasPeso', 'projecaoTRI', 'projecaoSegmento',
            'estatisticasPorEscola'
        ));
    }
    
    // Métodos auxiliares
    private function calcularMediaPorPeso($query, $peso)
    {
        $respostas = (clone $query)
            ->whereHas('pergunta', fn($q) => $q->where('peso', $peso))
            ->selectRaw('SUM(correta) as acertos, COUNT(*) as total')
            ->first();
    
        $total = $respostas->total ?: 1; // Evitar divisão por zero
        return round(($respostas->acertos / $total) * 10, 2);
    }
    
    private function calcularMediaGeral($query)
    {
        $respostas = (clone $query)
            ->join('perguntas', 'respostas_simulados.pergunta_id', '=', 'perguntas.id')
            ->selectRaw('SUM(correta * peso) as pontos, SUM(peso) as total_peso')
            ->first();
    
        $totalPeso = $respostas->total_peso ?: 1; // Evitar divisão por zero
        return round(($respostas->pontos / $totalPeso) * 10, 2);
    }
    
    private function calcularProjecaoSegmento($query, $rangeAnos)
    {
        $respostas = (clone $query)
            ->whereHas('aluno', fn($q) => $q->whereIn('ano_id', $rangeAnos))
            ->join('perguntas', 'respostas_simulados.pergunta_id', '=', 'perguntas.id')
            ->selectRaw('SUM(correta * peso) as pontos, SUM(peso) as total_peso')
            ->first();
    
        $totalPeso = $respostas->total_peso ?: 1;
        $media = round(($respostas->pontos / $totalPeso) * 10, 2);
        
        $meta = in_array(1, $rangeAnos) ? 6.0 : 5.0; // 1-5 meta 6, 6-9 meta 5
        $projecao = min(10, $media * 1.15); // Fator de ajuste
        
        return [
            'media' => $media,
            'projecao' => round($projecao, 2),
            'atingiu_meta' => $projecao >= $meta,
            'diferenca' => round($projecao - $meta, 2)
        ];
    }
    
    private function calcularEstatisticasPorEscola($request)
    {
                // Alunos por escola (sem o filtro de ativo)
            $alunosPorEscola = User::where('role', 'aluno')
            ->when($request->ano_id, fn($q) => $q->where('ano_id', $request->ano_id))
            ->when($request->deficiencia, function($q) use ($request) {
                if ($request->deficiencia === 'ND') {
                    return $q->whereNull('deficiencia');
                }
                return $q->where('deficiencia', $request->deficiencia);
            })
            ->selectRaw('escola_id, COUNT(*) as total')
            ->groupBy('escola_id')
            ->get()
            ->keyBy('escola_id');

    
        // Respostas por escola
        $respostasPorEscola = RespostaSimulado::where('simulado_id', $request->simulado_id)
            ->when($request->ano_id, fn($q) => $q->whereHas('aluno', fn($q) => $q->where('ano_id', $request->ano_id)))
            ->when($request->deficiencia, function($q) use ($request) {
                if ($request->deficiencia === 'ND') {
                    return $q->whereHas('aluno', fn($q) => $q->whereNull('deficiencia'));
                }
                return $q->whereHas('aluno', fn($q) => $q->where('deficiencia', $request->deficiencia));
            })
            ->selectRaw('escola_id, COUNT(DISTINCT user_id) as responderam, 
                         SUM(correta * peso) as pontos, SUM(peso) as total_peso')
            ->join('perguntas', 'respostas_simulados.pergunta_id', '=', 'perguntas.id')
            ->groupBy('escola_id')
            ->get();
    
        // Combinar dados
        return Escola::orderBy('nome')
            ->get()
            ->map(function($escola) use ($alunosPorEscola, $respostasPorEscola) {
                $alunosAtivos = $alunosPorEscola->get($escola->id)?->total ?? 0;
                $respostas = $respostasPorEscola->firstWhere('escola_id', $escola->id);
                
                $totalPeso = $respostas->total_peso ?? 1;
                $mediaPonderada = round(($respostas->pontos ?? 0) / $totalPeso * 10, 2);
                $projecaoTRI = min(10, $mediaPonderada * 1.2);
                
                // Determinar se atingiu a meta (considerando a maioria dos alunos)
                $atingiuMeta = $projecaoTRI >= ($escola->ensino_fundamental ? 5.0 : 6.0);
                
                return [
                    'nome' => $escola->nome,
                    'alunos_ativos' => $alunosAtivos,
                    'alunos_responderam' => $respostas->responderam ?? 0,
                    'media_ponderada' => $mediaPonderada,
                    'projecao_tri' => round($projecaoTRI, 2),
                    'atingiu_meta' => $atingiuMeta
                ];
            });
    }
    
    public function pdfRede(Request $request)
    {
        $data = $this->estatisticasRede($request);
        $pdf = PDF::loadView('relatorios.pdf.rede-municipal', $data);
        
        // Configurações adicionais
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOption('margin-top', 20);
        $pdf->setOption('margin-bottom', 20);
        $pdf->setOption('margin-left', 10);
        $pdf->setOption('margin-right', 10);
        
        return $pdf->download('relatorio-rede-municipal.pdf');
    }

    public function excelRede(Request $request)
    {
        $data = $this->getReportData($request);
        return Excel::download(new RedeMunicipalExport($data), 'relatorio-rede-municipal.xlsx');
    }

    private function getReportData(Request $request)
    {
        // Esta função deve conter a mesma lógica de estatisticasRede
        // para garantir que os dados sejam consistentes entre as visualizações
        // Retornar um array com todos os dados necessários para o relatório
        // Implementação similar à estatisticasRede, mas retornando os dados
    }
  
    public function estatisticasEscola(Request $request)
    {
        $simuladoId = $request->input('simulado_id');
        $escolaId = $request->input('escola_id');
        
        $simulados = Simulado::orderBy('created_at', 'desc')->get();
        $escolas = Escola::orderBy('nome')->get();

        if ($simuladoId) {
            $baseQuery = $this->baseQueryEstatisticas($simuladoId);
            
            if ($escolaId) {
                $baseQuery->where('escolas.id', $escolaId);
            }

            $resultados = $baseQuery->get();
            
            $page = LengthAwarePaginator::resolveCurrentPage();
            $perPage = 15;
            $estatisticasPorEscola = new LengthAwarePaginator(
                $resultados->forPage($page, $perPage),
                $resultados->count(),
                $perPage,
                $page,
                ['path' => LengthAwarePaginator::resolveCurrentPath()]
            );
            
            $estatisticasPorEscola->appends([
                'simulado_id' => $simuladoId,
                'escola_id' => $escolaId
            ]);
        } else {
            $estatisticasPorEscola = collect();
        }

        return view('relatorios.relatorio-escolas', [
            'estatisticasPorEscola' => $estatisticasPorEscola,
            'simuladoId' => $simuladoId,
            'simulados' => $simulados,
            'escolas' => $escolas,
            'escolaIdSelecionada' => $escolaId, // Variável corrigida aqui
            'filtrosAplicados' => $request->anyFilled(['simulado_id', 'escola_id'])
        ]);
    }

    private function baseQueryEstatisticas($simuladoId)
{
    $alunosPorEscola = User::where('role', 'aluno')
        ->select('escola_id', DB::raw('COUNT(*) as total'))
        ->groupBy('escola_id');

    $respostasSubquery = DB::table('respostas_simulados')
        ->join('perguntas', 'respostas_simulados.pergunta_id', '=', 'perguntas.id')
        ->where('simulado_id', $simuladoId)
        ->select(
            'escola_id',
            DB::raw('COUNT(DISTINCT user_id) as responderam'),
            DB::raw('SUM(correta * peso) as pontos'),
            DB::raw('SUM(peso) as total_peso')
        )
        ->groupBy('escola_id');

    return DB::table('escolas')
        ->orderBy('nome')
        ->leftJoinSub($alunosPorEscola, 'alunos', function($join) {
            $join->on('escolas.id', '=', 'alunos.escola_id');
        })
        ->leftJoinSub($respostasSubquery, 'respostas', function($join) {
            $join->on('escolas.id', '=', 'respostas.escola_id');
        })
        ->select([
            'escolas.id',
            'escolas.nome',
            // Removido 'escolas.ensino_fundamental' pois não existe na tabela
            DB::raw('COALESCE(alunos.total, 0) as alunos_ativos'),
            DB::raw('COALESCE(respostas.responderam, 0) as alunos_responderam'),
            DB::raw('COALESCE(respostas.pontos, 0) as pontos'),
            DB::raw('COALESCE(respostas.total_peso, 1) as total_peso'),
            DB::raw('ROUND(COALESCE(respostas.pontos, 0) / COALESCE(respostas.total_peso, 1) * 10, 2) as media_ponderada'),
            DB::raw('LEAST(10, ROUND(COALESCE(respostas.pontos, 0) / COALESCE(respostas.total_peso, 1) * 10 * 1.2, 2)) as projecao_tri'),
            // Simplificado o cálculo da meta sem ensino_fundamental
            DB::raw('CASE WHEN LEAST(10, ROUND(COALESCE(respostas.pontos, 0) / COALESCE(respostas.total_peso, 1) * 10 * 1.2, 2)) >= 6.0 THEN TRUE ELSE FALSE END as atingiu_meta')
        ]);
}


    private function calcularEstatisticasEscolaPaginadaQuery($simuladoId)
    {
        $alunosPorEscola = User::where('role', 'aluno')
            ->selectRaw('escola_id, COUNT(*) as total')
            ->groupBy('escola_id');

        return Escola::orderBy('nome')
            ->leftJoinSub($alunosPorEscola, 'alunos', function ($join) {
                $join->on('escolas.id', '=', 'alunos.escola_id');
            })
            ->leftJoin(
                RespostaSimulado::where('simulado_id', $simuladoId)
                    ->selectRaw('escola_id, COUNT(DISTINCT user_id) as responderam, SUM(correta * peso) as pontos, SUM(peso) as total_peso')
                    ->join('perguntas', 'respostas_simulados.pergunta_id', '=', 'perguntas.id')
                    ->groupBy('escola_id'),
                'respostas',
                function ($join) {
                    $join->on('escolas.id', '=', 'respostas.escola_id');
                }
            )
            ->select([
                'escolas.id',
                'escolas.nome',
                'escolas.ensino_fundamental',
                DB::raw('COALESCE(alunos.total, 0) as alunos_ativos'),
                DB::raw('COALESCE(respostas.responderam, 0) as alunos_responderam'),
                DB::raw('COALESCE(respostas.pontos, 0) as pontos'),
                DB::raw('COALESCE(respostas.total_peso, 1) as total_peso'),
                DB::raw('ROUND(COALESCE(respostas.pontos, 0) / COALESCE(respostas.total_peso, 1) * 10, 2) as media_ponderada'),
                DB::raw('LEAST(10, ROUND(COALESCE(respostas.pontos, 0) / COALESCE(respostas.total_peso, 1) * 10 * 1.2, 2)) as projecao_tri'),
                DB::raw('CASE WHEN LEAST(10, ROUND(COALESCE(respostas.pontos, 0) / COALESCE(respostas.total_peso, 1) * 10 * 1.2, 2)) >= (CASE WHEN escolas.ensino_fundamental THEN 5.0 ELSE 6.0 END) THEN TRUE ELSE FALSE END as atingiu_meta')
            ])
            ->groupBy('escolas.id', 'escolas.nome', 'escolas.ensino_fundamental', 'alunos.total', 'respostas.responderam', 'respostas.pontos', 'respostas.total_peso');
    }

    public function exportarEscolaPdf(Request $request)
    {
        $simuladoId = $request->input('simulado_id');
        $escolaId = $request->input('escola_id');
        
        if (!$simuladoId) {
            return back()->with('error', 'Selecione um simulado para exportar o PDF.');
        }
        
        $simulado = Simulado::findOrFail($simuladoId);
        $query = $this->calcularEstatisticasEscolaPaginadaQuery($simuladoId);
        
        if ($escolaId) {
            $query->where('escolas.id', $escolaId);
        }
        
        $estatisticasPorEscola = $query->get();
        
        $pdf = PDF::loadView('relatorios.pdf.estatisticas-escola', [
            'estatisticasPorEscola' => $estatisticasPorEscola,
            'simulado' => $simulado,
            'escolaFiltrada' => $escolaId ? Escola::find($escolaId) : null
        ]);
        
        return $pdf->download('estatisticas_escola_'.$simuladoId.'.pdf');
    }


            private function calcularEstatisticasEscolaSimples($simuladoId)
        {
            // Versão simplificada sem filtros adicionais
            $alunosPorEscola = User::where('role', 'aluno')
                ->selectRaw('escola_id, COUNT(*) as total')
                ->groupBy('escola_id')
                ->get()
                ->keyBy('escola_id');

            $respostasPorEscola = RespostaSimulado::where('simulado_id', $simuladoId)
                ->selectRaw('escola_id, COUNT(DISTINCT user_id) as responderam, 
                            SUM(correta * peso) as pontos, SUM(peso) as total_peso')
                ->join('perguntas', 'respostas_simulados.pergunta_id', '=', 'perguntas.id')
                ->groupBy('escola_id')
                ->get();

            // Combinar dados
            return Escola::orderBy('nome')
                ->get()
                ->map(function($escola) use ($alunosPorEscola, $respostasPorEscola) {
                    $alunosAtivos = $alunosPorEscola->get($escola->id)?->total ?? 0;
                    $respostas = $respostasPorEscola->firstWhere('escola_id', $escola->id);
                    
                    $totalPeso = $respostas->total_peso ?? 1;
                    $mediaPonderada = round(($respostas->pontos ?? 0) / $totalPeso * 10, 2);
                    $projecaoTRI = min(10, $mediaPonderada * 1.2);
                    
                    $atingiuMeta = $projecaoTRI >= ($escola->ensino_fundamental ? 5.0 : 6.0);
                    
                    return [
                        'nome' => $escola->nome,
                        'alunos_ativos' => $alunosAtivos,
                        'alunos_responderam' => $respostas->responderam ?? 0,
                        'media_ponderada' => $mediaPonderada,
                        'projecao_tri' => round($projecaoTRI, 2),
                        'atingiu_meta' => $atingiuMeta
                    ];
                });
        }
    

    public function exportarEscolaExcel(Request $request)
    {
        $simuladoId = $request->input('simulado_id');
        return Excel::download(new EscolaEstatisticasExport($simuladoId), 'estatisticas_escola.xlsx');
    }

    public function estatisticasAnoEnsino(Request $request)
    {
        $simuladoId = $request->input('simulado_id');
        $anoEnsinoId = $request->input('ano_id');
        
        $simulados = Simulado::with('ano')->orderBy('created_at', 'desc')->get();
        $anosEnsino = Ano::orderBy('nome')->get();
    
        $estatisticas = collect();
        $consolidado = collect();
    
        if ($simuladoId) {
            // Estatísticas detalhadas por ano de ensino
            $estatisticas = $this->calcularEstatisticasDetalhadas($simuladoId, $anoEnsinoId);
            
            // Estatísticas consolidadas por segmento
            $consolidado = $this->calcularEstatisticasConsolidadas($simuladoId);
        }
    
        return view('relatorios.ano-ensino', [
            'estatisticas' => $estatisticas,
            'consolidado' => $consolidado,
            'simuladoId' => $simuladoId,
            'anoEnsinoId' => $anoEnsinoId,
            'simulados' => $simulados,
            'anosEnsino' => $anosEnsino,
            'filtrosAplicados' => $request->anyFilled(['simulado_id', 'ano_id'])
        ]);
    }
    
    private function calcularEstatisticasDetalhadas($simuladoId, $anoEnsinoId = null)
    {
        $query = DB::table('respostas_simulados')
            ->join('users', 'respostas_simulados.user_id', '=', 'users.id')
            ->join('perguntas', 'respostas_simulados.pergunta_id', '=', 'perguntas.id')
            ->join('simulados', 'respostas_simulados.simulado_id', '=', 'simulados.id')
            ->join('anos', 'simulados.ano_id', '=', 'anos.id')
            ->where('users.role', 'aluno')
            ->where('respostas_simulados.simulado_id', $simuladoId);
    
        if ($anoEnsinoId) {
            $query->where('simulados.ano_id', $anoEnsinoId);
        }
    
        return $query->select(
                'anos.nome as ano_ensino',
                DB::raw('COUNT(DISTINCT users.id) as total_alunos'),
                DB::raw('ROUND(SUM(respostas_simulados.correta * perguntas.peso) / SUM(perguntas.peso) * 10, 2) as media_ponderada'),
                DB::raw('LEAST(10, ROUND(SUM(respostas_simulados.correta * perguntas.peso) / SUM(perguntas.peso) * 10 * 1.2, 2)) as media_tri'),
                DB::raw('ROUND(SUM(respostas_simulados.correta * perguntas.peso) / SUM(perguntas.peso) * 10 * 0.6, 2) as projecao_ideb'),
                DB::raw('CASE WHEN LEAST(10, ROUND(SUM(respostas_simulados.correta * perguntas.peso) / SUM(perguntas.peso) * 10 * 1.2, 2)) >= 6.0 THEN TRUE ELSE FALSE END as atingiu_meta'),
                DB::raw('ROUND(SUM(respostas_simulados.correta * perguntas.peso) / SUM(perguntas.peso) * 10, 2) - 
                        LEAST(10, ROUND(SUM(respostas_simulados.correta * perguntas.peso) / SUM(perguntas.peso) * 10 * 1.2, 2)) as diferenca_media_tri')
            )
            ->groupBy('anos.id', 'anos.nome')
            ->orderBy('anos.nome')
            ->paginate(15);
    }
    
    private function calcularEstatisticasConsolidadas($simuladoId)
    {
        // Fundamental I (1º ao 5º ano)
        $fundamentalI = $this->calcularEstatisticasPorSegmento($simuladoId, [1, 2, 3, 4, 5], 'Fundamental I');
        
        // Fundamental II (6º ao 9º ano)
        $fundamentalII = $this->calcularEstatisticasPorSegmento($simuladoId, [6, 7, 8, 9], 'Fundamental II');
        
        // Total Geral
        $totalGeral = $this->calcularEstatisticasPorSegmento($simuladoId, null, 'Total Geral');
    
        return collect([$fundamentalI, $fundamentalII, $totalGeral]);
    }
    
    private function calcularEstatisticasPorSegmento($simuladoId, $anosIds, $segmento)
    {
        $query = DB::table('respostas_simulados')
            ->join('users', 'respostas_simulados.user_id', '=', 'users.id')
            ->join('perguntas', 'respostas_simulados.pergunta_id', '=', 'perguntas.id')
            ->join('simulados', 'respostas_simulados.simulado_id', '=', 'simulados.id')
            ->where('users.role', 'aluno')
            ->where('respostas_simulados.simulado_id', $simuladoId);
    
        if ($anosIds !== null) {
            $query->whereIn('simulados.ano_id', $anosIds);
        }
    
        return $query->select(
                DB::raw("'$segmento' as segmento"),
                DB::raw('COUNT(DISTINCT users.id) as total_alunos'),
                DB::raw('ROUND(SUM(respostas_simulados.correta * perguntas.peso) / SUM(perguntas.peso) * 10, 2) as media_ponderada'),
                DB::raw('LEAST(10, ROUND(SUM(respostas_simulados.correta * perguntas.peso) / SUM(perguntas.peso) * 10 * 1.2, 2)) as media_tri'),
                DB::raw('ROUND(SUM(respostas_simulados.correta * perguntas.peso) / SUM(perguntas.peso) * 10 * 0.6, 2) as projecao_ideb'),
                DB::raw('ROUND(SUM(respostas_simulados.correta * perguntas.peso) / SUM(perguntas.peso) * 10, 2) - 
                        LEAST(10, ROUND(SUM(respostas_simulados.correta * perguntas.peso) / SUM(perguntas.peso) * 10 * 1.2, 2)) as diferenca_media_tri')
            )
            ->first();
    }
    
    private function calcularConsolidado($simuladoId)
    {
        if (!$simuladoId) return null;
    
        $resultados = DB::table('users')
            ->join('anos', 'users.ano_id', '=', 'anos.id')
            ->join('respostas_simulados', 'users.id', '=', 'respostas_simulados.user_id')
            ->join('perguntas', 'respostas_simulados.pergunta_id', '=', 'perguntas.id')
            ->where('users.role', 'aluno')
            ->where('respostas_simulados.simulado_id', $simuladoId)
            ->select(
                DB::raw('CASE WHEN anos.nome IN ("1º ano", "2º ano", "3º ano", "4º ano", "5º ano") THEN "Fundamental I" ELSE "Fundamental II" END as segmento'),
                DB::raw('COUNT(DISTINCT users.id) as total_alunos'),
                DB::raw('ROUND(SUM(respostas_simulados.correta * perguntas.peso) / SUM(perguntas.peso) * 10, 2) as media_ponderada'),
                DB::raw('LEAST(10, ROUND(SUM(respostas_simulados.correta * perguntas.peso) / SUM(perguntas.peso) * 10 * 1.2, 2)) as media_tri'),
                DB::raw('ROUND(SUM(respostas_simulados.correta * perguntas.peso) / SUM(perguntas.peso) * 10 * 0.6, 2) as projecao_ideb')
            )
            ->groupBy('segmento')
            ->get();
    
        // Adiciona total geral
        $geral = DB::table('users')
            ->join('respostas_simulados', 'users.id', '=', 'respostas_simulados.user_id')
            ->join('perguntas', 'respostas_simulados.pergunta_id', '=', 'perguntas.id')
            ->where('users.role', 'aluno')
            ->where('respostas_simulados.simulado_id', $simuladoId)
            ->select(
                DB::raw('"Total Geral" as segmento'),
                DB::raw('COUNT(DISTINCT users.id) as total_alunos'),
                DB::raw('ROUND(SUM(respostas_simulados.correta * perguntas.peso) / SUM(perguntas.peso) * 10, 2) as media_ponderada'),
                DB::raw('LEAST(10, ROUND(SUM(respostas_simulados.correta * perguntas.peso) / SUM(perguntas.peso) * 10 * 1.2, 2)) as media_tri'),
                DB::raw('ROUND(SUM(respostas_simulados.correta * perguntas.peso) / SUM(perguntas.peso) * 10 * 0.6, 2) as projecao_ideb')
            )
            ->first();
    
        return $resultados->push($geral);
    }
    public function exportarAnoEnsinoExcel(Request $request)
{
    $simuladoId = $request->input('simulado_id');
    $anoEnsinoId = $request->input('ano_ensino_id');
    
    if (!$simuladoId) {
        return back()->with('error', 'Selecione um simulado para exportar.');
    }
    
    return Excel::download(new EstatisticasAnoEnsinoExport($simuladoId, $anoEnsinoId), 'estatisticas_ano_ensino.xlsx');
}

        public function exportarAnoEnsinoPdf(Request $request)
        {
            $simuladoId = $request->input('simulado_id');
            $anoEnsinoId = $request->input('ano_ensino_id');
            
            if (!$simuladoId) {
                return back()->with('error', 'Selecione um simulado para exportar.');
            }
            
            $estatisticas = $this->queryEstatisticasAnoEnsino($simuladoId)
                ->when($anoEnsinoId, function($query) use ($anoEnsinoId) {
                    return $query->where('users.ano_id', $anoEnsinoId);
                })
                ->get();
            
            $consolidado = $this->calcularConsolidado($simuladoId);
            $simulado = Simulado::find($simuladoId);
            
            $pdf = PDF::loadView('relatorios.pdf.ano-ensino', [
                'estatisticas' => $estatisticas,
                'consolidado' => $consolidado,
                'simulado' => $simulado,
                'filtroAno' => $anoEnsinoId ? DB::table('anos')->find($anoEnsinoId)->nome : 'Todos'
            ]);
            
            return $pdf->download("estatisticas_ano_ensino_{$simuladoId}.pdf");
        }

    public function estatisticasQuestoes(Request $request)
    {
        $filtros = $request->only(['simulado_id', 'disciplina_id', 'ano_id']);
        
        $simulados = Simulado::orderBy('nome')->get();
        $disciplinas = Disciplina::orderBy('nome')->get();
        $anos = Ano::orderBy('nome')->get();

        $baseQuery = RespostaSimulado::query()
            ->with(['pergunta', 'simulado', 'aluno'])
            ->when($request->simulado_id, function($query) use ($request) {
                return $query->where('simulado_id', $request->simulado_id);
            });

        // Estatísticas por questão
                $estatisticasPorQuestao = $baseQuery->clone()
            ->select([
                'perguntas.id',
                'perguntas.enunciado',
                'perguntas.peso',
                'perguntas.tri_a',
                'perguntas.tri_b',
                'perguntas.tri_c',
                'disciplinas.nome as disciplina',
                'habilidades.descricao as habilidade',
                DB::raw('COUNT(*) as total_respostas'),
                DB::raw('SUM(correta) as acertos'),
                DB::raw('ROUND((SUM(correta) / COUNT(*)) * 10, 2) media_simples'), // Removido 'as'
                DB::raw('ROUND((SUM(correta * perguntas.peso) / SUM(perguntas.peso)) * 10, 2) as media_ponderada'),
                DB::raw('ROUND((SUM(correta) / COUNT(*)) * 100, 2) as percentual_acerto'),
                DB::raw('ROUND((1 - (SUM(correta) / COUNT(*))) * 100, 2) as percentual_erro'),
                DB::raw('ROUND(AVG(correta), 4) as raw_media') // Para cálculos internos
            ])
            ->join('perguntas', 'respostas_simulados.pergunta_id', '=', 'perguntas.id')
            ->join('disciplinas', 'perguntas.disciplina_id', '=', 'disciplinas.id')
            ->join('habilidades', 'perguntas.habilidade_id', '=', 'habilidades.id')
            ->when($request->disciplina_id, function($query) use ($request) {
                return $query->where('perguntas.disciplina_id', $request->disciplina_id);
            })
            ->when($request->ano_id, function($query) use ($request) {
                return $query->where('perguntas.ano_id', $request->ano_id);
            })
            ->groupBy('perguntas.id', 'perguntas.enunciado', 'perguntas.peso', 'disciplinas.nome',
                    'habilidades.descricao', 'perguntas.tri_a', 'perguntas.tri_b', 'perguntas.tri_c')
            ->orderBy('disciplinas.nome')
            ->orderBy('percentual_acerto', 'desc')
            ->paginate(15);
        // Cálculo do TRI médio para cada questão (escala 0-10)
        foreach ($estatisticasPorQuestao as $questao) {
            $questao->tri_medio = $this->calcularTriMedio($questao);
            
            // Garantir que as médias estejam na escala correta
            $questao->media_simples = number_format($questao->media_simples, 1);
            $questao->media_ponderada = number_format($questao->media_ponderada, 1);
            $questao->percentual_acerto = number_format($questao->percentual_acerto, 1);
            $questao->percentual_erro = number_format($questao->percentual_erro, 1);
        }

        // Estatísticas por disciplina (escala 0-10)
        $estatisticasPorDisciplina = $baseQuery->clone()
        ->select([
            'disciplinas.nome as disciplina',
            DB::raw('COUNT(DISTINCT perguntas.id) as total_questoes'),
            DB::raw('ROUND(AVG(correta) * 10, 2) media_simples'), // Removido 'as'
            DB::raw('ROUND((SUM(correta * perguntas.peso) / SUM(perguntas.peso)) * 10, 2) as media_ponderada'),
            DB::raw('ROUND((SUM(correta) / COUNT(*)) * 100, 2) as percentual_acerto'),
            DB::raw('ROUND(AVG(correta), 4) as raw_media') // Para cálculos internos
        ])
        ->join('perguntas', 'respostas_simulados.pergunta_id', '=', 'perguntas.id')
        ->join('disciplinas', 'perguntas.disciplina_id', '=', 'disciplinas.id')
        ->groupBy('disciplinas.nome')
        ->get();

        // Formatar médias por disciplina
        foreach ($estatisticasPorDisciplina as $disciplina) {
            $disciplina->media_simples = number_format($disciplina->media_simples, 1);
            $disciplina->media_ponderada = number_format($disciplina->media_ponderada, 1);
            $disciplina->percentual_acerto = number_format($disciplina->percentual_acerto, 1);
        }

        return view('relatorios.questoes', compact(
            'estatisticasPorQuestao',
            'estatisticasPorDisciplina',
            'simulados',
            'disciplinas',
            'anos',
            'filtros'
        ));
    }

    private function calcularTriMedio($questao)
    {
        // Parâmetros TRI da questão
        $a = $questao->tri_a; // Poder discriminativo
        $b = $questao->tri_b; // Dificuldade
        $c = $questao->tri_c; // Acerto casual
        
        // Percentual de acerto (convertido para proporção 0-1)
        $p = $questao->raw_media;
        
        // Evitar divisão por zero e valores extremos
        $p = max(0.0001, min(0.9999, $p));
        
        // Cálculo do theta (habilidade estimada)
        $theta = $b + (1 / (1.7 * $a)) * log(($p - $c) / (1 - $p));
        
        // Função logística para converter para escala 0-10
        $tri_normalizado = $c + (1 - $c) / (1 + exp(-1.7 * $a * ($theta - $b)));
        
        // Ajustar para escala 0-10
        $tri_normalizado = $tri_normalizado * 10;
        
        return round($tri_normalizado, 1);
    }

        public function exportPdf(Request $request)
        {
            $filtros = $request->only(['simulado_id', 'disciplina_id', 'ano_id']);
            
            $data = $this->getDataForExport($filtros);
            
            $pdf = PDF::loadView('relatorios.pdf.questoes', $data);
            return $pdf->download('relatorio_questoes_'.now()->format('YmdHis').'.pdf');
        }

        public function exportExcel(Request $request)
        {
            $filtros = $request->only(['simulado_id', 'disciplina_id', 'ano_id']);
            
            $data = $this->getDataForExport($filtros);
            
            return Excel::download(new QuestoesExport($data), 'relatorio_questoes_'.now()->format('YmdHis').'.xlsx');
        }

        private function getDataForExport($filtros)
        {
            $query = RespostaSimulado::query()
                ->with(['pergunta', 'simulado', 'aluno'])
                ->when($filtros['simulado_id'] ?? null, function($query) use ($filtros) {
                    return $query->where('simulado_id', $filtros['simulado_id']);
                });

            $estatisticasPorQuestao = $query->clone()
                ->select([
                    'perguntas.id',
                    'perguntas.enunciado',
                    'perguntas.peso',
                    'perguntas.tri_a',
                    'perguntas.tri_b',
                    'perguntas.tri_c',
                    'disciplinas.nome as disciplina',
                    'habilidades.descricao as habilidade',
                    DB::raw('COUNT(*) as total_respostas'),
                    DB::raw('SUM(correta) as acertos'),
                    DB::raw('ROUND(AVG(correta) * 10, 2) as media_simples'),
                    DB::raw('ROUND((SUM(correta * perguntas.peso) / SUM(perguntas.peso)) * 10, 2) as media_ponderada'),
                    DB::raw('ROUND((SUM(correta) / COUNT(*)) * 100, 2) as percentual_acerto')
                ])
                ->join('perguntas', 'respostas_simulados.pergunta_id', '=', 'perguntas.id')
                ->join('disciplinas', 'perguntas.disciplina_id', '=', 'disciplinas.id')
                ->join('habilidades', 'perguntas.habilidade_id', '=', 'habilidades.id')
                ->when($filtros['disciplina_id'] ?? null, function($query) use ($filtros) {
                    return $query->where('perguntas.disciplina_id', $filtros['disciplina_id']);
                })
                ->when($filtros['ano_id'] ?? null, function($query) use ($filtros) {
                    return $query->where('perguntas.ano_id', $filtros['ano_id']);
                })
                ->groupBy('perguntas.id', 'perguntas.enunciado', 'perguntas.peso', 'disciplinas.nome', 
                        'habilidades.descricao', 'perguntas.tri_a', 'perguntas.tri_b', 'perguntas.tri_c')
                ->orderBy('disciplinas.nome')
                ->orderBy('percentual_acerto', 'desc')
                ->get();

            foreach ($estatisticasPorQuestao as $questao) {
                $questao->tri_medio = $this->calcularTriMedio($questao);
            }

            $estatisticasPorDisciplina = $query->clone()
            ->select([
                'disciplinas.nome as disciplina',
                DB::raw('COUNT(DISTINCT perguntas.id) as total_questoes'),
                DB::raw('ROUND(AVG(correta) * 10, 2) as media_simples'),
                DB::raw('ROUND((SUM(correta * perguntas.peso) / SUM(perguntas.peso)) * 10, 2) as media_ponderada'),
                DB::raw('ROUND((SUM(correta) / COUNT(*)) * 100, 2) as percentual_acerto')
            ])
            ->join('perguntas', 'respostas_simulados.pergunta_id', '=', 'perguntas.id')
            ->join('disciplinas', 'perguntas.disciplina_id', '=', 'disciplinas.id')
            ->groupBy('disciplinas.nome')
            ->get();
        

            return [
                'estatisticasPorQuestao' => $estatisticasPorQuestao,
                'estatisticasPorDisciplina' => $estatisticasPorDisciplina,
                'filtros' => $filtros
            ];
        }
        public function estatisticasHabilidade(Request $request)
    {
        $filtros = $request->only(['simulado_id', 'disciplina_id', 'ano_id', 'habilidade_id']);

        $simulados = Simulado::orderBy('nome')->get();
        $disciplinas = Disciplina::orderBy('nome')->get();
        $anos = Ano::orderBy('nome')->get();
        $habilidades = Habilidade::orderBy('descricao')->get();


        $baseQuery = RespostaSimulado::query()
            ->join('perguntas', 'respostas_simulados.pergunta_id', '=', 'perguntas.id')
            ->join('habilidades', 'perguntas.habilidade_id', '=', 'habilidades.id')
            ->join('disciplinas', 'perguntas.disciplina_id', '=', 'disciplinas.id')
            
            ->select([
                'habilidades.id',
                'habilidades.descricao',
                'disciplinas.nome as disciplina_nome',
                DB::raw('COUNT(DISTINCT perguntas.id) as total_questoes'),
                DB::raw('COUNT(*) as total_respostas'),
                DB::raw('SUM(correta) as acertos'),
                DB::raw('ROUND((SUM(correta) / COUNT(*)) * 10, 2) as media_simples'),
                DB::raw('ROUND((SUM(correta * perguntas.peso) / SUM(perguntas.peso)) * 10, 2) as media_ponderada'),
                DB::raw('ROUND((SUM(correta) / COUNT(*)) * 100, 2) as percentual_acerto'),
                DB::raw('ROUND(AVG(correta), 4) as raw_media') // Para cálculos internos
            ])
            ->when($request->simulado_id, function ($query) use ($request) {
                return $query->where('respostas_simulados.simulado_id', $request->simulado_id);
            })
            ->when($request->disciplina_id, function ($query) use ($request) {
                return $query->where('perguntas.disciplina_id', $request->disciplina_id);
            })
            ->when($request->ano_id, function ($query) use ($request) {
                return $query->where('perguntas.ano_id', $request->ano_id);
            })
            ->when($request->habilidade_id, function ($query) use ($request) {
                return $query->where('perguntas.habilidade_id', $request->habilidade_id);
            })
            ->groupBy('habilidades.id', 'habilidades.descricao', 'disciplinas.nome')
            ->orderBy('disciplinas.nome')
            ->orderBy('habilidades.descricao');

        $estatisticasPorHabilidade = $baseQuery->paginate(15);

        // Cálculo do TRI médio para cada habilidade (escala 0-10)
        foreach ($estatisticasPorHabilidade as $habilidade) {
            $habilidade->tri_medio = $this->calcularTriMedioHabilidade($habilidade);

            // Garantir que as médias estejam na escala correta
            $habilidade->media_simples = number_format($habilidade->media_simples, 1);
            $habilidade->media_ponderada = number_format($habilidade->media_ponderada, 1);
            $habilidade->percentual_acerto = number_format($habilidade->percentual_acerto, 1);
        }

        return view('relatorios.habilidades', compact(
            'estatisticasPorHabilidade',
            'simulados',
            'habilidades',
            'disciplinas',
            'anos',
            'filtros'
        ));
    }

    private function calcularTriMedioHabilidade($habilidade)
    {
        // Usando a média bruta de acertos para estimar o TRI da habilidade
        $p = $habilidade->raw_media;

        // Evitar divisão por zero e valores extremos
        $p = max(0.0001, min(0.9999, $p));

        // Parâmetros TRI médios (podem ser ajustados conforme necessidade)
        $a = 1.0; // Poder discriminativo médio
        $b = 0.0; // Dificuldade média
        $c = 0.2; // Acerto casual médio

        // Cálculo do theta (habilidade estimada)
        $theta = $b + (1 / (1.7 * $a)) * log(($p - $c) / (1 - $p));

        // Função logística para converter para escala 0-10
        $tri_normalizado = $c + (1 - $c) / (1 + exp(-1.7 * $a * ($theta - $b)));

        // Ajustar para escala 0-10
        $tri_normalizado = $tri_normalizado * 10;

        return round($tri_normalizado, 1);
    }

    public function pdfHabilidade(Request $request)
    {
        $filtros = $request->only(['simulado_id', 'disciplina_id', 'ano_id']);
        $data = $this->getDataForHabilidadeExport($filtros);
        $pdf = PDF::loadView('relatorios.pdf.habilidades', $data);
        return $pdf->download('relatorio_habilidades_' . now()->format('YmdHis') . '.pdf');
    }

    public function excelHabilidade(Request $request)
    {
        $filtros = $request->only(['simulado_id', 'disciplina_id', 'ano_id']);
        $data = $this->getDataForHabilidadeExport($filtros);
        return Excel::download(new HabilidadesExport($data), 'relatorio_habilidades_' . now()->format('YmdHis') . '.xlsx');
    }

    private function getDataForHabilidadeExport($filtros)
    {
        $query = RespostaSimulado::query()
            ->join('perguntas', 'respostas_simulados.pergunta_id', '=', 'perguntas.id')
            ->join('habilidades', 'perguntas.habilidade_id', '=', 'habilidades.id')
            ->join('disciplinas', 'perguntas.disciplina_id', '=', 'disciplinas.id')
            ->select([
                'habilidades.id',
                'habilidades.descricao',
                'disciplinas.nome as disciplina_nome',
                DB::raw('COUNT(DISTINCT perguntas.id) as total_questoes'),
                DB::raw('COUNT(*) as total_respostas'),
                DB::raw('SUM(correta) as acertos'),
                DB::raw('ROUND((SUM(correta) / COUNT(*)) * 10, 2) as media_simples'),
                DB::raw('ROUND((SUM(correta * perguntas.peso) / SUM(perguntas.peso)) * 10, 2) as media_ponderada'),
                DB::raw('ROUND((SUM(correta) / COUNT(*)) * 100, 2) as percentual_acerto'),
                DB::raw('ROUND(AVG(correta), 4) as raw_media') // Para cálculos internos
            ])
            ->when($filtros['simulado_id'] ?? null, function ($query) use ($filtros) {
                return $query->where('respostas_simulados.simulado_id', $filtros['simulado_id']);
            })
            ->when($filtros['disciplina_id'] ?? null, function ($query) use ($filtros) {
                return $query->where('perguntas.disciplina_id', $filtros['disciplina_id']);
            })
            ->when($filtros['ano_id'] ?? null, function ($query) use ($filtros) {
                return $query->where('perguntas.ano_id', $filtros['ano_id']);
            })
            ->groupBy('habilidades.id', 'habilidades.descricao', 'disciplinas.nome')
            ->orderBy('disciplinas.nome')
            ->orderBy('habilidades.descricao')
            ->get();

        foreach ($estatisticasPorHabilidade = $query as $habilidade) {
            $habilidade->tri_medio = $this->calcularTriMedioHabilidade($habilidade);
        }

        return [
            'estatisticasPorHabilidade' => $estatisticasPorHabilidade,
            'filtros' => $filtros
        ];
    }
    public function estatisticasRaca(Request $request)
    {
        // Validação dos filtros
        $request->validate([
            'simulado_id' => 'nullable|exists:simulados,id',
            'ano_id' => 'nullable|exists:anos,id',
            'disciplina_id' => 'nullable|exists:disciplinas,id',
            'raca' => 'nullable|string|max:255', // Adiciona a validação para o filtro de raça
        ]);

        // Obter dados para os filtros
        $simulados = Simulado::orderBy('nome')->get();
        $anos = Ano::orderBy('nome')->get();
        $disciplinas = Disciplina::orderBy('nome')->get();
        $racasDisponiveis = RespostaSimulado::distinct('raca')->orderBy('raca')->pluck('raca')->toArray(); // Obtém as raças distintas

        // Se não tiver simulado selecionado, retorna apenas os filtros
        if (!$request->simulado_id) {
            return view('relatorios.raca', compact('simulados', 'anos', 'disciplinas', 'racasDisponiveis'));
        }

        // Query base para estatísticas por raça
        $query = RespostaSimulado::where('simulado_id', $request->simulado_id)
            ->join('users', 'respostas_simulados.user_id', 'users.id')
            ->join('perguntas', 'respostas_simulados.pergunta_id', 'perguntas.id')
            ->select(
                'respostas_simulados.raca',
                DB::raw('COUNT(*) as total_respostas'),
                DB::raw('SUM(correta) as acertos'),
                DB::raw('ROUND((SUM(correta) / COUNT(*)) * 100, 2) as percentual_acerto'),
                DB::raw('ROUND((SUM(correta * perguntas.peso) / SUM(perguntas.peso)) * 10, 2) as media_ponderada')
            )
            ->groupBy('respostas_simulados.raca');

        // Aplicar filtros adicionais
        if ($request->ano_id) {
            $query->where('users.ano_id', $request->ano_id);
        }

        if ($request->disciplina_id) {
            $query->where('perguntas.disciplina_id', $request->disciplina_id);
        }

        // Aplica o filtro por raça se estiver presente na requisição
        if ($request->raca) {
            $query->where('respostas_simulados.raca', $request->raca);
        }

        $estatisticasPorRaca = $query->get();

        // Calcular totais
        $totalRespostas = $estatisticasPorRaca->sum('total_respostas');
        $totalAcertos = $estatisticasPorRaca->sum('acertos');

        // Adicionar porcentagem do total para cada raça
        $estatisticasPorRaca = $estatisticasPorRaca->map(function ($item) use ($totalRespostas) {
            $item->percentual_total = $totalRespostas > 0 ? round(($item->total_respostas / $totalRespostas) * 100, 2) : 0;
            return $item;
        });

        return view('relatorios.raca', compact(
            'simulados',
            'anos',
            'disciplinas',
            'estatisticasPorRaca',
            'totalRespostas',
            'totalAcertos',
            'racasDisponiveis' // Passa as raças disponíveis para o filtro na blade
        ));
    }

    public function pdfRaca(Request $request)
    {
        $data = $this->estatisticasRaca($request);

        if ($data instanceof \Illuminate\View\View) {
            $data = $data->getData();
        }

        $pdf = PDF::loadView('relatorios.pdf.raca', $data);
        return $pdf->download('estatisticas_por_raca.pdf');
    }

    public function excelRaca(Request $request)
    {
        return Excel::download(new RelatorioRacaExport($request), 'estatisticas_por_raca.xlsx');
    }
    public function estatisticasDeficiencia(Request $request)
{
    // Obter filtros
    $simuladoId = $request->input('simulado_id');
    $deficienciaSelecionada = $request->input('deficiencia');
    $escolaIdSelecionada = $request->input('escola_id');

    // Obter dados para filtros
    $escolas = Escola::all();
    $deficienciasNoBanco = User::where('role', 'aluno')
        ->whereNotNull('deficiencia')
        ->distinct()
        ->pluck('deficiencia')
        ->sort()
        ->prepend('ND');

    $opcoesDeficiencia = collect($deficienciasNoBanco)->mapWithKeys(function ($deficiencia) {
        return [$deficiencia => $this->traduzirDeficiencia($deficiencia)];
    })->toArray();

    $filtros = [
        'simulados' => Simulado::all(),
        'deficiencias' => $opcoesDeficiencia,
        'escolas' => $escolas,
    ];

    // Se não tiver simulado selecionado, retorna apenas os filtros
    if (empty($simuladoId)) {
        return view('relatorios.deficiencias', [
            'filtros' => $filtros,
            'request' => $request,
            'semFiltro' => true
        ]);
    }

    // Query base para alunos com deficiência
    $query = User::where('role', 'aluno')
        ->when($deficienciaSelecionada && $deficienciaSelecionada !== 'ND', function($q) use ($deficienciaSelecionada) {
            return $q->where('deficiencia', $deficienciaSelecionada);
        })
        ->when($deficienciaSelecionada === 'ND', function($q) {
            return $q->whereNull('deficiencia');
        })
        ->when(!$deficienciaSelecionada, function($q) {
            return $q->whereNotNull('deficiencia');
        })
        ->when($escolaIdSelecionada, function($q) use ($escolaIdSelecionada) {
            return $q->where('escola_id', $escolaIdSelecionada);
        });

    $totalAlunos = $query->count();
    $alunosIds = $query->pluck('id');

    // Estatísticas por aluno
    $estatisticas = RespostaSimulado::whereIn('user_id', $alunosIds)
        ->where('simulado_id', $simuladoId)
        ->with(['user.escola', 'user.turma', 'simulado.perguntas'])
        ->get()
        ->groupBy('user_id')
        ->map(function ($respostas, $userId) {
            $aluno = $respostas->first()->user;
            $simulado = $respostas->first()->simulado;
            $totalQuestoes = $simulado->perguntas->count();
            $acertos = $respostas->where('correta', true)->count();
            $porcentagem = $totalQuestoes > 0 ? ($acertos / $totalQuestoes) * 100 : 0;

            return [
                'aluno_id' => $userId,
                'escola_nome' => $aluno->escola->nome ?? 'N/A',
                'aluno_nome' => $aluno->name,
                'deficiencia' => $aluno->deficiencia ?? 'ND',
                'turma' => $aluno->turma->nome_turma ?? 'N/A',
                'total_questoes' => $totalQuestoes,
                'acertos' => $acertos,
                'porcentagem' => $porcentagem,
                'media' => $totalQuestoes > 0 ? ($acertos / $totalQuestoes) * 10 : 0,
                'desempenho' => $porcentagem >= 70 ? 'Ótimo' :
                                    ($porcentagem >= 50 ? 'Regular' : 'Ruim')
            ];
        })
        ->sortByDesc('porcentagem')
        ->values();

    // Médias gerais
    $mediaGeral = $estatisticas->avg('media');
    $totalResponderam = $estatisticas->count();

    // Exportações
    if ($request->has('export_pdf')) {
        $pdf = PDF::loadView('relatorios.pdf.deficiencias', [
            'estatisticas' => $estatisticas,
            'mediaGeral' => $mediaGeral,
            'totalAlunos' => $totalAlunos,
            'totalResponderam' => $totalResponderam,
            'filtros' => $filtros,
            'request' => $request
        ]);
        return $pdf->download('relatorio_deficiencias_'.now()->format('Ymd_His').'.pdf');
    }

    if ($request->has('export_excel')) {
        return Excel::download(new DeficienciasExport($estatisticas, $request), 'relatorio_deficiencias_'.now()->format('Ymd_His').'.xlsx');
    }

    return view('relatorios.deficiencias', [
        'estatisticas' => $estatisticas,
        'mediaGeral' => $mediaGeral,
        'totalAlunos' => $totalAlunos,
        'totalResponderam' => $totalResponderam,
        'filtros' => $filtros,
        'request' => $request,
        'semFiltro' => false
    ]);
}

private function traduzirDeficiencia(?string $deficiencia): string
{
    return match ($deficiencia) {
        'DV' => 'Deficiência Visual',
        'DA' => 'Deficiência Auditiva',
        'DF' => 'Deficiência Física',
        'DI' => 'Deficiência Intelectual',
        'TEA' => 'Transtorno Espectro Autista',
        'ND' => 'Sem Deficiência',
        null => 'Sem Deficiência',
        default => $deficiencia,
    };
}

public function exportDeficienciaExcel(Request $request)
{
    $user = Auth::user();

    if (!in_array($user->role, ['admin', 'inclusiva', 'coordenador'])) {
        abort(403, 'Acesso não autorizado.');
    }

    $simuladoId = $request->input('simulado_id');
    $anoId = $request->input('ano_id');
    $turmaId = $request->input('turma_id');

    $alunosDeficiencia = User::where('escola_id', $user->escola_id)
        ->where('role', 'aluno')
        ->whereNotNull('deficiencia')
        ->when($turmaId, fn($q) => $q->where('turma_id', $turmaId))
        ->get();

    $data = collect();

    foreach ($alunosDeficiencia as $aluno) {
        $respostas = RespostaSimulado::where('user_id', $aluno->id)
            ->where('simulado_id', $simuladoId)
            ->when($anoId, fn($q) => $q->whereHas('simulado', fn($sq) => $sq->where('ano_id', $anoId)))
            ->with('simulado.perguntas')
            ->get();

        if ($respostas->isNotEmpty()) {
            $totalAcertos = $respostas->where('correta', true)->count();
            $totalQuestoes = $respostas->first()->simulado->perguntas_count;
            $media = $totalQuestoes > 0 ? round(($totalAcertos / $totalQuestoes) * 10, 2) : 0;

            $data->push([
                'Nome do Aluno' => $aluno->name,
                'Deficiência' => $aluno->deficiencia,
                'Simulado' => $respostas->first()->simulado->nome ?? 'N/A',
                'Acertos' => $totalAcertos,
                'Total de Questões' => $totalQuestoes,
                'Média' => $media,
            ]);
        }
    }

    return Excel::download(new EstatisticasDeficienciaExport($data), 'estatisticas_deficiencia_' . now()->format('Ymd_His') . '.xlsx');
}

public function exportDeficienciaPdf(Request $request)
{
    $user = Auth::user();

    if (!in_array($user->role, ['admin', 'inclusiva', 'coordenador'])) {
        abort(403, 'Acesso não autorizado.');
    }

    $simuladoId = $request->input('simulado_id');
    $anoId = $request->input('ano_id');
    $turmaId = $request->input('turma_id');

    $alunosDeficiencia = User::where('escola_id', $user->escola_id)
        ->where('role', 'aluno')
        ->whereNotNull('deficiencia')
        ->when($turmaId, fn($q) => $q->where('turma_id', $turmaId))
        ->get();

    $resultadosPdf = collect();

    foreach ($alunosDeficiencia as $aluno) {
        $respostas = RespostaSimulado::where('user_id', $aluno->id)
            ->where('simulado_id', $simuladoId)
            ->when($anoId, fn($q) => $q->whereHas('simulado', fn($sq) => $sq->where('ano_id', $anoId)))
            ->with('simulado.perguntas')
            ->get();

        if ($respostas->isNotEmpty()) {
            $totalAcertos = $respostas->where('correta', true)->count();
            $totalQuestoes = $respostas->first()->simulado->perguntas_count;
            $media = $totalQuestoes > 0 ? round(($totalAcertos / $totalQuestoes) * 10, 2) : 0;

            $resultadosPdf->push([
                'aluno' => $aluno,
                'acertos' => $totalAcertos,
                'total_questoes' => $totalQuestoes,
                'media' => $media,
                'deficiencia' => $aluno->deficiencia,
            ]);
        }
    }

    $pdf = PDF::loadView('relatorios.pdf.deficiencias', compact('resultadosPdf', 'request'));
    return $pdf->download('relatorio_deficiencias_' . now()->format('Ymd_His') . '.pdf');
}

       
}