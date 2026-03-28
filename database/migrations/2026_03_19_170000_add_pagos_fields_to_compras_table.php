<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('compras', function (Blueprint $table) {
            $table->decimal('saldo_pendiente', 10, 2)->default(0)->after('total');
            $table->decimal('total_pagado', 10, 2)->default(0)->after('saldo_pendiente');
            $table->enum('estado_pago', ['pendiente', 'parcial', 'pagado', 'vencido'])->default('pendiente')->after('estado');
        });

        // Actualizar datos existentes para que las compras a crédito tengan saldo correcto
        DB::statement("UPDATE compras SET saldo_pendiente = total WHERE tipo_compra = 'credito'");
    }

    public function down(): void
    {
        Schema::table('compras', function (Blueprint $table) {
            $table->dropColumn(['saldo_pendiente', 'total_pagado', 'estado_pago']);
        });
    }
};
