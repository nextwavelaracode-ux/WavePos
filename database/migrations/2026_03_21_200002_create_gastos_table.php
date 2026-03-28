<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gastos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('categoria_gasto_id')->constrained('categorias_gasto')->restrictOnDelete();
            $table->foreignId('sucursal_id')->constrained('sucursales')->restrictOnDelete();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->decimal('monto', 10, 2);
            $table->enum('metodo_pago', ['efectivo', 'transferencia', 'tarjeta', 'cheque', 'yappy'])->default('efectivo');
            $table->string('referencia', 100)->nullable();
            $table->date('fecha');
            $table->text('descripcion')->nullable();
            $table->string('comprobante')->nullable(); // path al archivo adjunto
            $table->boolean('es_recurrente')->default(false);
            $table->enum('frecuencia', ['diario', 'semanal', 'quincenal', 'mensual', 'anual'])->nullable();
            $table->date('fecha_programada')->nullable();
            $table->enum('estado', ['activo', 'anulado'])->default('activo');
            $table->text('notas_anulacion')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gastos');
    }
};
