<?php
namespace App\Http\Controllers;


use Illuminate\Support\Facades\Validator;
use App\Models\Atividade;
use App\Models\Disciplina;
use App\Models\Ano;
use Illuminate\Support\Facades\Log;

use App\Models\Habilidade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; 

class AtividadeController extends Controller
{
    public function index()
    {
        // Carrega atividades com relacionamentos eager loading
        $atividades = Atividade::with(['ano', 'disciplinas', 'habilidades'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
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
            $validator = Validator::make($request->all(), [
                'ano_id' => 'required|exists:anos,id',
                'disciplinas' => 'required|array|min:1',
                'disciplinas.*' => 'exists:disciplinas,id',
                'habilidades' => 'required|array|min:1',
                'habilidades.*' => 'exists:habilidades,id',
                'titulo' => 'required|string|max:255',
                'objetivo' => 'required|string',
                'metodologia' => 'required|string',
                'materiais' => 'required|string',
                'resultados_esperados' => 'required|string',
                'links_sugestoes' => 'nullable|string',
            ], [
                'disciplinas.required' => 'Selecione pelo menos uma disciplina',
                'habilidades.required' => 'Selecione pelo menos uma habilidade',
                'disciplinas.*.exists' => 'Uma das disciplinas selecionadas é inválida',
                'habilidades.*.exists' => 'Uma das habilidades selecionadas é inválida',
            ]);
    
            if ($validator->fails()) {
                return redirect()->back()
                                ->withErrors($validator)
                                ->withInput();
            }
    
            DB::beginTransaction();
    
            try {
                // Cria a atividade
                $atividade = Atividade::create([
                    'ano_id' => $request->ano_id,
                    'titulo' => $request->titulo,
                    'objetivo' => $request->objetivo,
                    'metodologia' => $request->metodologia,
                    'materiais' => $request->materiais,
                    'resultados_esperados' => $request->resultados_esperados,
                    'links_sugestoes' => $this->formatarLinks($request->links_sugestoes),
                ]);
    
                // Associa as disciplinas
                $atividade->disciplinas()->sync($request->disciplinas);
                
                // Associa as habilidades
                $atividade->habilidades()->sync($request->habilidades);
    
                DB::commit();
    
                return redirect()->route('atividades.index')
                               ->with('success', 'Atividade criada com sucesso!');
    
            } catch (\Exception $e) {
                DB::rollBack();
                
                return redirect()->back()
                               ->with('error', 'Erro ao criar atividade: ' . $e->getMessage())
                               ->withInput();
            }
        }
    
        public function update(Request $request, Atividade $atividade)
        {
            $validator = Validator::make($request->all(), [
                'ano_id' => 'required|exists:anos,id',
                'disciplinas' => 'required|array|min:1',
                'disciplinas.*' => 'exists:disciplinas,id',
                'habilidades' => 'required|array|min:1',
                'habilidades.*' => 'exists:habilidades,id',
                'titulo' => 'required|string|max:255',
                'objetivo' => 'required|string',
                'metodologia' => 'required|string',
                'materiais' => 'required|string',
                'resultados_esperados' => 'required|string',
                'links_sugestoes' => 'nullable|string',
            ], [
                'disciplinas.required' => 'Selecione pelo menos uma disciplina',
                'habilidades.required' => 'Selecione pelo menos uma habilidade',
                'disciplinas.*.exists' => 'Uma das disciplinas selecionadas é inválida',
                'habilidades.*.exists' => 'Uma das habilidades selecionadas é inválida',
            ]);
    
            if ($validator->fails()) {
                return redirect()->back()
                                ->withErrors($validator)
                                ->withInput();
            }
    
            DB::beginTransaction();
    
            try {
                // Atualiza a atividade
                $atividade->update([
                    'ano_id' => $request->ano_id,
                    'titulo' => $request->titulo,
                    'objetivo' => $request->objetivo,
                    'metodologia' => $request->metodologia,
                    'materiais' => $request->materiais,
                    'resultados_esperados' => $request->resultados_esperados,
                    'links_sugestoes' => $this->formatarLinks($request->links_sugestoes),
                ]);
    
                // Sincroniza as disciplinas
                $atividade->disciplinas()->sync($request->disciplinas);
                
                // Sincroniza as habilidades
                $atividade->habilidades()->sync($request->habilidades);
    
                DB::commit();
    
                return redirect()->route('atividades.index')
                               ->with('success', 'Atividade atualizada com sucesso!');
    
            } catch (\Exception $e) {
                DB::rollBack();
                
                return redirect()->back()
                               ->with('error', 'Erro ao atualizar atividade: ' . $e->getMessage())
                               ->withInput();
            }
        }
    
        /**
         * Formata os links removendo espaços e linhas vazias
         */
        private function formatarLinks($links)
        {
            if (empty($links)) {
                return null;
            }
    
            return collect(explode("\n", $links))
                ->map(fn($link) => trim($link))
                ->filter(fn($link) => !empty($link))
                ->join("\n");
        }
    
    public function show(Atividade $atividade)
    {
        return view('atividades.show', compact('atividade'));
    }

    public function edit(Atividade $atividade)
{
    // Carrega os dados necessários para os selects
    $disciplinas = Disciplina::all();
    $anos = Ano::all();
    $habilidades = Habilidade::all();

    return view('atividades.edit', compact('atividade', 'disciplinas', 'anos', 'habilidades'));
}

    public function destroy(Atividade $atividade)
    {
        $atividade->delete();
        return redirect()->route('atividades.index')->with('success', 'Atividade excluída com sucesso!');
    }
}