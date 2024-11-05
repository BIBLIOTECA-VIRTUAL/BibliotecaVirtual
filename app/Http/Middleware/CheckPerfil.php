<?php

namespace App\Http\Middleware;

use App\Enums\PerfilEnum;
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
            'admin' => PerfilEnum::ADMIN,
            'librarian' => PerfilEnum::LIBRARIAN,
            'user' => PerfilEnum::USER,
            default => null,
        };

        if ($perfilMinimo === null || Auth::user()->perfil_id < $perfilMinimo) {
            return response()->json(['error' => 'Acesso negado.'], 403);
        }

        return $next($request);
    }
}