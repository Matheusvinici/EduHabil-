<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class EstatisticasExport implements WithMultipleSheets
{
    protected $data;
    protected $tipo;

    public function __construct(array $data, string $tipo)
    {
        $this->data = $data;
        $this->tipo = $tipo;
    }

    public function sheets(): array
    {
        $sheets = [];
        
        // Sheet com dados gerais
        $sheets[] = new class($this->data) implements FromArray, WithTitle, WithHeadings, ShouldAutoSize {
            protected $data;
            
            public function __construct($data)
            {
                $this->data = $data;
            }
            
            public function array(): array
            {
                return [
                    ['Total de Simulados', $this->data['totalSimulados']],
                    ['Total de Professores', $this->data['totalProfessores']],
                    ['Total de Alunos', $this->data['totalAlunos']],
                    ['Alunos com Deficiência', $this->data['totalAlunosComDeficiencia'] ?? 0],
                    ['Total de Respostas', $this->data['totalRespostas']],
                    ['Professores que Responderam', $this->data['professoresResponderam']],
                    ['Alunos que Responderam', $this->data['alunosResponderam']],
                    ['Média Geral 1º ao 5º Ano', number_format($this->data['mediaGeral1a5'], 2)],
                    ['Média Geral 6º ao 9º Ano', number_format($this->data['mediaGeral6a9'], 2)],
                ];
            }
            
            public function title(): string
            {
                return 'Dados Gerais';
            }
            
            public function headings(): array
            {
                return [['Dados Gerais'], ['Descrição', 'Valor']];
            }
        };
        
        // Sheet por escola
        if (!empty($this->data['estatisticasPorEscola'])) {
            $sheets[] = new class($this->data['estatisticasPorEscola']) implements FromArray, WithTitle, WithHeadings, ShouldAutoSize {
                protected $data;
                
                public function __construct($data)
                {
                    $this->data = $data;
                }
                
                public function array(): array
                {
                    $result = [];
                    foreach ($this->data as $item) {
                        $result[] = [
                            $item['escola'],
                            $item['total_respostas'],
                            $item['acertos'],
                            number_format($item['porcentagem_acertos'], 2) . '%',
                            number_format($item['media_final'], 2)
                        ];
                    }
                    return $result;
                }
                
                public function title(): string
                {
                    return 'Por Escola';
                }
                
                public function headings(): array
                {
                    return ['Escola', 'Total Respostas', 'Acertos', '% Acertos', 'Média Final'];
                }
            };
        }
        
        // Sheet por ano
        if (!empty($this->data['estatisticasPorAno'])) {
            $sheets[] = new class($this->data['estatisticasPorAno']) implements FromArray, WithTitle, WithHeadings, ShouldAutoSize {
                protected $data;
                
                public function __construct($data)
                {
                    $this->data = $data;
                }
                
                public function array(): array
                {
                    $result = [];
                    foreach ($this->data as $item) {
                        $result[] = [
                            $item['ano'],
                            $item['total_respostas'],
                            $item['acertos'],
                            number_format($item['porcentagem_acertos'], 2) . '%',
                            number_format($item['media_final'], 2)
                        ];
                    }
                    return $result;
                }
                
                public function title(): string
                {
                    return 'Por Ano';
                }
                
                public function headings(): array
                {
                    return ['Ano', 'Total Respostas', 'Acertos', '% Acertos', 'Média Final'];
                }
            };
        }
        
        // Sheet por habilidade
        if (!empty($this->data['estatisticasPorHabilidade'])) {
            $sheets[] = new class($this->data['estatisticasPorHabilidade']) implements FromArray, WithTitle, WithHeadings, ShouldAutoSize {
                protected $data;
                
                public function __construct($data)
                {
                    $this->data = $data;
                }
                
                public function array(): array
                {
                    $result = [];
                    foreach ($this->data as $item) {
                        $result[] = [
                            $item['habilidade'],
                            $item['total_respostas'],
                            $item['acertos'],
                            number_format($item['porcentagem_acertos'], 2) . '%'
                        ];
                    }
                    return $result;
                }
                
                public function title(): string
                {
                    return 'Por Habilidade';
                }
                
                public function headings(): array
                {
                    return ['Habilidade', 'Total Respostas', 'Acertos', '% Acertos'];
                }
            };
        }
        
        // Sheet por raça
        if (!empty($this->data['estatisticasPorRaca'])) {
            $sheets[] = new class($this->data['estatisticasPorRaca']) implements FromArray, WithTitle, WithHeadings, ShouldAutoSize {
                protected $data;
                
                public function __construct($data)
                {
                    $this->data = $data;
                }
                
                public function array(): array
                {
                    $result = [];
                    foreach ($this->data as $item) {
                        $result[] = [
                            $item['raca'],
                            $item['total_respostas'],
                            $item['acertos'],
                            number_format($item['porcentagem_acertos'], 2) . '%',
                            number_format($item['media_final'], 2)
                        ];
                    }
                    return $result;
                }
                
                public function title(): string
                {
                    return 'Por Raça/Cor';
                }
                
                public function headings(): array
                {
                    return ['Raça/Cor', 'Total Respostas', 'Acertos', '% Acertos', 'Média Final'];
                }
            };
        }
        
        // Sheet por deficiência
        if (!empty($this->data['estatisticasPorDeficiencia'])) {
            $sheets[] = new class($this->data['estatisticasPorDeficiencia']) implements FromArray, WithTitle, WithHeadings, ShouldAutoSize {
                protected $data;
                
                public function __construct($data)
                {
                    $this->data = $data;
                }
                
                public function array(): array
                {
                    $result = [];
                    foreach ($this->data as $item) {
                        $result[] = [
                            $item['deficiencia'],
                            $item['total_respostas'],
                            $item['acertos'],
                            number_format($item['porcentagem_acertos'], 2) . '%',
                            number_format($item['media_final'], 2)
                        ];
                    }
                    return $result;
                }
                
                public function title(): string
                {
                    return 'Por Deficiência';
                }
                
                public function headings(): array
                {
                    return ['Deficiência', 'Total Respostas', 'Acertos', '% Acertos', 'Média Final'];
                }
            };
        }
        
        // Sheet alunos por deficiência
        if (!empty($this->data['alunosPorDeficiencia'])) {
            $sheets[] = new class($this->data['alunosPorDeficiencia']) implements FromArray, WithTitle, WithHeadings, ShouldAutoSize {
                protected $data;
                
                public function __construct($data)
                {
                    $this->data = $data;
                }
                
                public function array(): array
                {
                    $result = [];
                    foreach ($this->data as $item) {
                        $result[] = [
                            $item['deficiencia'],
                            $item['total']
                        ];
                    }
                    return $result;
                }
                
                public function title(): string
                {
                    return 'Alunos por Deficiência';
                }
                
                public function headings(): array
                {
                    return ['Deficiência', 'Total de Alunos'];
                }
            };
        }
        
        // Sheet por questão (com disciplina e habilidade)
        if (!empty($this->data['estatisticasPorQuestao'])) {
            $sheets[] = new class($this->data['estatisticasPorQuestao']) implements FromArray, WithTitle, WithHeadings, ShouldAutoSize {
                protected $data;
                
                public function __construct($data)
                {
                    $this->data = $data;
                }
                
                public function array(): array
                {
                    $result = [];
                    foreach ($this->data as $item) {
                        $result[] = [
                            $item['pergunta_id'],
                            $item['enunciado'],
                            $item['disciplina'],
                            $item['habilidade'],
                            $item['total_respostas'],
                            $item['acertos'],
                            number_format($item['porcentagem_acertos'], 2) . '%',
                            number_format($item['media_final'], 2)
                        ];
                    }
                    return $result;
                }
                
                public function title(): string
                {
                    return 'Por Questão';
                }
                
                public function headings(): array
                {
                    return [
                        'ID Pergunta', 
                        'Enunciado', 
                        'Disciplina', 
                        'Habilidade', 
                        'Total Respostas', 
                        'Acertos', 
                        '% Acertos', 
                        'Média'
                    ];
                }
            };
        }
        
        // Sheet média por disciplina
        if (!empty($this->data['mediaPorQuestaoPorDisciplina'])) {
            $sheets[] = new class($this->data['mediaPorQuestaoPorDisciplina']) implements FromArray, WithTitle, WithHeadings, ShouldAutoSize {
                protected $data;
                
                public function __construct($data)
                {
                    $this->data = $data;
                }
                
                public function array(): array
                {
                    $result = [];
                    foreach ($this->data as $item) {
                        $result[] = [
                            $item['disciplina'],
                            $item['total_respostas'],
                            $item['total_acertos'],
                            number_format(($item['total_acertos'] / $item['total_respostas']) * 100, 2) . '%',
                            number_format($item['media'], 2)
                        ];
                    }
                    return $result;
                }
                
                public function title(): string
                {
                    return 'Média por Disciplina';
                }
                
                public function headings(): array
                {
                    return ['Disciplina', 'Total Respostas', 'Total Acertos', '% Acertos', 'Média'];
                }
            };
        }
        
        return $sheets;
    }
}