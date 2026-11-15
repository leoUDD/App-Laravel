<?php

namespace App\Services\IA;

/**
 * Respuesta estructurada que entrega la IA para una consulta.
 */
final readonly class RespuestaIa
{
    /**
     * Niveles de riesgo posibles.
     *
     * @var list<string>
     */
    public const NIVELES_RIESGO = ['bajo', 'medio', 'alto'];

    public function __construct(
        public string $respuesta,
        public string $nivelRiesgo,
        public bool $requiereVeterinario,
    ) {}
}
