<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

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
        
        // Chama o seeder do formador
    }
}
