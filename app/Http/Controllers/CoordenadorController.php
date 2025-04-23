<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Escola;
use Illuminate\Support\Facades\Auth;

class CoordenadorController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $escola = $this->getEscolaAtual();

        // Verificação adicional de segurança
        if (!$escola || !$user->escolas->contains($escola->id)) {
            session()->forget('escola_selecionada');
            return redirect()->route('coordenador.selecionar.escola')
                   ->with('error', 'Escola selecionada não é mais válida.');
        }

        return view('coordenador.dashboard', compact('escola'));
    }

    public function selecionarEscola()
    {
        $user = Auth::user();
        $escolas = $user->escolas()->get();
        
        if ($escolas->isEmpty()) {
            return redirect()->route('profile.edit')
                   ->with('error', 'Você não está vinculado a nenhuma escola.');
        }

        return view('coordenador.selecionar_escola', compact('escolas'));
    }

   // app/Http/Controllers/CoordenadorController.php

public function definirEscola(Request $request)
{
    $validated = $request->validate([
        'escola_id' => 'required|exists:escolas,id'
    ]);

    $user = Auth::user();

    // Verificação mais robusta
    $escolaPermitida = $user->escolas()
        ->where('escolas.id', $validated['escola_id'])
        ->exists();

    if (!$escolaPermitida) {
        return redirect()->route('coordenador.selecionar.escola')
               ->with('error', 'Você não tem permissão para acessar esta escola');
    }

    session(['escola_selecionada' => $validated['escola_id']]);
    
    return redirect()->route('coordenador.dashboard')
           ->with('success', 'Escola selecionada com sucesso!');
}
    public function trocarEscola()
    {
        session()->forget('escola_selecionada');
        return redirect()->route('coordenador.selecionar.escola');
    }

    protected function getEscolaAtual()
    {
        if (session('escola_selecionada')) {
            return Escola::find(session('escola_selecionada'));
        }
        return null;
    }
}