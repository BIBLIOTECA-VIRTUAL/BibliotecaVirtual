<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Livro extends Model
{
    use HasFactory;
    protected $table = 'livros';
    protected $fillable = [
        'titulo',
        'autor_id',
        'genero_id',
        'disponibilidade',
    ];
    public function getTitulo()
    {
        return $this->titulo;
    }
    public function estaDisponivel()
    {
        return $this->disponibilidade;
    }
}
