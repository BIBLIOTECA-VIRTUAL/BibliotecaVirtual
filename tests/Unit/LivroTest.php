<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Livro;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LivroTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_list_all_livros()
    {
        Livro::factory()->count(3)->create();

        $response = $this->getJson('/api/livros');
        $response->assertStatus(200)->assertJsonCount(3);
    }

    public function test_it_can_create_a_livro()
    {
        $data = [
            'titulo' => 'Novo Livro',
            'autor' => 'Autor Teste',
            'preco' => 50.00,
        ];

        $response = $this->postJson('/api/livros', $data);
        $response->assertStatus(201);
        $this->assertDatabaseHas('livros', $data);
    }

    public function test_it_can_delete_a_livro()
    {
        $livro = Livro::factory()->create();

        $response = $this->deleteJson("/api/livros/{$livro->id}");
        $response->assertStatus(200);
        $this->assertDatabaseMissing('livros', ['id' => $livro->id]);
    }
}
