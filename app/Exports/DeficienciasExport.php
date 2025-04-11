<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DeficienciasExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $estatisticas;
    protected $request;

    public function __construct($estatisticas, $request)
    {
        $this->estatisticas = $estatisticas;
        $this->request = $request;
    }

    public function collection()
    {
        return collect($this->estatisticas);
    }

    public function headings(): array
    {
        return [
            'Nome da Escola',
            'Nome do Aluno',
            'Deficiência',
            'Nota',
            'Acertos',
            '% Acertos',
            'Turma',
            'Desempenho'
        ];
    }

    public function map($estatistica): array
    {
        return [
            $estatistica['escola_nome'], // Certifique-se de que esta chave existe no seu array $estatisticas
            $estatistica['aluno_nome'],
            $estatistica['deficiencia'],
            number_format($estatistica['media'], 2), // 'Média' é agora 'Nota'
            $estatistica['acertos'] . '/' . $estatistica['total_questoes'],
            number_format($estatistica['porcentagem'], 2) . '%',
            $estatistica['turma'],
            $estatistica['desempenho']
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
            'A:H' => ['alignment' => ['horizontal' => 'center']],
        ];
    }
}