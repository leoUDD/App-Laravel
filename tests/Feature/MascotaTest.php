<?php

namespace Tests\Feature;

use App\Models\Mascota;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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

    public function test_un_usuario_puede_registrar_una_mascota_con_foto(): void
    {
        Storage::fake('public');
        $usuario = User::factory()->create();

        $this->actingAs($usuario)->post(route('mascotas.store'), [
            ...$this->datosValidos(),
            'foto' => UploadedFile::fake()->image('firulais.jpg', 600, 600),
        ])->assertSessionHasNoErrors();

        $mascota = Mascota::firstOrFail();
        $this->assertNotNull($mascota->foto);
        Storage::disk('public')->assertExists($mascota->foto);

        $this->actingAs($usuario)->get(route('mascotas.show', $mascota))
            ->assertOk()
            ->assertSee($mascota->urlFoto());
    }

    public function test_la_foto_debe_ser_una_imagen_permitida(): void
    {
        Storage::fake('public');
        $usuario = User::factory()->create();

        $this->actingAs($usuario)->post(route('mascotas.store'), [
            ...$this->datosValidos(),
            'foto' => UploadedFile::fake()->create('documento.pdf', 100, 'application/pdf'),
        ])->assertSessionHasErrors('foto');

        $this->actingAs($usuario)->post(route('mascotas.store'), [
            ...$this->datosValidos(),
            'foto' => UploadedFile::fake()->image('enorme.jpg')->size(5000),
        ])->assertSessionHasErrors('foto');

        $this->assertDatabaseCount('mascotas', 0);
    }

    public function test_al_cambiar_la_foto_se_borra_la_anterior(): void
    {
        Storage::fake('public');
        $mascota = Mascota::factory()->create([
            'foto' => UploadedFile::fake()->image('antigua.jpg')->store('mascotas', 'public'),
        ]);
        $fotoAnterior = $mascota->foto;

        $this->actingAs($mascota->usuario)->put(route('mascotas.update', $mascota), [
            ...$this->datosValidos(),
            'foto' => UploadedFile::fake()->image('nueva.png'),
        ])->assertSessionHasNoErrors();

        $mascota->refresh();
        $this->assertNotSame($fotoAnterior, $mascota->foto);
        Storage::disk('public')->assertMissing($fotoAnterior);
        Storage::disk('public')->assertExists($mascota->foto);
    }

    public function test_se_puede_quitar_la_foto_sin_subir_otra(): void
    {
        Storage::fake('public');
        $mascota = Mascota::factory()->create([
            'foto' => UploadedFile::fake()->image('antigua.jpg')->store('mascotas', 'public'),
        ]);
        $fotoAnterior = $mascota->foto;

        $this->actingAs($mascota->usuario)->put(route('mascotas.update', $mascota), [
            ...$this->datosValidos(),
            'quitar_foto' => '1',
        ])->assertSessionHasNoErrors();

        $this->assertNull($mascota->fresh()->foto);
        Storage::disk('public')->assertMissing($fotoAnterior);
    }

    public function test_editar_sin_foto_mantiene_la_foto_actual(): void
    {
        Storage::fake('public');
        $mascota = Mascota::factory()->create([
            'foto' => UploadedFile::fake()->image('actual.jpg')->store('mascotas', 'public'),
        ]);
        $foto = $mascota->foto;

        $this->actingAs($mascota->usuario)
            ->put(route('mascotas.update', $mascota), $this->datosValidos())
            ->assertSessionHasNoErrors();

        $this->assertSame($foto, $mascota->fresh()->foto);
        Storage::disk('public')->assertExists($foto);
    }

    public function test_al_eliminar_la_mascota_se_borra_su_foto(): void
    {
        Storage::fake('public');
        $mascota = Mascota::factory()->create([
            'foto' => UploadedFile::fake()->image('foto.jpg')->store('mascotas', 'public'),
        ]);

        $this->actingAs($mascota->usuario)->delete(route('mascotas.destroy', $mascota));

        Storage::disk('public')->assertMissing($mascota->foto);
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
