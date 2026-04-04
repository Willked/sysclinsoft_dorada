<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Atención APH. Debe ejecutarse ANTES de signos_vitales, glasgow_registros y notas_clinicas.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('atenciones', function (Blueprint $table) {
            $table->id();

            $table->foreignId('paciente_id')
                ->constrained('pacientes')
                ->restrictOnDelete();

            $table->foreignId('acompanante_id')
                ->nullable()
                ->constrained('acompanantes')
                ->nullOnDelete();

            $table->foreignId('ambulancia_id')
                ->nullable()
                ->constrained('ambulancias')
                ->nullOnDelete();

            $table->foreignId('conductor_id')
                ->nullable()
                ->constrained('conductores')
                ->nullOnDelete();

            $table->foreignId('enfermero_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('medico_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->dateTime('hora_llamada');
            $table->dateTime('hora_despacho')->nullable();
            $table->dateTime('salida_base')->nullable();
            $table->dateTime('llegada_escena')->nullable();
            $table->dateTime('salida_escena')->nullable();
            $table->dateTime('llegada_destino')->nullable();

            $table->foreignId('cups_id')
                ->nullable()
                ->constrained('cups')
                ->nullOnDelete();

            $table->string('tipo_servicio', 8)->nullable();

            $table->foreignId('causa_externa_id')
                ->nullable()
                ->constrained('causa_externas')
                ->nullOnDelete();

            $table->string('institucion_origen', 255)->nullable();
            $table->string('institucion_destino', 255)->nullable();

            $table->foreignId('departamento_id')
                ->nullable()
                ->constrained('departamentos')
                ->nullOnDelete();

            $table->foreignId('municipio_id')
                ->nullable()
                ->constrained('municipios')
                ->nullOnDelete();

            $table->foreignId('eps_id')
                ->nullable()
                ->constrained('eps')
                ->nullOnDelete();

            $table->string('autorizacion_eps', 64)->nullable();

            $table->foreignId('tipo_usuario_id')
                ->nullable()
                ->constrained('tipo_usuarios')
                ->nullOnDelete();

            $table->char('zona', 1)->nullable();

            $table->text('evaluacion_fisica')->nullable();
            $table->text('comentario')->nullable();

            $table->string('estado', 32)->default('en_atencion');
            $table->string('triage', 8)->nullable();

            $table->unsignedBigInteger('diagnostico_id')->nullable();
            $table->unsignedBigInteger('signos_vitales_id')->nullable();

            $table->timestamps();

            $table->index(['paciente_id', 'created_at']);
            $table->index('estado');
            $table->index('hora_llamada');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('atenciones');
    }
};
