<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Catálogo CIE-10 (Clasificación Internacional de Enfermedades, 10.ª revisión).
 *
 * Estructura habitual en sistemas de salud / RIPS: código oficial único, descripción del diagnóstico,
 * capítulo (agrupación ICD) opcional para consultas y reportes. Referencia: tablas de referencia MSPS/SISPRO.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cie_10', function (Blueprint $table) {
            $table->id();
            /** Código alfanumérico CIE-10 (ej. A09, E11.9, I10). Sin espacios; se normaliza en mayúsculas en la app. */
            $table->string('codigo', 16)->unique();
            /** Texto del diagnóstico según nomenclatura vigente. */
            $table->string('descripcion', 512);
            /** Identificador o etiqueta de capítulo/sección (ej. I, X, A00-B99), opcional. */
            $table->string('capitulo', 32)->nullable();
            $table->unsignedSmallInteger('orden')->default(0);
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->index('activo');
            $table->index('capitulo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cie_10');
    }
};
