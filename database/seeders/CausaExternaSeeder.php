<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CausaExternaSeeder extends Seeder
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

        DB::table('causa_externas')->upsert(
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
            ['01', '01 — Accidente de tránsito'],
            ['30', '30 — Enfermedad general'],
            ['02', '02 — Lesión por agresión'],
        ];
    }
}
