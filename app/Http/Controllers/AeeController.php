<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Escola;

class AeeController extends Controller
{
    public function dashboard()
    {
        if (auth()->user()->isAee() && !session('escola_selecionada_aee')) {
            return redirect()->route('aee.selecionar.escola');
        }

        $escolaId = session('escola_selecionada_aee');
        $escola = $escolaId ? Escola::findOrFail($escolaId) : null;

        return view('aee.dashboard', compact('escola'));
    }

    public function selecionarEscola()
    {
        $escolas = auth()->user()->escolas()->get();
        return view('aee.selecionar_escola', compact('escolas'));
    }

    public function definirEscola(Request $request)
    {
        $validated = $request->validate([
            'escola_id' => 'required|exists:escolas,id'
        ]);

        session(['escola_selecionada_aee' => $validated['escola_id']]);
        return redirect()->intended(route('aee.dashboard'));
    }
}