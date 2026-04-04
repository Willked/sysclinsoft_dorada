<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoDocumentoSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $orden = 0;
        $rows = [];

        foreach ($this->filas() as [$codigo, $nombre]) {
            $rows[] = [
                'codigo' => $codigo,
                'nombre' => $nombre,
                'orden' => ++$orden,
                'activo' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        DB::table('tipo_documentos')->upsert(
            $rows,
            ['codigo'],
            ['nombre', 'orden', 'activo', 'updated_at']
        );
    }

    /**
     * @return list<array{0: string, 1: string}>
     */
    private function filas(): array
    {
        return [
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
    }
}
