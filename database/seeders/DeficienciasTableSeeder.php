<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DeficienciasTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $deficiencias = [
            [
                'nome' => 'Deficiência Intelectual',
                'descricao' => 'Caracterizada por limitações significativas no funcionamento intelectual e no comportamento adaptativo.',
            ],
            [
                'nome' => 'Transtorno do Espectro Autista (TEA)',
                'descricao' => 'Condição caracterizada por desafios na comunicação social e comportamentos repetitivos.',
            ],
            [
                'nome' => 'Deficiência Física',
                'descricao' => 'Comprometimento da mobilidade e/ou da coordenação motora.',
            ],
            [
                'nome' => 'Deficiência Visual',
                'descricao' => 'Perda total ou parcial da visão.',
            ],
            [
                'nome' => 'Deficiência Auditiva',
                'descricao' => 'Perda total ou parcial da audição.',
            ],
            [
                'nome' => 'Surdocegueira',
                'descricao' => 'Deficiência única que apresenta a perda da visão e da audição.',
            ],
            [
                'nome' => 'Deficiência Múltipla',
                'descricao' => 'Associação de duas ou mais deficiências.',
            ],
            [
                'nome' => 'Altas Habilidades/Superdotação',
                'descricao' => 'Alunos com notável desempenho e elevada potencialidade em áreas específicas.',
            ],
        ];

        DB::table('deficiencias')->insert($deficiencias);
    }
}