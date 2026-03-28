<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('categorias', function (Blueprint $table) {
            $table->string('unidad_medida')->nullable()->after('impuesto')->comment('Unidad de medida (Pza, Par/Set, etc.)');
            $table->string('ubicacion')->nullable()->after('unidad_medida')->comment('Ubicación en Pasillo / Estantería');
            $table->string('atributos_tecnicos')->nullable()->after('ubicacion')->comment('Medida, Material, Voltaje, etc.');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categorias', function (Blueprint $table) {
            $table->dropColumn(['unidad_medida', 'ubicacion', 'atributos_tecnicos']);
        });
    }
};
