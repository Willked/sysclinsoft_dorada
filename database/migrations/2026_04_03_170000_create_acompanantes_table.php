<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Acompañante del paciente (formulario atención). En FHIR suele modelarse como RelatedPerson o contacto.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('acompanantes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('paciente_id')
                ->constrained('pacientes')
                ->cascadeOnDelete();

            $table->string('nombre', 160);

            /** Código del formulario: P, M, H, A, O, etc. */
            $table->char('parentesco', 1);

            $table->foreignId('tipo_documento_id')
                ->constrained('tipo_documentos')
                ->restrictOnDelete();

            $table->string('numero_documento', 32);
            $table->string('telefono', 32)->nullable();

            $table->timestamps();

            $table->unique('paciente_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('acompanantes');
    }
};
