<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class Usuario extends Model
{
    use HasFactory;
    protected $table = 'usuarios';

    protected $fillable = [
        'nome',
        'email',
        'senha',
        'perfil_id',
    ];

    public function getNome()
    {
        return $this->nome;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getPerfilId()
    {
        return $this->perfil_id;
    }

    public function validarSenha($senha)
    {
        return Hash::check($senha, $this->senha);
    }

    public function setSenhaAttribute($senha)
    {
        $this->attributes['senha'] = Hash::make($senha);
    }
}
