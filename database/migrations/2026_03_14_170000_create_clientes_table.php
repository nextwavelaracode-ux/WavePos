<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->enum('tipo_cliente', [
                'natural',
                'juridico',
                'extranjero',
                'b2b',
                'b2c',
            ])->default('natural');

            // Datos personales / empresa
            $table->string('nombre')->nullable();
            $table->string('apellido')->nullable();
            $table->string('empresa')->nullable();

            // Documentos de identificación
            $table->string('cedula', 30)->nullable();      // Cédula panameña
            $table->string('ruc', 50)->nullable();         // RUC empresa
            $table->string('dv', 5)->nullable();            // Dígito verificador
            $table->string('pasaporte', 50)->nullable();   // Clientes extranjeros

            // Contacto
            $table->string('telefono', 30)->nullable();
            $table->string('email', 200)->nullable();

            // Dirección
            $table->string('direccion')->nullable();
            $table->string('provincia', 100)->nullable();
            $table->string('distrito', 100)->nullable();
            $table->string('pais', 100)->default('Panamá');

            // Finanzas / Crédito
            $table->decimal('limite_credito', 12, 2)->default(0.00);

            // Extra
            $table->text('notas')->nullable();
            $table->boolean('estado')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
