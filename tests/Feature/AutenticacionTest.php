<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AutenticacionTest extends TestCase
{
    use RefreshDatabase;

    public function test_la_raiz_muestra_la_bienvenida_si_no_hay_sesion(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('El bienestar de tu mascota comienza aquí')
            ->assertSee(route('login'))
            ->assertSee(route('registro'));

        $this->get('/mascotas')->assertRedirect(route('login'));
    }

    public function test_la_raiz_redirige_a_mis_mascotas_si_hay_sesion(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/')
            ->assertRedirect(route('mascotas.index'));
    }

    public function test_muestra_los_formularios_de_registro_y_login(): void
    {
        $this->get(route('registro'))->assertOk()->assertSee('Crear cuenta');
        $this->get(route('login'))->assertOk()->assertSee('Iniciar sesión');
    }

    public function test_usa_la_url_publica_en_los_enlaces_y_redirecciones_de_desarrollo(): void
    {
        $publica = 'https://petcare-8000.app.github.dev';
        $interna = 'http://localhost:8000';

        $this->app->instance('env', 'local');
        config(['app.url' => $publica]);

        $this->get($interna.'/')->assertOk()
            ->assertSee('href="'.$publica.'/login"', false)
            ->assertSee('href="'.$publica.'/registro"', false);

        $this->get($interna.'/login')->assertOk()->assertSee('action="'.$publica.'/login"', false);
        $this->get($interna.'/registro')->assertOk()->assertSee('action="'.$publica.'/registro"', false);
        $this->get($interna.'/mascotas')->assertRedirect($publica.'/login');

        $this->assertSame($publica.'/mascotas', session('url.intended'));

        $usuario = User::factory()->create();
        $this->withHeader('Sec-Fetch-Site', 'same-origin')
            ->post($interna.'/login', ['email' => $usuario->email, 'password' => 'password'])
            ->assertRedirect($publica.'/mascotas');

        $this->assertAuthenticatedAs($usuario);
    }

    public function test_un_usuario_puede_registrarse(): void
    {
        $response = $this->post(route('registro'), [
            'name' => 'Ana Pérez',
            'email' => 'ana@example.com',
            'password' => 'secreto123',
            'password_confirmation' => 'secreto123',
        ]);

        $response->assertRedirect(route('mascotas.index'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', ['email' => 'ana@example.com']);
    }

    public function test_el_registro_valida_correo_unico_y_confirmacion(): void
    {
        User::factory()->create(['email' => 'ana@example.com']);

        $response = $this->post(route('registro'), [
            'name' => 'Ana',
            'email' => 'ana@example.com',
            'password' => 'secreto123',
            'password_confirmation' => 'otra-clave',
        ]);

        $response->assertSessionHasErrors(['email', 'password']);
        $this->assertGuest();
    }

    public function test_un_usuario_puede_iniciar_sesion(): void
    {
        $usuario = User::factory()->create();

        $response = $this->post(route('login'), [
            'email' => $usuario->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('mascotas.index'));
        $this->assertAuthenticatedAs($usuario);
    }

    public function test_no_inicia_sesion_con_contrasena_incorrecta(): void
    {
        $usuario = User::factory()->create();

        $response = $this->post(route('login'), [
            'email' => $usuario->email,
            'password' => 'incorrecta',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_un_usuario_puede_cerrar_sesion(): void
    {
        $usuario = User::factory()->create();

        $response = $this->actingAs($usuario)->post(route('logout'));

        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }
}
