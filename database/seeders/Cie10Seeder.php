<?php

namespace Database\Seeders;

use App\Models\Cie10;
use Illuminate\Database\Seeder;

class Cie10Seeder extends Seeder
{
    /**
     * Registros de ejemplo (CIE-10). Amplíe con export oficial MSPS/SISPRO o archivos de referencia.
     */
    public function run(): void
    {
        $rows = [
            ['codigo' => 'A09', 'descripcion' => 'GASTROENTERITIS Y COLITIS DE ORIGEN NO ESPECIFICADO', 'capitulo' => 'I', 'orden' => 10],
            ['codigo' => 'I10', 'descripcion' => 'HIPERTENSIÓN ESENCIAL (PRIMARIA)', 'capitulo' => 'IX', 'orden' => 20],
            ['codigo' => 'E11.9', 'descripcion' => 'DIABETES MELLITUS TIPO 2 SIN COMPLICACIONES', 'capitulo' => 'IV', 'orden' => 30],
            ['codigo' => 'J18.9', 'descripcion' => 'NEUMONÍA, NO ESPECIFICADA', 'capitulo' => 'X', 'orden' => 40],
            ['codigo' => 'R50.9', 'descripcion' => 'FIEBRE, NO ESPECIFICADA', 'capitulo' => 'XVIII', 'orden' => 50],
            ['codigo' => 'R06.0', 'descripcion' => 'DISNEA', 'capitulo' => 'XVIII', 'orden' => 60],
            ['codigo' => 'R51', 'descripcion' => 'CEFALEA', 'capitulo' => 'XVIII', 'orden' => 70],
            ['codigo' => 'S06.0', 'descripcion' => 'CONMOCIÓN CEREBRAL', 'capitulo' => 'XIX', 'orden' => 80],
            ['codigo' => 'T14.9', 'descripcion' => 'TRAUMATISMO, NO ESPECIFICADO', 'capitulo' => 'XIX', 'orden' => 90],
            ['codigo' => 'Z99.9', 'descripcion' => 'DEPENDENCIA DE MÁQUINAS Y DISPOSITIVOS CAPACITANTES, NO ESPECIFICADA', 'capitulo' => 'XXI', 'orden' => 100],
        ];

        foreach ($rows as $row) {
            Cie10::query()->updateOrCreate(
                ['codigo' => $row['codigo']],
                [
                    'descripcion' => $row['descripcion'],
                    'capitulo' => $row['capitulo'],
                    'orden' => $row['orden'],
                    'activo' => true,
                ],
            );
        }
    }
}
