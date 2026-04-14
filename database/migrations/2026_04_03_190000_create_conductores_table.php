<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Conductores de ambulancia sin acceso al sistema (sin fila en users).
 * Se referencian desde atenciones / turnos / despachos.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conductores', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tipo_documento_id')
                ->constrained('tipo_documentos')
                ->restrictOnDelete();

            $table->string('numero_documento', 32);
            $table->string('primer_nombre', 120);
            $table->string('segundo_nombre', 120)->nullable();
            $table->string('primer_apellido', 120);
            $table->string('segundo_apellido', 120)->nullable();
            $table->string('telefono', 32)->nullable();

            $table->string('numero_licencia', 64)->nullable()->comment('Número licencia de conducción');
            $table->string('categoria_licencia', 16)->nullable()->comment('p.ej. B3, C2');
            $table->date('fecha_vencimiento_licencia')->nullable();

            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->unique(['tipo_documento_id', 'numero_documento'], 'conductores_documento_unique');
            $table->index('activo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conductores');
    }
};
