<?php

namespace Database\Factories;

use App\Models\EntradaBitacora;
use App\Models\Mascota;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EntradaBitacora>
 */
class EntradaBitacoraFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'mascota_id' => Mascota::factory(),
            'fecha' => today(),
            'tipo' => 'observacion',
            'nota' => fake()->sentence(),
        ];
    }
}
