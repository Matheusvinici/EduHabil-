<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\DB;

class EstatisticasAnoEnsinoExport implements FromCollection, WithHeadings, WithMapping
{
    protected $simuladoId;
    protected $anoEnsinoId;

    public function __construct($simuladoId, $anoEnsinoId)
    {
        $this->simuladoId = $simuladoId;
        $this->anoEnsinoId = $anoEnsinoId;
    }

    public function collection()
    {
        $query = DB::table('users')
            ->join('anos', 'users.ano_id', '=', 'anos.id')
            ->join('respostas_simulados', 'users.id', '=', 'respostas_simulados.user_id')
            ->join('perguntas', 'respostas_simulados.pergunta_id', '=', 'perguntas.id')
            ->where('users.role', 'aluno')
            ->where('respostas_simulados.simulado_id', $this->simuladoId)
            ->select(
                'anos.nome as ano_ensino',
                DB::raw('COUNT(DISTINCT users.id) as total_alunos'),
                DB::raw('ROUND(SUM(respostas_simulados.correta * perguntas.peso) / SUM(perguntas.peso) * 10, 2) as media_ponderada'),
                DB::raw('LEAST(10, ROUND(SUM(respostas_simulados.correta * perguntas.peso) / SUM(perguntas.peso) * 10 * 1.2, 2)) as media_tri'),
                DB::raw('ROUND(SUM(respostas_simulados.correta * perguntas.peso) / SUM(perguntas.peso) * 10 * 0.6, 2) as projecao_ideb'),
                DB::raw('CASE WHEN LEAST(10, ROUND(SUM(respostas_simulados.correta * perguntas.peso) / SUM(perguntas.peso) * 10 * 1.2, 2)) >= 6.0 THEN "Sim" ELSE "Não" END as atingiu_meta')
            )
            ->groupBy('anos.id', 'anos.nome')
            ->orderBy('anos.nome');

        if ($this->anoEnsinoId) {
            $query->where('users.ano_id', $this->anoEnsinoId);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'Ano de Ensino',
            'Total de Alunos',
            'Média Ponderada',
            'Média TRI',
            'Projeção IDEB',
            'Meta Atingida'
        ];
    }

    public function map($item): array
    {
        return [
            $item->ano_ensino,
            $item->total_alunos,
            number_format($item->media_ponderada, 2, ',', ''),
            number_format($item->media_tri, 2, ',', ''),
            number_format($item->projecao_ideb, 2, ',', ''),
            $item->atingiu_meta
        ];
    }
}