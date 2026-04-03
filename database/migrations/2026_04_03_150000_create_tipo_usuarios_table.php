<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Tipo de usuario / afiliación (códigos alineados con tablas de referencia RIPS).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tipo_usuarios', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 4)->unique()->comment('Código RIPS p.ej. 01, 02');
            $table->string('nombre', 255);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        $now = now();
        $orden = 0;

        $rows = [
            ['01', 'Contributivo cotizante'],
            ['02', 'Contributivo beneficiario'],
            ['03', 'Contributivo adicional'],
            ['04', 'Subsidiado'],
            ['05', 'No afiliado'],
            ['06', 'Especial o Excepción cotizante'],
            ['07', 'Especial o Excepción beneficiario'],
            ['08', 'Personas privadas de la libertad a cargo del Fondo Nacional de Salud'],
            ['09', 'Tomador / Amparado ARL'],
            ['10', 'Tomador / Amparado SOAT'],
        ];

        foreach ($rows as [$codigo, $nombre]) {
            DB::table('tipo_usuarios')->insert([
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
        Schema::dropIfExists('tipo_usuarios');
    }
};
