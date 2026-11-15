<?php

namespace App\Policies;

use App\Models\Mascota;
use App\Models\User;

class MascotaPolicy
{
    /**
     * Un usuario solo puede ver sus propias mascotas.
     */
    public function view(User $user, Mascota $mascota): bool
    {
        return $user->id === $mascota->user_id;
    }

    /**
     * Un usuario solo puede editar sus propias mascotas.
     */
    public function update(User $user, Mascota $mascota): bool
    {
        return $user->id === $mascota->user_id;
    }

    /**
     * Un usuario solo puede eliminar sus propias mascotas.
     */
    public function delete(User $user, Mascota $mascota): bool
    {
        return $user->id === $mascota->user_id;
    }
}
