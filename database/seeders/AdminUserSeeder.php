<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Cria o usuário administrador
        User::create([
            'name' => 'Admin SEDUC',
            'email' => 'seduc@gmail.com',
            'password' => Hash::make('eduhabil#'), // Senha criptografada
            'role' => 'admin', // Papel do usuário
        ]);

        $this->command->info('Usuário administrador criado com sucesso!');
    }
}