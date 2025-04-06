<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithColumnWidths;

class RespostasSimuladoExport implements FromArray, WithHeadings, WithTitle, WithStyles, WithColumnWidths
{
    protected $data;
    protected $turmaSelecionada;
    protected $filtros;

    public function __construct(array $data, $turmaSelecionada, $filtros)
    {
        $this->data = $data;
        $this->turmaSelecionada = $turmaSelecionada;
        $this->filtros = $filtros;
    }

    public function array(): array
    {
        $exportData = [
            ['Relatório de Desempenho - Professor'],
            ['Gerado em: ' . now()->format('d/m/Y H:i')],
            [''],
            ['Filtros Aplicados:'],
            ['Turma: ' . ($this->turmaSelecionada->nome_turma ?? 'Todas')],
            ['Habilidade: ' . ($this->filtros['habilidade_id'] ?? 'Todas')],
            [''],
            ['Dados Gerais'],
            ['Total de Alunos', $this->data['totalAlunosTurma'] ?? 0],
            ['Alunos Responderam', count($this->data['estatisticas'] ?? [])],
            ['Alunos Não Responderam', count($this->data['alunosSemResposta'] ?? [])],
            ['Alunos com Deficiência', $this->data['alunosComDeficiencia'] ?? 0],
            [''],
            ['Resultados Detalhados'],
            ['Aluno', 'Turma', 'Simulado', 'Total Questões', 'Acertos', '% Acertos', 'Média (0-10)', 'Deficiência', 'Data']
        ];

        // Adiciona os dados dos alunos
        foreach ($this->data['estatisticas'] as $estatistica) {
            $exportData[] = [
                $estatistica['aluno'] ?? 'N/A',
                $estatistica['turma'] ?? 'N/A',
                $estatistica['simulado'] ?? 'N/A',
                $estatistica['total_questoes'] ?? 0,
                $estatistica['acertos'] ?? 0,
                number_format($estatistica['porcentagem'] ?? 0, 2) . '%',
                number_format($estatistica['media'] ?? 0, 2),
                ($estatistica['deficiencia'] ?? false) ? 'Sim' : 'Não',
                isset($estatistica['data']) ? (is_string($estatistica['data']) ? $estatistica['data'] : $estatistica['data']->format('d/m/Y')) : 'N/A'
            ];
        }

        // Adiciona alunos sem resposta se houver
        if (!empty($this->data['alunosSemResposta'])) {
            $exportData[] = [''];
            $exportData[] = ['Alunos que ainda não responderam'];
            $exportData[] = ['Aluno', 'Turma', 'Deficiência'];

            foreach ($this->data['alunosSemResposta'] as $aluno) {
                $exportData[] = [
                    $aluno->name ?? 'N/A',
                    $this->turmaSelecionada->nome_turma ?? 'N/A',
                    ($aluno->deficiencia ?? false) ? 'Sim' : 'Não'
                ];
            }
        }

        return $exportData;
    }

    public function headings(): array
    {
        return [
            ['Relatório de Desempenho - Professor'],
            ['Gerado em: ' . now()->format('d/m/Y H:i')]
        ];
    }

    public function title(): string
    {
        return 'Desempenho da Turma';
    }

    public function styles(Worksheet $sheet)
    {
        // Estilo para os títulos
        $sheet->mergeCells('A1:I1');
        $sheet->mergeCells('A2:I2');
        $sheet->getStyle('A1:A2')->getFont()->setBold(true);
        $sheet->getStyle('A1:A2')->getAlignment()->setHorizontal('center');

        // Estilo para os cabeçalhos das tabelas
        $sheet->getStyle('A4:I4')->getFont()->setBold(true);
        $sheet->getStyle('A15:I15')->getFont()->setBold(true);
        
        if (!empty($this->data['alunosSemResposta'])) {
            $alunosSemRespostaRow = 15 + count($this->data['estatisticas']) + 3;
            $sheet->getStyle('A'.$alunosSemRespostaRow.':C'.$alunosSemRespostaRow)->getFont()->setBold(true);
        }

        // Aplicar bordas para todas as tabelas
        $lastRow = $sheet->getHighestRow();
        $sheet->getStyle('A4:I'.$lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ]);

        // Estilo para os títulos das seções
        $sectionTitles = ['Dados Gerais', 'Resultados Detalhados', 'Alunos que ainda não responderam'];
        foreach ($sectionTitles as $title) {
            for ($row = 1; $row <= $lastRow; $row++) {
                if ($sheet->getCell('A' . $row)->getValue() === $title) {
                    $sheet->getStyle('A' . $row)->getFont()->setBold(true);
                    $sheet->getStyle('A' . $row)->getFont()->setSize(12);
                    $sheet->getStyle('A' . $row)->getFont()->getColor()->setARGB('FF0066CC');
                }
            }
        }
    }

    public function columnWidths(): array
    {
        return [
            'A' => 25, // Aluno
            'B' => 15, // Turma
            'C' => 25, // Simulado
            'D' => 12, // Total Questões
            'E' => 10, // Acertos
            'F' => 12, // % Acertos
            'G' => 12, // Média
            'H' => 12, // Deficiência
            'I' => 15  // Data
        ];
    }
}