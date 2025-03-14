<?php
namespace App\Http\Controllers;

use App\Models\Atividade;
use App\Models\Disciplina;
use App\Models\Ano;
use App\Models\Habilidade;
use Illuminate\Http\Request;

class AtividadeController extends Controller
{
    public function index()
    {
        $atividades = Atividade::all();
        return view('atividades.index', compact('atividades'));
    }

    public function create()
    {
        $disciplinas = Disciplina::all();
        $anos = Ano::all();
        $habilidades = Habilidade::all();
        return view('atividades.create', compact('disciplinas', 'anos', 'habilidades'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'disciplina_id' => 'required|exists:disciplinas,id',
            'ano_id' => 'required|exists:anos,id',
            'habilidade_id' => 'required|exists:habilidades,id',
            'titulo' => 'required|string|max:255',
            'objetivo' => 'required|string',
            'metodologia' => 'required|string',
            'materiais' => 'required|string',
            'resultados_esperados' => 'required|string',
        ]);

        Atividade::create($request->all());

        return redirect()->route('atividades.index')->with('success', 'Atividade criada com sucesso!');
    }

    public function show(Atividade $atividade)
    {
        return view('atividades.show', compact('atividade'));
    }

    public function edit(Atividade $atividade)
    {
        $disciplinas = Disciplina::all();
        $anos = Ano::all();
        $habilidades = Habilidade::all();
        return view('atividades.edit', compact('atividade', 'disciplinas', 'anos', 'habilidades'));
    }

    public function update(Request $request, Atividade $atividade)
    {
        $request->validate([
            'disciplina_id' => 'required|exists:disciplinas,id',
            'ano_id' => 'required|exists:anos,id',
            'habilidade_id' => 'required|exists:habilidades,id',
            'titulo' => 'required|string|max:255',
            'objetivo' => 'required|string',
            'metodologia' => 'required|string',
            'materiais' => 'required|string',
            'resultados_esperados' => 'required|string',
        ]);

        $atividade->update($request->all());

        return redirect()->route('atividades.index')->with('success', 'Atividade atualizada com sucesso!');
    }

    public function destroy(Atividade $atividade)
    {
        $atividade->delete();
        return redirect()->route('atividades.index')->with('success', 'Atividade excluída com sucesso!');
    }
}