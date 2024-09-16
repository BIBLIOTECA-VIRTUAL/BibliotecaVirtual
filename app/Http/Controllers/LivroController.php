<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LivroController extends Controller {
    public function index() {
        $livros = Livro::all();
        return response()->json($livros);
    }

    public function store(Request $request) {
        $livro = new Livro($request->titulo, $request->autor_id, $request->genero_id);
        $livro->save();
        return response()->json($livro);
    }

    public function update(Request $request, $id) {
        $livro = Livro::find($id);
        $livro->titulo = $request->titulo;
        $livro->save();
        return response()->json($livro);
    }

    public function destroy($id) {
        $livro = Livro::find($id);
        $livro->delete();
        return response()->json('Livro deletado');
    }
}

