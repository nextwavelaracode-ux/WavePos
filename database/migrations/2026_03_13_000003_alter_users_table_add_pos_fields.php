<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('apellido')->nullable()->after('name');
            $table->string('telefono', 20)->nullable()->after('email');
            $table->foreignId('sucursal_id')->nullable()->after('telefono')->constrained('sucursales')->nullOnDelete();
            $table->boolean('estado')->default(true)->after('sucursal_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['sucursal_id']);
            $table->dropColumn(['apellido', 'telefono', 'sucursal_id', 'estado']);
        });
    }
};
