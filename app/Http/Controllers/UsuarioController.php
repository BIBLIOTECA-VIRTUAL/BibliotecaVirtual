<?php

namespace App\Http\Controllers;

use App\Http\Requests\UsuarioRequest; // Presumindo que há uma classe de requisição para validação
use App\Http\Resources\UsuarioResource;
use App\Models\Usuario;
use Illuminate\Http\Request;
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

        $resource = new UsuarioResource($usuario);
        return $resource->response()->setStatusCode(201); 
    }

    public function update(UsuarioRequest $request, string $id)
    {
        $usuario = Usuario::find($id);

        if (!$usuario) {
            return response(['error' => 'Usuário não encontrado.'], 404);
        }

        $usuario->update([
            'nome' => $request->nome,
            'email' => $request->email,
        ]);

        $resource = new UsuarioResource($usuario);
        return $resource->response()->setStatusCode(200); 
    }

    public function destroy(string $id)
    {
        $usuario = Usuario::find($id);

        if (!$usuario) {
            return response(['error' => 'Usuário não encontrado.'], 404);
        }

        $usuario->delete();
        return response(['message' => 'Usuário deletado com sucesso.'], 200);
    }
}
