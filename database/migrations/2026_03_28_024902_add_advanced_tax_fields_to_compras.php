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
        Schema::table('compras', function (Blueprint $table) {
            $table->decimal('subtotal', 15, 2)->default(0)->after('fecha_vencimiento');
            $table->decimal('total_impuestos', 15, 2)->default(0)->after('subtotal');
            $table->decimal('total_descuentos', 15, 2)->default(0)->after('total_impuestos');
        });

        Schema::table('detalle_compras', function (Blueprint $table) {
            $table->decimal('tasa_impuesto', 5, 2)->default(0)->after('precio_compra')->comment('Porcentaje devolucion de IVA/Tax');
            $table->decimal('porcentaje_descuento', 5, 2)->default(0)->after('tasa_impuesto')->comment('Porcentaje de descuento');
            $table->decimal('monto_impuesto', 15, 2)->default(0)->after('porcentaje_descuento');
            $table->decimal('monto_descuento', 15, 2)->default(0)->after('monto_impuesto');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('compras', function (Blueprint $table) {
            $table->dropColumn(['subtotal', 'total_impuestos', 'total_descuentos']);
        });

        Schema::table('detalle_compras', function (Blueprint $table) {
            $table->dropColumn(['tasa_impuesto', 'porcentaje_descuento', 'monto_impuesto', 'monto_descuento']);
        });
    }
};
