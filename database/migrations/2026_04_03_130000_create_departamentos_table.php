<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * División administrativa Colombia — Departamento (nivel 1).
 *
 * Estándar de códigos: DANE DIVIPOLA (2 dígitos, cero a la izquierda p.ej. "05", "11").
 *
 * RIPS: identificación de ubicación del usuario/servicio con códigos oficiales compatibles
 * con tablas de referencia del ecosistema de salud.
 *
 * FHIR R4: mapeo típico a Address.state como código (no solo texto libre). En perfiles CO
 * suele usarse el mismo código DANE; al serializar a CodeableConcept, el sistema terminológico
 * debe ser el definido en el Implementation Guide nacional (no hardcodear aquí la URI final).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('departamentos', function (Blueprint $table) {
            $table->id();
            $table->char('dane_code', 2)->comment('Código DANE del departamento (2 dígitos)');
            $table->string('nombre', 128);
            $table->char('country_code', 2)->default('CO')->comment('ISO 3166-1 alpha-2');
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->unique(['country_code', 'dane_code'], 'departamentos_pais_dane_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('departamentos');
    }
};
