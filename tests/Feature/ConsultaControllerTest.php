<?php

namespace Tests\Feature;

use App\Models\Consulta;
use App\Models\Mascota;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ConsultaControllerTest extends TestCase
{
    use RefreshDatabase;

    private const URL_GROQ = 'https://api.groq.com/openai/v1/chat/completions';

    protected function setUp(): void
    {
        parent::setUp();

        config(['services.groq.key' => 'clave-de-prueba', 'services.groq.url' => self::URL_GROQ]);
        Http::preventStrayRequests();
    }

    public function test_las_consultas_requieren_iniciar_sesion(): void
    {
        $this->get(route('consultas.index'))->assertRedirect(route('login'));
        $this->get(route('consultas.create'))->assertRedirect(route('login'));
        $this->get(route('consultas.show', 1))->assertRedirect(route('login'));
        $this->post(route('consultas.store'))->assertRedirect(route('login'));

        Http::assertNothingSent();
    }

    public function test_el_formulario_solo_ofrece_mascotas_propias_y_respeta_la_preseleccion(): void
    {
        $usuario = User::factory()->create();
        Mascota::factory()->for($usuario, 'usuario')->create(['nombre' => 'Alma']);
        $mascota = Mascota::factory()->for($usuario, 'usuario')->create(['nombre' => 'Nube']);
        Mascota::factory()->create(['nombre' => 'Ajena']);

        $this->actingAs($usuario)->get(route('consultas.create', ['mascota' => $mascota->id]))
            ->assertOk()
            ->assertViewHas('mascotaSeleccionada', $mascota->id)
            ->assertSee('Alma')
            ->assertSee('Nube')
            ->assertDontSee('Ajena');
    }

    public function test_el_asistente_invita_a_registrar_una_mascota_cuando_no_hay_perfiles(): void
    {
        $this->actingAs(User::factory()->create())->get(route('consultas.create'))
            ->assertOk()
            ->assertSee('Registra una mascota para consultar')
            ->assertSee(route('mascotas.create'));
    }

    public function test_guarda_la_respuesta_con_riesgo_alto_y_la_muestra_en_el_historial(): void
    {
        $mascota = Mascota::factory()->create(['nombre' => 'Nube']);
        $pregunta = '¿Qué hago si Nube comió algo peligroso?';
        $respuesta = 'Respuesta de prueba con <strong>texto</strong>.';

        Http::fake([self::URL_GROQ => Http::response([
            'choices' => [[
                'message' => ['content' => json_encode([
                    'respuesta' => $respuesta,
                    'nivel_riesgo' => 'alto',
                    'requiere_veterinario' => false,
                ], JSON_THROW_ON_ERROR)],
            ]],
        ])]);

        $envio = $this->actingAs($mascota->usuario)->post(route('consultas.store'), [
            'mascota_id' => $mascota->id,
            'pregunta' => $pregunta,
            'user_id' => User::factory()->create()->id,
            'nivel_riesgo' => 'bajo',
        ]);

        $consulta = Consulta::sole();
        $envio->assertRedirect(route('consultas.show', $consulta));
        $this->assertDatabaseHas('consultas', [
            'id' => $consulta->id,
            'user_id' => $mascota->user_id,
            'mascota_id' => $mascota->id,
            'mascota_nombre' => 'Nube',
            'pregunta' => $pregunta,
            'respuesta' => $respuesta,
            'nivel_riesgo' => 'alto',
            'requiere_veterinario' => true,
        ]);
        Http::assertSent(fn (Request $solicitud): bool => str_contains($solicitud['messages'][1]['content'], 'Nube')
            && str_contains($solicitud['messages'][1]['content'], $pregunta));

        $this->get(route('consultas.show', $consulta))
            ->assertOk()
            ->assertSee('Alerta de riesgo alto')
            ->assertSee($respuesta)
            ->assertDontSee($respuesta, false);

        $this->get(route('consultas.index'))
            ->assertOk()
            ->assertSee($pregunta)
            ->assertSee('Riesgo alto')
            ->assertSee(route('consultas.show', $consulta));
    }

    public function test_rechaza_una_mascota_ajena_antes_de_llamar_a_groq(): void
    {
        $ajena = Mascota::factory()->create();

        $this->actingAs(User::factory()->create())
            ->from(route('consultas.create'))
            ->post(route('consultas.store'), ['mascota_id' => $ajena->id, 'pregunta' => 'Mi consulta'])
            ->assertRedirect(route('consultas.create'))
            ->assertSessionHasErrors(['mascota_id' => 'Selecciona una de tus mascotas registradas.']);

        $this->assertDatabaseCount('consultas', 0);
        Http::assertNothingSent();
    }

    public function test_rechaza_una_pregunta_vacia_antes_de_llamar_a_groq(): void
    {
        $mascota = Mascota::factory()->create();

        $this->actingAs($mascota->usuario)
            ->from(route('consultas.create'))
            ->post(route('consultas.store'), ['mascota_id' => $mascota->id, 'pregunta' => ' '])
            ->assertRedirect(route('consultas.create'))
            ->assertSessionHasErrors('pregunta');

        $this->assertDatabaseCount('consultas', 0);
        Http::assertNothingSent();
    }

    public function test_si_groq_falla_conserva_la_pregunta_para_reintentar(): void
    {
        $mascota = Mascota::factory()->create();
        $pregunta = '¿Cómo puedo entretener a mi mascota?';
        Http::fake([self::URL_GROQ => Http::response(['error' => ['message' => 'Invalid API Key']], 401)]);

        $envio = $this->actingAs($mascota->usuario)->post(route('consultas.store'), [
            'mascota_id' => $mascota->id,
            'pregunta' => $pregunta,
        ]);

        $envio->assertRedirect(route('consultas.create', ['mascota' => $mascota->id]))
            ->assertSessionHasInput('pregunta', $pregunta)
            ->assertSessionHas('error', fn (string $mensaje): bool => str_contains($mensaje, 'no pudimos obtener una respuesta'));
        $this->assertDatabaseCount('consultas', 0);
        Http::assertSentCount(1);

        $this->get(route('consultas.create', ['mascota' => $mascota->id]))
            ->assertOk()
            ->assertSee($pregunta)
            ->assertSee('no pudimos obtener una respuesta');
    }

    public function test_una_repregunta_envia_el_contexto_de_la_conversacion_elegida(): void
    {
        $mascota = Mascota::factory()->create(['nombre' => 'Nube', 'especie' => 'gato']);
        $datos = ['user_id' => $mascota->user_id, 'mascota_id' => $mascota->id, 'mascota_nombre' => $mascota->nombre];

        $inicio = Consulta::factory()->create([
            ...$datos, 'pregunta' => '¿Qué juegos puedo hacer en casa?', 'respuesta' => 'Prueba con juguetes interactivos.',
        ]);
        $anterior = Consulta::factory()->create([
            ...$datos, 'consulta_anterior_id' => $inicio->id, 'consulta_raiz_id' => $inicio->id,
            'pregunta' => '¿Qué juegos sirven si hay poco espacio?', 'respuesta' => 'Puedes probar juegos en un rincón tranquilo.',
        ]);
        Consulta::factory()->create([
            ...$datos, 'consulta_anterior_id' => $inicio->id, 'consulta_raiz_id' => $inicio->id, 'pregunta' => 'Otra rama de la conversación',
        ]);

        Http::fake([self::URL_GROQ => Http::response([
            'choices' => [[
                'message' => ['content' => json_encode([
                    'respuesta' => 'Puedes usar un juguete que ya tenga Nube.',
                    'nivel_riesgo' => 'bajo',
                    'requiere_veterinario' => false,
                ], JSON_THROW_ON_ERROR)],
            ]],
        ])]);

        $this->actingAs($mascota->usuario)
            ->get(route('consultas.show', $anterior))
            ->assertOk()
            ->assertSee('Continuar consulta')
            ->assertSee('name="consulta_anterior_id" value="'.$anterior->id.'"', false);

        $pregunta = '¿Y si quiero jugar en el living?';
        $envio = $this->post(route('consultas.store'), [
            'mascota_id' => (string) $mascota->id,
            'consulta_anterior_id' => (string) $anterior->id,
            'pregunta' => $pregunta,
        ]);

        $repregunta = $mascota->usuario->consultas()->where('pregunta', $pregunta)->sole();
        $envio->assertRedirect(route('consultas.show', $repregunta));
        $this->assertDatabaseHas('consultas', [
            'id' => $repregunta->id,
            'mascota_id' => $mascota->id,
            'consulta_anterior_id' => $anterior->id,
            'consulta_raiz_id' => $inicio->id,
            'respuesta' => 'Puedes usar un juguete que ya tenga Nube.',
        ]);

        Http::assertSent(function (Request $solicitud) use ($inicio, $anterior, $pregunta): bool {
            $mensajes = $solicitud['messages'];
            $nuevo = $mensajes[array_key_last($mensajes)];

            return array_slice($mensajes, 1, -1) === [
                ['role' => 'user', 'content' => $inicio->pregunta],
                ['role' => 'assistant', 'content' => $inicio->respuesta],
                ['role' => 'user', 'content' => $anterior->pregunta],
                ['role' => 'assistant', 'content' => $anterior->respuesta],
            ] && $nuevo['role'] === 'user'
                && str_contains($nuevo['content'], 'Nube')
                && str_contains($nuevo['content'], $pregunta);
        });
        Http::assertSentCount(1);

        $this->get(route('consultas.show', $repregunta))
            ->assertOk()
            ->assertSee('Consulta anterior')
            ->assertSee(route('consultas.show', $anterior))
            ->assertSee('Puedes usar un juguete que ya tenga Nube.');

        $this->get(route('consultas.index'))->assertOk()->assertSee($pregunta);
    }

    public function test_rechaza_continuar_una_consulta_de_otra_mascota_o_cuenta(): void
    {
        $mascota = Mascota::factory()->create();
        $otraMascota = Mascota::factory()->for($mascota->usuario, 'usuario')->create();
        $ajena = Mascota::factory()->create();

        $anterior = Consulta::factory()->create([
            'user_id' => $mascota->user_id, 'mascota_id' => $otraMascota->id,
        ]);
        $privada = Consulta::factory()->create([
            'user_id' => $ajena->user_id, 'mascota_id' => $ajena->id,
        ]);
        $this->actingAs($mascota->usuario);

        foreach ([$anterior, $privada] as $consulta) {
            $this->from(route('consultas.create'))
                ->post(route('consultas.store'), [
                    'mascota_id' => $mascota->id,
                    'consulta_anterior_id' => $consulta->id,
                    'pregunta' => '¿Y cómo sigo?',
                ])
                ->assertRedirect(route('consultas.create'))
                ->assertSessionHasErrors(['consulta_anterior_id' => 'Selecciona una consulta anterior de esta mascota.']);
        }

        $this->assertDatabaseCount('consultas', 2);
        Http::assertNothingSent();
    }

    public function test_si_groq_falla_la_repregunta_vuelve_a_la_consulta_anterior(): void
    {
        $mascota = Mascota::factory()->create();
        $anterior = Consulta::factory()->create([
            'user_id' => $mascota->user_id, 'mascota_id' => $mascota->id,
        ]);
        $pregunta = '¿Y si tiene poco espacio para jugar?';

        Http::fake([self::URL_GROQ => Http::response(['error' => ['message' => 'Invalid API Key']], 401)]);

        $this->actingAs($mascota->usuario)->post(route('consultas.store'), [
            'mascota_id' => (string) $mascota->id,
            'consulta_anterior_id' => (string) $anterior->id,
            'pregunta' => $pregunta,
        ])->assertRedirect(route('consultas.show', $anterior))
            ->assertSessionHasInput('pregunta', $pregunta)
            ->assertSessionHasInput('consulta_anterior_id', (string) $anterior->id);

        $this->get(route('consultas.show', $anterior))->assertOk()->assertSee($pregunta);
        $this->assertDatabaseCount('consultas', 1);
    }

    public function test_el_historial_combina_la_busqueda_con_el_filtro_de_mascota(): void
    {
        $usuario = User::factory()->create();
        $nube = Mascota::factory()->for($usuario, 'usuario')->create();
        $luna = Mascota::factory()->for($usuario, 'usuario')->create();

        Consulta::factory()->create(['user_id' => $usuario->id, 'mascota_id' => $nube->id, 'pregunta' => 'Juegos para Nube']);
        Consulta::factory()->create(['user_id' => $usuario->id, 'mascota_id' => $nube->id, 'pregunta' => 'Cuidados para Nube']);
        Consulta::factory()->create(['user_id' => $usuario->id, 'mascota_id' => $luna->id, 'pregunta' => 'Juegos para Luna']);

        $this->actingAs($usuario)
            ->get(route('consultas.index', ['mascota' => $nube->id, 'buscar' => 'juegos']))
            ->assertOk()
            ->assertSee('Juegos para Nube')
            ->assertDontSee('Cuidados para Nube')
            ->assertDontSee('Juegos para Luna');
    }

    public function test_historial_agrupa_las_repreguntas_y_busca_en_toda_la_conversacion(): void
    {
        $mascota = Mascota::factory()->create();
        $datos = ['user_id' => $mascota->user_id, 'mascota_id' => $mascota->id];

        $raiz = Consulta::factory()->create([...$datos, 'pregunta' => '¿Juegos para mi gato?']);
        $intermedia = Consulta::factory()->create([
            ...$datos, 'consulta_raiz_id' => $raiz->id, 'consulta_anterior_id' => $raiz->id,
            'pregunta' => '¿Con espacio reducido?', 'nivel_riesgo' => 'alto', 'requiere_veterinario' => true,
        ]);
        $ultima = Consulta::factory()->create([
            ...$datos, 'consulta_raiz_id' => $raiz->id, 'consulta_anterior_id' => $intermedia->id,
            'pregunta' => '¿Y con sus juguetes?',
        ]);
        Consulta::factory()->create([...$datos, 'pregunta' => '¿Qué hago con su comedero?']);
        $this->actingAs($mascota->usuario);

        $this->get(route('consultas.index'))->assertOk()
            ->assertSee('2 conversaciones')
            ->assertSee('3 intercambios')
            ->assertSee('Riesgo alto')
            ->assertSee(route('consultas.show', $ultima))
            ->assertDontSee('href="'.route('consultas.show', $intermedia).'"', false);

        $this->get(route('consultas.index', ['buscar' => 'espacio reducido']))
            ->assertOk()
            ->assertSee('1 conversación')
            ->assertSee($raiz->pregunta)
            ->assertSee(route('consultas.show', $ultima))
            ->assertDontSee('¿Qué hago con su comedero?');

        $this->get(route('consultas.show', $ultima))
            ->assertOk()
            ->assertSeeInOrder([$raiz->pregunta, $intermedia->pregunta, $ultima->pregunta]);
    }

    public function test_un_favorito_agrupa_la_conversacion_y_se_puede_quitar(): void
    {
        $mascota = Mascota::factory()->create();
        $datos = ['user_id' => $mascota->user_id, 'mascota_id' => $mascota->id];
        $raiz = Consulta::factory()->create([...$datos, 'pregunta' => 'Conversación con favorito']);
        $ultima = Consulta::factory()->create([
            ...$datos, 'consulta_raiz_id' => $raiz->id, 'consulta_anterior_id' => $raiz->id,
        ]);
        Consulta::factory()->create([...$datos, 'pregunta' => 'Conversación sin guardar']);

        $this->actingAs($mascota->usuario)->from(route('consultas.show', $ultima))
            ->patch(route('consultas.favorita', $raiz), ['favorita' => '1'])
            ->assertRedirect(route('consultas.show', $ultima));

        $this->assertTrue($raiz->fresh()->favorita);
        $this->get(route('consultas.index', ['favoritas' => 1]))->assertOk()
            ->assertSee($raiz->pregunta)
            ->assertSee(route('consultas.show', $ultima))
            ->assertDontSee('Conversación sin guardar');

        $this->from(route('consultas.show', $ultima))
            ->patch(route('consultas.favorita', $raiz), ['favorita' => '0'])
            ->assertRedirect(route('consultas.show', $ultima));
        $this->assertFalse($raiz->fresh()->favorita);

        $this->get(route('consultas.index', ['favoritas' => 1]))
            ->assertOk()
            ->assertSee('Aún no hay respuestas favoritas');
    }

    public function test_no_expone_consultas_de_otra_cuenta(): void
    {
        $ajena = Consulta::factory()->create(['pregunta' => 'Pregunta privada de otra cuenta']);
        $this->actingAs(User::factory()->create());

        $this->get(route('consultas.index'))->assertOk()->assertDontSee($ajena->pregunta);
        $this->get(route('consultas.show', $ajena))->assertNotFound();
        $this->patch(route('consultas.favorita', $ajena), ['favorita' => 1])->assertNotFound();
        $this->assertFalse($ajena->fresh()->favorita);
    }

    public function test_el_historial_conserva_la_consulta_si_se_elimina_la_mascota(): void
    {
        $mascota = Mascota::factory()->create(['nombre' => 'Nube']);
        $consulta = Consulta::factory()->create([
            'user_id' => $mascota->user_id,
            'mascota_id' => $mascota->id,
            'mascota_nombre' => $mascota->nombre,
        ]);

        $this->actingAs($mascota->usuario)
            ->delete(route('mascotas.destroy', $mascota))
            ->assertRedirect(route('mascotas.index'));

        $this->assertDatabaseHas('consultas', ['id' => $consulta->id, 'mascota_id' => null]);
        $this->get(route('consultas.show', $consulta))->assertOk()
            ->assertSee('Respuesta para Nube')
            ->assertDontSee('Continuar consulta');
    }
}
