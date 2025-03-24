<?php
namespace App\Http\Controllers;

use App\Models\Simulado;
use App\Models\Pergunta;
use App\Models\Ano;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class SimuladoController extends Controller
{
    // Exibir todos os simulados
    public function index()
    {
        $simulados = Simulado::all();
        return view('simulados.index', compact('simulados'));
    }

    // Exibir formulário de criação
    public function create()
    {
        $perguntas = Pergunta::all();
        $anos = Ano::all();

        return view('simulados.create', compact('perguntas', 'anos'));
    }

    // Armazenar novo simulado
    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'perguntas' => 'required|string', 
            'ano_id' => 'required|string',
        ]);
    
        $perguntasIds = explode(',', $request->perguntas);
    
        $simulado = Simulado::create([
            'nome' => $request->nome,
            'descricao' => $request->descricao,
            'ano_id' => $request->ano_id,
        ]);
    
        $simulado->perguntas()->attach($perguntasIds);
    
        return redirect()->route('simulados.index')->with('success', 'Simulado criado com sucesso!');
    }

    // Exibir detalhes de um simulado
    public function show(Simulado $simulado)
    {
        return view('simulados.show', compact('simulado'));
    }

    // Exibir formulário de edição
    public function edit(Simulado $simulado)
    {
        $perguntas = Pergunta::all();
        return view('simulados.edit', compact('simulado', 'perguntas'));
    }

    // Atualizar simulado
    public function update(Request $request, Simulado $simulado)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'perguntas' => 'required|array',
            'perguntas.*' => 'exists:perguntas,id',
        ]);

        $simulado->update([
            'nome' => $request->nome,
            'descricao' => $request->descricao,
        ]);

        $simulado->perguntas()->sync($request->perguntas);

        return redirect()->route('simulados.index')->with('success', 'Simulado atualizado com sucesso!');
    }

    // Excluir simulado
    public function destroy(Simulado $simulado)
    {
        $simulado->perguntas()->detach();
        $simulado->delete();
        return redirect()->route('simulados.index')->with('success', 'Simulado excluído com sucesso!');
    }

    // Gerar PDF padrão
    public function gerarPdf(Simulado $simulado)
    {
        $simulado->load('perguntas');
        $pdf = Pdf::loadView('simulados.pdf', compact('simulado'));
        return $pdf->download('simulado_' . $simulado->id . '.pdf');
    }

    // Teste de conversão para Braille
    public function testBrailleConversion()
    {
        $texto = "Teste de conversão para Braille: áéíóú 123 ABC";
        $braille = $this->converterParaBraille($texto);
        
        return response()->json([
            'original' => $texto,
            'braille' => $braille,
            'is_utf8' => mb_check_encoding($braille, 'UTF-8'),
            'html' => view('simulados.pdf-braille', [
                'simulado' => (object)[
                    'nome' => 'Teste', 
                    'descricao' => $texto, 
                    'perguntas' => []
                ],
                'converterParaBraille' => [$this, 'converterParaBraille']
            ])->render()
        ]);
    }
    public function gerarPdfBraille(Simulado $simulado)
{
    $simulado->load('perguntas');
    
    // Configurações otimizadas para imagens e texto
    $options = [
        'isRemoteEnabled' => true,
        'isHtml5ParserEnabled' => true,
        'defaultFont' => 'DejaVu Sans',
        'isPhpEnabled' => true,
        'dpi' => 150,
        'margin_top' => 10,
        'margin_bottom' => 10,
        'margin_left' => 15,
        'margin_right' => 15,
        'enable_php' => true,
    ];
    
    // Verifica e ajusta caminho das imagens
    foreach ($simulado->perguntas as $pergunta) {
        if ($pergunta->imagem) {
            $pergunta->imagem_path = storage_path('app/public/' . $pergunta->imagem);
        }
    }
    
    $pdf = Pdf::setOptions($options)
        ->loadView('simulados.pdf-braille', [
            'simulado' => $simulado,
            'converterParaBraille' => [$this, 'converterParaBraille']
        ])
        ->setPaper('a4', 'portrait');
    
    return $pdf->download('simulado_braille_'.$simulado->id.'.pdf');
}
    
public function converterParaBraille($texto)
{
    $mapa = [
        // Letras minúsculas
        'a' => '⠁', 'b' => '⠃', 'c' => '⠉', 'd' => '⠙', 'e' => '⠑',
        'f' => '⠋', 'g' => '⠛', 'h' => '⠓', 'i' => '⠊', 'j' => '⠚',
        'k' => '⠅', 'l' => '⠇', 'm' => '⠍', 'n' => '⠝', 'o' => '⠕',
        'p' => '⠏', 'q' => '⠟', 'r' => '⠗', 's' => '⠎', 't' => '⠞',
        'u' => '⠥', 'v' => '⠧', 'w' => '⠺', 'x' => '⠭', 'y' => '⠽',
        'z' => '⠵',
        
        // Números (precedidos por ⠼)
        '0' => '⠚', '1' => '⠁', '2' => '⠃', '3' => '⠉', '4' => '⠙',
        '5' => '⠑', '6' => '⠋', '7' => '⠛', '8' => '⠓', '9' => '⠊',
        
        // Pontuação
        '.' => '⠲', ',' => '⠂', ';' => '⠆', ':' => '⠒', 
        '!' => '⠖', '?' => '⠦', '(' => '⠶', ')' => '⠶', 
        '-' => '⠤', '_' => '⠔', '"' => '⠐⠶', "'" => '⠄',
        
        // Espaço e quebras
        ' ' => '⠀', "\n" => '<br>',
        
        // Acentuação
        'á' => '⠷', 'à' => '⠷', 'â' => '⠩', 'ã' => '⠡',
        'é' => '⠿', 'ê' => '⠮', 'í' => '⠽', 'ó' => '⠾',
        'ô' => '⠬', 'õ' => '⠣', 'ú' => '⠳', 'ç' => '⠯',
        
        // Maiúsculas (precedidas por ⠠)
        'A' => '⠠⠁', 'B' => '⠠⠃', 'C' => '⠠⠉', 'D' => '⠠⠙',
        'E' => '⠠⠑', 'F' => '⠠⠋', 'G' => '⠠⠛', 'H' => '⠠⠓',
        'I' => '⠠⠊', 'J' => '⠠⠚', 'K' => '⠠⠅', 'L' => '⠠⠇',
        'M' => '⠠⠍', 'N' => '⠠⠝', 'O' => '⠠⠕', 'P' => '⠠⠏',
        'Q' => '⠠⠟', 'R' => '⠠⠗', 'S' => '⠠⠎', 'T' => '⠠⠞',
        'U' => '⠠⠥', 'V' => '⠠⠧', 'W' => '⠠⠺', 'X' => '⠠⠭',
        'Y' => '⠠⠽', 'Z' => '⠠⠵'
    ];
    
    $resultado = '';
    $emNumero = false;
    
    foreach (mb_str_split($texto) as $char) {
        // Verifica se é um dígito
        if (is_numeric($char)) {
            if (!$emNumero) {
                $resultado .= '⠼'; // Sinal de início de número
                $emNumero = true;
            }
            $resultado .= $mapa[$char] ?? '⠿';
        } else {
            if ($emNumero && $char != ' ' && $char != '.') {
                $resultado .= '⠰'; // Sinal de fim de número (exceto para espaço e ponto)
                $emNumero = false;
            }
            $resultado .= $mapa[$char] ?? '⠿';
        }
    }
    
    return $resultado;
}
            // Gerar PDF com fonte ampliada para baixa visão
        public function gerarPdfBaixaVisao(Simulado $simulado)
        {
            $simulado->load('perguntas');
            $pdf = Pdf::loadView('simulados.pdf-baixa-visao', compact('simulado'));
            return $pdf->download('simulado_' . $simulado->id . '.pdf');
        }
}