<?php
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\LivroController;
use Illuminate\Support\Facades\Route;



Route::match(['get', 'post'], uri: '/login', action: [UserController::class, 'login'])->name('login');
Route::post('/register', action: [UserController::class, 'register'])->name('register');
Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('profile')->group(function () {
        Route::get('/', action: [UserController::class, 'profile'])->name('profile');
        Route::put('/edit', action: [UserController::class, 'editProfile'])->name('edit-profile');
    });
    Route::get('/logout', action: [UserController::class, 'logout'])->name('logout');
});

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
