<?php

namespace Database\Factories;

use App\Models\Mascota;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Mascota>
 */
class MascotaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $especie = fake()->randomElement(array_keys(Mascota::ESPECIES));

        return [
            'user_id' => User::factory(),
            'nombre' => fake()->firstName(),
            'especie' => $especie,
            'raza' => $especie === 'perro'
                ? fake()->randomElement(['Mestizo', 'Labrador', 'Poodle', 'Beagle'])
                : fake()->randomElement(['Mestizo', 'Siamés', 'Persa', 'Maine Coon']),
            'edad' => fake()->numberBetween(0, 15),
            'peso' => fake()->randomFloat(2, 1, 40),
        ];
    }
}
