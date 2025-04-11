<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class HabilidadesExport implements FromCollection, WithHeadings, WithMapping
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return collect($this->data['estatisticasPorHabilidade']);
    }

    public function headings(): array
    {
        return [
            'Código',
            'Habilidade',
            'Disciplina',
            'Total Questões',
            'Total Respostas',
            'Acertos',
            'Média Simples',
            'Média Ponderada',
            '% Acerto',
            'TRI Médio'
        ];
    }

    public function map($habilidade): array
    {
        return [
            $habilidade->codigo,
            $habilidade->descricao,
            $habilidade->disciplina,
            $habilidade->total_questoes,
            $habilidade->total_respostas,
            $habilidade->acertos,
            $habilidade->media_simples,
            $habilidade->media_ponderada,
            $habilidade->percentual_acerto,
            $habilidade->tri_medio
        ];
    }
}