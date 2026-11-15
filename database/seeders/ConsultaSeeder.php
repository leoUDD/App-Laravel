<?php

namespace Database\Seeders;

use App\Models\Consulta;
use App\Models\Mascota;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class ConsultaSeeder extends Seeder
{
    public function run(): void
    {
        $usuario = User::query()->where('email', 'test@example.com')->firstOrFail();
        $mascota = $usuario->mascotas()->first() ?? Mascota::factory()->for($usuario, 'usuario')->create();

        $datos = Consulta::factory()->raw([
            'user_id' => $usuario->id,
            'mascota_id' => $mascota->id,
            'mascota_nombre' => $mascota->nombre,
        ]);

        $usuario->consultas()->firstOrCreate(
            Arr::only($datos, ['mascota_id', 'pregunta']),
            Arr::except($datos, ['user_id', 'mascota_id', 'pregunta']),
        );
    }
}
