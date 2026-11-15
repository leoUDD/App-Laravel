<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Agrega la ruta de la foto de la mascota (opcional) dentro del disco "public".
     */
    public function up(): void
    {
        Schema::table('mascotas', function (Blueprint $table) {
            $table->string('foto')->nullable()->after('peso');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mascotas', function (Blueprint $table) {
            $table->dropColumn('foto');
        });
    }
};
