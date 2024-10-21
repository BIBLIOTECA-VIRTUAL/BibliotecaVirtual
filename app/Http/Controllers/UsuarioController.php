<?php

namespace App\Http\Controllers;

use App\Http\Requests\UsuarioRequest;
use App\Http\Resources\UsuarioResource;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    public function index()
    {
        return UsuarioResource::collection(Usuario::all());
    }

    public function store(UsuarioRequest $request)
    {
        $usuario = Usuario::create([
            'nome' => $request->nome,
            'email' => $request->email,
            'senha' => Hash::make($request->senha),
            'perfil_id' => $request->perfil_id,
        ]);

        return (new UsuarioResource($usuario))->response()->setStatusCode(201);
    }

    public function update(UsuarioRequest $request, string $id)
    {
        $usuario = Usuario::findOrFail($id);
        $usuario->update($request->only(['nome', 'email']));

        return (new UsuarioResource($usuario))->response()->setStatusCode(200);
    }

    public function destroy(string $id)
    {
        $usuario = Usuario::findOrFail($id);
        $usuario->delete();

        return response()->json(['mensagem' => 'Usuário deletado com sucesso.'], 200);
    }
}
