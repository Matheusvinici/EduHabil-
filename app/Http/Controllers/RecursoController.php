<?php
namespace App\Http\Controllers;

use App\Models\Recurso;

use App\Models\Deficiencia;
use Illuminate\Http\Request;

class RecursoController extends Controller
{
    // Exibir lista de recursos
    public function index()
    {
        $recursos = Recurso::with('deficiencias')
            ->orderBy('created_at', 'desc') // Ordena pelos mais recentes
            ->paginate(5); // Paginação com 5 registros por página

        return view('recursos.index', compact('recursos'));
    }
    // Exibir formulário de criação
    public function create()
    {
     
     
        $deficiencias = Deficiencia::all();
        return view('recursos.create', compact('deficiencias'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:100',
            'descricao' => 'required|string',
            'como_trabalhar' => 'required|string',
            'direcionamentos' => 'required|string',
            'deficiencias' => 'nullable|array',
            'deficiencias.*' => 'exists:deficiencias,id',
            'caracteristicas' => 'nullable|array',
            'caracteristicas.*' => 'exists:caracteristicas,id',
        ]);
    
        // Cria o recurso com os campos corretos
        $recurso = Recurso::create([
            'nome' => $request->nome,
            'descricao' => $request->descricao,
        
            'como_trabalhar' => $request->como_trabalhar,
            'direcionamentos' => $request->direcionamentos,
        ]);
    
        // Associar deficiências ao recurso
        if ($request->deficiencias) {
            $recurso->deficiencias()->attach($request->deficiencias);
        }
    
        // Associar características ao recurso
        if ($request->caracteristicas) {
            $recurso->caracteristicas()->attach($request->caracteristicas);
        }
    
        return redirect()->route('recursos.index')->with('success', 'Recurso criado com sucesso!');
    }
    // Exibir detalhes de um recurso
    public function show($id)
    {
        $recurso = Recurso::with(['deficiencias', 'caracteristicas'])->findOrFail($id);
        return view('recursos.show', compact('recurso'));
    }

    // Exibir formulário de edição
    public function edit($id)
    {
        $recurso = Recurso::with(['deficiencias', 'caracteristicas'])->findOrFail($id);
        $deficiencias = Deficiencia::all(); // Carrega todas as deficiências para o formulário
        return view('recursos.edit', compact('recurso', 'deficiencias'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nome' => 'required|string|max:100',
            'descricao' => 'required|string',
            'como_trabalhar' => 'required|string',
            'direcionamentos' => 'required|string',
            'deficiencias' => 'nullable|array',
            'deficiencias.*' => 'exists:deficiencias,id',
            'caracteristicas' => 'nullable|array',
            'caracteristicas.*' => 'exists:caracteristicas,id',
        ]);

        // Atualiza o recurso
        $recurso = Recurso::findOrFail($id);
        $recurso->update([
            'nome' => $request->nome,
            'descricao' => $request->descricao,
            'como_trabalhar' => $request->como_trabalhar,
            'direcionamentos' => $request->direcionamentos,
        ]);

        // Sincroniza deficiências e características
        $recurso->deficiencias()->sync($request->deficiencias);
        $recurso->caracteristicas()->sync($request->caracteristicas);

        return redirect()->route('recursos.index')->with('success', 'Recurso atualizado com sucesso!');
    }

    // Excluir recurso
    public function destroy(Recurso $recurso)
    {
        $recurso->deficiencias()->detach();
        $recurso->delete();
        return redirect()->route('recursos.index')->with('success', 'Recurso excluído com sucesso!');
    }
}