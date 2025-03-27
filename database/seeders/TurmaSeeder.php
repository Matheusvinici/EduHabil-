<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TurmaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $escolas = DB::table('escolas')->pluck('id');
        $professores = DB::table('users')->where('role', 'professor')->pluck('id');

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
    }
}
