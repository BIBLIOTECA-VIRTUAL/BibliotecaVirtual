<?php

use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\LivroController;
use App\Http\Controllers\EmprestimoController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Rotas públicas
Route::prefix('auth')->group(function () {
    Route::post('/login', [UserController::class, 'login'])->name('auth.login');
    Route::post('/register', [UserController::class, 'register'])->name('auth.register');
});

// Rotas protegidas
Route::middleware('auth:sanctum')->group(function () {
    // Autenticação
    Route::post('/logout', [UserController::class, 'logout'])->name('auth.logout');

    // Perfil do usuário
    Route::prefix('profile')->group(function () {
        Route::get('/', [UserController::class, 'profile'])->name('profile.show');
        Route::put('/', [UserController::class, 'editProfile'])->name('profile.update');
    });

    // Rotas de Administrador
    Route::prefix('admin')->middleware('can:admin')->group(function () {
        Route::apiResource('usuarios', UserController::class);
        // ->except('show')->names([
        //     'index' => 'admin.usuarios.index',
        //     'store' => 'admin.usuarios.store',
        //     'update' => 'admin.usuarios.update',
        //     'destroy' => 'admin.usuarios.destroy'
        // ]);
    });

    // Rotas de Bibliotecário
    Route::prefix('biblioteca')->middleware('check_perfil:librarian')->group(function () {
        Route::apiResource('livros', LivroController::class)->names([
            'index' => 'biblioteca.livros.index',
            'store' => 'biblioteca.livros.store',
            'show' => 'biblioteca.livros.show',
            'update' => 'biblioteca.livros.update',
            'destroy' => 'biblioteca.livros.destroy'
        ]);
    });

    // Rotas de Usuário
    Route::prefix('emprestimos')->middleware('check_perfil:user')->group(function () {
        Route::get('/', [EmprestimoController::class, 'index'])->name('emprestimos.index');
        Route::post('/', [EmprestimoController::class, 'store'])->name('emprestimos.store');
        Route::put('/{emprestimo}/devolver', [EmprestimoController::class, 'devolver'])
            ->name('emprestimos.devolver');
        Route::delete('/{emprestimo}', [EmprestimoController::class, 'destroy'])
            ->name('emprestimos.destroy');
    });

    // Rotas públicas autenticadas (qualquer usuário logado)
    Route::prefix('livros')->group(function () {
        Route::get('/', [LivroController::class, 'index'])->name('livros.index');
        Route::get('/{livro}', [LivroController::class, 'show'])->name('livros.show');
    });
});

// Fallback para rotas não encontradas
// Route::fallback(function() {
//     return response()->json([
//         'message' => 'Rota não encontrada.',
//         'error' => 'Not Found',
//         'status_code' => 404
//     ], 404);
// });
