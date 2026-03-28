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
        Schema::table('clientes', function (Blueprint $table) {
            $table->integer('tipo_documento_dian_id')->nullable()->default(13)->comment('13=CC, 31=NIT...');
            $table->integer('tipo_organizacion_dian_id')->nullable()->default(2)->comment('1=Jurídica, 2=Natural');
            $table->integer('tributo_dian_id')->nullable()->default(21)->comment('21=No aplica');
            $table->integer('municipio_dian_id')->nullable()->default(980)->comment('Factus city ID');
        });

        Schema::table('productos', function (Blueprint $table) {
            $table->decimal('tasa_impuesto', 5, 2)->nullable()->default(19.00)->comment('Tax rate (e.g. 19.00)');
            $table->boolean('is_excluded')->default(false)->comment('Excluido de IVA (0: no, 1: sí)');
            $table->integer('unidad_medida_dian_id')->nullable()->default(70)->comment('70=Unidad');
        });

        Schema::table('ventas', function (Blueprint $table) {
            $table->integer('forma_pago_dian')->nullable()->default(1)->comment('1=Contado, 2=Crédito');
            $table->integer('metodo_pago_dian')->nullable()->default(10)->comment('10=Efectivo, 47=Débito, etc.');
            $table->date('fecha_vencimiento_dian')->nullable()->comment('Solo si forma_pago_dian=2');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->dropColumn(['forma_pago_dian', 'metodo_pago_dian', 'fecha_vencimiento_dian']);
        });

        Schema::table('productos', function (Blueprint $table) {
            $table->dropColumn(['tasa_impuesto', 'is_excluded', 'unidad_medida_dian_id']);
        });

        Schema::table('clientes', function (Blueprint $table) {
            $table->dropColumn(['tipo_documento_dian_id', 'tipo_organizacion_dian_id', 'tributo_dian_id', 'municipio_dian_id']);
        });
    }
};
