<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Causa externa (clasificación RIPS / atención de urgencias y traslados).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('causa_externas', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 8)->unique()->comment('Código según tabla de referencia vigente');
            $table->string('nombre', 255);
            $table->unsignedSmallInteger('orden')->default(0);
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->index('activo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('causa_externas');
    }
};
