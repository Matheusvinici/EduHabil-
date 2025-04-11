<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RedeMunicipalExport implements WithMultipleSheets
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function sheets(): array
    {
        $sheets = [
            new ResumoGeralSheet($this->data),
            new DadosTRISheet($this->data),
            new EscolasSheet($this->data),
            new ProjecaoIDEBSheet($this->data)
        ];

        return $sheets;
    }
}

class ResumoGeralSheet implements FromCollection, WithTitle, WithHeadings, WithStyles
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        $resumo = [
            ['Total de Simulados', $this->data['totalSimulados']],
            ['Total de Professores', $this->data['totalProfessores']],
            ['Total de Alunos', $this->data['totalAlunos']],
            ['Alunos com Deficiência', $this->data['totalAlunosComDeficiencia']],
            ['Total de Respostas', $this->data['totalRespostas']],
            ['Alunos que Responderam', $this->data['alunosResponderam']],
            ['Média Theta (TRI)', number_format($this->data['mediaTRI'], 3)],
            ['Nota TRI Convertida', number_format($this->data['notaTRIConvertida'], 1)],
            ['Média 1º-5º Ano', number_format($this->data['mediaGeral1a5'], 2)],
            ['Média 6º-9º Ano', number_format($this->data['mediaGeral6a9'], 2)],
            ['Projeção IDEB 1º-5º', number_format($this->data['projecaoIDEB1a5'], 1)],
            ['Projeção IDEB 6º-9º', number_format($this->data['projecaoIDEB6a9'], 1)]
        ];

        return collect($resumo);
    }

    public function headings(): array
    {
        return ['Indicador', 'Valor'];
    }

    public function title(): string
    {
        return 'Resumo Geral';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
            'A:B' => ['alignment' => ['horizontal' => 'left']]
        ];
    }
}

class DadosTRISheet implements FromCollection, WithTitle, WithHeadings
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        $dadosTRI = [
            ['Muito Baixa (< -1.5)', $this->data['distribuicaoTheta']['muito_baixa']],
            ['Baixa (-1.5 a -0.5)', $this->data['distribuicaoTheta']['baixa']],
            ['Adequada (-0.5 a 1.0)', $this->data['distribuicaoTheta']['adequada']],
            ['Avançada (> 1.0)', $this->data['distribuicaoTheta']['avancada']]
        ];

        return collect($dadosTRI);
    }

    public function headings(): array
    {
        return ['Nível de Habilidade', 'Quantidade de Alunos'];
    }

    public function title(): string
    {
        return 'Distribuição TRI';
    }
}

class EscolasSheet implements FromCollection, WithTitle, WithHeadings
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return collect($this->data['estatisticasPorEscola'])->map(function($escola) {
            return [
                'Escola' => $escola['escola'],
                'Média TRI' => number_format($escola['media_tri'], 3),
                'Nota TRI' => $escola['nota_tri_convertida'],
                'Nota Peso' => $escola['media_ponderada'],
                'Nota Híbrida' => $escola['media_hibrida'],
                'Projeção IDEB' => $escola['projecao_ideb'],
                'Meta Atingida' => $escola['atingiu_meta'] ? 'Sim' : 'Não'
            ];
        });
    }

    public function headings(): array
    {
        return ['Escola', 'Média TRI', 'Nota TRI', 'Nota Peso', 'Nota Híbrida', 'Projeção IDEB', 'Meta Atingida'];
    }

    public function title(): string
    {
        return 'Escolas';
    }
}

class ProjecaoIDEBSheet implements FromCollection, WithTitle, WithHeadings
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        $projecoes = [
            ['Anos Iniciais (1º-5º)', 
             number_format($this->data['mediaGeral1a5'], 2),
             number_format($this->data['notaTRIConvertida'], 1),
             number_format($this->data['notaHibridaGeral1a5'], 1),
             number_format($this->data['projecaoIDEB1a5'], 1),
             $this->data['alertaMeta']['atingiu_meta'] ? 'Sim' : 'Não'
            ],
            ['Anos Finais (6º-9º)', 
             number_format($this->data['mediaGeral6a9'], 2),
             number_format($this->data['notaTRIConvertida'], 1),
             number_format($this->data['notaHibridaGeral6a9'], 1),
             number_format($this->data['projecaoIDEB6a9'], 1),
             $this->data['projecaoIDEB6a9'] >= 5.0 ? 'Sim' : 'Não'
            ]
        ];

        return collect($projecoes);
    }

    public function headings(): array
    {
        return ['Segmento', 'Média Theta', 'Nota TRI', 'Nota Híbrida', 'Projeção IDEB', 'Meta Atingida'];
    }

    public function title(): string
    {
        return 'Projeção IDEB';
    }
}