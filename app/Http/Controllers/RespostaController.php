<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Resposta;
use App\Models\Prova;
use App\Models\Questao;
use App\Models\Escola;

use App\Models\User;
use App\Models\Habilidade; // Modelo para habilidades
use App\Models\Ano; // Modelo para anos
use App\Models\Unidade; // Modelo para unidades
use Illuminate\Support\Facades\Auth;
use PDF; // Para gerar PDF

class RespostaController extends Controller
{
    // Exibe a lista de provas disponíveis para o aluno responder
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'professor') {
            // Redireciona o professor para suas estatísticas
            return redirect()->route('respostas.professor.estatisticas');
        } elseif ($user->role === 'aluno') {
            // Aluno só vê as provas criadas pelo professor da sua turma
            $professorId = $user->turma->professor_id; // Pega o professor_id da turma do aluno
            $provas = Prova::where('user_id', $professorId)->withCount('questoes')->get();
            return view('respostas.index', compact('provas'));
        } else {
            // Redireciona o admin para as estatísticas gerais
            return redirect()->route('respostas.admin.estatisticas');
        }
    }

    // Exibe o formulário para responder uma prova específica
    public function create(Prova $prova)
    {
        // Carrega as questões da prova
        $questoes = $prova->questoes;

        return view('respostas.create', compact('prova', 'questoes'));
    }

    // Salva as respostas do aluno
    public function store(Request $request, Prova $prova)
    {
        // Validação das respostas
        $request->validate([
            'respostas' => 'required|array',
            'respostas.*' => 'required|in:A,B,C,D'
        ]);

        // Salva cada resposta do aluno
        foreach ($request->respostas as $questao_id => $resposta) {
            $questao = Questao::find($questao_id);
            $correta = ($questao->resposta_correta == $resposta);

            Resposta::create([
                'user_id' => auth()->id(),
                'prova_id' => $prova->id,
                'questao_id' => $questao_id,
                'resposta' => $resposta,
                'correta' => $correta,
            ]);
        }

        return redirect()->route('respostas.index')->with('success', 'Prova finalizada!');
    }

    // Exibe os detalhes de uma prova respondida (para o aluno)
    public function show($id)
    {
        $prova = Prova::with('questoes')->findOrFail($id);
        return view('provas.show', compact('prova'));
    }

    // Exibe a lista de respostas dos alunos cadastrados pelo professor
    public function professorIndex()
    {
        $user = Auth::user();

        if ($user->role !== 'professor') {
            abort(403, 'Acesso não autorizado.');
        }

        // Busca as respostas dos alunos cadastrados pelo professor
        $respostas = Resposta::whereHas('user', function ($query) use ($user) {
            $query->where('professor_id', $user->id);
        })->get();

        return view('respostas.professor.index', compact('respostas'));
    }

    // Exibe os detalhes das respostas de uma prova específica (para o professor)
    public function professorShow(Prova $prova)
    {
        $user = Auth::user();

        if ($user->role !== 'professor') {
            abort(403, 'Acesso não autorizado.');
        }

        // Busca as respostas dos alunos cadastrados pelo professor para a prova específica
        $respostas = $prova->respostas()->whereHas('user', function ($query) use ($user) {
            $query->where('professor_id', $user->id);
        })->get();

        return view('respostas.professor.show', compact('prova', 'respostas'));
    }

    // Exibe as estatísticas do professor
    public function professorEstatisticas(Request $request)
    {
        $user = Auth::user();

        if ($user->role !== 'professor') {
            abort(403, 'Acesso não autorizado.');
        }

        // Filtros
        $provaId = $request->input('prova_id');
        $habilidadeId = $request->input('habilidade_id');
        $anoId = $request->input('ano_id');
        $unidadeId = $request->input('unidade_id');

        // Busca as provas criadas pelo professor
        $provas = Prova::where('user_id', $user->id)->get();

        // Busca as habilidades, anos e unidades para os filtros
        $habilidades = Habilidade::all(); // Todas as habilidades
        $anos = Ano::all(); // Todos os anos
        $unidades = Unidade::all(); // Todas as unidades

        // Filtra as respostas dos alunos
        $respostas = Resposta::whereHas('prova', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->when($provaId, function ($query, $provaId) {
            return $query->where('prova_id', $provaId);
        })
        ->when($habilidadeId, function ($query, $habilidadeId) {
            return $query->whereHas('questao', function ($q) use ($habilidadeId) {
                $q->where('habilidade_id', $habilidadeId);
            }); // <-- Corrigido: removido o ponto e vírgula e adicionado o fechamento de parêntese
        })
        ->when($anoId, function ($query, $anoId) {
            return $query->whereHas('prova', function ($q) use ($anoId) {
                $q->where('ano_id', $anoId);
            });
        })
        ->when($unidadeId, function ($query, $unidadeId) {
            return $query->whereHas('prova', function ($q) use ($unidadeId) {
                $q->where('unidade_id', $unidadeId);
            });
        })
        ->with(['user', 'prova', 'questao'])
        ->get();

        // Calcula as estatísticas
        $estatisticas = [];
        foreach ($respostas->groupBy('prova_id') as $provaId => $respostasProva) {
            $prova = $respostasProva->first()->prova;
            $totalQuestoes = $prova->questoes->count();
            $alunos = $respostasProva->groupBy('user_id');

            foreach ($alunos as $userId => $respostasAluno) {
                $aluno = $respostasAluno->first()->user;
                $acertos = $respostasAluno->where('correta', true)->count();
                $porcentagemAcertos = ($acertos / $totalQuestoes) * 100;

                $estatisticas[] = [
                    'prova' => $prova->nome,
                    'aluno' => $aluno->name,
                    'acertos' => $acertos,
                    'total_questoes' => $totalQuestoes,
                    'porcentagem_acertos' => $porcentagemAcertos,
                ];
            }
        }

        // Passa as variáveis para a view
        return view('respostas.professor.estatisticas', compact('estatisticas', 'provas', 'habilidades', 'anos', 'unidades'));
    }

  
    public function adminEstatisticas(Request $request)
    {
        $user = Auth::user();
    
        if ($user->role !== 'admin') {
            abort(403, 'Acesso não autorizado.');
        }
    
        // Filtros
        $escolaId = $request->input('escola_id');
        $habilidadeId = $request->input('habilidade_id');
        $anoId = $request->input('ano_id');
        $unidadeId = $request->input('unidade_id');
    
        // Busca todas as escolas, habilidades, anos e unidades para os filtros
        $escolas = Escola::all();
        $habilidades = Habilidade::all();
        $anos = Ano::all();
        $unidades = Unidade::all();
    
        // Dados gerais
        $totalProvas = Prova::count();
        $totalProfessores = User::where('role', 'professor')->count();
        $totalQuestoesRespondidas = Resposta::count();
    
        // Estatísticas por escola
        $estatisticasPorEscola = [];
        $respostasPorEscola = Resposta::when($escolaId, function ($query, $escolaId) {
            return $query->whereHas('user', function ($q) use ($escolaId) {
                $q->where('escola_id', $escolaId);
            });
        })
        ->get()
        ->groupBy('user.escola_id');
    
        foreach ($respostasPorEscola as $escolaId => $respostas) {
            $escola = Escola::find($escolaId);
            $totalQuestoes = $respostas->count();
            $acertos = $respostas->where('correta', true)->count();
            $porcentagemAcertos = ($totalQuestoes > 0) ? ($acertos / $totalQuestoes) * 100 : 0;
    
            $estatisticasPorEscola[] = [
                'escola' => $escola->nome,
                'total_questoes' => $totalQuestoes,
                'acertos' => $acertos,
                'porcentagem_acertos' => $porcentagemAcertos,
            ];
        }
    
        // Estatísticas por habilidade
        $estatisticasPorHabilidade = [];
        $respostasPorHabilidade = Resposta::when($habilidadeId, function ($query, $habilidadeId) {
            return $query->whereHas('questao', function ($q) use ($habilidadeId) {
                $q->where('habilidade_id', $habilidadeId);
            });
        })
        ->get()
        ->groupBy('questao.habilidade_id');
    
        foreach ($respostasPorHabilidade as $habilidadeId => $respostas) {
            $habilidade = Habilidade::find($habilidadeId);
            $totalQuestoes = $respostas->count();
            $acertos = $respostas->where('correta', true)->count();
            $porcentagemAcertos = ($totalQuestoes > 0) ? ($acertos / $totalQuestoes) * 100 : 0;
    
            $estatisticasPorHabilidade[] = [
                'habilidade' => $habilidade->descricao,
                'total_questoes' => $totalQuestoes,
                'acertos' => $acertos,
                'porcentagem_acertos' => $porcentagemAcertos,
            ];
        }
    
        // Passa as variáveis para a view
        return view('respostas.admin.estatisticas', compact(
            'totalProvas',
            'totalProfessores',
            'totalQuestoesRespondidas',
            'estatisticasPorEscola',
            'estatisticasPorHabilidade',
            'escolas',
            'habilidades',
            'anos',
            'unidades',
            'request' 
        ));
    }
    public function gerarPdfEstatisticas(Request $request)
    {
        $user = Auth::user();
    
        if ($user->role !== 'admin') {
            abort(403, 'Acesso não autorizado.');
        }
    
        // Aplica os mesmos filtros do método adminEstatisticas
        $escolaId = $request->input('escola_id');
        $habilidadeId = $request->input('habilidade_id');
        $anoId = $request->input('ano_id');
        $unidadeId = $request->input('unidade_id');
    
        // Dados gerais
        $totalProvas = Prova::count();
        $totalProfessores = User::where('role', 'professor')->count();
        $totalQuestoesRespondidas = Resposta::count();
    
        // Estatísticas por escola
        $estatisticasPorEscola = [];
        $respostasPorEscola = Resposta::when($escolaId, function ($query, $escolaId) {
            return $query->whereHas('user', function ($q) use ($escolaId) {
                $q->where('escola_id', $escolaId);
            });
        })
        ->get()
        ->groupBy('user.escola_id');
    
        foreach ($respostasPorEscola as $escolaId => $respostas) {
            $escola = Escola::find($escolaId);
            $totalQuestoes = $respostas->count();
            $acertos = $respostas->where('correta', true)->count();
            $porcentagemAcertos = ($totalQuestoes > 0) ? ($acertos / $totalQuestoes) * 100 : 0;
    
            $estatisticasPorEscola[] = [
                'escola' => $escola->nome,
                'total_questoes' => $totalQuestoes,
                'acertos' => $acertos,
                'porcentagem_acertos' => $porcentagemAcertos,
            ];
        }
    
        // Estatísticas por habilidade
        $estatisticasPorHabilidade = [];
        $respostasPorHabilidade = Resposta::when($habilidadeId, function ($query, $habilidadeId) {
            return $query->whereHas('questao', function ($q) use ($habilidadeId) {
                $q->where('habilidade_id', $habilidadeId);
            });
        })
        ->get()
        ->groupBy('questao.habilidade_id');
    
        foreach ($respostasPorHabilidade as $habilidadeId => $respostas) {
            $habilidade = Habilidade::find($habilidadeId);
            $totalQuestoes = $respostas->count();
            $acertos = $respostas->where('correta', true)->count();
            $porcentagemAcertos = ($totalQuestoes > 0) ? ($acertos / $totalQuestoes) * 100 : 0;
    
            $estatisticasPorHabilidade[] = [
                'habilidade' => $habilidade->descricao,
                'total_questoes' => $totalQuestoes,
                'acertos' => $acertos,
                'porcentagem_acertos' => $porcentagemAcertos,
            ];
        }
    
        // Gera o PDF
        $pdf = PDF::loadView('respostas.pdf.estatisticas', compact(
            'totalProvas',
            'totalProfessores',
            'totalQuestoesRespondidas',
            'estatisticasPorEscola',
            'estatisticasPorHabilidade'
        ));
    
        return $pdf->download('estatisticas.pdf');
    }

    // Exibe a lista de todas as respostas (para o admin)
    public function adminIndex()
    {
        $respostas = Resposta::all();
        return view('respostas.admin.index', compact('respostas'));
    }

    // Exibe os detalhes das respostas de uma prova específica (para o admin)
    public function adminShow(Prova $prova)
    {
        $respostas = $prova->respostas;
        return view('respostas.admin.show', compact('prova', 'respostas'));
    }
}