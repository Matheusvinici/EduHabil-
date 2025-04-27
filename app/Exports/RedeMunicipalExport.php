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
                $styleArray = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => 'FF000000'],
                        ],
                    ],
                ];

                $event->sheet->getStyle('A5:B9')->applyFromArray($styleArray);
                $event->sheet->getStyle('A12:C16')->applyFromArray($styleArray);
                $event->sheet->getStyle('A19:C23')->applyFromArray($styleArray);
                $event->sheet->getStyle('A25:F28')->applyFromArray($styleArray);
                
                $event->sheet->getStyle('A1:F1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $event->sheet->getStyle('A5:F5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $event->sheet->getStyle('A12:F12')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $event->sheet->getStyle('A19:F19')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $event->sheet->getStyle('A25:F25')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            },
        ];
    }
}

class QuadranteSheet implements FromArray, WithTitle, WithStyles, WithColumnWidths
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
        $rows[] = ['Média Geral TRI: ' . number_format($this->data['mediaGeralTRI'], 2)];
        $rows[] = []; // Linha vazia
        
        // Cabeçalho da tabela
        $rows[] = [
            'Escola', 
            'Total Alunos', 
            'Alunos Respondentes', 
            'Média Tradicional', 
            'Média TRI', 
            'Diferença para Média Geral'
        ];

        // Filtrar escolas do quadrante
        $escolasQuadrante = collect($this->data['dadosEscolas'])->filter(function($escola) {
            // Aplicar mesma lógica de filtro dos quadrantes
            $grande = $escola['total_alunos'] >= 200;
            $acimaMedia = $escola['media_tri'] >= $this->data['mediaGeralTRI'];
            
            switch($this->quadrante) {
                case 'q1': return $grande && $acimaMedia;
                case 'q2': return $grande && !$acimaMedia;
                case 'q3': return !$grande && !$acimaMedia;
                case 'q4': return !$grande && $acimaMedia;
                default: return false;
            }
        });

        // Adicionar dados das escolas
        foreach ($escolasQuadrante as $escola) {
            $rows[] = [
                $escola['nome'],
                $escola['total_alunos'],
                $escola['alunos_respondentes'],
                number_format($escola['media_simulado'], 2),
                number_format($escola['media_tri'], 2),
                number_format($escola['media_tri'] - $this->data['mediaGeralTRI'], 2)
            ];
        }

        return $rows;
    }
    public function title(): string
    {
        return $this->titulo;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14]], // Título da página
            3 => ['font' => ['bold' => true]], // Cabeçalho
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 40,
            'B' => 15,
            'C' => 20,
            'D' => 20,
            'E' => 20,
        ];
    }
}