<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entradas_bitacora', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mascota_id')->constrained()->cascadeOnDelete();
            $table->date('fecha');
            $table->string('tipo', 25);
            $table->text('nota');
            $table->timestamps();

            $table->index(['mascota_id', 'fecha', 'id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entradas_bitacora');
    }
};
