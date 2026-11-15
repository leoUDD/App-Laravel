<?php

namespace Database\Factories;

use App\Models\Consulta;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Consulta>
 */
class ConsultaFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'mascota_id' => null,
            'mascota_nombre' => fake()->firstName(),
            'pregunta' => '¿Cómo puedo jugar con mi mascota en casa?',
            'respuesta' => 'Prueba juegos cortos con sus juguetes favoritos.',
            'nivel_riesgo' => 'bajo',
            'requiere_veterinario' => false,
        ];
    }
}
