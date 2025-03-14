<?php

namespace App\Http\Controllers;

use App\Models\Habilidade;
use App\Models\Ano;
use App\Models\Disciplina;
use Illuminate\Http\Request;

class HabilidadeController extends Controller
{
    // Função para exibir a lista de habilidades
    public function index()
    {
        $habilidades = Habilidade::with(['ano', 'disciplina'])
            ->orderBy('created_at', 'desc')  // Ordena pela data de criação em ordem decrescente
            ->paginate(5);
    
        return view('habilidades.index', compact('habilidades'));
    }
    

    // Função para mostrar o formulário de criação de habilidades
    public function create()
    {
        $anos = Ano::all();
        $disciplinas = Disciplina::all();
       
        return view('habilidades.create', compact('anos', 'disciplinas'));
    }

    // Função para armazenar uma nova habilidade no banco de dados
    public function store(Request $request)
    {
        $request->validate([
            'ano_id' => 'required|exists:anos,id',
            'disciplina_id' => 'required|exists:disciplinas,id',
            'descricao' => 'required|string|max:255',
        ]);

        Habilidade::create([
            'ano_id' => $request->ano_id,
            'disciplina_id' => $request->disciplina_id,
            'descricao' => $request->descricao,
        ]);

        return redirect()->route('habilidades.index')->with('success', 'Habilidade cadastrada com sucesso!');
    }

    public function show(Habilidade $habilidade)
    {
        return view('habilidades.show', compact('habilidade'));
    }
    // Função para mostrar o formulário de edição de uma habilidade
    public function edit(Habilidade $habilidade)
    {
        $anos = Ano::all();
        $disciplinas = Disciplina::all();
        return view('habilidades.edit', compact('habilidade', 'anos', 'disciplinas'));
    }

    // Função para atualizar a habilidade no banco de dados
    public function update(Request $request, Habilidade $habilidade)
    {
        $request->validate([
            'ano_id' => 'required|exists:anos,id',
            'disciplina_id' => 'required|exists:disciplinas,id',
                     'descricao' => 'required|string|max:255',
        ]);

        $habilidade->update([
            'ano_id' => $request->ano_id,
            'disciplina_id' => $request->disciplina_id,
                  'descricao' => $request->descricao,
        ]);

        return redirect()->route('habilidades.index')->with('success', 'Habilidade atualizada com sucesso!');
    }

    // Função para excluir uma habilidade
    public function destroy(Habilidade $habilidade)
    {
        $habilidade->delete();
        return redirect()->route('habilidades.index')->with('success', 'Habilidade excluída com sucesso!');
    }
}
