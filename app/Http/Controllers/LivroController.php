<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Livro;

class LivroController extends Controller {
    public function index() {
        $livros = Livro::all();
        return response()->json($livros, 200);
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'titulo' => 'required|string',
            'autor_id' => 'required|integer',
            'genero_id' => 'required|integer',
        ]);
        
        $livro = Livro::create($validated);
        return response()->json($livro, 201);
    }

    public function update(Request $request, $id) {
        $livro = Livro::findOrFail($id);
        $validated = $request->validate([
            'titulo' => 'required|string',
        ]);
        
        $livro->update($validated);
        return response()->json($livro, 200);
    }

    public function destroy($id) {
        $livro = Livro::findOrFail($id);
        $livro->delete();
        return response()->json(['mensagem' => 'Livro deletado com sucesso.'], 200);
    }
}
