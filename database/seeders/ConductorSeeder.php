<?php

namespace Database\Seeders;

use App\Models\TipoDocumento;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConductorSeeder extends Seeder
{
    public function run(): void
    {
        $tipoCcId = TipoDocumento::query()->where('codigo', 'CC')->value('id');

        if ($tipoCcId === null) {
            $this->command?->warn('ConductorSeeder: no existe tipo de documento CC. Ejecuta TipoDocumentoSeeder antes.');

            return;
        }

        $now = now();
        $rows = [];

        foreach ($this->filas() as $fila) {
            $rows[] = array_merge($fila, [
                'tipo_documento_id' => $tipoCcId,
                'activo' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        DB::table('conductores')->upsert(
            $rows,
            ['tipo_documento_id', 'numero_documento'],
            [
                'primer_nombre',
                'segundo_nombre',
                'primer_apellido',
                'segundo_apellido',
                'telefono',
                'numero_licencia',
                'categoria_licencia',
                'fecha_vencimiento_licencia',
                'activo',
                'updated_at',
            ]
        );
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function filas(): array
    {
        return [
            [
                'numero_documento' => '80123456',
                'primer_nombre' => 'Carlos',
                'segundo_nombre' => 'Alberto',
                'primer_apellido' => 'Ramírez',
                'segundo_apellido' => null,
                'telefono' => '3001112233',
                'numero_licencia' => 'LIC-APH-001',
                'categoria_licencia' => 'B3',
                'fecha_vencimiento_licencia' => '2028-06-15',
            ],
            [
                'numero_documento' => '80234567',
                'primer_nombre' => 'María',
                'segundo_nombre' => 'Elena',
                'primer_apellido' => 'Gómez',
                'segundo_apellido' => 'Soto',
                'telefono' => '3004445566',
                'numero_licencia' => 'LIC-APH-002',
                'categoria_licencia' => 'C2',
                'fecha_vencimiento_licencia' => '2027-12-01',
            ],
            [
                'numero_documento' => '80345678',
                'primer_nombre' => 'Jorge',
                'segundo_nombre' => null,
                'primer_apellido' => 'Pineda',
                'segundo_apellido' => null,
                'telefono' => '3007778899',
                'numero_licencia' => 'LIC-APH-003',
                'categoria_licencia' => 'B2',
                'fecha_vencimiento_licencia' => '2029-03-20',
            ],
        ];
    }
}
