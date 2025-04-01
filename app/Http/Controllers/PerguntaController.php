<?php
namespace App\Http\Controllers;

use App\Models\Pergunta;
use App\Models\Ano;
use App\Models\Disciplina;
use App\Models\Habilidade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PerguntaController extends Controller
{
    // Exibir formulário de criação
    public function create()
    {
        $anos = Ano::all();
        $disciplinas = Disciplina::all();
        $habilidades = Habilidade::all();

        return view('perguntas.create', compact('anos', 'disciplinas', 'habilidades'));
    }

    // Salvar nova pergunta
    public function store(Request $request)
    {
    // Validação dos dados
    $request->validate([
        'ano_id' => 'required|exists:anos,id',
        'disciplina_id' => 'required|exists:disciplinas,id',
        'habilidade_id' => 'required|exists:habilidades,id',
        'enunciado' => 'required',
        'alternativa_a' => 'required',
        'alternativa_b' => 'required',
        'alternativa_c' => 'required',
        'alternativa_d' => 'required',
        'resposta_correta' => 'required|in:A,B,C,D',
        'imagem' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validação da imagem
        'imagem_tamanho' => 'nullable|in:img-fluid,w-25,w-50,w-75,w-100', // Validação do tamanho
        'peso' => 'required|integer|min:1|max:10', 

    ]);

    // Upload da imagem (se existir)
    $imagemPath = null;
    if ($request->hasFile('imagem')) {
        $imagemPath = $request->file('imagem')->store('perguntas', 'public');
    }

    // Cria a pergunta
    Pergunta::create([
        'ano_id' => $request->ano_id,
        'disciplina_id' => $request->disciplina_id,
        'habilidade_id' => $request->habilidade_id,
        'enunciado' => $request->enunciado,
        'alternativa_a' => $request->alternativa_a,
        'alternativa_b' => $request->alternativa_b,
        'alternativa_c' => $request->alternativa_c,
        'alternativa_d' => $request->alternativa_d,
        'resposta_correta' => $request->resposta_correta,
        'peso' => $request->peso, 
        'imagem' => $imagemPath, // Salva o caminho da imagem
        'imagem_tamanho' => $request->imagem_tamanho, // Salva o tamanho escolhido
    ]);

    return redirect()->route('perguntas.index')->with('success', 'Pergunta cadastrada com sucesso!');
    }

    // Listar todas as perguntas
    public function index()
{
    // Paginação de 10 perguntas por página, ordenadas por criação (última primeiro)
    $perguntas = Pergunta::orderBy('created_at', 'desc')
                        ->paginate(10);
    
    return view('perguntas.index', compact('perguntas'));
}

    // Exibir uma pergunta específica
    public function show($id)
    {
        $pergunta = Pergunta::findOrFail($id);
        return view('perguntas.show', compact('pergunta'));
    }

    // Exibir formulário de edição
    public function edit($id)
    {
        $pergunta = Pergunta::findOrFail($id);
        $anos = Ano::all();
        $disciplinas = Disciplina::all();
        $habilidades = Habilidade::all();

        return view('perguntas.edit', compact('pergunta', 'anos', 'disciplinas', 'habilidades'));
    }

    // Atualizar uma pergunta
    public function update(Request $request, $id)
    {
        $request->validate([
            'ano_id' => 'required|exists:anos,id',
            'disciplina_id' => 'required|exists:disciplinas,id',
            'habilidade_id' => 'required|exists:habilidades,id',
            'enunciado' => 'required',
            'alternativa_a' => 'required',
            'alternativa_b' => 'required',
            'alternativa_c' => 'required',
            'alternativa_d' => 'required',
            'resposta_correta' => 'required|in:A,B,C,D',
            'imagem' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validação da imagem
        ]);

        $pergunta = Pergunta::findOrFail($id);

        // Upload da imagem (se existir)
        if ($request->hasFile('imagem')) {
            // Remove a imagem antiga (se existir)
            if ($pergunta->imagem) {
                Storage::disk('public')->delete($pergunta->imagem);
            }
            $imagemPath = $request->file('imagem')->store('perguntas', 'public');
            $pergunta->imagem = $imagemPath;
        }

        // Atualiza os outros campos
        $pergunta->update($request->except('imagem'));

        return redirect()->route('perguntas.index')->with('success', 'Pergunta atualizada com sucesso!');
    }

    // Excluir uma pergunta
    public function destroy($id)
    {
        $pergunta = Pergunta::findOrFail($id);

        // Remove a imagem (se existir)
        if ($pergunta->imagem) {
            Storage::disk('public')->delete($pergunta->imagem);
        }

        $pergunta->delete();
        return redirect()->route('perguntas.index')->with('success', 'Pergunta excluída com sucesso!');
    }
}
