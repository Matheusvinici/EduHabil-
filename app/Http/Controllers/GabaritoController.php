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
        // Salvar imagem temporária
        $imagePath = $this->salvarImagemTemporaria($validated['processed_image']);
        
        // Obter número REAL de questões desta prova
        $totalQuestoes = $simulado->perguntas()->count();
        
        // Processar o gabarito (o Python ainda analisará 60, mas filtramos depois)
        $respostasBrutas = $this->processarGabarito($validated['processed_image'], $simulado);
        
        // Filtrar só as questões que existem nesta prova
        $respostas = [];
        for ($i = 1; $i <= $totalQuestoes; $i++) {
            $respostas[$i] = $respostasBrutas[$i] ?? [
                'resposta' => '',
                'confianca' => 0,
                'imagem' => null
            ];
        }

        // Restante do método permanece igual...
        $respostasCorretas = $simulado->perguntas()
            ->select('perguntas.id', 'perguntas.resposta_correta')
            ->get()
            ->pluck('resposta_correta', 'id')
            ->toArray();

        foreach ($respostas as $questaoId => &$resposta) {
            if (isset($respostasCorretas[$questaoId])) {
                $resposta['correta'] = ($resposta['resposta'] === $respostasCorretas[$questaoId]);
            }
        }

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
        return back()->with('error', 'Erro ao processar gabarito: '.$e->getMessage());
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
    try {
        // Salvar a imagem temporariamente
        $imagePath = $this->salvarImagemTemporaria($imageData);
        $absoluteImagePath = storage_path('app/public/' . $imagePath);
        
        // Caminho para o script Python (ajuste conforme sua estrutura de pastas)
        $pythonScript = base_path('scripts/gabarito_processor.py');
        
        // Comando para executar o script Python
        $command = escapeshellcmd("python3 {$pythonScript} {$absoluteImagePath}");
        
        // Executar o comando
        $output = shell_exec($command);
        
        // Decodificar o JSON retornado
        $result = json_decode($output, true);
        
        if (!$result || !isset($result['success'])) {
            throw new \Exception("Falha ao processar gabarito: " . ($result['error'] ?? 'Erro desconhecido'));
        }
        
        // Processar as respostas do Python para o formato esperado
        $respostasProcessadas = [];
        $totalQuestoes = $simulado->perguntas()->count();
        
        // Mapear as respostas detectadas pelo Python
        foreach ($result['respostas'] as $questaoId => $dados) {
            if ($questaoId <= $totalQuestoes) {
                $respostasProcessadas[$questaoId] = [
                    'resposta' => $dados['resposta'],
                    'confianca' => $dados['confianca'] ?? 0.9, // Valor padrão de confiança
                    'imagem' => $dados['imagem'] ?? null
                ];
            }
        }
        
        // Preencher questões não detectadas
        for ($i = 1; $i <= $totalQuestoes; $i++) {
            if (!isset($respostasProcessadas[$i])) {
                $respostasProcessadas[$i] = [
                    'resposta' => '',
                    'confianca' => 0,
                    'imagem' => null
                ];
            }
        }
        
        return $respostasProcessadas;
        
    } catch (\Exception $e) {
        // Fallback: retornar array vazio em caso de erro
        $totalQuestoes = $simulado->perguntas()->count();
        $respostas = [];
        
        for ($i = 1; $i <= $totalQuestoes; $i++) {
            $respostas[$i] = [
                'resposta' => '',
                'confianca' => 0,
                'imagem' => null
            ];
        }
        
        logger()->error('Erro no processarGabarito: ' . $e->getMessage());
        return $respostas;
    }
}
    
private function salvarImagemTemporaria($imageData)
{
    // Remove o cabeçalho da string base64
    $imageData = str_replace('data:image/jpeg;base64,', '', $imageData);
    $imageData = str_replace(' ', '+', $imageData);
    $imageName = 'gabarito_' . time() . '.jpg';
    $path = 'temp/' . $imageName;
    
    // Garantir que a pasta temp existe
    if (!Storage::disk('public')->exists('temp')) {
        Storage::disk('public')->makeDirectory('temp');
    }
    
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
    
            $respostasCorretas = $simulado->perguntas()
                ->select('perguntas.id', 'perguntas.resposta_correta')
                ->get()
                ->pluck('resposta_correta', 'id')
                ->toArray();
    
            $escolaId = Turma::findOrFail($validated['turma_id'])->escola_id;
            
            // Contadores para calcular a nota
            $totalCorretas = 0;
            $totalQuestoes = count($respostasCorretas);
            
            foreach ($validated['respostas'] as $perguntaId => $respostaAluno) {
                if (!array_key_exists($perguntaId, $respostasCorretas)) {
                    continue;
                }
    
                $correta = ($respostaAluno === $respostasCorretas[$perguntaId]);
                if ($correta) {
                    $totalCorretas++;
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
                        'correta' => $correta,
                        'raca' => $validated['raca'],
                        'metodo_aplicacao' => 'gabarito',
                        'imagem_gabarito' => $validated['imagePath']
                    ]
                );
            }
    
            DB::commit();
            
            // Calcular nota (0-100)
            $nota = ($totalCorretas / $totalQuestoes) * 100;
            
            // Obter dados do aluno
            $aluno = User::find($validated['aluno_id']);
            
            // Limpar sessão
            session()->forget(['correcao_data', 'aluno_selecionado', 'aluno_id', 'aluno_nome', 'aluno_turma', 'turma_id', 'raca']);
            
            // Redirecionar para a view de seleção com mensagem
            return redirect()
                ->route('respostas_simulados.aplicador.camera', $simulado)
                ->with('success', [
                    'message' => "Respostas do aluno {$aluno->name} salvas com sucesso!",
                    'nota' => number_format($nota, 1),
                    'aluno_nome' => $aluno->name
                ]);
    
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