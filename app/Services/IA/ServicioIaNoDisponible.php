<?php

namespace App\Services\IA;

use RuntimeException;

/**
 * Se lanza cuando no se pudo obtener una respuesta válida de la IA
 * (sin clave configurada, sin conexión, límite de uso, error del servidor o respuesta inválida).
 *
 * El mensaje de la excepción es técnico (para los logs); para el usuario se usa mensajeParaUsuario().
 */
class ServicioIaNoDisponible extends RuntimeException
{
    public function mensajeParaUsuario(): string
    {
        return 'En este momento no pudimos obtener una respuesta del asistente. Intenta nuevamente en unos minutos. '
            .'Si tu mascota presenta síntomas preocupantes o ingirió algo peligroso, contacta de inmediato a un médico veterinario.';
    }
}
