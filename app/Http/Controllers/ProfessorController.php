<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Escola;

class ProfessorController extends Controller
{
    public function dashboard()
    {
        // Se for professor e não tiver escola selecionada, redireciona
        if (auth()->user()->isProfessor() && !session('escola_selecionada')) {
            return redirect()->route('selecionar.escola');
        }

        // Busca a ID da escola selecionada na sessão
        $escolaId = session('escola_selecionada');

        // Busca os dados da escola no banco de dados, se a ID existir
        $escola = null;
        if ($escolaId) {
            $escola = Escola::findOrFail($escolaId);
        }

        return view('professor.dashboard', compact('escola'));
    }

    public function selecionarEscola()
    {
        $escolas = auth()->user()->escolas()->get();
        return view('professor.selecionar_escola', compact('escolas'));
    }

    public function definirEscola(Request $request)
    {
        $validated = $request->validate([
            'escola_id' => 'required|exists:escolas,id'
        ]);

        session(['escola_selecionada' => $validated['escola_id']]);
        return redirect()->intended(route('professor.dashboard'));
    }
}