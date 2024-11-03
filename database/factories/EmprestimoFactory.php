<?php

namespace Database\Factories;

use App\Models\Emprestimo;
use App\Models\Livro;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmprestimoFactory extends Factory
{
    protected $model = Emprestimo::class;

    /**
     * Define o estado padrão do modelo.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'livro_id' => Livro::factory(), // Cria um livro associado
            'usuario_id' => User::factory(), // Cria um usuário associado
            'data_emprestimo' => $this->faker->dateTimeBetween('-1 month', 'now'), // Data aleatória no último mês
            'data_devolucao' => null, // Devolução inicialmente nula para empréstimos ativos
        ];
    }

    /**
     * Define um estado para empréstimos já devolvidos.
     *
     * @return static
     */
    public function devolvido(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'data_devolucao' => $this->faker->dateTimeBetween($attributes['data_emprestimo'], 'now'), // Data após o empréstimo
            ];
        });
    }
}
