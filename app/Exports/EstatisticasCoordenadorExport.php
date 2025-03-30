<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithColumnWidths;

class EstatisticasCoordenadorExport implements FromArray, WithHeadings, WithTitle, WithStyles, WithColumnWidths
{
    protected $data;
    protected $tipo;

    public function __construct(array $data, $tipo = 'geral')
    {
        $this->data = $data;
        $this->tipo = $tipo;
    }

    public function array(): array
    {
        // Dados principais
        $exportData = [
            ['Relatório de Estatísticas - Coordenador'],
            ['Gerado em: ' . now()->format('d/m/Y H:i')],
            [''],
            ['Filtros Aplicados:'],
            ['Simulado: ' . ($this->data['request']->simulado_id ? $this->data['simulados']->firstWhere('id', $this->data['request']->simulado_id)?->nome : 'Todos')],
            ['Ano: ' . ($this->data['request']->ano_id ? $this->data['anos']->firstWhere('id', $this->data['request']->ano_id)?->nome : 'Todos')],
            ['Turma: ' . ($this->data['request']->turma_id ? $this->data['turmas']->firstWhere('id', $this->data['request']->turma_id)?->nome_turma : 'Todas')],
            ['Habilidade: ' . ($this->data['request']->habilidade_id ? $this->data['habilidades']->firstWhere('id', $this->data['request']->habilidade_id)?->descricao : 'Todas')],
            [''],
            ['Dados Gerais'],
            ['Total de Alunos', $this->data['totalAlunos']],
            ['Total de Professores', $this->data['totalProfessores']],
            ['Total de Respostas', $this->data['totalRespostas']],
            [''],
            ['Médias por Faixa de Ano'],
            ['Média 1º ao 5º Ano', number_format($this->data['media1a5'], 2)],
            ['Média 6º ao 9º Ano', number_format($this->data['media6a9'], 2)],
            [''],
            ['Média Geral da Escola', number_format($this->data['mediaGeralEscola'], 2)],
            [''],
        ];

        // Adiciona estatísticas por turma se aplicável
        if (!$this->data['request']->habilidade_id || $this->data['request']->turma_id) {
            $exportData[] = ['Estatísticas por Turma'];
            $exportData[] = ['Turma', 'Professor', 'Total Respostas', 'Acertos', '% Acertos', 'Média (0-10)'];
            
            foreach ($this->data['estatisticasPorTurma'] as $estatistica) {
                $exportData[] = [
                    $estatistica['turma'],
                    $estatistica['professor'] ?? 'N/A',
                    $estatistica['total_respostas'],
                    $estatistica['acertos'],
                    number_format($estatistica['porcentagem_acertos'], 2) . '%',
                    number_format($estatistica['media_final'], 2)
                ];
            }
            $exportData[] = [''];
        }

        // Adiciona estatísticas por habilidade se aplicável
        if ($this->data['request']->habilidade_id) {
            $exportData[] = ['Estatísticas por Habilidade'];
            $exportData[] = ['Habilidade', 'Total Respostas', 'Acertos', '% Acertos'];
            
            foreach ($this->data['estatisticasPorHabilidade'] as $estatistica) {
                $exportData[] = [
                    $estatistica['habilidade'],
                    $estatistica['total_respostas'],
                    $estatistica['acertos'],
                    number_format($estatistica['porcentagem_acertos'], 2) . '%'
                ];
            }
        }

        return $exportData;
    }

    public function headings(): array
    {
        return [
            ['Relatório de Estatísticas - Coordenador'],
            ['Gerado em: ' . now()->format('d/m/Y H:i')]
        ];
    }

    public function title(): string
    {
        return 'Estatísticas Coordenador';
    }

    public function styles(Worksheet $sheet)
    {
        // Estilo para os títulos
        $sheet->mergeCells('A1:F1');
        $sheet->mergeCells('A2:F2');
        $sheet->getStyle('A1:A2')->getFont()->setBold(true);
        $sheet->getStyle('A1:A2')->getAlignment()->setHorizontal('center');

        // Estilo para os cabeçalhos das tabelas
        $sheet->getStyle('A4:F4')->getFont()->setBold(true);
        
        $lastRow = $sheet->getHighestRow();
        
        // Aplicar bordas e estilos para as tabelas de dados
        $sheet->getStyle('A4:F' . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ]);

        // Estilo para os títulos das seções
        $sectionTitles = ['Dados Gerais', 'Médias por Faixa de Ano', 'Média Geral da Escola', 'Estatísticas por Turma', 'Estatísticas por Habilidade'];
        foreach ($sectionTitles as $title) {
            $row = $sheet->getCellByColumnAndRow(1, $sheet->getRowIterator()->current()->getRowIndex())->getRow();
            if ($sheet->getCell('A' . $row)->getValue() === $title) {
                $sheet->getStyle('A' . $row)->getFont()->setBold(true);
                $sheet->getStyle('A' . $row)->getFont()->setSize(12);
            }
        }

        // Auto ajustar largura das colunas
        foreach(range('A','F') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }
    }

    public function columnWidths(): array
    {
        return [
            'A' => 25,
            'B' => 25,
            'C' => 15,
            'D' => 15,
            'E' => 15,
            'F' => 15,
        ];
    }
}