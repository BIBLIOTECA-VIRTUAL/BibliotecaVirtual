<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Livro;
use App\Models\Emprestimo;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Limpar tabelas existentes
        DB::table('users')->truncate();

        // Criar usuário admin
        User::factory()->create([
            'name' => 'Administrador',
            'email' => 'admin@example.com',
            'password' => bcrypt('admin123'),
            'perfil_id' => 2,
            'date_birthday' => '1990-01-01',
            'gender' => 'M'
        ]);

        // Criar bibliotecário
        User::factory()->create([
            'name' => 'Bibliotecário',
            'email' => 'bibliotecario@example.com',
            'password' => bcrypt('123456'),
            'perfil_id' => 1,
            'date_birthday' => '1990-01-01',
            'gender' => 'M'
        ]);

        // Criar usuário comum
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('123456'),
            'perfil_id' => 0,
            'date_birthday' => '1990-01-01',
            'gender' => 'M'
        ]);

        // Criar dados de teste
        Livro::factory(50)->create();
        Emprestimo::factory(50)->create();
    }
}
