<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(): void
    {
        // Chama o seeder de administrador
        $this->call(AdminUserSeeder::class);

        // Chama o seeder das Deficiências
        $this->call(DeficienciasTableSeeder::class);

        // Chama o seeder das Características
        $this->call(CaracteristicasTableSeeder::class);

        // Chama o seeder das Habilidades
        $this->call(HabilidadesTableSeeder::class);

        // Chama o seeder das Escolas
        $this->call(EscolasTableSeeder::class);

        // Chama o seeder das Escolas
         $this->call(QuestoesTableSeeder::class);

        // Chama o seeder das Critérios de avalição (Tutores)
        $this->call(CriteriosAvaliacaoSeeder::class);

    }
}
