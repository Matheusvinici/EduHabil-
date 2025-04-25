<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class RedeMunicipalExport implements WithMultipleSheets
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function sheets(): array
    {
        $sheets = [];
        
        // Página Resumo
        $sheets[] = new ResumoSheet($this->data);
        
        // Páginas por Quadrante
        $sheets[] = new QuadranteSheet($this->data, 'q1', 'Q1 - Alto Desempenho/Grande');
        $sheets[] = new QuadranteSheet($this->data, 'q2', 'Q2 - Baixo Desempenho/Grande');
        $sheets[] = new QuadranteSheet($this->data, 'q3', 'Q3 - Baixo Desempenho/Pequena');
        $sheets[] = new QuadranteSheet($this->data, 'q4', 'Q4 - Alto Desempenho/Pequena');
        
        return $sheets;
    }
}

class ResumoSheet implements FromArray, WithTitle, WithStyles, WithColumnWidths, WithEvents
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        $rows = [];
        
        // Cabeçalho
        $rows[] = ['RELATÓRIO DA REDE MUNICIPAL'];
        $rows[] = ['Simulado:', $this->data['simulados']->firstWhere('id', $this->data['request']->simulado_id)->nome ?? ''];
        $rows[] = ['Data:', now()->format('d/m/Y H:i')];
        $rows[] = [];
        
        // Dados Gerais
        $rows[] = ['DADOS GERAIS'];
        $rows[] = ['Total de Alunos', $this->data['totalAlunos']];
        $rows[] = ['Alunos Ativos', $this->data['alunosAtivos']];
        $rows[] = ['Alunos Responderam', $this->data['alunosResponderam']];
        $rows[] = ['Taxa de Participação', $this->data['totalAlunos'] > 0 ? round(($this->data['alunosResponderam'] / $this->data['totalAlunos']) * 100, 2) . '%' : '0%'];
        $rows[] = [];
        
        // Médias
        $rows[] = ['MÉDIAS DE DESEMPENHO'];
        $rows[] = ['', 'Tradicional', 'TRI'];
        $rows[] = ['Peso 1', $this->data['mediasPeso']['peso_1'], $this->data['analiseTRI']['peso_1']['media']];
        $rows[] = ['Peso 2', $this->data['mediasPeso']['peso_2'], $this->data['analiseTRI']['peso_2']['media']];
        $rows[] = ['Peso 3', $this->data['mediasPeso']['peso_3'], $this->data['analiseTRI']['peso_3']['media']];
        $rows[] = ['Geral', $this->data['mediasPeso']['media_geral'], $this->data['analiseTRI']['media_geral']];
        $rows[] = [];
        
        // Consistência
        $rows[] = ['Índice de Consistência (Alpha de Cronbach):', $this->data['analiseTRI']['indice_consistencia']];
        $rows[] = [];
        
        // Quadrantes Resumo
        $rows[] = ['QUADRANTES - RESUMO'];
        $rows[] = ['Quadrante', 'N° Escolas', 'Média TRI'];
        $rows[] = ['Q1 - Alto Desempenho/Grande', $this->data['quadrantes']['q1']['count'], $this->data['quadrantes']['q1']['media_tri']];
        $rows[] = ['Q2 - Baixo Desempenho/Grande', $this->data['quadrantes']['q2']['count'], $this->data['quadrantes']['q2']['media_tri']];
        $rows[] = ['Q3 - Baixo Desempenho/Pequena', $this->data['quadrantes']['q3']['count'], $this->data['quadrantes']['q3']['media_tri']];
        $rows[] = ['Q4 - Alto Desempenho/Pequena', $this->data['quadrantes']['q4']['count'], $this->data['quadrantes']['q4']['media_tri']];
        $rows[] = [];
        
        // Segmentos
        $rows[] = ['MÉDIAS POR SEGMENTO'];
        $rows[] = ['Segmento', 'Média Tradicional', 'Média TRI', 'Projeção TRI', 'Meta', 'Status'];
        
        $status1a5 = $this->data['projecaoSegmento']['1a5']['atingiu_meta'] ? '✅ Atingiu' : '❌ Não Atingiu';
        $rows[] = [
            '1º ao 5º Ano',
            $this->data['projecaoSegmento']['1a5']['media'],
            $this->data['projecaoSegmento']['1a5']['media_tri'],
            $this->data['projecaoSegmento']['1a5']['projecao'],
            '6.0',
            $status1a5
        ];
        
        $status6a9 = $this->data['projecaoSegmento']['6a9']['atingiu_meta'] ? '✅ Atingiu' : '❌ Não Atingiu';
        $rows[] = [
            '6º ao 9º Ano',
            $this->data['projecaoSegmento']['6a9']['media'],
            $this->data['projecaoSegmento']['6a9']['media_tri'],
            $this->data['projecaoSegmento']['6a9']['projecao'],
            '5.0',
            $status6a9
        ];

        return $rows;
    }

    public function title(): string
    {
        return 'Resumo';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 16]],
            5 => ['font' => ['bold' => true, 'size' => 14]],
            12 => ['font' => ['bold' => true, 'size' => 14]],
            19 => ['font' => ['bold' => true, 'size' => 14]],
            20 => ['font' => ['bold' => true]],
            21 => ['font' => ['bold' => true]],
            22 => ['font' => ['bold' => true]],
            23 => ['font' => ['bold' => true]],
            25 => ['font' => ['bold' => true, 'size' => 14]],
            26 => ['font' => ['bold' => true]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 35,
            'B' => 20,
            'C' => 20,
            'D' => 15,
            'E' => 15,
            'F' => 15,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // Aplicar bordas às tabelas
                $this->applyBorders($event);
                
                // Centralizar cabeçalhos
                $event->sheet->getStyle('A1:F1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $event->sheet->getStyle('A5:F5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $event->sheet->getStyle('A12:F12')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $event->sheet->getStyle('A19:F19')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $event->sheet->getStyle('A25:F25')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            },
        ];
    }

    protected function applyBorders(AfterSheet $event)
    {
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ];

        // Aplicar bordas às tabelas
        $event->sheet->getStyle('A5:F9')->applyFromArray($styleArray); // Dados Gerais
        $event->sheet->getStyle('A12:F16')->applyFromArray($styleArray); // Médias
        $event->sheet->getStyle('A19:F23')->applyFromArray($styleArray); // Quadrantes
        $event->sheet->getStyle('A25:F28')->applyFromArray($styleArray); // Segmentos
    }
}

class QuadranteSheet implements FromArray, WithTitle, WithStyles, WithColumnWidths, WithEvents
{
    protected $data;
    protected $quadrante;
    protected $titulo;

    public function __construct($data, $quadrante, $titulo)
    {
        $this->data = $data;
        $this->quadrante = $quadrante;
        $this->titulo = $titulo;
    }

    public function array(): array
    {
        $rows = [];
        
        // Cabeçalho
        $rows[] = [$this->titulo];
        $rows[] = ['Total de Escolas:', $this->data['quadrantes'][$this->quadrante]['count'] ?? 0];
        $rows[] = ['Média TRI:', $this->data['quadrantes'][$this->quadrante]['media_tri'] ?? 0];
        $rows[] = ['Média Geral TRI:', $this->data['mediaGeralTRI'] ?? 0];
        $rows[] = [];
        
        // Descrição do Quadrante
        $descricao = [
            'q1' => 'Escolas com grande quantidade de matrículas (200+) e desempenho TRI acima da média',
            'q2' => 'Escolas com grande quantidade de matrículas (200+) e desempenho TRI abaixo da média',
            'q3' => 'Escolas com menor quantidade de matrículas (<200) e desempenho TRI abaixo da média',
            'q4' => 'Escolas com menor quantidade de matrículas (<200) e desempenho TRI acima da média',
        ];
        
        $rows[] = ['Descrição:', $descricao[$this->quadrante] ?? ''];
        $rows[] = [];
        
        // Cabeçalho da tabela
        $rows[] = ['Escola', 'Total Alunos', 'Média Tradicional', 'Média TRI', 'Diferença p/ Média'];
        
        // Dados das escolas
        if (isset($this->data['quadrantes'][$this->quadrante]['escolas']) && is_array($this->data['quadrantes'][$this->quadrante]['escolas'])) {
            foreach ($this->data['quadrantes'][$this->quadrante]['escolas'] as $escola) {
                if (is_array($escola)) {
                    $diferenca = ($escola['media_tri'] ?? 0) - ($this->data['mediaGeralTRI'] ?? 0);
                    $rows[] = [
                        $escola['nome'] ?? 'N/A',
                        $escola['total_alunos'] ?? 0,
                        number_format($escola['media_simulado'] ?? 0, 2),
                        number_format($escola['media_tri'] ?? 0, 2),
                        number_format($diferenca, 2)
                    ];
                }
            }
        }
        
        return $rows;
    }

    public function title(): string
    {
        return substr($this->titulo, 0, 31); // Limita a 31 caracteres (limite do Excel)
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 16]],
            7 => ['font' => ['bold' => true]],
            'A7:E7' => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFD3D3D3']
                ]
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 40, // Coluna mais larga para nomes das escolas
            'B' => 15,
            'C' => 20,
            'D' => 15,
            'E' => 20,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // Aplicar bordas a toda a tabela de escolas
                $lastRow = $event->sheet->getHighestRow();
                
                $styleArray = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => 'FF000000'],
                        ],
                    ],
                    'alignment' => [
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ];
                
                // Aplicar bordas da linha 7 até a última linha
                $event->sheet->getStyle('A7:E'.$lastRow)->applyFromArray($styleArray);
                
                // Formatar números com 2 casas decimais
                $event->sheet->getStyle('C8:E'.$lastRow)->getNumberFormat()->setFormatCode('0.00');
                
                // Centralizar cabeçalho
                $event->sheet->getStyle('A1:E1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                
                // Destacar diferença positiva/negativa
                $this->applyConditionalFormatting($event);
            },
        ];
    }

    protected function applyConditionalFormatting(AfterSheet $event)
{
    $lastRow = $event->sheet->getHighestRow();
    
    // Create conditional styles
    $conditionalStyles = [];
    
    // Green for positive differences
    $conditionalStyles[] = (new \PhpOffice\PhpSpreadsheet\Style\Conditional())
        ->setConditionType(\PhpOffice\PhpSpreadsheet\Style\Conditional::CONDITION_CELLIS)
        ->setOperatorType(\PhpOffice\PhpSpreadsheet\Style\Conditional::OPERATOR_GREATERTHAN)
        ->addCondition(0)
        ->setStyle(
            (new \PhpOffice\PhpSpreadsheet\Style\Style())
                ->applyFromArray([
                    'font' => ['color' => ['argb' => 'FF006400']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF90EE90']]
                ])
        );
    
    // Red for negative differences
    $conditionalStyles[] = (new \PhpOffice\PhpSpreadsheet\Style\Conditional())
        ->setConditionType(\PhpOffice\PhpSpreadsheet\Style\Conditional::CONDITION_CELLIS)
        ->setOperatorType(\PhpOffice\PhpSpreadsheet\Style\Conditional::OPERATOR_LESSTHAN)
        ->addCondition(0)
        ->setStyle(
            (new \PhpOffice\PhpSpreadsheet\Style\Style())
                ->applyFromArray([
                    'font' => ['color' => ['argb' => 'FF8B0000']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFFFA07A']]
                ])
        );
    
    // Apply all conditional styles at once
    $event->sheet->getStyle('E8:E'.$lastRow)
        ->setConditionalStyles($conditionalStyles);
}
}