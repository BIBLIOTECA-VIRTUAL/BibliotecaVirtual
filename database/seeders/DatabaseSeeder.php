<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Livro;
use App\Models\Emprestimo;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Cria 10 usuários aleatórios
        User::factory(10)->create();

        // Cria um usuário específico
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Cria 50 livros com dados aleatórios
        Livro::factory(50)->create();
        Emprestimo::factory(50)->create();
    }
}
