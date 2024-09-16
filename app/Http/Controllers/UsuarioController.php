<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UsuarioController extends Controller {
    public function index() {
        $usuarios = Usuario::all();
        return response()->json($usuarios);
    }

    public function store(Request $request) {
        $usuario = new Usuario($request->nome, $request->email, $request->senha, $request->perfil_id);
        $usuario->save();
        return response()->json($usuario);
    }

    public function update(Request $request, $id) {
        $usuario = Usuario::find($id);
        $usuario->nome = $request->nome;
        $usuario->email = $request->email;
        $usuario->save();
        return response()->json($usuario);
    }

    public function destroy($id) {
        $usuario = Usuario::find($id);
        $usuario->delete();
        return response()->json('Usuario deletado');
    }
}