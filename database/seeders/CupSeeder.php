<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CupSeeder extends Seeder
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

        DB::table('cups')->upsert(
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
            ['601T01', 'TRASLADO ASISTENCIAL BASICO TERRESTRE PRIMARIO'],
            ['601T02', 'TRASLADO ASISTENCIAL BÁSICO TERRESTRE SECUNDARIO'],
            ['602T01', 'TRASLADO ASISTENCIAL MEDICALIZADO TERRESTRE PRIMARIO'],
            ['602T02', 'TRASLADO ASISTENCIAL MEDICALIZADO TERRESTRE SECUNDARIO'],
            ['602T03', 'TRASLADO NEONATAL MEDICALIZADO'],
        ];
    }
}
