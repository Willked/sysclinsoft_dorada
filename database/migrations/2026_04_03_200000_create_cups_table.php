<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * CUPS — procedimientos (traslados asistenciales APH). Código único según manual vigente.
 *
 * Nota: en el listado suministrado "602T01" aparecía duplicado; el neonatal quedó como 602T03
 * para respetar unicidad. Ajusta el código si tu fuente oficial difiere.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cups', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 16)->unique();
            $table->string('nombre', 255);
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->index('activo');
        });

        $now = now();
        $orden = 0;

        $rows = [
            ['601T01', 'TRASLADO ASISTENCIAL BASICO TERRESTRE PRIMARIO'],
            ['601T02', 'TRASLADO ASISTENCIAL BÁSICO TERRESTRE SECUNDARIO'],
            ['602T01', 'TRASLADO ASISTENCIAL MEDICALIZADO TERRESTRE PRIMARIO'],
            ['602T02', 'TRASLADO ASISTENCIAL MEDICALIZADO TERRESTRE SECUNDARIO'],
            ['602T03', 'TRASLADO NEONATAL MEDICALIZADO'],
        ];

        foreach ($rows as [$codigo, $nombre]) {
            DB::table('cups')->insert([
                'codigo' => $codigo,
                'nombre' => $nombre,
                'activo' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('cups');
    }
};
