<?php

namespace Database\Factories;

use App\Models\Livro;
use Illuminate\Database\Eloquent\Factories\Factory;

class LivroFactory extends Factory
{
    protected $model = Livro::class;

    public function definition()
    {
        return [
            'titulo' => $this->faker->sentence(3), // Gera um título com 3 palavras
            'isbn' => $this->faker->isbn13, // Gera um ISBN válido
            'descricao' => $this->faker->paragraph, // Gera um parágrafo como descrição
            'ano_publicacao' => $this->faker->year, // Ano aleatório
            'autor' => fake()->name(),
            'generos' => $this->faker->randomElement([
                                            'Ficção Científica',
                                            'Romance',
                                            'Terror',
                                            'Aventura',
                                            'Fantasia',
                                            'Suspense',
                                            'Drama',
                                            'Policial',
                                            'História',
                                            'Biografia'
                                              ]), 
            'disponibilidade' => $this->faker->boolean, // True ou False
            'quantidade_total' => $this->faker->numberBetween(1, 100), // Número entre 1 e 100
            'quantidade_disponivel' => $this->faker->numberBetween(0, 100), // Número entre 0 e 100
        ];
    }
}
