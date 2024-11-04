<?php

// App/Http/Middleware/CheckPerfil.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckPerfil
{
    public function handle(Request $request, Closure $next, string $role)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Não autenticado.'], 401);
        }

        $perfilMinimo = match ($role) {
            'admin' => PERFIL_ADMIN,
            'librarian' => PERFIL_BIBLIOTECARIO,
            'user' => PERFIL_PESSOA,
            default => null,
        };

        if ($perfilMinimo === null || Auth::user()->perfil_id < $perfilMinimo) {
            return response()->json(['error' => 'Acesso negado.'], 403);
        }

        return $next($request);
    }
}