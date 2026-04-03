<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * División administrativa Colombia — Municipio (nivel 2).
 *
 * Estándar de códigos: DANE DIVIPOLA municipal (5 dígitos). Los dos primeros coinciden con
 * departamentos.dane_code del departamento al que pertenece el municipio.
 *
 * RIPS: código de municipio de atención / procedencia según tablas oficiales.
 *
 * FHIR R4: mapeo típico a Address.district o Address.city según el perfil nacional; el
 * identificador canónico para interoperabilidad es codigo_dane (5 dígitos), alineado con
 * ValueSets / CodeSystems que publique MinSalud para ubicación CO.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('municipios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('departamento_id')
                ->constrained('departamentos')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->char('dane_code', 5)->unique()->comment('Código DANE del municipio (5 dígitos)');
            $table->string('nombre', 128);
            $table->char('country_code', 2)->default('CO')->comment('ISO 3166-1 alpha-2');
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->index(['departamento_id', 'dane_code'], 'municipios_departamento_dane_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('municipios');
    }
};
