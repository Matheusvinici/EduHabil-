<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Simulado;
use App\Models\Ano;
use App\Models\Escola;
use App\Models\Disciplina;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache; 

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
            $request->validate([
                'simulado_id' => 'nullable|exists:simulados,id',
                'ano_id' => 'nullable|exists:anos,id',
                'escola_id' => 'nullable|exists:escolas,id',
                'deficiencia' => 'nullable|string'
            ]);
        
            $simulados = Simulado::orderBy('nome')->get();
            $anos = Ano::orderBy('nome')->get();
            $escolas = Escola::orderBy('nome')->get();
        
            if (!$request->simulado_id) {
                return view('relatorios.rede-municipal', compact('simulados', 'anos', 'escolas'));
            }
        
            // Query base para alunos
            $queryAlunos = User::where('role', 'aluno');
            
            // Query base para respostas
            $queryRespostas = RespostaSimulado::where('simulado_id', $request->simulado_id)
                ->with(['aluno', 'pergunta']);
        
            // Aplicar filtros
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
        
            // Dados gerais
            $totalAlunos = $queryAlunos->count();
            $alunosAtivos = $queryAlunos->count();
            $alunosResponderam = $queryRespostas->distinct('user_id')->count('user_id');
        
            // Cálculo das médias tradicionais por peso
            $mediasPeso = [
                'peso_1' => $this->calcularMediaPorPeso($queryRespostas, 1),
                'peso_2' => $this->calcularMediaPorPeso($queryRespostas, 2),
                'peso_3' => $this->calcularMediaPorPeso($queryRespostas, 3),
                'media_geral' => $this->calcularMediaGeral($queryRespostas)
            ];
        
            // Cálculo TRI completo
            $analiseTRI = $this->calcularAnaliseTRICompleta($queryRespostas);
        
            // Dados das escolas
            $dadosEscolas = $this->prepararDadosEscolas($request->simulado_id, $request);
        
            // Quadrantes baseados na média TRI
            $quadrantes = $this->analisarQuadrantes($dadosEscolas, $analiseTRI['media_geral']);
        
            // Projeção por segmento com TRI
            $projecaoSegmento = [
                '1a5' => $this->calcularProjecaoSegmentoTRI($queryRespostas, range(1, 5)),
                '6a9' => $this->calcularProjecaoSegmentoTRI($queryRespostas, range(6, 9))
            ];
        
            // Dados para gráficos
            $graficoData = $this->prepararDadosGraficos($queryRespostas);
        
        
            return view('relatorios.rede-municipal', [
                'simulados' => $simulados,
                'anos' => $anos,
                'escolas' => $escolas,
                'totalAlunos' => $totalAlunos,
                'alunosAtivos' => $alunosAtivos,
                'alunosResponderam' => $alunosResponderam,
                'mediasPeso' => $mediasPeso,
                'analiseTRI' => $analiseTRI,
                'projecaoSegmento' => $projecaoSegmento,
                'graficoData' => $graficoData,
                'dadosEscolas' => $dadosEscolas,
                'quadrantes' => $quadrantes,
                'mediaGeralTRI' => $analiseTRI['media_geral'],
                'filtros' => $request->only(['simulado_id', 'ano_id', 'escola_id', 'deficiencia'])
            ]);
        }
        
        private function calcularProjecaoSegmentoTRI($queryRespostas, $rangeAnos)
        {
            // Mapeamento de turmas para anos (ajuste conforme sua estrutura real)
            $turmasPorAno = [
                1 => range(1, 2),   // Turmas do 1º ano
                2 => range(3, 4),    // Turmas do 2º ano
                3 => range(5, 6),    // Turmas do 3º ano
                4 => range(7, 8),    // Turmas do 4º ano
                5 => range(9, 10),   // Turmas do 5º ano
                6 => range(11, 13),  // Turmas do 6º ano
                7 => range(14, 16),  // Turmas do 7º ano
                8 => range(17, 19),  // Turmas do 8º ano
                9 => range(20, 22)   // Turmas do 9º ano
            ];
        
            // Obter todas as turmas dos anos solicitados
            $turmasFiltro = [];
            foreach ($rangeAnos as $ano) {
                if (isset($turmasPorAno[$ano])) {
                    $turmasFiltro = array_merge($turmasFiltro, $turmasPorAno[$ano]);
                }
            }
        
            // Obter respostas filtradas
            $respostas = (clone $queryRespostas)
                ->whereHas('aluno', function($q) use ($turmasFiltro) {
                    $q->where('role', 'aluno')
                      ->whereIn('turma_id', $turmasFiltro);
                })
                ->with(['pergunta', 'aluno'])
                ->get();
        
            // Inicializar variáveis para cálculo
            $totalPontos = 0;
            $totalPeso = 0;
            $somaTRI = 0;
            $countTRI = 0;
            
            // Calcular médias tradicionais e TRI
            foreach ($respostas as $resposta) {
                if (!$resposta->pergunta) continue;
                
                $peso = $resposta->pergunta->peso;
                
                // Média tradicional
                $totalPontos += $resposta->correta * $peso;
                $totalPeso += $peso;
                
                // Cálculo TRI - usando os parâmetros da questão
                $triA = $resposta->pergunta->tri_a ?? 1;     // Discriminação
                $triB = $resposta->pergunta->tri_b ?? 0;     // Dificuldade
                $triC = $resposta->pergunta->tri_c ?? 0.2;   // Acerto casual
                
                // Probabilidade de acerto para theta=0 (habilidade média)
                $probabilidade = $triC + (1 - $triC) / (1 + exp(-1.7 * $triA * (0 - $triB)));
                
                // Valor TRI contribuído por esta resposta
                $valorTRI = $resposta->correta ? $probabilidade : (1 - $probabilidade);
                
                // Soma ponderada pelo peso da questão
                $somaTRI += $valorTRI * $peso;
                $countTRI += $peso;
            }
            
            // Calcular médias finais
            $mediaTradicional = $totalPeso > 0 ? round(($totalPontos / $totalPeso) * 10, 2) : 0;
            $mediaTRI = $countTRI > 0 ? round(($somaTRI / $countTRI) * 10, 2) : 0;
        
            // Definir meta e projeção
            $meta = in_array(1, $rangeAnos) ? 6.0 : 5.0; // Meta mais alta para anos iniciais
            $projecaoTRI = min(10, $mediaTRI * 1.05); // Projeção de 5% de crescimento
        
            return [
                'media' => $mediaTradicional,
                'media_tri' => $mediaTRI,
                'projecao' => round($projecaoTRI, 2),
                'atingiu_meta' => $projecaoTRI >= $meta,
                'diferenca' => round($projecaoTRI - $meta, 2)
            ];
        }
        public function escolasQuadrante(Request $request)
        {
            $request->validate([
                'simulado_id' => 'required|exists:simulados,id',
                'quadrante' => 'required|in:q1,q2,q3,q4',
                'ano_id' => 'nullable|exists:anos,id',
                'deficiencia' => 'nullable|string'
            ]);
        
            // Obter dados das escolas (já filtrados por alunos respondentes)
            $dadosEscolas = $this->prepararDadosEscolas($request->simulado_id, $request);
            
            // Calcular média geral TRI
            $analiseTRI = $this->calcularAnaliseTRICompleta(
                RespostaSimulado::where('simulado_id', $request->simulado_id)
            );
            
            // Filtrar escolas por quadrante com regras consistentes
            $limiteGrande = 200;
            $escolasQuadrante = collect($dadosEscolas)->filter(function($escola) use ($request, $analiseTRI, $limiteGrande) {
                // Ignorar escolas sem alunos respondentes
                if ($escola['total_alunos'] <= 0) return false;
                
                $grande = $escola['total_alunos'] >= $limiteGrande;
                $acimaMedia = $escola['media_tri'] >= $analiseTRI['media_geral']; // >= em vez de >
                
                switch($request->quadrante) {
                    case 'q1': return $grande && $acimaMedia;
                    case 'q2': return $grande && !$acimaMedia;
                    case 'q3': return !$grande && !$acimaMedia;
                    case 'q4': return !$grande && $acimaMedia;
                    default: return false;
                }
            })->values(); // Reindexar o array
        
            // Títulos dos quadrantes
            $titulosQuadrantes = [
                'q1' => 'Escolas com grande quantidade de matrículas (200+) e desempenho TRI acima da média',
                'q2' => 'Escolas com grande quantidade de matrículas (200+) e desempenho TRI abaixo da média',
                'q3' => 'Escolas com menor quantidade de matrículas (<200) e desempenho TRI abaixo da média',
                'q4' => 'Escolas com menor quantidade de matrículas (<200) e desempenho TRI acima da média'
            ];
        
            return view('relatorios.escolas-quadrante', [
                'escolas' => $escolasQuadrante,
                'quadrante' => $request->quadrante,
                'tituloQuadrante' => $titulosQuadrantes[$request->quadrante],
                'mediaGeralTRI' => $analiseTRI['media_geral'],
                'filtros' => $request->only(['simulado_id', 'ano_id', 'deficiencia'])
            ]);
        }
        private function prepararDadosEscolas($simuladoId, $request)
        {
            $escolas = Escola::withCount(['alunos' => function($q) {
                $q->where('role', 'aluno');
            }])
            ->with(['alunos' => function($q) use ($simuladoId) {
                $q->where('role', 'aluno')
                ->with(['respostasSimulado' => function($q) use ($simuladoId) {
                    $q->where('simulado_id', $simuladoId)
                        ->with('pergunta');
                }]);
            }])
            ->get()
            ->map(function ($escola) {
                $alunosComRespostas = $escola->alunos->filter(function ($aluno) {
                    return $aluno->respostasSimulado->isNotEmpty();
                });
                
                // Cálculo da média tradicional
                $mediaSimulado = 0;
                $totalPontos = 0;
                $totalPeso = 0;
                
                // Cálculo da média TRI
                $mediaTri = 0;
                $totalTri = 0;
                $countTri = 0;
                
                foreach ($alunosComRespostas as $aluno) {
                    foreach ($aluno->respostasSimulado as $resposta) {
                        // Média tradicional
                        $totalPontos += $resposta->correta * $resposta->pergunta->peso;
                        $totalPeso += $resposta->pergunta->peso;
                        
                        // Cálculo TRI
                        $triA = $resposta->pergunta->tri_a ?? 1;
                        $triB = $resposta->pergunta->tri_b ?? 0;
                        $triC = $resposta->pergunta->tri_c ?? 0.2;
                        
                        $probabilidade = $triC + (1 - $triC) / (1 + exp(-1.7 * $triA * (0 - $triB)));
                        $valorTRI = $resposta->correta ? $probabilidade : (1 - $probabilidade);
                        $totalTri += $valorTRI;
                        $countTri++;
                    }
                }
                
                $mediaSimulado = $totalPeso > 0 ? round(($totalPontos / $totalPeso) * 10, 2) : 0;
                $mediaTri = $countTri > 0 ? round(($totalTri / $countTri) * 10, 2) : 0;
                
                return [
                    'id' => $escola->id,
                    'nome' => $escola->nome,
                    'total_alunos' => $escola->alunos_count,
                    'media_simulado' => $mediaSimulado,
                    'media_tri' => $mediaTri,
                    'alunos_respondentes' => $alunosComRespostas->count(),
                    'debug' => [
                        'total_pontos' => $totalPontos,
                        'total_peso' => $totalPeso,
                        'total_tri' => $totalTri,
                        'count_tri' => $countTri
                    ]
                ];
            })
            ->toArray();

            return $escolas;
        }
        private function analisarQuadrantes(array $dadosEscolas, float $mediaGeralTRI): array
        {
            $limiteGrande = 200;
            
            $quadrantes = [
                'q1' => ['count' => 0, 'escolas' => [], 'media_tri' => 0, 'total_alunos' => 0],
                'q2' => ['count' => 0, 'escolas' => [], 'media_tri' => 0, 'total_alunos' => 0],
                'q3' => ['count' => 0, 'escolas' => [], 'media_tri' => 0, 'total_alunos' => 0],
                'q4' => ['count' => 0, 'escolas' => [], 'media_tri' => 0, 'total_alunos' => 0]
            ];
        
            foreach ($dadosEscolas as $escola) {
                if (empty($escola['total_alunos'])) continue;
                
                $grande = $escola['total_alunos'] >= $limiteGrande;
                $acimaMedia = $escola['media_tri'] >= $mediaGeralTRI; // Corrigido para >=
        
                if ($grande && $acimaMedia) {
                    $quadrante = 'q1';
                } elseif ($grande && !$acimaMedia) {
                    $quadrante = 'q2';
                } elseif (!$grande && !$acimaMedia) {
                    $quadrante = 'q3';
                } else {
                    $quadrante = 'q4';
                }
        
                $quadrantes[$quadrante]['count']++;
                $quadrantes[$quadrante]['escolas'][] = $escola['nome'];
                $quadrantes[$quadrante]['media_tri'] += $escola['media_tri'];
                $quadrantes[$quadrante]['total_alunos'] += $escola['total_alunos'];
            }
        
            // Calcular médias finais
            foreach ($quadrantes as $key => $quadrante) {
                if ($quadrante['count'] > 0) {
                    $quadrantes[$key]['media_tri'] = round($quadrante['media_tri'] / $quadrante['count'], 2);
                }
            }
            
            return $quadrantes;
        }
        private function calcularAnaliseTRICompleta($queryRespostas)
        {
            $respostas = (clone $queryRespostas)->with('pergunta')->get();
            
            if ($respostas->isEmpty()) {
                return [
                    'peso_1' => ['media' => 0, 'dificuldade' => 0, 'discriminacao' => 0],
                    'peso_2' => ['media' => 0, 'dificuldade' => 0, 'discriminacao' => 0],
                    'peso_3' => ['media' => 0, 'dificuldade' => 0, 'discriminacao' => 0],
                    'media_geral' => 0,
                    'indice_consistencia' => 0
                ];
            }

            // Agrupar por peso
            $porPeso = [
                1 => ['soma' => 0, 'count' => 0, 'dificuldade' => 0, 'discriminacao' => 0],
                2 => ['soma' => 0, 'count' => 0, 'dificuldade' => 0, 'discriminacao' => 0],
                3 => ['soma' => 0, 'count' => 0, 'dificuldade' => 0, 'discriminacao' => 0]
            ];

            foreach ($respostas as $resposta) {
                $peso = $resposta->pergunta->peso;
                $triA = $resposta->pergunta->tri_a;
                $triB = $resposta->pergunta->tri_b;
                $triC = $resposta->pergunta->tri_c;
                
                // Fórmula TRI completa
                $probabilidade = $triC + (1 - $triC) / (1 + exp(-1.7 * $triA * (0 - $triB)));
                $valorTRI = $resposta->correta ? $probabilidade : (1 - $probabilidade);
                
                $porPeso[$peso]['soma'] += $valorTRI;
                $porPeso[$peso]['count']++;
                $porPeso[$peso]['dificuldade'] += $triB;
                $porPeso[$peso]['discriminacao'] += $triA;
            }

            // Calcular médias por peso
            $resultados = [];
            foreach ([1, 2, 3] as $peso) {
                $count = $porPeso[$peso]['count'];
                $resultados["peso_$peso"] = [
                    'media' => $count > 0 ? round(($porPeso[$peso]['soma'] / $count) * 10, 2) : 0,
                    'dificuldade' => $count > 0 ? round($porPeso[$peso]['dificuldade'] / $count, 2) : 0,
                    'discriminacao' => $count > 0 ? round($porPeso[$peso]['discriminacao'] / $count, 2) : 0
                ];
            }

            // Média geral TRI
            $totalCount = $porPeso[1]['count'] + $porPeso[2]['count'] + $porPeso[3]['count'];
            $resultados['media_geral'] = $totalCount > 0 
                ? round(($porPeso[1]['soma'] + $porPeso[2]['soma'] + $porPeso[3]['soma']) / $totalCount * 10, 2)
                : 0;
            
            // Índice de consistência interna (Alpha de Cronbach simplificado)
            $resultados['indice_consistencia'] = $this->calcularConsistenciaInterna($respostas);

            return $resultados;
        }     
        private function calcularConsistenciaInterna($respostas)
        {
            // Implementação simplificada do Alpha de Cronbach
            $alunos = $respostas->groupBy('user_id');
            $n = $alunos->count();
            
            if ($n < 2) return 0;
            
            // Converter para array os scores antes de calcular a variância
            $scores = $alunos->map(function ($respostasAluno) {
                return $respostasAluno->sum('correta');
            })->values()->all();
            
            $varianciaTotal = $this->calcularVariancia($scores);
            
            $varianciaItens = $respostas->groupBy('pergunta_id')->map(function ($respostasItem) {
                // Converter para array os valores antes de calcular a variância
                return $this->calcularVariancia($respostasItem->pluck('correta')->all());
            })->sum();
            
            return round(($n / ($n - 1)) * (1 - ($varianciaItens / $varianciaTotal)), 2);
        }
        private function calcularVariancia($valores)
        {
            if (!is_array($valores)) {
                $valores = (array)$valores;
            }
            
            $count = count($valores);
            if ($count < 2) return 0;
            
            $media = array_sum($valores) / $count;
            $somaQuadrados = 0;
            
            foreach ($valores as $valor) {
                $somaQuadrados += pow($valor - $media, 2);
            }
            
            return $somaQuadrados / $count;
        }
        private function calcularMediaPorPeso($query, $peso)
        {
            $respostas = (clone $query)
                ->whereHas('pergunta', fn($q) => $q->where('peso', $peso))
                ->selectRaw('SUM(correta) as acertos, COUNT(*) as total')
                ->first();

            $total = $respostas->total ?: 1;
            return round(($respostas->acertos / $total) * 10, 2);
        }
        private function calcularMediaGeral($query)
        {
            $respostas = (clone $query)
                ->join('perguntas', 'respostas_simulados.pergunta_id', '=', 'perguntas.id')
                ->selectRaw('SUM(correta * peso) as pontos, SUM(peso) as total_peso')
                ->first();

            $totalPeso = $respostas->total_peso ?: 1;
            return round(($respostas->pontos / $totalPeso) * 10, 2);
        }
        private function prepararDadosGraficos($queryRespostas)
        {
            $respostas = (clone $queryRespostas)->with('pergunta')->get();
            
            // Dados para gráfico de desempenho por peso
            $pesos = [
                'Peso 1' => $respostas->where('pergunta.peso', 1)->count(),
                'Peso 2' => $respostas->where('pergunta.peso', 2)->count(),
                'Peso 3' => $respostas->where('pergunta.peso', 3)->count()
            ];

            // Dados para gráfico de acertos/erros
            $acertos = $respostas->where('correta', true)->count();
            $erros = $respostas->where('correta', false)->count();

            return [
                'pesos' => $pesos,
                'acertos_erros' => ['Acertos' => $acertos, 'Erros' => $erros],
                'cores' => [
                    'pesos' => ['#4e73df', '#1cc88a', '#36b9cc'],
                    'acertos_erros' => ['#1cc88a', '#e74a3b']
                ]
            ];
        }
        private function getOptimizedReportData(Request $request)
        {
            // Consultas otimizadas
            $query = RespostaSimulado::with(['aluno' => function($q) {
                        $q->select('id', 'name', 'ano_id', 'escola_id');
                     }, 'pergunta'])
                     ->when($request->simulado_id, function($q) use ($request) {
                        $q->where('simulado_id', $request->simulado_id);
                     });
            
            // Aplicar filtros
            if ($request->ano_id) {
                $query->whereHas('aluno', function($q) use ($request) {
                    $q->where('ano_id', $request->ano_id);
                });
            }
    
            // Dados essenciais
            return [
                'totalAlunos' => User::where('role', 'aluno')->count(),
                'alunosAtivos' => User::where('role', 'aluno')->active()->count(),
                'alunosResponderam' => $query->distinct('user_id')->count('user_id'),
                'mediasPeso' => $this->calcularMediasOtimizado($query),
                // ... outros dados necessários
            ];
        }   
        private function calcularMediasOtimizado($query)
        {
            // Cálculo otimizado das médias
            return [
                'peso_1' => $query->clone()->whereHas('pergunta', fn($q) => $q->where('peso', 1))->avg('nota'),
                'peso_2' => $query->clone()->whereHas('pergunta', fn($q) => $q->where('peso', 2))->avg('nota'),
                'peso_3' => $query->clone()->whereHas('pergunta', fn($q) => $q->where('peso', 3))->avg('nota'),
                'media_geral' => $query->avg('nota')
            ];
        }       
        private function getReportData(Request $request)
        {
            // Reutiliza a lógica do método estatisticasRede
            $simuladoId = $request->simulado_id;
            $simulados = Simulado::orderBy('nome')->get();
            $anos = Ano::orderBy('nome')->get();
            $escolas = Escola::orderBy('nome')->get();

            $queryAlunos = User::where('role', 'aluno');
            $queryRespostas = RespostaSimulado::where('simulado_id', $simuladoId)
                ->with(['aluno', 'pergunta']);

            // Aplicar filtros
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

            // Dados gerais
            $totalAlunos = $queryAlunos->count();
            $alunosAtivos = $queryAlunos->count();
            $alunosResponderam = $queryRespostas->distinct('user_id')->count('user_id');

            // Cálculo das médias
            $mediasPeso = [
                'peso_1' => $this->calcularMediaPorPeso($queryRespostas, 1),
                'peso_2' => $this->calcularMediaPorPeso($queryRespostas, 2),
                'peso_3' => $this->calcularMediaPorPeso($queryRespostas, 3),
                'media_geral' => $this->calcularMediaGeral($queryRespostas)
            ];

            // Projeção TRI
            $projecaoTRI = $this->calcularProjecaoTRI($queryRespostas);

            // Projeção por segmento
            $projecaoSegmento = [
                '1a5' => $this->calcularProjecaoSegmento($queryRespostas, range(1, 5)),
                '6a9' => $this->calcularProjecaoSegmento($queryRespostas, range(6, 9))
            ];

            // Estatísticas por escola
            $estatisticasPorEscola = $this->prepararEstatisticasPorEscola($queryRespostas, $simuladoId, $request);

            return compact(
                'simulados', 'anos', 'escolas',
                'totalAlunos', 'alunosAtivos', 'alunosResponderam',
                'mediasPeso', 'projecaoTRI', 'projecaoSegmento',
                'estatisticasPorEscola'
            );
        }
        public function exportarPdf(Request $request)
        {
            $request->validate([
                'simulado_id' => 'nullable|exists:simulados,id',
                'ano_id' => 'nullable|exists:anos,id',
                'escola_id' => 'nullable|exists:escolas,id',
                'deficiencia' => 'nullable|string'
            ]);

            // Obter dados básicos
            $simulados = Simulado::orderBy('nome')->get();
            $anos = Ano::orderBy('nome')->get();
            $escolas = Escola::orderBy('nome')->get();

            // Query base para alunos
            $queryAlunos = User::where('role', 'aluno');
            $queryRespostas = RespostaSimulado::where('simulado_id', $request->simulado_id)
                ->with(['aluno', 'pergunta']);

            // Aplicar filtros
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

            // Dados gerais
            $totalAlunos = $queryAlunos->count();
            $alunosAtivos = $queryAlunos->count();
            $alunosResponderam = $queryRespostas->distinct('user_id')->count('user_id');

            // Cálculo das médias
            $mediasPeso = [
                'peso_1' => $this->calcularMediaPorPeso($queryRespostas, 1),
                'peso_2' => $this->calcularMediaPorPeso($queryRespostas, 2),
                'peso_3' => $this->calcularMediaPorPeso($queryRespostas, 3),
                'media_geral' => $this->calcularMediaGeral($queryRespostas)
            ];

            // Cálculo TRI completo
            $analiseTRI = $this->calcularAnaliseTRICompleta($queryRespostas);

            // Dados das escolas
            $dadosEscolas = $this->prepararDadosEscolas($request->simulado_id, $request);
            $mediaGeralTRI = $analiseTRI['media_geral'];
            $limiteGrande = 200;

            // Preparar dados dos quadrantes
            $quadrantes = [
                'q1' => ['escolas' => [], 'count' => 0, 'media_tri' => 0],
                'q2' => ['escolas' => [], 'count' => 0, 'media_tri' => 0],
                'q3' => ['escolas' => [], 'count' => 0, 'media_tri' => 0],
                'q4' => ['escolas' => [], 'count' => 0, 'media_tri' => 0]
            ];

            foreach ($dadosEscolas as $escola) {
                if ($escola['total_alunos'] <= 0) continue;
                
                $grande = $escola['total_alunos'] >= $limiteGrande;
                $acimaMedia = $escola['media_tri'] >= $mediaGeralTRI;
                
                if ($grande && $acimaMedia) {
                    $quadrante = 'q1';
                } elseif ($grande && !$acimaMedia) {
                    $quadrante = 'q2';
                } elseif (!$grande && !$acimaMedia) {
                    $quadrante = 'q3';
                } else {
                    $quadrante = 'q4';
                }
                
                $quadrantes[$quadrante]['escolas'][] = $escola;
                $quadrantes[$quadrante]['count']++;
                $quadrantes[$quadrante]['media_tri'] = $quadrantes[$quadrante]['count'] > 0 
                    ? ($quadrantes[$quadrante]['media_tri'] * ($quadrantes[$quadrante]['count'] - 1) + $escola['media_tri']) / $quadrantes[$quadrante]['count']
                    : 0;
            }

            // Projeção por segmento com TRI
            $projecaoSegmento = [
                '1a5' => $this->calcularProjecaoSegmentoTRI($queryRespostas, range(1, 5)),
                '6a9' => $this->calcularProjecaoSegmentoTRI($queryRespostas, range(6, 9))
            ];

            // Configurar PDF
            $pdf = PDF::loadView('relatorios.pdf.rede-municipal', [
                'simulados' => $simulados,
                'anos' => $anos,
                'escolas' => $escolas,
                'totalAlunos' => $totalAlunos,
                'alunosAtivos' => $alunosAtivos,
                'alunosResponderam' => $alunosResponderam,
                'mediasPeso' => $mediasPeso,
                'analiseTRI' => $analiseTRI,
                'projecaoSegmento' => $projecaoSegmento,
                'dadosEscolas' => $dadosEscolas,
                'quadrantes' => $quadrantes,
                'mediaGeralTRI' => $mediaGeralTRI,
                'request' => $request,
                'logoPath' => public_path('images/logoprefeitura.png')
            ]);

            $pdf->setOption('margin-top', 10);
            $pdf->setOption('margin-bottom', 10);
            $pdf->setOption('margin-left', 10);
            $pdf->setOption('margin-right', 10);
            $pdf->setOption('dpi', 150);
            $pdf->setOption('enable-local-file-access', true);

            return $pdf->download('relatorio_rede_municipal.pdf');
        }
        public function exportarExcel(Request $request)
        {
            $request->validate([
                'simulado_id' => 'required|exists:simulados,id',
                'ano_id' => 'nullable|exists:anos,id',
                'escola_id' => 'nullable|exists:escolas,id',
                'deficiencia' => 'nullable|string'
            ]);
        
            // Reutiliza a mesma lógica do método estatisticasRede
            $queryAlunos = User::where('role', 'aluno');
            $queryRespostas = RespostaSimulado::where('simulado_id', $request->simulado_id)
                ->with(['aluno', 'pergunta']);
        
            // Aplicar filtros
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
        
            // Dados gerais
            $totalAlunos = $queryAlunos->count();
            $alunosAtivos = $queryAlunos->count();
            $alunosResponderam = $queryRespostas->distinct('user_id')->count('user_id');
        
            // Cálculo das médias tradicionais por peso
            $mediasPeso = [
                'peso_1' => $this->calcularMediaPorPeso($queryRespostas, 1),
                'peso_2' => $this->calcularMediaPorPeso($queryRespostas, 2),
                'peso_3' => $this->calcularMediaPorPeso($queryRespostas, 3),
                'media_geral' => $this->calcularMediaGeral($queryRespostas)
            ];
        
            // Cálculo TRI completo
            $analiseTRI = $this->calcularAnaliseTRICompleta($queryRespostas);
        
            // Dados das escolas
            $dadosEscolas = $this->prepararDadosEscolas($request->simulado_id, $request);
        
            // Quadrantes baseados na média TRI
            $quadrantes = $this->analisarQuadrantes($dadosEscolas, $analiseTRI['media_geral']);
        
            // Projeção por segmento com TRI
            $projecaoSegmento = [
                '1a5' => $this->calcularProjecaoSegmentoTRI($queryRespostas, range(1, 5)),
                '6a9' => $this->calcularProjecaoSegmentoTRI($queryRespostas, range(6, 9))
            ];
        
            // Dados para gráficos
            $graficoData = $this->prepararDadosGraficos($queryRespostas);
        
            // Preparar dados para exportação
            $exportData = [
                'simulados' => Simulado::orderBy('nome')->get(),
                'request' => $request,
                'totalAlunos' => $totalAlunos,
                'alunosAtivos' => $alunosAtivos,
                'alunosResponderam' => $alunosResponderam,
                'mediasPeso' => $mediasPeso,
                'analiseTRI' => $analiseTRI,
                'projecaoSegmento' => $projecaoSegmento,
                'dadosEscolas' => $dadosEscolas,
                'quadrantes' => $quadrantes,
                'graficoData' => $graficoData,
                'mediaGeralTRI' => $analiseTRI['media_geral']
            ];
        
            return Excel::download(new RedeMunicipalExport($exportData), 'relatorio_rede_municipal.xlsx');
        }
        private function prepareExcelData(Request $request)
        {
            $data = $this->getReportData($request);
            
            // Formatar os dados para o Excel
            return [
                'simuladoSelecionado' => $data['simulados']->firstWhere('id', $request->simulado_id),
                'totalAlunos' => $data['totalAlunos'],
                'alunosResponderam' => $data['alunosResponderam'],
                'taxaFaltantes' => $data['alunosAtivos'] > 0 
                    ? round((($data['alunosAtivos'] - $data['alunosResponderam'])/$data['alunosAtivos'])*100, 2).'%'
                    : '0%',
                
                'mediasPeso' => [
                    'peso_1' => $data['mediasPeso']['peso_1'],
                    'peso_2' => $data['mediasPeso']['peso_2'],
                    'peso_3' => $data['mediasPeso']['peso_3'],
                    'geral' => $data['mediasPeso']['media_geral']
                ],
                
                'projecaoTRI' => [
                    'peso_1' => $data['projecaoTRI']['peso_1'],
                    'peso_2' => $data['projecaoTRI']['peso_2'],
                    'peso_3' => $data['projecaoTRI']['peso_3'],
                    'geral' => $data['projecaoTRI']['media_geral']
                ],
                
                // Dados adicionais para o Excel
                'alunos' => $this->prepararDadosAlunosExcel($request),
                'escolas' => $data['estatisticasPorEscola']
            ];
        }
        private function prepararEstatisticasPorEscola($queryRespostas, $simuladoId, $request)
        {
            return Escola::withCount(['users as alunos_count' => function($q) use ($request) {
                    $q->where('role', 'aluno');
                    if ($request->ano_id) $q->where('ano_id', $request->ano_id);
                    if ($request->deficiencia) {
                        $request->deficiencia === 'ND' 
                            ? $q->whereNull('deficiencia')
                            : $q->where('deficiencia', $request->deficiencia);
                    }
                }])
                ->with(['users as alunos' => function($q) use ($simuladoId, $request) {
                    $q->where('role', 'aluno');
                    if ($request->ano_id) $q->where('ano_id', $request->ano_id);
                    if ($request->deficiencia) {
                        $request->deficiencia === 'ND' 
                            ? $q->whereNull('deficiencia')
                            : $q->where('deficiencia', $request->deficiencia);
                    }
                    $q->with(['respostasSimulado' => function($q) use ($simuladoId) {
                        $q->where('simulado_id', $simuladoId)
                        ->with('pergunta');
                    }]);
                }])
                ->get()
                ->map(function ($escola) use ($queryRespostas) {
                    $alunosComRespostas = $escola->alunos->filter(function ($aluno) {
                        return $aluno->respostasSimulado->isNotEmpty();
                    });
                    
                    $mediaEscola = 0;
                    $mediaTRI = 0;
                    
                    if ($alunosComRespostas->isNotEmpty()) {
                        // Cálculo da média tradicional
                        $totalPontos = 0;
                        $totalPeso = 0;
                        
                        // Cálculo da média TRI
                        $somaTRI = 0;
                        $countTRI = 0;
                        
                        foreach ($alunosComRespostas as $aluno) {
                            foreach ($aluno->respostasSimulado as $resposta) {
                                $totalPontos += $resposta->correta * $resposta->pergunta->peso;
                                $totalPeso += $resposta->pergunta->peso;
                                
                                // Cálculo TRI para cada resposta
                                $triA = $resposta->pergunta->tri_a;
                                $triB = $resposta->pergunta->tri_b;
                                $triC = $resposta->pergunta->tri_c;
                                $prob = $triC + (1 - $triC) / (1 + exp(-1.7 * $triA * (0 - $triB)));
                                $valorTRI = $resposta->correta ? $prob : (1 - $prob);
                                
                                $somaTRI += $valorTRI * $resposta->pergunta->peso;
                                $countTRI += $resposta->pergunta->peso;
                            }
                        }
                        
                        $mediaEscola = $totalPeso > 0 ? round(($totalPontos / $totalPeso) * 10, 2) : 0;
                        $mediaTRI = $countTRI > 0 ? round(($somaTRI / $countTRI) * 10, 2) : 0;
                    }
                    
                    return [
                        'nome' => $escola->nome,
                        'alunos_ativos' => $escola->alunos_count,
                        'alunos_responderam' => $alunosComRespostas->count(),
                        'media_ponderada' => $mediaEscola,
                        'projecao_tri' => $mediaTRI,
                        'atingiu_meta' => $mediaEscola >= ($escola->alunos_count > 400 ? 6.0 : 5.0)
                    ];
                })
                ->sortByDesc('media_ponderada')
                ->values()
                ->toArray();
        }
        private function prepararDadosAlunosExcel(Request $request)
        {
            $query = User::where('role', 'aluno')
                ->with(['respostasSimulado' => function($q) use ($request) {
                    $q->where('simulado_id', $request->simulado_id)
                    ->with('pergunta');
                }]);

            // Aplicar filtros
            if ($request->ano_id) $query->where('ano_id', $request->ano_id);
            if ($request->escola_id) $query->where('escola_id', $request->escola_id);
            if ($request->deficiencia) {
                $request->deficiencia === 'ND' 
                    ? $query->whereNull('deficiencia')
                    : $query->where('deficiencia', $request->deficiencia);
            }

            return $query->get()
                ->map(function ($aluno) {
                    $respostas = $aluno->respostasSimulado;
                    $total = $respostas->count();
                    $acertos = $respostas->where('correta', true)->count();
                    
                    // Cálculo TRI simplificado
                    $triScore = 0;
                    if ($total > 0) {
                        $triScore = $respostas->sum(function ($resposta) {
                            $triA = $resposta->pergunta->tri_a;
                            $triB = $resposta->pergunta->tri_b;
                            $triC = $resposta->pergunta->tri_c;
                            $prob = $triC + (1 - $triC) / (1 + exp(-1.7 * $triA * (0 - $triB)));
                            return $resposta->correta ? $prob : (1 - $prob);
                        });
                        $triScore = round(($triScore / $total) * 10, 2);
                    }
                    
                    return [
                        'nome' => $aluno->name,
                        'acertos' => $acertos,
                        'total' => $total,
                        'porcentagem' => $total > 0 ? round(($acertos / $total) * 100, 2) : 0,
                        'tri' => $triScore
                    ];
                })
                ->sortByDesc('porcentagem')
                ->values()
                ->toArray();
        }
        private function calcularProjecaoTRI($queryRespostas)
        {
            $respostas = (clone $queryRespostas)->with('pergunta')->get();
            
            if ($respostas->isEmpty()) {
                return [
                    'peso_1' => 0,
                    'peso_2' => 0,
                    'peso_3' => 0,
                    'media_geral' => 0
                ];
            }

            // Agrupar por peso
            $porPeso = [
                1 => ['soma' => 0, 'count' => 0],
                2 => ['soma' => 0, 'count' => 0],
                3 => ['soma' => 0, 'count' => 0]
            ];

            foreach ($respostas as $resposta) {
                $peso = $resposta->pergunta->peso;
                $triA = $resposta->pergunta->tri_a;
                $triB = $resposta->pergunta->tri_b;
                $triC = $resposta->pergunta->tri_c;
                
                // Fórmula TRI para theta=0 (habilidade média)
                $probabilidade = $triC + (1 - $triC) / (1 + exp(-1.7 * $triA * (0 - $triB)));
                
                // Valor TRI baseado no acerto/erro
                $valorTRI = $resposta->correta ? $probabilidade : (1 - $probabilidade);
                
                $porPeso[$peso]['soma'] += $valorTRI;
                $porPeso[$peso]['count']++;
            }

            // Calcular médias por peso
            $mediaPeso1 = $porPeso[1]['count'] > 0 ? ($porPeso[1]['soma'] / $porPeso[1]['count']) * 10 : 0;
            $mediaPeso2 = $porPeso[2]['count'] > 0 ? ($porPeso[2]['soma'] / $porPeso[2]['count']) * 10 : 0;
            $mediaPeso3 = $porPeso[3]['count'] > 0 ? ($porPeso[3]['soma'] / $porPeso[3]['count']) * 10 : 0;

            // Média geral ponderada
            $totalPeso = $porPeso[1]['count'] + $porPeso[2]['count'] + $porPeso[3]['count'];
            $mediaGeral = $totalPeso > 0 
                ? (($porPeso[1]['soma'] + $porPeso[2]['soma'] + $porPeso[3]['soma']) / $totalPeso) * 10
                : 0;

            return [
                'peso_1' => round(max(0, min(10, $mediaPeso1)), 2),
                'peso_2' => round(max(0, min(10, $mediaPeso2)), 2),
                'peso_3' => round(max(0, min(10, $mediaPeso3)), 2),
                'media_geral' => round(max(0, min(10, $mediaGeral)), 2)
            ];
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