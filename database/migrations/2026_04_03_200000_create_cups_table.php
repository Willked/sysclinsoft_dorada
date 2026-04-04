<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * CUPS — procedimientos (traslados asistenciales APH). Código único según manual vigente.
 *
 * Nota: en el listado suministrado "602T01" aparecía duplicado; el neonatal quedó como 602T03
 * para respetar unicidad. Ajusta el código si tu fuente oficial difiere.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cups', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 16)->unique();
            $table->string('nombre', 255);
            $table->unsignedSmallInteger('orden')->default(0);
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->index('activo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cups');
    }
};
