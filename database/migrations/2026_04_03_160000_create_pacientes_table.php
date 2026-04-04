<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Paciente — datos alineados con el formulario de atención y con Patient (FHIR) / usuario RIPS.
 * El formulario envía tipo_documento como código (p.ej. CC); en BD se relaciona por tipo_documento_id.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pacientes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tipo_documento_id')
                ->constrained('tipo_documentos')
                ->restrictOnDelete();

            $table->string('numero_documento', 32);
            $table->string('primer_nombre', 120);
            $table->string('segundo_nombre', 120)->nullable();
            $table->string('primer_apellido', 120);
            $table->string('segundo_apellido', 120)->nullable();
            $table->date('fecha_nacimiento');
            /** M, F, I — coincide con opciones del formulario */
            $table->char('sexo', 1);
            /** S, C, D, V, U — coincide con opciones del formulario */
            $table->char('estado_civil', 1);
            $table->string('direccion', 255);
            $table->string('email', 255);
            $table->string('telefono', 32);

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['tipo_documento_id', 'numero_documento'], 'pacientes_documento_unique');
            $table->index('fecha_nacimiento');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pacientes');
    }
};
