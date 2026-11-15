<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('consultas', function (Blueprint $table) {
            $table->foreignId('consulta_raiz_id')->nullable()->constrained('consultas')->nullOnDelete();
            $table->boolean('favorita')->default(false);
        });

        DB::table('consultas')
            ->select(['id', 'consulta_anterior_id'])
            ->whereNotNull('consulta_anterior_id')
            ->chunkById(500, function (Collection $consultas): void {
                foreach ($consultas as $consulta) {
                    $anterior = DB::table('consultas')->find($consulta->consulta_anterior_id, ['id', 'consulta_raiz_id']);

                    DB::table('consultas')->where('id', $consulta->id)->update([
                        'consulta_raiz_id' => $anterior->consulta_raiz_id ?? $anterior->id,
                    ]);
                }
            });

        Schema::table('consultas', function (Blueprint $table) {
            $table->index(['user_id', 'consulta_raiz_id']);
        });
    }

    public function down(): void
    {
        Schema::table('consultas', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'consulta_raiz_id']);
            $table->dropConstrainedForeignId('consulta_raiz_id');
            $table->dropColumn('favorita');
        });
    }
};
