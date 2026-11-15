<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consultas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('mascota_id')->nullable()->constrained()->nullOnDelete();
            $table->string('mascota_nombre', 60);
            $table->text('pregunta');
            $table->text('respuesta');
            $table->string('nivel_riesgo', 5);
            $table->boolean('requiere_veterinario');
            $table->timestamps();

            $table->index(['user_id', 'id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consultas');
    }
};
