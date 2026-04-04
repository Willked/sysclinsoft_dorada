<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoUsuarioSeeder extends Seeder
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

        DB::table('tipo_usuarios')->upsert(
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
    }
}
