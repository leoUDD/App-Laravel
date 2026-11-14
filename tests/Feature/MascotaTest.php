<?php

namespace Tests\Feature;

use App\Models\Mascota;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MascotaTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array{nombre: string, especie: string, raza: string, edad: int, peso: float}
     */
    private function datosValidos(): array
    {
        return [
            'nombre' => 'Firulais',
            'especie' => 'perro',
            'raza' => 'Mestizo',
            'edad' => 3,
            'peso' => 12.5,
        ];
    }

    public function test_el_listado_muestra_solo_las_mascotas_del_usuario(): void
    {
        $usuario = User::factory()->create();
        Mascota::factory()->for($usuario, 'usuario')->create(['nombre' => 'Michi']);
        Mascota::factory()->create(['nombre' => 'Ajena']);

        $this->actingAs($usuario)->get(route('mascotas.index'))
            ->assertOk()
            ->assertSee('Michi')
            ->assertDontSee('Ajena');
    }

    public function test_un_usuario_puede_registrar_una_mascota(): void
    {
        $usuario = User::factory()->create();

        $response = $this->actingAs($usuario)->post(route('mascotas.store'), $this->datosValidos());

        $mascota = Mascota::firstOrFail();
        $response->assertRedirect(route('mascotas.show', $mascota));
        $this->assertSame($usuario->id, $mascota->user_id);
        $this->assertSame('Firulais', $mascota->nombre);
    }

    public function test_valida_los_datos_de_la_mascota(): void
    {
        $usuario = User::factory()->create();

        $response = $this->actingAs($usuario)->post(route('mascotas.store'), [
            'nombre' => '',
            'especie' => 'tortuga',
            'raza' => '',
            'edad' => -1,
            'peso' => 0,
        ]);

        $response->assertSessionHasErrors(['nombre', 'especie', 'raza', 'edad', 'peso']);
        $this->assertDatabaseCount('mascotas', 0);
    }

    public function test_un_usuario_puede_ver_y_editar_su_mascota(): void
    {
        $mascota = Mascota::factory()->create();

        $this->actingAs($mascota->usuario)->get(route('mascotas.show', $mascota))->assertOk()->assertSee($mascota->nombre);
        $this->actingAs($mascota->usuario)->get(route('mascotas.edit', $mascota))->assertOk();

        $response = $this->actingAs($mascota->usuario)
            ->put(route('mascotas.update', $mascota), [...$this->datosValidos(), 'nombre' => 'Toby']);

        $response->assertRedirect(route('mascotas.show', $mascota));
        $this->assertSame('Toby', $mascota->fresh()->nombre);
    }

    public function test_un_usuario_puede_eliminar_su_mascota(): void
    {
        $mascota = Mascota::factory()->create();

        $response = $this->actingAs($mascota->usuario)->delete(route('mascotas.destroy', $mascota));

        $response->assertRedirect(route('mascotas.index'));
        $this->assertModelMissing($mascota);
    }

    public function test_un_usuario_no_puede_acceder_a_mascotas_ajenas(): void
    {
        $mascota = Mascota::factory()->create();
        $otroUsuario = User::factory()->create();

        $this->actingAs($otroUsuario)->get(route('mascotas.show', $mascota))->assertForbidden();
        $this->actingAs($otroUsuario)->get(route('mascotas.edit', $mascota))->assertForbidden();
        $this->actingAs($otroUsuario)->put(route('mascotas.update', $mascota), $this->datosValidos())->assertForbidden();
        $this->actingAs($otroUsuario)->delete(route('mascotas.destroy', $mascota))->assertForbidden();

        $this->assertModelExists($mascota);
    }
}
