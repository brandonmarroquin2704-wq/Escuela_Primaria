<?php

namespace App\Policies;

use App\Models\Comunicado;
use App\Models\User;

class ComunicadoPolicy
{
    private function esAutor(User $user, Comunicado $comunicado): bool
    {
        $id = $user->usuario?->idUsuario;

        return $id !== null && (int) $id === (int) $comunicado->docente_idUsuario;
    }

    public function update(User $user, Comunicado $comunicado): bool
    {
        return $this->esAutor($user, $comunicado);
    }

    public function delete(User $user, Comunicado $comunicado): bool
    {
        return $this->esAutor($user, $comunicado);
    }
}