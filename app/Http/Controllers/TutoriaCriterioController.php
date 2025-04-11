<?php

namespace App\Http\Controllers;

use App\Models\TutoriaCriterio;
use Illuminate\Http\Request;

class TutoriaCriterioController extends Controller
{
    public function index()
    {
        $tutoria_criterios = TutoriaCriterio::orderBy('categoria', 'asc')->paginate(15);
        return view('tutoria_criterios.index', compact('tutoria_criterios'));
    }

    public function create()
    {
        return view('tutoria_criterios.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'categoria' => 'required|string|max:50',
            'descricao' => 'required|string|max:100',
        ]);

        TutoriaCriterio::create($request->all());

        return redirect()
            ->route('tutoria_criterios.index')
            ->with('message', 'Critério criado com sucesso!')
            ->with('type', 'success');
    }

    public function edit(TutoriaCriterio $tutoria_criterio)
    {
        return view('tutoria_criterios.edit', compact('tutoria_criterio'));
    }

    public function update(Request $request, TutoriaCriterio $tutoria_criterio)
    {
        $request->validate([
            'categoria' => 'required|string|max:50',
            'descricao' => 'required|string|max:100',
        ]);

        $tutoria_criterio->update($request->all());

        return redirect()
            ->route('tutoria_criterios.index')
            ->with('message', 'Critério atualizado com sucesso!')
            ->with('type', 'info');
    }

    public function destroy(TutoriaCriterio $tutoria_criterio)
    {
        $tutoria_criterio->delete();

        return redirect()
            ->route('tutoria_criterios.index')
            ->with('message', 'Critério removido com sucesso!')
            ->with('type', 'danger');
    }
}
