<?php
namespace App\Http\Controllers;

use App\Models\Adaptacao;
use App\Models\Recurso;
use App\Models\Deficiencia;
use App\Models\Caracteristica;
use Illuminate\Http\Request;
use PDF; // Para gerar PDF


class AdaptacaoController extends Controller
{
    public function index()
    {
        $adaptacoes = Adaptacao::with(['recurso', 'deficiencias', 'caracteristicas'])->paginate(5); // 5 itens por página
        return view('adaptacoes.index', compact('adaptacoes'));
    }

    // Exibir formulário de criação
    public function create()
    {
        $recursos = Recurso::all();
        $deficiencias = Deficiencia::all();
        $caracteristicas = Caracteristica::all();
        return view('adaptacoes.create', compact('recursos', 'deficiencias', 'caracteristicas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'deficiencias' => 'required|array',
            'deficiencias.*' => 'exists:deficiencias,id',
            'caracteristicas' => 'required|array',
            'caracteristicas.*' => 'exists:caracteristicas,id',
        ]);
    
        // Busca recursos que correspondam às deficiências e características selecionadas
        $recursos = Recurso::whereHas('deficiencias', function ($query) use ($request) {
            $query->whereIn('deficiencias.id', $request->deficiencias);
        })->whereHas('caracteristicas', function ($query) use ($request) {
            $query->whereIn('caracteristicas.id', $request->caracteristicas);
        })->get();
    
        // Verifica se há recursos disponíveis
        if ($recursos->isEmpty()) {
            return redirect()->back()->with('error', 'Nenhum recurso encontrado para as deficiências e características selecionadas.');
        }
    
        // Seleciona um recurso aleatório
        $recursoAleatorio = $recursos->random();
    
        // Cria a adaptação
        $adaptacao = Adaptacao::create([
            'recurso_id' => $recursoAleatorio->id,
        ]);
    
        // Associa as deficiências e características à adaptação
        $adaptacao->deficiencias()->attach($request->deficiencias);
        $adaptacao->caracteristicas()->attach($request->caracteristicas);
    
        return redirect()->route('adaptacoes.index')->with('success', 'Atividade gerada com sucesso!');
    }

    // Exibir detalhes de uma adaptação
    public function show(Adaptacao $adaptacao)
    {
        return view('adaptacoes.show', compact('adaptacao'));
    }

    // Exibir formulário de edição
    public function edit(Adaptacao $adaptacao)
    {
        $recursos = Recurso::all();
        $deficiencias = Deficiencia::all();
        $caracteristicas = Caracteristica::all();
        return view('adaptacoes.edit', compact('adaptacao', 'recursos', 'deficiencias', 'caracteristicas'));
    }

    // Atualizar adaptação
    public function update(Request $request, Adaptacao $adaptacao)
    {
        $request->validate([
            'professor_id' => 'required|exists:users,id',
            'recurso_id' => 'required|exists:recursos,id',
            'deficiencia_id' => 'required|exists:deficiencias,id',
            'caracteristica_id' => 'required|exists:caracteristicas,id',
        ]);

        $adaptacao->update($request->all());
        return redirect()->route('adaptacoes.index')->with('success', 'Adaptação atualizada com sucesso!');
    }

    // Excluir adaptação
    public function destroy(Adaptacao $adaptacao)
    {
        $adaptacao->delete();
        return redirect()->route('adaptacoes.index')->with('success', 'Adaptação excluída com sucesso!');
    }
    public function gerarPdf(Adaptacao $adaptacao)
    {
        // Carrega os dados da adaptação com os relacionamentos corretos
        $adaptacao->load(['recurso', 'deficiencias', 'caracteristicas']);
    
        // Gera o PDF
        $pdf = Pdf::loadView('adaptacoes.pdf', compact('adaptacao'));
    
        // Retorna o PDF para download ou visualização
        return $pdf->download('atividade_gerada.pdf');

    }
}