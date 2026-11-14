<?php

namespace Tests\Feature;

use App\Models\Mascota;
use App\Services\IA\GroqService;
use App\Services\IA\ServicioIaNoDisponible;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Sleep;
use Tests\TestCase;

class GroqServiceTest extends TestCase
{
    private const URL_GROQ = 'https://api.groq.com/openai/v1/chat/completions';

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.groq.key' => 'clave-de-prueba',
            'services.groq.url' => self::URL_GROQ,
            'services.groq.model' => 'openai/gpt-oss-20b',
        ]);

        Http::preventStrayRequests();
        Sleep::fake();
    }

    /**
     * Respuesta simulada de Groq con el contenido JSON indicado.
     *
     * @param  array<string, mixed>|string  $contenido
     * @return array<string, mixed>
     */
    private function respuestaDeGroq(array|string $contenido): array
    {
        return [
            'choices' => [[
                'message' => [
                    'role' => 'assistant',
                    'content' => is_string($contenido) ? $contenido : json_encode($contenido),
                ],
                'finish_reason' => 'stop',
            ]],
        ];
    }

    public function test_envia_la_pregunta_con_los_datos_de_la_mascota_y_devuelve_la_respuesta(): void
    {
        Http::fake([self::URL_GROQ => Http::response($this->respuestaDeGroq([
            'respuesta' => 'Puedes darle zanahoria cocida en trozos pequeños.',
            'nivel_riesgo' => 'bajo',
            'requiere_veterinario' => false,
        ]))]);

        $mascota = Mascota::factory()->make([
            'user_id' => 1,
            'nombre' => 'Firulais',
            'especie' => 'perro',
            'raza' => 'Labrador',
            'edad' => 3,
            'peso' => 24.5,
        ]);

        $respuesta = app(GroqService::class)->consultar('¿Puede comer zanahoria?', $mascota);

        $this->assertSame('Puedes darle zanahoria cocida en trozos pequeños.', $respuesta->respuesta);
        $this->assertSame('bajo', $respuesta->nivelRiesgo);
        $this->assertFalse($respuesta->requiereVeterinario);

        Http::assertSent(function (Request $request): bool {
            $mensajeUsuario = $request['messages'][1]['content'];

            return $request->hasHeader('Authorization', 'Bearer clave-de-prueba')
                && $request['model'] === 'openai/gpt-oss-20b'
                && $request['response_format']['type'] === 'json_schema'
                && str_contains($mensajeUsuario, 'Firulais')
                && str_contains($mensajeUsuario, 'Perro')
                && str_contains($mensajeUsuario, 'Labrador')
                && str_contains($mensajeUsuario, '3 años')
                && str_contains($mensajeUsuario, '24.50 kg')
                && str_contains($mensajeUsuario, '¿Puede comer zanahoria?');
        });
    }

    public function test_un_riesgo_alto_siempre_requiere_veterinario(): void
    {
        Http::fake([self::URL_GROQ => Http::response($this->respuestaDeGroq([
            'respuesta' => 'El chocolate es tóxico para los perros.',
            'nivel_riesgo' => 'alto',
            'requiere_veterinario' => false,
        ]))]);

        $respuesta = app(GroqService::class)->consultar('Mi perro comió chocolate', Mascota::factory()->make(['user_id' => 1]));

        $this->assertSame('alto', $respuesta->nivelRiesgo);
        $this->assertTrue($respuesta->requiereVeterinario);
    }

    public function test_falla_si_no_hay_clave_configurada(): void
    {
        config(['services.groq.key' => null]);
        Http::fake();

        $this->expectException(ServicioIaNoDisponible::class);

        try {
            app(GroqService::class)->consultar('Hola');
        } finally {
            Http::assertNothingSent();
        }
    }

    public function test_falla_si_no_hay_conexion(): void
    {
        Http::fake([self::URL_GROQ => Http::failedConnection()]);

        $this->expectException(ServicioIaNoDisponible::class);

        app(GroqService::class)->consultar('Hola');
    }

    public function test_reintenta_ante_errores_temporales_y_luego_falla(): void
    {
        Http::fake([self::URL_GROQ => Http::response(['error' => ['message' => 'Rate limit']], 429)]);

        try {
            app(GroqService::class)->consultar('Hola');
            $this->fail('Se esperaba ServicioIaNoDisponible.');
        } catch (ServicioIaNoDisponible) {
            Http::assertSentCount(3);
        }
    }

    public function test_no_reintenta_si_la_clave_es_invalida(): void
    {
        Http::fake([self::URL_GROQ => Http::response(['error' => ['message' => 'Invalid API Key']], 401)]);

        try {
            app(GroqService::class)->consultar('Hola');
            $this->fail('Se esperaba ServicioIaNoDisponible.');
        } catch (ServicioIaNoDisponible) {
            Http::assertSentCount(1);
        }
    }

    public function test_falla_si_la_respuesta_no_tiene_el_formato_esperado(): void
    {
        Http::fake([self::URL_GROQ => Http::response($this->respuestaDeGroq('esto no es JSON'))]);

        $this->expectException(ServicioIaNoDisponible::class);

        app(GroqService::class)->consultar('Hola');
    }

    public function test_el_mensaje_para_el_usuario_recomienda_al_veterinario(): void
    {
        $this->assertStringContainsString('veterinario', (new ServicioIaNoDisponible)->mensajeParaUsuario());
    }
}
