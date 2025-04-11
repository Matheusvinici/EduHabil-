<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TutoriaCriteriosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $criterios = [
            // 1. Organização pedagógica
            ['categoria' => 'Organização pedagógica', 'descricao' => 'Planejamentos de aula atualizados e compatíveis com o currículo.'],
            ['categoria' => 'Organização pedagógica', 'descricao' => 'Uso de metodologias ativas e diversificadas.'],

            // 2. Práticas docentes
            ['categoria' => 'Práticas docentes', 'descricao' => 'Presença e pontualidade dos professores.'],
            ['categoria' => 'Práticas docentes', 'descricao' => 'Domínio de conteúdo e didática.'],

            // 3. Gestão da escola
            ['categoria' => 'Gestão da escola', 'descricao' => 'Funcionamento regular da equipe gestora.'],
            ['categoria' => 'Gestão da escola', 'descricao' => 'Organização da documentação escolar (diários, livros de ponto, atas).'],

            // 4. Ambiente escolar
            ['categoria' => 'Ambiente escolar', 'descricao' => 'Condições físicas das salas, banheiros e pátios.'],
            ['categoria' => 'Ambiente escolar', 'descricao' => 'Segurança e acessibilidade.'],

            // 5. Participação da comunidade
            ['categoria' => 'Participação da comunidade', 'descricao' => 'Envolvimento das famílias nas atividades escolares.'],
            ['categoria' => 'Participação da comunidade', 'descricao' => 'Reuniões com pais e responsáveis.'],

            // 6. Resultados de aprendizagem
            ['categoria' => 'Resultados de aprendizagem', 'descricao' => 'Indicadores de rendimento escolar (notas, aprovações, evasão).'],
            ['categoria' => 'Resultados de aprendizagem', 'descricao' => 'Evolução dos alunos em avaliações internas e externas.'],
        ];

        DB::table('tutoria_criterios')->insert($criterios);

        $this->command->info('Critérios de avaliação inseridos com sucesso!');
    }
}
