<?php

namespace Tests\Feature;

use App\Models\EntradaBitacora;
use App\Models\Mascota;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BitacoraControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_registra_edita_y_elimina_una_entrada_de_su_mascota(): void
    {
        $mascota = Mascota::factory()->create(['nombre' => 'Nube']);
        $otra = EntradaBitacora::factory()->create(['nota' => 'Nota de otra mascota']);
        $bitacora = route('mascotas.bitacora.index', $mascota);
        $datos = ['tipo' => 'visita_veterinaria', 'fecha' => '2026-11-15', 'nota' => 'Primera visita de Nube.'];

        $this->actingAs($mascota->usuario)
            ->get(route('mascotas.show', $mascota))
            ->assertOk()
            ->assertSee('Abrir bitácora')
            ->assertSee($bitacora);

        $this->get($bitacora)->assertOk()->assertSee('Aún no hay entradas')->assertDontSee($otra->nota);
        $this->post(route('mascotas.bitacora.store', $mascota), $datos)
            ->assertRedirect($bitacora);

        $entrada = $mascota->entradas()->sole();
        $this->assertSame($datos['fecha'], $entrada->fecha->toDateString());
        $this->get($bitacora)->assertOk()->assertSee($datos['nota'])->assertSee('Visita al veterinario');

        $this->get(route('mascotas.bitacora.edit', [$mascota, $entrada]))
            ->assertOk()
            ->assertSee($datos['nota']);

        $this->patch(route('mascotas.bitacora.update', [$mascota, $entrada]), [
            ...$datos, 'nota' => 'Nota actualizada.',
        ])->assertRedirect($bitacora);
        $this->assertSame('Nota actualizada.', $entrada->fresh()->nota);

        $this->delete(route('mascotas.bitacora.destroy', [$mascota, $entrada]))->assertRedirect($bitacora);
        $this->assertModelMissing($entrada);
        $this->assertModelExists($otra);
    }

    public function test_valida_fecha_tipo_y_nota(): void
    {
        $mascota = Mascota::factory()->create();

        $this->actingAs($mascota->usuario)->from(route('mascotas.bitacora.index', $mascota))
            ->post(route('mascotas.bitacora.store', $mascota), [
                'fecha' => '2026-02-31', 'tipo' => 'inventado', 'nota' => ['valor inválido'],
            ])->assertSessionHasErrors(['fecha', 'tipo', 'nota']);
        $this->assertDatabaseCount('entradas_bitacora', 0);
    }

    public function test_protege_la_bitacora_de_otra_cuenta(): void
    {
        $usuario = User::factory()->create();
        $propia = Mascota::factory()->for($usuario, 'usuario')->create();
        $ajena = Mascota::factory()->create();
        $entrada = EntradaBitacora::factory()->create(['mascota_id' => $ajena->id]);

        $this->get(route('mascotas.bitacora.index', $propia))->assertRedirect(route('login'));
        $this->actingAs($usuario);

        $this->get(route('mascotas.bitacora.index', $ajena))->assertForbidden();
        $this->post(route('mascotas.bitacora.store', $ajena))->assertForbidden();
        $this->get(route('mascotas.bitacora.edit', [$ajena, $entrada]))->assertForbidden();
        $this->patch(route('mascotas.bitacora.update', [$ajena, $entrada]))->assertForbidden();
        $this->delete(route('mascotas.bitacora.destroy', [$ajena, $entrada]))->assertForbidden();
        $this->delete(route('mascotas.bitacora.destroy', [$propia, $entrada]))->assertNotFound();

        $this->assertModelExists($entrada);
    }
}
