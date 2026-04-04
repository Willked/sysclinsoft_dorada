<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Escala de Glasgow por atención (ocular, verbal, motor; total 3–15).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('glasgow_registros', function (Blueprint $table) {
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
    }

    public function down(): void
    {
        Schema::dropIfExists('glasgow_registros');
    }
};
