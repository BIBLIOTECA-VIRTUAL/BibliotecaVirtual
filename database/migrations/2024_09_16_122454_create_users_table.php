<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');  
            $table->string('email')->unique();
            $table->string('password', 255);  
            $table->foreignId('perfil_id')->constrained('perfis')->onDelete('cascade');  
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();  
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};

// Explicação do que essa migration faz:

// Esta migration cria uma tabela chamada 'users' no banco de dados:

// 1. $table->id(): Cria uma coluna de ID auto-incrementável como chave primária.
// 2. $table->string('name'): Cria uma coluna para armazenar o nome do usuário.
// 3. $table->string('email')->unique(): Cria uma coluna para o email do usuário, que deve ser único.
// 4. $table->string('password'): Cria uma coluna para armazenar a senha do usuário.
// 5. $table->foreignId('perfil_id')->constrained('perfils')->onDelete('cascade'): Cria uma chave estrangeira 'perfil_id' que se relaciona com a tabela 'perfils'. Se um perfil for deletado, todos os usuários associados a ele também serão deletados (cascade).
// 6. $table->timestamp('email_verified_at')->nullable(): Cria uma coluna para armazenar quando o email foi verificado, que pode ser nula.
// 7. $table->rememberToken(): Cria uma coluna para armazenar o token "lembrar-me" para autenticação.
// 8. $table->timestamps(): Cria colunas 'created_at' e 'updated_at' para rastrear quando o registro foi criado e atualizado.

// O método down() é usado para reverter a migration, neste caso, ele simplesmente exclui a tabela 'users' se ela existir.

