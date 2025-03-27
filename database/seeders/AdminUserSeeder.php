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
    // Cria os usuários administradores
    User::create([
        'name' => 'Admin SEDUC',
        'email' => 'admin@gmail.com',
        'password' => Hash::make('12345678'), // Senha criptografada
        'role' => 'admin', // Papel do usuário
    ]);

    User::create([
        'name' => 'Professor SEDUC',
        'email' => 'professor@gmail.com',
        'password' => Hash::make('12345678'), // Senha criptografada
        'role' => 'professor', // Papel do usuário
    ]);

    User::create([
        'name' => 'Coordenador SEDUC',
        'email' => 'coordenador@gmail.com',
        'password' => Hash::make('12345678'), // Senha criptografada
        'role' => 'coordenador', // Papel do usuário
    ]);

    User::create([
        'name' => 'AEE SEDUC',
        'email' => 'aee@gmail.com',
        'password' => Hash::make('12345678'), // Senha criptografada
        'role' => 'aee', // Papel do usuário
    ]);
            $this->command->info('Usuário administrador criado com sucesso!');
    }
}
