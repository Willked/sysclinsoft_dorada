<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Unidades de ambulancia del operador (identificación en atenciones, despacho, RIPS).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ambulancias', function (Blueprint $table) {
            $table->id();
            /** Código visible en flota p.ej. "03", "APH-12" */
            $table->string('codigo', 32)->unique();
            /** Placa vehicular (Colombia); opcional si aún no asignada */
            $table->string('placa', 16)->nullable()->unique();
            $table->string('descripcion', 255)->nullable();
            /** SVB, SVA, etc. — texto libre o catálogo futuro */
            $table->string('clasificacion_servicio', 32)->nullable()->comment('p.ej. SVB, SVA');
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->index('activo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ambulancias');
    }
};
