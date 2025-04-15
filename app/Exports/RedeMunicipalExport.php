<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RedeMunicipalExport implements FromCollection, WithHeadings, WithStyles, WithTitle, WithColumnWidths, WithEvents
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        $rows = [];
        
        // Cabeçalho
        $rows[] = ['RELATÓRIO DA REDE MUNICIPAL'];
        $rows[] = ['Data:', now()->format('d/m/Y H:i:s')];
        $rows[] = ['Simulado:', $this->data['simuladoSelecionado']->nome ?? 'N/A'];
        $rows[] = [];
        
        // Dados Gerais
        $rows[] = ['DADOS GERAIS'];
        $rows[] = ['Total de Alunos', $this->data['totalAlunos']];
        $rows[] = ['Alunos Responderam', $this->data['alunosResponderam']];
        $rows[] = ['Taxa de Participação', $this->data['alunosAtivos'] > 0 
            ? round(($this->data['alunosResponderam']/$this->data['alunosAtivos'])*100, 2).'%' 
            : '0%'];
        $rows[] = [];
        
        // Médias Ponderadas
        $rows[] = ['MÉDIAS PONDERADAS'];
        $rows[] = ['Peso 1', $this->data['mediasPeso']['peso_1']];
        $rows[] = ['Peso 2', $this->data['mediasPeso']['peso_2']];
        $rows[] = ['Peso 3', $this->data['mediasPeso']['peso_3']];
        $rows[] = ['Média Geral', $this->data['mediasPeso']['geral']];
        $rows[] = [];
        
        // Projeção TRI
        $rows[] = ['PROJEÇÃO TRI'];
        $rows[] = ['Peso 1', $this->data['projecaoTRI']['peso_1']];
        $rows[] = ['Peso 2', $this->data['projecaoTRI']['peso_2']];
        $rows[] = ['Peso 3', $this->data['projecaoTRI']['peso_3']];
        $rows[] = ['Média Geral TRI', $this->data['projecaoTRI']['geral']];
        $rows[] = [];
        
        // Escolas
        if (!empty($this->data['escolas'])) {
            $rows[] = ['DESEMPENHO POR ESCOLA'];
            $rows[] = ['Escola', 'Alunos', 'Respondentes', 'Participação', 'Média', 'TRI', 'Meta'];
            
            foreach ($this->data['escolas'] as $escola) {
                $rows[] = [
                    $escola['nome'],
                    $escola['alunos_ativos'],
                    $escola['alunos_responderam'],
                    $escola['alunos_ativos'] > 0 
                        ? round(($escola['alunos_responderam']/$escola['alunos_ativos'])*100, 2).'%' 
                        : '0%',
                    $escola['media_ponderada'],
                    $escola['projecao_tri'],
                    $escola['atingiu_meta'] ? 'Sim' : 'Não'
                ];
            }
            $rows[] = [];
        }
        
        // Alunos
        if (!empty($this->data['alunos'])) {
            $rows[] = ['DESEMPENHO POR ALUNO'];
            $rows[] = ['Aluno', 'Acertos', 'Total', 'Percentual', 'TRI'];
            
            foreach ($this->data['alunos'] as $aluno) {
                $rows[] = [
                    $aluno['nome'],
                    $aluno['acertos'],
                    $aluno['total'],
                    $aluno['porcentagem'].'%',
                    $aluno['tri']
                ];
            }
        }

        return collect($rows);
    }

    public function headings(): array
    {
        return [];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Estilo para títulos principais
            'A1' => ['font' => ['bold' => true, 'size' => 16]],
            
            // Estilo para seções
            'A5' => ['font' => ['bold' => true, 'size' => 14]],
            'A11' => ['font' => ['bold' => true, 'size' => 14]],
            'A17' => ['font' => ['bold' => true, 'size' => 14]],
            'A24' => ['font' => ['bold' => true, 'size' => 14]],
            
            // Estilo para cabeçalhos de tabela
            'A6:B6' => ['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'color' => ['rgb' => 'D9E1F2']]],
            'A12:B12' => ['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'color' => ['rgb' => 'D9E1F2']]],
            'A18:B18' => ['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'color' => ['rgb' => 'D9E1F2']]],
            'A25:G25' => ['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'color' => ['rgb' => 'D9E1F2']]],
            'A32:E32' => ['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'color' => ['rgb' => 'D9E1F2']]],
        ];
    }

    public function title(): string
    {
        return 'Relatório Rede';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 30,
            'B' => 15,
            'C' => 15,
            'D' => 15,
            'E' => 15,
            'F' => 15,
            'G' => 10,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // Mesclar células para títulos
                $event->sheet->mergeCells('A1:G1');
                $event->sheet->mergeCells('A5:G5');
                $event->sheet->mergeCells('A11:G11');
                $event->sheet->mergeCells('A17:G17');
                $event->sheet->mergeCells('A24:G24');
                $event->sheet->mergeCells('A31:G31');
                
                // Alinhamento central para títulos
                $event->sheet->getStyle('A1:G1')->getAlignment()->setHorizontal('center');
                $event->sheet->getStyle('A5:G5')->getAlignment()->setHorizontal('center');
                $event->sheet->getStyle('A11:G11')->getAlignment()->setHorizontal('center');
                $event->sheet->getStyle('A17:G17')->getAlignment()->setHorizontal('center');
                $event->sheet->getStyle('A24:G24')->getAlignment()->setHorizontal('center');
                $event->sheet->getStyle('A31:G31')->getAlignment()->setHorizontal('center');
                
                // Bordas para tabelas
                $event->sheet->getStyle('A6:B9')->applyFromArray([
                    'borders' => [
                        'allBorders' => ['borderStyle' => 'thin']
                    ]
                ]);
                
                $event->sheet->getStyle('A12:B15')->applyFromArray([
                    'borders' => [
                        'allBorders' => ['borderStyle' => 'thin']
                    ]
                ]);
                
                $event->sheet->getStyle('A18:B21')->applyFromArray([
                    'borders' => [
                        'allBorders' => ['borderStyle' => 'thin']
                    ]
                ]);
                
                $event->sheet->getStyle('A25:G'.(24 + count($this->data['escolas'])))->applyFromArray([
                    'borders' => [
                        'allBorders' => ['borderStyle' => 'thin']
                    ]
                ]);
                
                if (!empty($this->data['alunos'])) {
                    $event->sheet->getStyle('A32:E'.(32 + count($this->data['alunos'])))->applyFromArray([
                        'borders' => [
                            'allBorders' => ['borderStyle' => 'thin']
                        ]
                    ]);
                }
            },
        ];
    }
}