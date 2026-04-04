<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Catálogo de tipos de documento de identidad (Colombia / interoperabilidad).
 * El código corto alinea con valores habituales en RIPS y con Identifier.type en FHIR Patient.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tipo_documentos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 8)->unique()->comment('Código corto p.ej. CC, TI');
            $table->string('nombre', 160);
            $table->unsignedSmallInteger('orden')->default(0);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tipo_documentos');
    }
};
