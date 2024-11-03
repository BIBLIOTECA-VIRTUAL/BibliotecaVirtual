<?php

namespace App\Http\Controllers;

use App\Models\Emprestimo;
use App\Models\Livro;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EmprestimoController extends Controller
{
    /**
     * Lista todos os empréstimos ativos.
     */
    public function index()
    {
        // Filtra apenas os empréstimos ativos e carrega as relações
        $emprestimos = Emprestimo::ativos()->with(['livro', 'usuario'])->get();
        return response()->json($emprestimos, 200);
    }

    /**
     * Cria um novo empréstimo.
     */
    public function store(Request $request)
    {
        $request->validate([
            'livro_id' => 'required|exists:livros,id',
            'usuario_id' => 'required|exists:users,id',
        ]);

        // Verifica se o livro está disponível para empréstimo
        $livro = Livro::findOrFail($request->livro_id);
        if ($livro->isEmprestado()) {
            return response()->json(['mensagem' => 'Livro já está emprestado.'], 400);
        }

        // Cria o empréstimo em uma transação para garantir consistência
        DB::beginTransaction();
        try {
            $emprestimo = Emprestimo::create([
                'livro_id' => $livro->id,
                'usuario_id' => $request->usuario_id,
                'data_emprestimo' => Carbon::now(),
            ]);

            // Atualiza o status do livro para indicar que está emprestado
            $livro->emprestar();

            DB::commit();
            return response()->json($emprestimo, 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['mensagem' => 'Erro ao registrar empréstimo.'], 500);
        }
    }

    /**
     * Marca o empréstimo como devolvido.
     */
    public function devolver($id)
    {
        $emprestimo = Emprestimo::findOrFail($id);

        // Verifica se o empréstimo já foi devolvido
        if ($emprestimo->data_devolucao) {
            return response()->json(['mensagem' => 'Livro já devolvido.'], 400);
        }

        DB::beginTransaction();
        try {
            // Marca a data de devolução e salva
            $emprestimo->data_devolucao = Carbon::now();
            $emprestimo->save();

            // Atualiza o status do livro para disponível
            $emprestimo->livro->devolver();

            DB::commit();
            return response()->json(['mensagem' => 'Livro devolvido com sucesso.'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['mensagem' => 'Erro ao devolver o livro.'], 500);
        }
    }

    /**
     * Exclui um registro de empréstimo.
     */
    public function destroy($id)
    {
        $emprestimo = Emprestimo::findOrFail($id);
        $emprestimo->delete();

        return response()->json(['mensagem' => 'Empréstimo excluído com sucesso.'], 200);
    }
}
