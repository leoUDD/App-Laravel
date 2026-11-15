<?php

namespace Database\Seeders;

use App\Models\Mascota;
use Illuminate\Database\Seeder;

class EntradaBitacoraSeeder extends Seeder
{
    public function run(): void
    {
        $mascota = Mascota::factory()->create(['nombre' => 'Nube', 'especie' => 'gato']);

        $mascota->entradas()->create([
            'fecha' => today(),
            'tipo' => 'observacion',
            'nota' => 'Jugó con su juguete nuevo durante la tarde.',
        ]);
    }
}
