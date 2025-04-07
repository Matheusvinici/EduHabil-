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

        // Estatísticas por Turma
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

        // Estatísticas por Habilidade
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
            $exportData[] = [''];
        }

        // Desempenho por Aluno
        if (!empty($this->data['estatisticasPorAluno'])) {
            $exportData[] = ['Desempenho por Aluno'];
            $exportData[] = ['Turma', 'Nome do Aluno', 'Aplicador', 'Total Respostas', 'Acertos', '% Acertos', 'Média'];

            foreach ($this->data['estatisticasPorAluno'] as $aluno) {
                $exportData[] = [
                    $aluno['nome_turma'] ?? 'N/A',
                    $aluno['nome_aluno'] ?? 'N/A',
                    $aluno['nome_aplicador'] ?? 'N/A',
                    $aluno['total_respostas'] ?? 0,
                    $aluno['acertos'] ?? 0,
                    isset($aluno['porcentagem_acertos']) ? number_format($aluno['porcentagem_acertos'], 2) . '%' : '0.00%',
                    isset($aluno['media']) ? number_format($aluno['media'], 2) : '0.00'
                ];
            }
        }

        return $exportData;
    }

    public function headings(): array
    {
        return [
            ['Relatório de Estatísticas - Coordenador'],
            ['Gerado em: ' . now()->format('d/m/Y H:i')],
        ];
    }

    public function title(): string
    {
        return 'Estatísticas Coordenador';
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->mergeCells('A1:G1');
        $sheet->mergeCells('A2:G2');
        $sheet->getStyle('A1:A2')->getFont()->setBold(true);
        $sheet->getStyle('A1:A2')->getAlignment()->setHorizontal('center');

        $lastRow = $sheet->getHighestRow();
        $lastCol = $sheet->getHighestColumn();

        // Aplica bordas a toda a área de dados
        $sheet->getStyle('A4:' . $lastCol . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ]);

        // Negrito para cabeçalhos de tabelas
        for ($i = 1; $i <= $lastRow; $i++) {
            $value = $sheet->getCell("A{$i}")->getValue();
            if (in_array($value, [
                'Dados Gerais',
                'Médias por Faixa de Ano',
                'Média Geral da Escola',
                'Estatísticas por Turma',
                'Estatísticas por Habilidade',
                'Desempenho por Aluno'
            ])) {
                $sheet->getStyle("A{$i}")->getFont()->setBold(true)->setSize(12);
            }
        }

        // Auto size
        foreach (range('A', $lastCol) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }

    public function columnWidths(): array
    {
        return [
            'A' => 25,
            'B' => 25,
            'C' => 20,
            'D' => 15,
            'E' => 15,
            'F' => 15,
            'G' => 15,
        ];
    }
}
