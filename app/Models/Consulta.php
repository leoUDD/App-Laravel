<?php

namespace App\Models;

use Database\Factories\ConsultaFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['mascota_id', 'mascota_nombre', 'consulta_anterior_id', 'consulta_raiz_id', 'pregunta', 'respuesta', 'nivel_riesgo', 'requiere_veterinario'])]
class Consulta extends Model
{
    /** @use HasFactory<ConsultaFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'requiere_veterinario' => 'boolean',
            'favorita' => 'boolean',
        ];
    }
}
