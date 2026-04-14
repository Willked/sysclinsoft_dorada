<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tipo de usuario / afiliación (códigos alineados con tablas de referencia RIPS).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tipo_usuarios', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 4)->unique()->comment('Código RIPS p.ej. 01, 02');
            $table->string('nombre', 255);
            $table->unsignedSmallInteger('orden')->default(0);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tipo_usuarios');
    }
};
