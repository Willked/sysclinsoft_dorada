<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        if (Schema::hasTable('atenciones')) {
            Schema::table('atenciones', function (Blueprint $table): void {
                if (Schema::hasColumn('atenciones', 'signos_vitales_id')) {
                    $table->dropForeign(['signos_vitales_id']);
                }
                if (Schema::hasColumn('atenciones', 'diagnostico_id')) {
                    $table->dropForeign(['diagnostico_id']);
                }
            });
        }

        Schema::dropIfExists('notas_clinicas');
        Schema::dropIfExists('glasgow_registros');
        Schema::dropIfExists('signos_vitales');
        Schema::dropIfExists('atenciones');

        Schema::create('atenciones', function (Blueprint $table): void {
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
            $table->dateTime('hora_entrega')->nullable();

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
            $table->foreignId('departamento_origen_id')
                ->nullable()
                ->constrained('departamentos')
                ->nullOnDelete();
            $table->foreignId('municipio_origen_id')
                ->nullable()
                ->constrained('municipios')
                ->nullOnDelete();

            $table->string('institucion_destino', 255)->nullable();
            $table->foreignId('departamento_destino_id')
                ->nullable()
                ->constrained('departamentos')
                ->nullOnDelete();
            $table->foreignId('municipio_destino_id')
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

        Schema::create('signos_vitales', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('atencion_id')
                ->constrained('atenciones')
                ->cascadeOnDelete();

            $table->dateTime('medicion_en')->nullable();

            $table->unsignedSmallInteger('presion_sistolica')->nullable()->comment('mmHg');
            $table->unsignedSmallInteger('presion_diastolica')->nullable()->comment('mmHg');
            $table->unsignedSmallInteger('frecuencia_cardiaca')->nullable()->comment('lpm');
            $table->unsignedSmallInteger('frecuencia_respiratoria')->nullable()->comment('rpm');
            $table->unsignedTinyInteger('saturacion_oxigeno')->nullable()->comment('SpO2 %');
            $table->decimal('temperatura', 4, 1)->nullable()->comment('°C');
            $table->unsignedSmallInteger('glicemia')->nullable()->comment('mg/dL');
            $table->string('fraccion_inspirada_oxigeno', 16)->nullable()->comment('FiO2');
            $table->text('observaciones')->nullable();

            $table->foreignId('registrado_por_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index(['atencion_id', 'medicion_en']);
        });

        Schema::create('glasgow_registros', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('atencion_id')
                ->constrained('atenciones')
                ->cascadeOnDelete();

            $table->dateTime('medicion_en')->nullable();
            $table->unsignedTinyInteger('ocular')->nullable()->comment('1–4');
            $table->unsignedTinyInteger('verbal')->nullable()->comment('1–5');
            $table->unsignedTinyInteger('motor')->nullable()->comment('1–6');
            $table->unsignedTinyInteger('total')->nullable()->comment('3–15');

            $table->foreignId('registrado_por_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index(['atencion_id', 'medicion_en']);
        });

        Schema::create('notas_clinicas', function (Blueprint $table): void {
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

        Schema::table('atenciones', function (Blueprint $table): void {
            $table->foreign('diagnostico_id')
                ->references('id')
                ->on('cie_10')
                ->nullOnDelete();

            $table->foreign('signos_vitales_id')
                ->references('id')
                ->on('signos_vitales')
                ->nullOnDelete();
        });

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        // No se revierte para evitar pérdida involuntaria de datos en tablas clínicas.
    }
};
