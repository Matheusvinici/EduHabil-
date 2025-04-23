<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Escola;
use Illuminate\Support\Facades\Auth;

class GestorController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $escola = $this->getEscolaAtual();
    
        if (!$escola || !$user->escolas->contains($escola->id)) {
            session()->forget('escola_selecionada');
            return redirect()->route('gestor.selecionar-escola')
                   ->with('error', 'Escola selecionada não é mais válida.');
        }
    
        // Carrega contagens básicas - AGORA FUNCIONARÁ
        $escola->loadCount(['turmas', 'alunos', 'professores']);
    
        // Contagem de atividades
        $atividades_count = \App\Models\AtividadeProfessor::whereHas('professor.escolas', function($q) use ($escola) {
                                $q->where('escolas.id', $escola->id);
                            })->count();
    
        return view('gestor.dashboard', compact('escola', 'atividades_count'));
    }

    public function selecionarEscola()
    {
        $user = Auth::user();
        $escolas = $user->escolas()->get();
        
        if ($escolas->isEmpty()) {
            return redirect()->route('profile.edit')
                   ->with('error', 'Você não está vinculado a nenhuma escola.');
        }

        return view('gestor.selecionar-escola', compact('escolas'));
    }

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
            return redirect()->route('gestor.selecionar-escola')
                   ->with('error', 'Você não tem permissão para acessar esta escola');
        }

        session(['escola_selecionada' => $validated['escola_id']]);
        
        return redirect()->route('gestor.dashboard')
               ->with('success', 'Escola selecionada com sucesso!');
    }

    public function trocarEscola()
    {
        session()->forget('escola_selecionada');
        return redirect()->route('gestor.selecionar-escola');
    }

    protected function getEscolaAtual()
    {
        if (session('escola_selecionada')) {
            return Escola::with(['turmas', 'alunos', 'professores'])
                      ->find(session('escola_selecionada'));
        }
        return null;
    }

    protected function getUltimasAtividades(Escola $escola)
    {
        return \App\Models\AtividadeProfessor::whereHas('professor.escolas', function($q) use ($escola) {
                $q->where('escolas.id', $escola->id);
            })
            ->with(['atividade.disciplina', 'professor'])
            ->latest()
            ->limit(5)
            ->get();
    }
}