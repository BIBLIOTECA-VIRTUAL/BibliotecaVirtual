<?php
// App/Http/Kernel.php
class Kernel extends HttpKernel {
    protected $routeMiddleware = [
        // outros middlewares
        'role' => \App\Http\Middleware\CheckPerfil::class,
    ];
}
