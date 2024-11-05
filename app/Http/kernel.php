<?php
// App/Http/Kernel.php
namespace App\Http\Middleware;

class Kernel extends HttpKernel {
    protected $routeMiddleware = [
        // outros middlewares
        'check_perfil' => \App\Http\Middleware\CheckPerfil::class,
    ];
}
