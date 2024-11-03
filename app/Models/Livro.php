<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Livro extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'livros';

    protected $fillable = [
        'titulo',
        'isbn',
        'descricao',
        'ano_publicacao',
        'autor',
        'genero',
        'disponibilidade',
        'quantidade_total',
        'quantidade_disponivel',
    ];

    protected $casts = [
        'disponibilidade' => 'boolean',
        'ano_publicacao' => 'integer',
        'quantidade_total' => 'integer',
        'quantidade_disponivel' => 'integer',
    ];

    public function emprestimos()
    {
        return $this->hasMany(Emprestimo::class);
    }

    public function estaDisponivel()
    {
        return $this->quantidade_disponivel > 0;
    }

    public function scopeDisponivel($query)
    {
        return $query->where('quantidade_disponivel', '>', 0);
    }

    public function atualizarDisponibilidade()
    {
        $this->disponibilidade = $this->quantidade_disponivel > 0;
        $this->save();
    }

    public function emprestarLivro()
    {
        if (!$this->estaDisponivel()) {
            return false;
        }

        \DB::transaction(function () {
            $this->quantidade_disponivel--;
            $this->atualizarDisponibilidade();
        });

        return true;
    }

    public function devolverLivro()
    {
        \DB::transaction(function () {
            if ($this->quantidade_disponivel < $this->quantidade_total) {
                $this->quantidade_disponivel++;
                $this->atualizarDisponibilidade();
            }
        });

        return true;
    }
}
