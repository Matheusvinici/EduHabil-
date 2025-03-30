<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class EstatisticasProfessorExport implements WithMultipleSheets
{
    protected $data;
    
    public function __construct(array $data)
    {
        $this->data = $data;
    }
    
    public function sheets(): array
    {
        return [
            new EstatisticasAlunosSheet($this->data),
            new EstatisticasSimuladosSheet($this->data),
            new EstatisticasHabilidadesSheet($this->data),
        ];
    }
}

class EstatisticasAlunosSheet implements FromArray, WithTitle, WithHeadings
{
    protected $data;
    
    public function __construct(array $data)
    {
        $this->data = $data;
    }
    
    public function array(): array
    {
        return array_map(function($item) {
            return [
                $item['aluno'],
                $item['total_respostas'],
                $item['acertos'],
                number_format($item['porcentagem_acertos'], 2) . '%',
                number_format($item['media_final'], 2)
            ];
        }, $this->data['estatisticasPorAluno']);
    }
    
    public function headings(): array
    {
        return [
            'Aluno',
            'Total Respostas',
            'Acertos',
            '% Acertos',
            'Média (0-10)'
        ];
    }
    
    public function title(): string
    {
        return 'Alunos';
    }
}

class EstatisticasSimuladosSheet implements FromArray, WithTitle, WithHeadings
{
    protected $data;
    
    public function __construct(array $data)
    {
        $this->data = $data;
    }
    
    public function array(): array
    {
        return array_map(function($item) {
            return [
                $item['simulado'],
                number_format($item['media_turma'], 2)
            ];
        }, $this->data['mediaPorSimulado']);
    }
    
    public function headings(): array
    {
        return [
            'Simulado',
            'Média da Turma (0-10)'
        ];
    }
    
    public function title(): string
    {
        return 'Simulados';
    }
}

class EstatisticasHabilidadesSheet implements FromArray, WithTitle, WithHeadings
{
    protected $data;
    
    public function __construct(array $data)
    {
        $this->data = $data;
    }
    
    public function array(): array
    {
        return array_map(function($item) {
            return [
                $item['habilidade'],
                $item['total_respostas'],
                $item['acertos'],
                number_format($item['porcentagem_acertos'], 2) . '%'
            ];
        }, $this->data['estatisticasPorHabilidade']);
    }
    
    public function headings(): array
    {
        return [
            'Habilidade',
            'Total Respostas',
            'Acertos',
            '% Acertos'
        ];
    }
    
    public function title(): string
    {
        return 'Habilidades';
    }
}