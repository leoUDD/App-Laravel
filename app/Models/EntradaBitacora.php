<?php

namespace App\Models;

use Database\Factories\EntradaBitacoraFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['fecha', 'tipo', 'nota'])]
class EntradaBitacora extends Model
{
    /** @use HasFactory<EntradaBitacoraFactory> */
    use HasFactory;

    protected $table = 'entradas_bitacora';

    public const TIPOS = [
        'visita_veterinaria' => 'Visita al veterinario',
        'cambio_alimento' => 'Cambio de alimento',
        'observacion' => 'Observación',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return ['fecha' => 'date'];
    }

    public function tipoTexto(): string
    {
        return self::TIPOS[$this->tipo];
    }
}
