<?php
// App/Providers/AuthServiceProvider.php
namespace App\Providers;

use App\Models\User;
use App\Policies\UsuarioPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        User::class => UsuarioPolicy::class,
    ];

    public function boot()
    {
        $this->registerPolicies();

        // Define constantes para os tipos de perfil
        define('PERFIL_PESSOA', 0);
        define('PERFIL_BIBLIOTECARIO', 1);
        define('PERFIL_ADMIN', 2);

        // Gates mais específicos e seguros
        Gate::define('admin', function (User $user) {
            return $user->perfil_id === PERFIL_ADMIN;
        });

        Gate::define('librarian', function (User $user) {
            return in_array($user->perfil_id, [PERFIL_BIBLIOTECARIO, PERFIL_ADMIN]);
        });

        Gate::define('person', function (User $user) {
            return $user->perfil_id === PERFIL_PESSOA;
        });

        // Gate para gerenciar usuários (create, update, delete)
        Gate::define('manage-users', function (User $user) {
            return $user->perfil_id === PERFIL_ADMIN;
        });
    }
}
