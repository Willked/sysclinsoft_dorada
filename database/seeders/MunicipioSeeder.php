<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

/**
 * Municipios DIVIPOLA (5 dígitos). Por defecto lee database/data/divipola_municipios.json.
 *
 * Para cargar el catálogo completo (~1.100+ registros), ejecuta antes:
 *   php database/scripts/download_divipola_municipios.php
 *
 * RIPS / FHIR: el código DANE del municipio es el identificador canónico; dep_dane enlaza
 * con departamentos.dane_code para Address.state + district/city según perfil nacional.
 */
class MunicipioSeeder extends Seeder
{
    private const DATA_PATH = 'database/data/divipola_municipios.json';

    public function run(): void
    {
        $path = base_path(self::DATA_PATH);

        if (! File::exists($path)) {
            $this->command?->error('No existe '.self::DATA_PATH.'. Crea el archivo o ejecuta database/scripts/download_divipola_municipios.php');

            return;
        }

        $raw = File::get($path);
        $items = json_decode($raw, true);

        if (! is_array($items) || $items === []) {
            $this->command?->error('JSON de municipios vacío o inválido.');

            return;
        }

        $depIds = DB::table('departamentos')
            ->where('country_code', 'CO')
            ->pluck('id', 'dane_code');

        $now = now();
        $rows = [];
        $skipped = 0;

        foreach ($items as $item) {
            if (! is_array($item)) {
                $skipped++;

                continue;
            }

            $depDane = isset($item['dep_dane']) ? str_pad((string) preg_replace('/\D/', '', (string) $item['dep_dane']), 2, '0', STR_PAD_LEFT) : null;
            $munDane = isset($item['dane_code']) ? str_pad((string) preg_replace('/\D/', '', (string) $item['dane_code']), 5, '0', STR_PAD_LEFT) : null;
            $nombre = isset($item['nombre']) ? trim((string) $item['nombre']) : '';

            if ($depDane === null || strlen($depDane) !== 2 || $munDane === null || strlen($munDane) !== 5 || $nombre === '') {
                $skipped++;

                continue;
            }

            $depId = $depIds[$depDane] ?? null;
            if ($depId === null) {
                $skipped++;

                continue;
            }

            $rows[] = [
                'departamento_id' => $depId,
                'dane_code' => $munDane,
                'nombre' => $nombre,
                'country_code' => 'CO',
                'activo' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('municipios')->upsert(
                $chunk,
                ['dane_code'],
                ['departamento_id', 'nombre', 'country_code', 'activo', 'updated_at']
            );
        }

        if ($skipped > 0) {
            $this->command?->warn("Municipios omitidos (datos incompletos o departamento inexistente): {$skipped}");
        }

        $this->command?->info('Municipios sembrados: '.count($rows));
    }
}
