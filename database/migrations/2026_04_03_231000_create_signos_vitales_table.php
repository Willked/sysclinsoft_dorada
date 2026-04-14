<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Signos vitales por atención (varias mediciones por encuentro).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('signos_vitales', function (Blueprint $table) {
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
    }

    public function down(): void
    {
        Schema::dropIfExists('signos_vitales');
    }
};
