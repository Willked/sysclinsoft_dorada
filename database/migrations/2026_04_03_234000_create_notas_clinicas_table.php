<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Notas por atención (médico / enfermería).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notas_clinicas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('atencion_id')
                ->constrained('atenciones')
                ->cascadeOnDelete();

            $table->foreignId('usuario_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->string('tipo_redactor', 32)->comment('medico | enfermeria');

            $table->text('contenido');

            $table->timestamps();

            $table->index(['atencion_id', 'tipo_redactor']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notas_clinicas');
    }
};
