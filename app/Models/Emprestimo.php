<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Emprestimo extends Model
{
    use HasFactory;

    protected $table = 'emprestimos';

    protected $fillable = [
        'livro_id',
        'usuario_id',
        'data_emprestimo',
        'data_devolucao',
    ];

    protected $dates = ['data_emprestimo', 'data_devolucao'];

    public function livro()
    {
        return $this->belongsTo(Livro::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function devolverLivro()
    {
        if ($this->data_devolucao) {
            throw new \App\Exceptions\LivroJaDevolvidoException('Livro já devolvido');
        }
        $this->data_devolucao = now();
        $this->save();
        $this->livro->devolverLivro();
    }

    public function scopeAtivos($query)
    {
        return $query->whereNull('data_devolucao');
    }
}
