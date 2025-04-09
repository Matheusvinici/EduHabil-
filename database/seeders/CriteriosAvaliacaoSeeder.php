<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CriteriosAvaliacaoSeeder extends Seeder
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
            ['categoria' => 'Organização pedagógica', 'descricao' => 'Avaliações coerentes com os objetivos de aprendizagem.'],
            ['categoria' => 'Organização pedagógica', 'descricao' => 'Acompanhamento da aprendizagem dos alunos (diários, relatórios, registros).'],

            // 2. Práticas docentes
            ['categoria' => 'Práticas docentes', 'descricao' => 'Presença e pontualidade dos professores.'],
            ['categoria' => 'Práticas docentes', 'descricao' => 'Domínio de conteúdo e didática.'],
            ['categoria' => 'Práticas docentes', 'descricao' => 'Relação interpessoal com os alunos.'],
            ['categoria' => 'Práticas docentes', 'descricao' => 'Uso adequado de recursos pedagógicos e tecnológicos.'],

            // 3. Gestão da escola
            ['categoria' => 'Gestão da escola', 'descricao' => 'Funcionamento regular da equipe gestora.'],
            ['categoria' => 'Gestão da escola', 'descricao' => 'Organização da documentação escolar (diários, livros de ponto, atas).'],
            ['categoria' => 'Gestão da escola', 'descricao' => 'Cumprimento do calendário letivo.'],
            ['categoria' => 'Gestão da escola', 'descricao' => 'Participação em formações e reuniões pedagógicas.'],

            // 4. Ambiente escolar
            ['categoria' => 'Ambiente escolar', 'descricao' => 'Condições físicas das salas, banheiros e pátios.'],
            ['categoria' => 'Ambiente escolar', 'descricao' => 'Segurança e acessibilidade.'],
            ['categoria' => 'Ambiente escolar', 'descricao' => 'Clima escolar (respeito, disciplina, acolhimento).'],

            // 5. Participação da comunidade
            ['categoria' => 'Participação da comunidade', 'descricao' => 'Envolvimento das famílias nas atividades escolares.'],
            ['categoria' => 'Participação da comunidade', 'descricao' => 'Reuniões com pais e responsáveis.'],
            ['categoria' => 'Participação da comunidade', 'descricao' => 'Projetos de integração com a comunidade local.'],

            // 6. Resultados de aprendizagem
            ['categoria' => 'Resultados de aprendizagem', 'descricao' => 'Indicadores de rendimento escolar (notas, aprovações, evasão).'],
            ['categoria' => 'Resultados de aprendizagem', 'descricao' => 'Evolução dos alunos em avaliações internas e externas.'],
            ['categoria' => 'Resultados de aprendizagem', 'descricao' => 'Intervenções pedagógicas para alunos com dificuldades.'],
        ];

        DB::table('criterios_avaliacao')->insert($criterios);

        $this->command->info('Critérios de avaliação inseridos com sucesso!');
    }
}
