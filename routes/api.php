<?php
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\LivroController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::prefix('usuarios')->group(function () {
        Route::get('/', [UsuarioController::class, 'index']);
        Route::post('/', [UsuarioController::class, 'store']);
        Route::put('/{id}', [UsuarioController::class, 'update']);
        Route::delete('/{id}', [UsuarioController::class, 'destroy']);
    });

    Route::prefix('livros')->group(function () {
        Route::get('/', [LivroController::class, 'index']);
        Route::post('/', [LivroController::class, 'store']);
        Route::put('/{id}', [LivroController::class, 'update']);
        Route::delete('/{id}', [LivroController::class, 'destroy']);
    });
});
