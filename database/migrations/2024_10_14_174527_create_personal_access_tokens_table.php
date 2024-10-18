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
        Schema::create('personal_access_tokens', function (Blueprint $table) {
            $table->id();
            $table->morphs('tokenable');
            $table->string('name');
            $table->string('token', 64)->unique();
            $table->text('abilities')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverte as migrações.
     */
    public function down(): void
    {
        Schema::dropIfExists('personal_access_tokens');
    }
};

// Explicação do código e o que uma migration faz:

// Este código é uma migration do Laravel, que é uma forma de controlar versões do banco de dados.

// Uma migration é usada para criar, modificar ou excluir tabelas e colunas no banco de dados de forma programática e controlada.

// Neste caso específico, esta migration cria uma tabela chamada 'personal_access_tokens'.

// O método 'up()' é executado quando a migration é aplicada. Ele cria a tabela com as seguintes colunas:
// - id: um identificador único auto-incrementado
// - tokenable: um campo polimórfico para associar o token a diferentes tipos de modelos
// - name: um nome para o token
// - token: o token em si, único e limitado a 64 caracteres
// - abilities: as permissões do token
// - last_used_at: quando o token foi usado pela última vez
// - expires_at: quando o token expira
// - timestamps: colunas created_at e updated_at para rastrear quando o registro foi criado e atualizado

// O método 'down()' é executado quando a migration é revertida, e neste caso, ele simplesmente exclui a tabela 'personal_access_tokens'.

// As migrations são importantes porque permitem que você versione seu esquema de banco de dados, facilitando o trabalho em equipe e o controle de mudanças ao longo do tempo.
