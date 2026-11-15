<?php

namespace App\Services\IA;

use App\Models\Mascota;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Cliente de la API de Groq: envía la pregunta del usuario junto con los datos de su mascota
 * y devuelve una respuesta estructurada (respuesta, nivel de riesgo y si requiere veterinario).
 */
class GroqService
{
    /**
     * Instrucciones de sistema que definen cómo debe responder el asistente.
     */
    private const PROMPT_SISTEMA = <<<'TEXTO'
        Eres PetCare AI, un asistente que orienta a dueños y cuidadores de perros y gatos sobre alimentación, cuidados diarios, comportamiento y prevención.

        Reglas:
        - Responde siempre en español, con un tono cercano, claro y práctico. Usa párrafos cortos o listas breves (máximo unas 200 palabras).
        - Usa los datos de la mascota (especie, raza, edad y peso) para personalizar la respuesta cuando sean relevantes.
        - Si hay mensajes anteriores, úsalos para interpretar la última pregunta.
        - Entregas orientación general: no diagnosticas enfermedades, no recetas medicamentos ni indicas dosis, y no reemplazas a un médico veterinario.
        - Si la consulta menciona alimentos o sustancias potencialmente tóxicos (por ejemplo chocolate, uvas o pasas, cebolla, ajo, xilitol, alcohol, cafeína, medicamentos humanos, productos de limpieza, venenos o ciertas plantas), explica el riesgo con prudencia y recomienda contactar de inmediato a un veterinario.
        - Si hay señales de urgencia (dificultad para respirar, convulsiones, vómitos o diarrea con sangre, decaimiento marcado, dolor intenso, traumatismos, sospecha de intoxicación), indica que debe acudir de inmediato a un veterinario.
        - Si la pregunta no trata sobre el cuidado de perros o gatos, indícalo amablemente y no la respondas.

        Clasifica la consulta:
        - nivel_riesgo "bajo": duda cotidiana sin riesgo para la salud.
        - nivel_riesgo "medio": situación que conviene vigilar o consultar con un veterinario pronto.
        - nivel_riesgo "alto": posible intoxicación o urgencia.
        - requiere_veterinario: true si se debe contactar a un veterinario (siempre true cuando el riesgo es "alto").

        Responde únicamente en formato JSON con los campos "respuesta", "nivel_riesgo" y "requiere_veterinario".
        TEXTO;

    /**
     * Envía una consulta a la IA, con los datos de la mascota como contexto si se indica.
     *
     * @param  list<array{pregunta: string, respuesta: string}>  $historial
     *
     * @throws ServicioIaNoDisponible
     */
    public function consultar(string $pregunta, ?Mascota $mascota = null, array $historial = []): RespuestaIa
    {
        $clave = config('services.groq.key');

        if (blank($clave)) {
            throw new ServicioIaNoDisponible('Falta configurar GROQ_API_KEY en el archivo .env.');
        }

        try {
            $respuesta = Http::withToken($clave)
                ->acceptJson()
                ->connectTimeout(5)
                ->timeout(config('services.groq.timeout'))
                ->retry([500, 1500], 0, fn (Throwable $error): bool => $this->esErrorTemporal($error))
                ->post(config('services.groq.url'), $this->cuerpoDeLaSolicitud($pregunta, $mascota, $historial));
        } catch (ConnectionException $error) {
            $this->registrarError('No se pudo conectar con Groq.', ['error' => $error->getMessage()]);

            throw new ServicioIaNoDisponible('No se pudo conectar con Groq.', previous: $error);
        } catch (RequestException $error) {
            $this->registrarError('Groq respondió con un error.', [
                'estado' => $error->response->status(),
                'detalle' => $error->response->json('error.message'),
            ]);

            throw new ServicioIaNoDisponible("Groq respondió con el estado {$error->response->status()}.", previous: $error);
        }

        return $this->interpretarRespuesta($respuesta);
    }

    /**
     * Arma el cuerpo de la solicitud para el endpoint de chat completions.
     *
     * @param  list<array{pregunta: string, respuesta: string}>  $historial
     * @return array<string, mixed>
     */
    private function cuerpoDeLaSolicitud(string $pregunta, ?Mascota $mascota, array $historial): array
    {
        $mensajes = [['role' => 'system', 'content' => self::PROMPT_SISTEMA]];

        foreach ($historial as $consulta) {
            $mensajes[] = ['role' => 'user', 'content' => $consulta['pregunta']];
            $mensajes[] = ['role' => 'assistant', 'content' => $consulta['respuesta']];
        }

        $mensajes[] = ['role' => 'user', 'content' => $this->mensajeDelUsuario($pregunta, $mascota)];

        return [
            'model' => config('services.groq.model'),
            'temperature' => 0.3,
            'max_completion_tokens' => 2048,
            'messages' => $mensajes,
            'response_format' => [
                'type' => 'json_schema',
                'json_schema' => [
                    'name' => 'respuesta_petcare',
                    'strict' => true,
                    'schema' => [
                        'type' => 'object',
                        'properties' => [
                            'respuesta' => ['type' => 'string'],
                            'nivel_riesgo' => ['type' => 'string', 'enum' => RespuestaIa::NIVELES_RIESGO],
                            'requiere_veterinario' => ['type' => 'boolean'],
                        ],
                        'required' => ['respuesta', 'nivel_riesgo', 'requiere_veterinario'],
                        'additionalProperties' => false,
                    ],
                ],
            ],
        ];
    }

    /**
     * Mensaje del usuario con los antecedentes de la mascota y la pregunta.
     */
    private function mensajeDelUsuario(string $pregunta, ?Mascota $mascota): string
    {
        if ($mascota === null) {
            return "Pregunta: {$pregunta}";
        }

        $edad = $mascota->edad === 1 ? '1 año' : "{$mascota->edad} años";

        return <<<TEXTO
            Datos de la mascota:
            - Nombre: {$mascota->nombre}
            - Especie: {$mascota->especieTexto()}
            - Raza: {$mascota->raza}
            - Edad: {$edad}
            - Peso: {$mascota->peso} kg

            Pregunta: {$pregunta}
            TEXTO;
    }

    /**
     * Convierte la respuesta HTTP de Groq en una RespuestaIa, validando su estructura.
     *
     * @throws ServicioIaNoDisponible
     */
    private function interpretarRespuesta(Response $respuesta): RespuestaIa
    {
        $contenido = $respuesta->json('choices.0.message.content');
        $datos = is_string($contenido) ? json_decode($contenido, true) : null;

        $esValida = is_array($datos)
            && is_string($datos['respuesta'] ?? null)
            && trim($datos['respuesta']) !== ''
            && in_array($datos['nivel_riesgo'] ?? null, RespuestaIa::NIVELES_RIESGO, true)
            && is_bool($datos['requiere_veterinario'] ?? null);

        if (! $esValida) {
            $this->registrarError('Groq devolvió una respuesta con un formato inesperado.', [
                'contenido' => is_string($contenido) ? mb_substr($contenido, 0, 500) : null,
                'motivo_fin' => $respuesta->json('choices.0.finish_reason'),
            ]);

            throw new ServicioIaNoDisponible('Groq devolvió una respuesta con un formato inesperado.');
        }

        return new RespuestaIa(
            respuesta: trim($datos['respuesta']),
            nivelRiesgo: $datos['nivel_riesgo'],
            requiereVeterinario: $datos['requiere_veterinario'] || $datos['nivel_riesgo'] === 'alto',
        );
    }

    /**
     * Indica si vale la pena reintentar: fallas de conexión, límite de uso (429) o errores del servidor (5xx).
     */
    private function esErrorTemporal(Throwable $error): bool
    {
        return $error instanceof ConnectionException
            || ($error instanceof RequestException
                && ($error->response->serverError() || $error->response->status() === 429));
    }

    /**
     * Registra el error en el log de Laravel (sin incluir la clave de la API).
     *
     * @param  array<string, mixed>  $contexto
     */
    private function registrarError(string $mensaje, array $contexto = []): void
    {
        Log::warning("[GroqService] {$mensaje}", $contexto);
    }
}
