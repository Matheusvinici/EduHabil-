<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Simulado;
use App\Models\RespostaSimulado;
use App\Models\User;
use App\Models\Turma;
use App\Models\Escola;
use App\Models\Pergunta;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Services\SAEBTRICalculator;

class GabaritoController extends Controller
{
    private $gabaritoConfig = [
        'questoes_por_linha' => 5,
        'margem_esquerda' => 30,
        'margem_superior' => 30,
        'espacamento_horizontal' => 120,
        'espacamento_vertical' => 80,
        'tamanho_circulo' => 20
    ];

    protected ImageManager $imageManager;

    public function __construct()
    {
        $this->imageManager = new ImageManager(new Driver());
    }

    public function showCameraForm(Simulado $simulado)
    {
        $user = auth()->user();
        $escolaId = session('escola_aplicador_id');
        
        if (!$escolaId) {
            return redirect()->route('respostas_simulados.aplicador.select')
                ->with('error', 'Selecione uma escola primeiro');
        }

        if (!request()->has('keep_session') || request()->has('reset')) {
            session()->forget([
                'aluno_selecionado',
                'aluno_id',
                'aluno_nome',
                'aluno_turma',
                'turma_id',
                'raca'
            ]);
        }

        $turmas = Turma::where('escola_id', $escolaId)
                    ->where('aplicador_id', $user->id)
                    ->orderBy('nome_turma')
                    ->get();

        return view('respostas_simulados.aplicador.camera', [
            'simulado' => $simulado,
            'turmas' => $turmas,
            'gabaritoConfig' => $this->gabaritoConfig
        ]);
    }

    public function selecionarAluno(Request $request, Simulado $simulado)
    {
        $request->validate([
            'turma_id' => 'required|exists:turmas,id',
            'aluno_id' => 'required|exists:users,id',
            'raca' => 'required'
        ]);

        $turma = Turma::find($request->turma_id);
        if (!$turma) {
            return back()->with('error', 'Turma não encontrada.');
        }

        $aluno = User::where('id', $request->aluno_id)
                ->where('role', 'aluno')
                ->first();
        
        if (!$aluno) {
            return back()->with('error', 'Aluno não encontrado ou não tem permissão.');
        }

        if ($aluno->turma_id != $turma->id) {
            return back()->with('error', 'O aluno não pertence à turma selecionada.');
        }

        $respostaExistente = RespostaSimulado::where([
            'simulado_id' => $simulado->id,
            'user_id' => $aluno->id,
            'aplicador_id' => auth()->id()
        ])->exists();

        if ($respostaExistente) {
            return back()->with('error', 'Este aluno já respondeu este simulado.');
        }

        session([
            'aluno_selecionado' => true,
            'aluno_id' => $aluno->id,
            'aluno_nome' => $aluno->name,
            'aluno_turma' => $turma->nome_turma,
            'turma_id' => $turma->id,
            'raca' => $request->raca,
        ]);

        return redirect()->route('respostas_simulados.aplicador.selecionar-aluno', [
            'simulado' => $simulado->id,
            'keep_session' => true
        ]);
    }

    // Retorna alunos de uma turma que ainda não responderam o simulado
    public function getAlunosPorTurma(Request $request)
    {
        $request->validate([
            'turma_id' => 'required|exists:turmas,id',
            'simulado_id' => 'required|exists:simulados,id'
        ]);

        try {
            $alunos = User::where('turma_id', $request->turma_id)
                        ->where('role', 'aluno')
                        ->whereNotIn('id', function($query) use ($request) {
                            $query->select('user_id')
                                  ->from('respostas_simulados')
                                  ->where('simulado_id', $request->simulado_id)
                                  ->where('aplicador_id', Auth::id());
                        })
                        ->select('id', 'name')
                        ->orderBy('name')
                        ->get();

            if($alunos->isEmpty()) {
                return response()->json([
                    ['id' => 0, 'name' => 'Todos os alunos desta turma já responderam']
                ]);
            }

            return response()->json($alunos);
        } catch (\Exception $e) {
            return response()->json([
                ['id' => 0, 'name' => 'Erro ao carregar alunos: ' . $e->getMessage()]
            ]);
        }
    }

    public function processImage(Request $request, Simulado $simulado)
    {
        $validated = $request->validate([
            'processed_image' => 'required|string',
            'aluno_id' => 'required|exists:users,id',
            'turma_id' => 'required|exists:turmas,id',
            'raca' => 'required|string'
        ]);
    
        try {
            $totalQuestoes = $simulado->perguntas()->count();
            $respostas = [];
            
            for ($i = 1; $i <= $totalQuestoes; $i++) {
                $respostas[$i] = [
                    'resposta' => '',
                    'confianca' => 0,
                    'imagem' => null
                ];
            }
            
            $imagePath = $this->salvarImagemTemporaria($validated['processed_image']);
            
            session([
                'correcao_data' => [
                    'simulado' => $simulado,
                    'aluno' => User::find($validated['aluno_id']),
                    'dados' => $validated,
                    'respostas' => $respostas,
                    'imagePath' => $imagePath
                ]
            ]);
    
            return redirect()->route('respostas_simulados.aplicador.correcao', $simulado);
    
        } catch (\Exception $e) {
            return back()->with('error', 'Erro: '.$e->getMessage());
        }
    }

    public function showCorrecao(Simulado $simulado)
    {
        if (!session()->has('correcao_data')) {
            return redirect()
                ->route('respostas_simulados.aplicador.camera', $simulado)
                ->with('error', 'Sessão expirada. Recapture o gabarito.');
        }

        $data = session('correcao_data');
        $totalQuestoes = $simulado->perguntas()->count();
        
        $perguntas = $simulado->perguntas()
            ->select('id', 'enunciado', 'resposta_correta')
            ->get()
            ->keyBy('id');
        
        $respostasProcessadas = [];
        foreach ($data['respostas'] as $questaoId => $resposta) {
            if ($questaoId <= $totalQuestoes) {
                $pergunta = $perguntas[$questaoId] ?? null;
                $respostasProcessadas[$questaoId] = [
                    'resposta' => $resposta['resposta'],
                    'confianca' => $resposta['confianca'],
                    'imagem' => $resposta['imagem'] ?? null,
                    'correta' => ($resposta['resposta'] === ($pergunta->resposta_correta ?? null)),
                    'enunciado' => $pergunta->enunciado ?? 'N/A'
                ];
            }
        }

        return view('respostas_simulados.aplicador.correcao', [
            'simulado' => $simulado,
            'aluno' => $data['aluno'],
            'respostas' => $respostasProcessadas,
            'imagePath' => $data['imagePath'],
            'dados' => $data['dados'],
            'totalQuestoes' => $totalQuestoes,
            'alternativas' => ['A', 'B', 'C', 'D'],
            'perguntas' => $perguntas
        ]);
    }

    public function salvarRespostas(Request $request, Simulado $simulado)
    {
        DB::beginTransaction();
        
        try {
            $validated = $request->validate([
                'aluno_id' => 'required|exists:users,id',
                'turma_id' => 'required|exists:turmas,id',
                'raca' => 'required|string',
                'imagePath' => 'nullable|string',
                'respostas' => 'required|array|min:1',
                'total_questoes' => 'required|integer|min:1'
            ]);

            $escolaId = Turma::findOrFail($validated['turma_id'])->escola_id;
            $respostasSalvas = [];
            $acertos = 0;
            
            foreach ($validated['respostas'] as $perguntaId => $respostaAluno) {
                $pergunta = Pergunta::find($perguntaId);
                if (!$pergunta) continue;
                
                $correta = strtoupper($respostaAluno) === strtoupper($pergunta->resposta_correta);
                if ($correta) $acertos++;
                
                $resposta = RespostaSimulado::create([
                    'simulado_id' => $simulado->id,
                    'pergunta_id' => $perguntaId,
                    'user_id' => $validated['aluno_id'],
                    'aplicador_id' => auth()->id(),
                    'escola_id' => $escolaId,
                    'resposta' => $respostaAluno,
                    'correta' => $correta,
                    'raca' => $validated['raca'],
                    'metodo_aplicacao' => 'gabarito',
                    'imagem_gabarito' => $validated['imagePath'],
                    'peso' => $pergunta->peso,
                    'tri_a' => $pergunta->tri_a,
                    'tri_b' => $pergunta->tri_b,
                    'tri_c' => $pergunta->tri_c
                ]);
                
                $respostasSalvas[] = $resposta;
            }

            $notaTRI = SAEBTRICalculator::calculateProficiency(collect($respostasSalvas));
            $porcentagemAcertos = ($acertos / $validated['total_questoes']) * 100;
            
            DB::commit();
            
            session()->forget(['correcao_data', 'aluno_selecionado', 'aluno_id', 'aluno_nome', 'aluno_turma', 'turma_id', 'raca']);
            
            return redirect()
                ->route('respostas_simulados.aplicador.camera', $simulado)
                ->with([
                    'success' => "Respostas do aluno salvas com sucesso!",
                    'nota_aluno' => number_format($notaTRI, 1),
                    'nota_tradicional' => number_format(($acertos / $validated['total_questoes']) * 10, 1),
                    'porcentagem_acertos' => number_format($porcentagemAcertos, 1),
                    'aluno_nome' => User::find($validated['aluno_id'])->name
                ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Erro ao salvar respostas: ' . $e->getMessage())
                ->withInput();
        }
    }

    private function salvarImagemTemporaria($imageData)
    {
        $imageData = str_replace('data:image/jpeg;base64,', '', $imageData);
        $imageData = str_replace(' ', '+', $imageData);
        $imageName = 'gabarito_' . time() . '.jpg';
        $path = 'temp/gabaritos/' . $imageName;
        
        Storage::disk('public')->put($path, base64_decode($imageData));
        
        return $path;
    }
}