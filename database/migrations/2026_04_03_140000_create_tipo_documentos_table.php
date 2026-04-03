<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Catálogo de tipos de documento de identidad (Colombia / interoperabilidad).
 * El código corto alinea con valores habituales en RIPS y con Identifier.type en FHIR Patient.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tipo_documentos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 8)->unique()->comment('Código corto p.ej. CC, TI');
            $table->string('nombre', 160);
            $table->unsignedSmallInteger('orden')->default(0);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        $now = now();
        $orden = 0;

        $rows = [
            ['CC', 'Cédula ciudadanía'],
            ['CE', 'Cédula de extranjería'],
            ['CD', 'Carné diplomático'],
            ['PA', 'Pasaporte'],
            ['SC', 'Salvoconducto'],
            ['PE', 'Permiso Especial de Permanencia'],
            ['RC', 'Registro civil'],
            ['TI', 'Tarjeta de identidad'],
            ['CN', 'Certificado de nacido vivo'],
            ['AS', 'Adulto sin identificar'],
            ['MS', 'Menor sin identificar'],
            ['DE', 'Documento extranjero'],
            ['PT', 'Permiso temporal de permanencia'],
            ['SI', 'Sin identificación'],
        ];

        foreach ($rows as [$codigo, $nombre]) {
            DB::table('tipo_documentos')->insert([
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
        Schema::dropIfExists('tipo_documentos');
    }
};
