<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class QuestoesExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return collect($this->data['estatisticasPorQuestao']);
    }

    public function headings(): array
    {
        return [
            'Disciplina',
            'Questão (resumo)',
            'Peso',
            'Habilidade',
            'Total Respostas',
            'Acertos',
            '% Acerto',
            'Média Ponderada',
            'TRI Médio',
            'Parâmetro TRI (a)',
            'Parâmetro TRI (b)',
            'Parâmetro TRI (c)'
        ];
    }

    public function map($questao): array
    {
        return [
            $questao->disciplina,
            strip_tags($questao->enunciado),
            $questao->peso,
            $questao->habilidade,
            $questao->total_respostas,
            $questao->acertos,
            $questao->percentual_acerto,
            $questao->media_ponderada,
            $questao->tri_medio,
            $questao->tri_a,
            $questao->tri_b,
            $questao->tri_c
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Estilo para o cabeçalho
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFD9D9D9']
                ]
            ],
            
            // Auto size para todas as colunas
            'A:L' => [
                'autoSize' => true
            ]
        ];
    }
}