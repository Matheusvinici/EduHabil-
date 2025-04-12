<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Escola; // Adicione esta linha

class ProfessorController extends Controller
{
    public function dashboard(Request $request)
    {
        $user = Auth::user();
        
        // Verifica se já tem escola selecionada na sessão
        $escolaSelecionada = session('escola_professor');
        
        // Se não tem escola selecionada ou foi solicitada mudança
        if (!$escolaSelecionada || $request->has('mudar_escola')) {
            return $this->selecionarEscola();
        }
        
        // Carrega os dados da escola selecionada
        $escola = $user->escolas()->find($escolaSelecionada);
        
        if (!$escola) {
            // Se a escola não existe mais no vínculo do professor
            return $this->selecionarEscola();
        }
        
        // Obter o tipo da escola para determinar os acessos
        $tipoEscola = $escola->tipo; // Assumindo que há um campo 'tipo' na tabela escolas
        
        return view('professor.dashboard', compact('escola', 'tipoEscola'));
    }
    
    public function selecionarEscola()
    {
        $user = Auth::user();
        $escolas = $user->escolas()->get();
        
        if ($escolas->count() === 1) {
            // Se só tem uma escola, seleciona automaticamente
            session(['escola_professor' => $escolas->first()->id]);
            return redirect()->route('professor.dashboard');
        }
        
        return view('professor.selecionar_escola', compact('escolas'));
    }
    
    public function definirEscola(Request $request)
    {
        $request->validate([
            'escola_id' => 'required|exists:escolas,id'
        ]);
        
        $user = Auth::user();
        
        // Verifica se o professor está vinculado à escola selecionada
        if (!$user->escolas()->where('escolas.id', $request->escola_id)->exists()) {
            return back()->withErrors(['escola_id' => 'Você não está vinculado a esta escola']);
        }
        
        // Armazena a escola selecionada na sessão
        session(['escola_professor' => $request->escola_id]);
        
        return redirect()->route('professor.dashboard');
    }
}