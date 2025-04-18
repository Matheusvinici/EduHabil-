<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Simulado;
use App\Models\RespostaSimulado;
use App\Models\User;
use App\Models\Turma;
use App\Models\Escola;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class GabaritoController extends Controller
{
    // Configurações fixas do gabarito padrão
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
        // Validação básica
        $request->validate([
            'turma_id' => 'required|exists:turmas,id',
            'aluno_id' => 'required|exists:users,id',
            'raca' => 'required'
        ]);
    
        // Busca a turma com verificação
        $turma = Turma::find($request->turma_id);
        if (!$turma) {
            return back()->with('error', 'Turma não encontrada.');
        }
    
        // Busca o aluno com verificação de papel
        $aluno = User::where('id', $request->aluno_id)
                ->where('role', 'aluno')
                ->first();
        
        if (!$aluno) {
            return back()->with('error', 'Aluno não encontrado ou não tem permissão.');
        }
    
        // Verifica se aluno pertence à turma
        if ($aluno->turma_id != $turma->id) {
            return back()->with('error', 'O aluno não pertence à turma selecionada.');
        }
    
        // Verifica se já respondeu (opcional)
        $respostaExistente = RespostaSimulado::where([
            'simulado_id' => $simulado->id,
            'user_id' => $aluno->id
        ])->exists();
    
        if ($respostaExistente) {
            return back()->with('error', 'Este aluno já respondeu este simulado.');
        }
    
        // Armazena na sessão
        session([
            'aluno_selecionado' => true,
            'aluno_id' => $aluno->id,
            'aluno_nome' => $aluno->name,
            'aluno_turma' => $turma->nome_turma,
            'turma_id' => $turma->id,
            'raca' => $request->raca,
        ]);
    
        // Redirecionamento corrigido
        return redirect()->route('respostas_simulados.aplicador.camera', [
            'simulado' => $simulado->id,
            'keep_session' => true // Mantém os dados da sessão
        ]);
    }
   
    public function getAlunosPorTurma(Request $request)
{
    $request->validate([
        'turma_id' => 'required|exists:turmas,id',
        'simulado_id' => 'required|exists:simulados,id'
    ]);

    try {
        $turmaId = $request->turma_id;
        $simuladoId = $request->simulado_id;

        // Busca alunos da turma que ainda não responderam o simulado
        $alunos = User::where('turma_id', $turmaId)
                    ->where('role', 'aluno')
                    ->whereDoesntHave('respostasSimulados', function($query) use ($simuladoId) {
                        $query->where('simulado_id', $simuladoId);
                    })
                    ->select('id', 'name')
                    ->orderBy('name')
                    ->get();

        return response()->json($alunos);

    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Erro ao carregar alunos: ' . $e->getMessage()
        ], 500);
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
            // Obter o número real de questões do simulado
            $totalQuestoes = $simulado->perguntas()->count();
            
            // Processar apenas as questões existentes
            $respostas = [];
            for ($i = 1; $i <= $totalQuestoes; $i++) {
                $respostas[$i] = [
                    'resposta' => '', // Inicia vazio
                    'confianca' => 0, // Confiança zero inicial
                    'imagem' => null
                ];
            }
            
            // Salvar imagem temporária
            $imagePath = $this->salvarImagemTemporaria($validated['processed_image']);
            
            // Armazenar dados na sessão
            session([
                'correcao_data' => [
                    'simulado' => $simulado,
                    'aluno' => User::find($validated['aluno_id']),
                    'dados' => $validated,
                    'respostas' => $respostas, // Agora só as questões existentes
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
    
    // Obter o número real de questões do simulado
    $totalQuestoes = $simulado->perguntas()->count();
    
    // Obter respostas corretas
    $respostasCorretas = $simulado->perguntas()
        ->select('perguntas.id', 'perguntas.resposta_correta')
        ->get()
        ->pluck('resposta_correta', 'id')
        ->toArray();

    // Processar as respostas (apenas as detectadas)
    $respostasProcessadas = [];
    foreach ($data['respostas'] as $questaoId => $resposta) {
        if ($questaoId <= $totalQuestoes) { // Só processa questões existentes
            $respostasProcessadas[$questaoId] = [
                'resposta' => $resposta['resposta'],
                'confianca' => $resposta['confianca'],
                'imagem' => $resposta['imagem'] ?? null,
                'correta' => ($resposta['resposta'] === ($respostasCorretas[$questaoId] ?? null))
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
        'alternativas' => ['A', 'B', 'C', 'D'] // Para os radio buttons
    ]);
}
private function processarGabarito($imageData, Simulado $simulado)
{
    $totalQuestoes = $simulado->perguntas()->count();
    $respostas = [];
    
    // Aqui você implementaria seu processamento real da imagem
    // Por enquanto, vamos retornar um array vazio para forçar a seleção manual
    for ($i = 1; $i <= $totalQuestoes; $i++) {
        $respostas[$i] = [
            'resposta' => '', // Deixe vazio para forçar seleção manual
            'confianca' => 0, // Confiança zero
            'imagem' => null
        ];
    }
    
    return $respostas;
}
    
    private function salvarImagemTemporaria($imageData)
    {
        // Remove o cabeçalho da string base64
        $imageData = str_replace('data:image/jpeg;base64,', '', $imageData);
        $imageData = str_replace(' ', '+', $imageData);
        $imageName = 'gabarito_' . time() . '.jpg';
        $path = 'temp/' . $imageName;
        
        // Salva no storage
        Storage::disk('public')->put($path, base64_decode($imageData));
        
        return $path;
    }


    private function saveTempImage($image): string
    {
        $path = 'temp/gabaritos/' . uniqid() . '.jpg';
        Storage::put($path, $image->toJpeg());
        return $path;
    }

    private function lerRespostasGabarito($image, $simulado): array
    {
        $respostas = [];
        $totalQuestoes = count($simulado->perguntas);
        
        for ($i = 0; $i < $totalQuestoes; $i++) {
            $linha = floor($i / $this->gabaritoConfig['questoes_por_linha']);
            $coluna = $i % $this->gabaritoConfig['questoes_por_linha'];
            
            $x = $this->gabaritoConfig['margem_esquerda'] + ($coluna * $this->gabaritoConfig['espacamento_horizontal']);
            $y = $this->gabaritoConfig['margem_superior'] + ($linha * $this->gabaritoConfig['espacamento_vertical']);
            
            foreach (['A', 'B', 'C', 'D'] as $opcao) {
                $offsetY = $y + (array_search($opcao, ['A', 'B', 'C', 'D']) * 
                    ($this->gabaritoConfig['tamanho_circulo'] + 5));
                
                if ($this->circuloMarcado($image->crop(
                    $this->gabaritoConfig['tamanho_circulo'],
                    $this->gabaritoConfig['tamanho_circulo'],
                    $x,
                    $offsetY
                ))) {
                    $respostas[$i + 1] = $opcao;
                    break;
                }
            }
        }

        return $respostas;
    }

    private function circuloMarcado($image): bool
    {
        $image->greyscale();
        $threshold = 0.5;
        $totalPixels = $image->width() * $image->height();
        $darkPixels = 0;
        
        for ($x = 0; $x < $image->width(); $x++) {
            for ($y = 0; $y < $image->height(); $y++) {
                $color = $image->pickColor($x, $y);
                
                // Get the numeric values from color channels
                $red = $color->channel('red')->value();
                $green = $color->channel('green')->value();
                $blue = $color->channel('blue')->value();
                
                $brightness = ($red + $green + $blue) / 3;
                $darkPixels += ($brightness < 128) ? 1 : 0;
            }
        }
        
        return ($darkPixels / $totalPixels) > $threshold;
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

        // SOLUÇÃO 1: Usando Eloquent (recomendado)
        $respostasCorretas = $simulado->perguntas()
            ->select('perguntas.id', 'perguntas.resposta_correta')
            ->get()
            ->pluck('resposta_correta', 'id')
            ->toArray();

        // SOLUÇÃO 2: Alternativa usando Query Builder
        // $respostasCorretas = DB::table('perguntas_simulados')
        //     ->join('perguntas', 'perguntas_simulados.pergunta_id', '=', 'perguntas.id')
        //     ->where('perguntas_simulados.simulado_id', $simulado->id)
        //     ->select('perguntas.id', 'perguntas.resposta_correta')
        //     ->pluck('resposta_correta', 'id')
        //     ->toArray();

        $escolaId = Turma::findOrFail($validated['turma_id'])->escola_id;
        
        foreach ($validated['respostas'] as $perguntaId => $respostaAluno) {
            if (!array_key_exists($perguntaId, $respostasCorretas)) {
                continue;
            }

            RespostaSimulado::updateOrCreate(
                [
                    'simulado_id' => $simulado->id,
                    'pergunta_id' => $perguntaId,
                    'user_id' => $validated['aluno_id']
                ],
                [
                    'aplicador_id' => auth()->id(),
                    'escola_id' => $escolaId,
                    'resposta' => $respostaAluno,
                    'correta' => ($respostaAluno === $respostasCorretas[$perguntaId]),
                    'raca' => $validated['raca'],
                    'metodo_aplicacao' => 'gabarito',
                    'imagem_gabarito' => $validated['imagePath']
                ]
            );
        }

        DB::commit();
        
        session()->forget(['correcao_data', 'aluno_selecionado', 'aluno_id', 'aluno_nome', 'aluno_turma', 'turma_id', 'raca']);
        
        return redirect()->route('respostas_simulados.aplicador.index')
            ->with('success', 'Respostas salvas com sucesso!');

    } catch (\Exception $e) {
        DB::rollBack();
        
        logger()->error('Erro ao salvar respostas', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'request' => $request->all()
        ]);

        return redirect()->back()
            ->with('error', 'Erro ao salvar respostas: ' . $e->getMessage())
            ->withInput();
    }
}
}