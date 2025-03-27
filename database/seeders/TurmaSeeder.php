<?php

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TurmaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lista de escolas que devem ser usadas na seed
        $nomesEscolas = [
            '02 DE JULHO', '15 DE JULHO', '25 DE JULHO', 'AMÉRICO TANURI - JUNCO',
            'AMÉRICO TANURI - MANIÇOBA', 'ANÁLIA BARBOSA DE SOUZA',
            'ANTONIO FRANCISCO DE OLIVEIRA', 'ARGEMIRO JOSE DA CRUZ',
            'BOM JESUS - BARAÚNA', 'BOM JESUS - NH1'
        ];

        // Obtendo IDs das escolas específicas
        $escolas = DB::table('escolas')
            ->whereIn('nome', $nomesEscolas)
            ->pluck('id');

        $professores = DB::table('users')->where('role', 'professor')->pluck('id');

        // Verificando se há escolas e professores cadastrados
        if ($escolas->isEmpty() || $professores->isEmpty()) {
            $this->command->warn('Nenhuma escola ou professor encontrado. Verifique os seeders das escolas e professores.');
            return;
        }

        // Criando turmas
        for ($i = 1; $i <= 20; $i++) {
            DB::table('turmas')->insert([
                'escola_id' => $escolas->random(),
                'professor_id' => $professores->random(),
                'nome_turma' => "Turma $i",
                'quantidade_alunos' => rand(15, 40),
                'codigo_turma' => Str::uuid(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('20 turmas foram criadas para as escolas específicas com sucesso!');
    }
}
