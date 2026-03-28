<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('empresas', function (Blueprint $table) {
            $table->string('prefijo_factura', 20)->default('FACT-')->after('zona_horaria');
            $table->string('prefijo_compra', 20)->default('COMP-')->after('prefijo_factura');
            $table->unsignedBigInteger('ultimo_numero_factura')->default(0)->after('prefijo_compra');
            $table->unsignedBigInteger('ultimo_numero_compra')->default(0)->after('ultimo_numero_factura');
            $table->tinyInteger('digitos_correlativo')->default(5)->after('ultimo_numero_compra');
        });
    }

    public function down(): void
    {
        Schema::table('empresas', function (Blueprint $table) {
            $table->dropColumn([
                'prefijo_factura',
                'prefijo_compra',
                'ultimo_numero_factura',
                'ultimo_numero_compra',
                'digitos_correlativo',
            ]);
        });
    }
};
