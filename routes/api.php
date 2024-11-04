<?php
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\LivroController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;



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
    Route::prefix('usuarios')->middleware('can:admin')->group(function () {
        Route::get('/', [UsuarioController::class, 'index']);
        Route::post('/', [UsuarioController::class, 'store']);
        Route::put('/{id}', [UsuarioController::class, 'update']);
        Route::delete('/{id}', [UsuarioController::class, 'destroy']);
    });

    Route::prefix('livros')->middleware('can:librarian')->group(function () {
        Route::get('/', [LivroController::class, 'index']);
        Route::post('/', [LivroController::class, 'store']);
        Route::put('/{id}', [LivroController::class, 'update']);
        Route::delete('/{id}', [LivroController::class, 'destroy']);
    });

    Route::prefix('emprestimo')->middleware('can:person')->group(function() {
        Route::get('/', [EmprestimoController::class, 'index']);
        Route::post('/', [EmprestimoController::class, 'store']);
        Route::put('/{id}/devolver', [EmprestimoController::class, 'devolver']);
        Route::delete('/{id}', [EmprestimoController::class, 'destroy']);
    });
});
