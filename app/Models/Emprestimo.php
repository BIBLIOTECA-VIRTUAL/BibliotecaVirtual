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

    
    public function devolverLivro()
    {
        $this->data_devolucao = now(); 
        $this->save(); 
    }
}
