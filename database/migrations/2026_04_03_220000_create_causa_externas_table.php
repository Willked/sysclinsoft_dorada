<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Causa externa (clasificación RIPS / atención de urgencias y traslados).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('causa_externas', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 8)->unique()->comment('Código según tabla de referencia vigente');
            $table->string('nombre', 255);
            $table->unsignedSmallInteger('orden')->default(0);
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->index('activo');
        });

        $now = now();
        $orden = 0;

        $rows = [
            ['01', '01 — Accidente de tránsito'],
            ['30', '30 — Enfermedad general'],
            ['02', '02 — Lesión por agresión'],
        ];

        foreach ($rows as [$codigo, $nombre]) {
            DB::table('causa_externas')->insert([
                'codigo' => $codigo,
                'nombre' => $nombre,
                'orden' => ++$orden,
                'activo' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('causa_externas');
    }
};
