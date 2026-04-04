<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AmbulanciaSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $rows = [];

        foreach ($this->filas() as [$codigo, $descripcion, $placa]) {
            $rows[] = [
                'codigo' => $codigo,
                'descripcion' => $descripcion,
                'placa' => $placa,
                'clasificacion_servicio' => 'SVB',
                'activo' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        DB::table('ambulancias')->upsert(
            $rows,
            ['codigo'],
            ['descripcion', 'placa', 'clasificacion_servicio', 'activo', 'updated_at']
        );
    }

    /**
     * @return list<array{0: string, 1: string, 2: string}>
     */
    private function filas(): array
    {
        return [
            ['01', 'Ambulancia 01', 'SVB001'],
            ['02', 'Ambulancia 02', 'SVB002'],
            ['03', 'Ambulancia 03', 'SVB003'],
        ];
    }
}
