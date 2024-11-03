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
        Schema::create('livros', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->string('isbn')->unique()->nullable();
            $table->text('descricao')->nullable();
            $table->year('ano_publicacao')->nullable();
            $table->string('autor');
            $table->string('genero');
            $table->boolean('disponibilidade')->default(true);
            // Unsigned integer para evitar ter valores negativos na quantidade de livros
            $table->unsignedInteger('quantidade_total')->default(1);
            $table->unsignedInteger('quantidade_disponivel')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverte as migrações.
     */
    public function down(): void
    {
        Schema::dropIfExists('livros');
    }
};

