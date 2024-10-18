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
        return $this -> belongsTo(Livro::class);
    }

    public function usuario()
    {
        return $this -> belongsTo(User::class, 'usuario_id');
    }

    
    public function devolverLivro()
    {
        /**
         * Realiza a devolução do livro emprestado.
         *
         * Este método executa as seguintes ações:
         * 1. Verifica se o livro já foi devolvido.
         * 2. Atualiza a data de devolução para a data atual.
         * 3. Salva as alterações no registro de empréstimo.
         * 4. Atualiza a disponibilidade do livro no sistema.
         *
         * @throws \Exception Se o livro já tiver sido devolvido anteriormente.
         * @return void
         */
        if($this -> data_devolucao){
            throw new \Exception('Livro já devolvido');
        }
        $this->data_devolucao = now();
        $this->save();
        $this->livro->devolverLivro();
    }

    public function scopeAtivos($query)
    {
        return $query -> whereNull("data_devolucao");
    }
}
