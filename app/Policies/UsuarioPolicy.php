<?php
namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UsuarioPolicy
{
    use HandlesAuthorization;

    public function before(User $user)
    {
        // Super admin tem todas as permissões
        if ($user->perfil_id === PERFIL_ADMIN) {
            return true;
        }
    }

    public function viewAny(User $user)
    {
        return $user->perfil_id === PERFIL_ADMIN;
    }

    public function create(User $user)
    {
        return $user->perfil_id === PERFIL_ADMIN;
    }

    public function update(User $user, User $targetUser)
    {
        // Admin pode atualizar qualquer usuário
        // Usuários normais só podem atualizar seu próprio perfil
        return $user->perfil_id === PERFIL_ADMIN || $user->id === $targetUser->id;
    }

    public function delete(User $user, User $targetUser)
    {
        return $user->perfil_id === PERFIL_ADMIN;
    }

    public function manageLibrary(User $user)
    {
        return in_array($user->perfil_id, [PERFIL_BIBLIOTECARIO, PERFIL_ADMIN]);
    }
}