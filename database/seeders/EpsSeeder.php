<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EpsSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $orden = 0;
        $rows = [];

        foreach ($this->filas() as [$codigo, $nombre, $nit, $direccion, $telefono, $email]) {
            $rows[] = [
                'codigo' => $codigo,
                'nombre' => $nombre,
                'nit' => $nit,
                'direccion' => $direccion,
                'telefono' => $telefono,
                'email' => $email,
                'orden' => ++$orden,
                'activo' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        DB::table('eps')->upsert(
            $rows,
            ['codigo'],
            ['nombre', 'nit', 'direccion', 'telefono', 'email', 'orden', 'activo', 'updated_at']
        );
    }

    /**
     * NIT de ejemplo; reemplazar por datos oficiales / REPS.
     *
     * @return list<array{0: string, 1: string, 2: string, 3: string, 4: string, 5: string}>
     */
    private function filas(): array
    {
        return [
            ['1', 'Sanitas', '860011153', 'Calle ejemplo # 1', '6010000001', 'contacto@sanitas.com'],
            ['2', 'Colmedica', '860013570', 'Calle ejemplo # 2', '6010000002', 'contacto@colmedica.com'],
        ];
    }
}
