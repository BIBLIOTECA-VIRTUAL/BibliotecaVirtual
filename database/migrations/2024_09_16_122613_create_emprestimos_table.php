<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Executa as migrações.
     */
    public function up(): void
    {
        Schema::create('emprestimos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('livro_id')->constrained('livros')->onDelete('cascade');
            $table->foreignId('usuario_id')->constrained('users')->onDelete('cascade');
            $table->date('data_emprestimo');
            $table->date('data_devolucao_prevista');
            $table->date('data_devolucao_real')->nullable();
            $table->enum('status', ['em_andamento', 'atrasado', 'devolvido'])->default('em_andamento');
            $table->text('observacoes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverte as migrações.
     */
    public function down(): void
    {
        Schema::dropIfExists('emprestimos');
    }
};

// Melhorias sugeridas:
// 1. Uso de foreignId() para melhor integridade referencial.
// 2. Adicionado campo 'data_devolucao_prevista' para controle de prazos.
// 3. Renomeado 'data_devolucao' para 'data_devolucao_real' para maior clareza.
// 4. Adicionado campo 'status' para melhor controle do estado do empréstimo.
// 5. Adicionado campo 'observacoes' para notas adicionais.
// 6. Implementado softDeletes() para exclusão lógica.
// 7. Comentários em português para melhor compreensão.
