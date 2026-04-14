<?php

/**
 * Descarga municipios DIVIPOLA (datos.gov.co, recurso Socrata vafm-j2df) y genera
 * database/data/divipola_municipios.json para MunicipioSeeder.
 *
 * Uso (desde la raíz del proyecto):
 *   php database/scripts/download_divipola_municipios.php
 *
 * Campos del dataset: cod_dpto, cod_mpio, nom_mpio (y georreferenciación).
 */
declare(strict_types=1);

$root = dirname(__DIR__, 2);
$out = $root.'/database/data/divipola_municipios.json';

$limit = 10000;
$url = 'https://www.datos.gov.co/resource/vafm-j2df.json?$limit='.$limit;

$raw = @file_get_contents($url);
if ($raw === false) {
    fwrite(STDERR, "No se pudo descargar: {$url}\n");
    exit(1);
}

$chunk = json_decode($raw, true);
if (! is_array($chunk)) {
    fwrite(STDERR, "Respuesta JSON inválida.\n");
    exit(1);
}

$all = [];

foreach ($chunk as $row) {
    if (! is_array($row)) {
        continue;
    }

    $mapped = mapRow($row);
    if ($mapped !== null) {
        $all[$mapped['dane_code']] = $mapped;
    }
}

ksort($all, SORT_STRING);
$json = array_values($all);
$dir = dirname($out);
if (! is_dir($dir)) {
    mkdir($dir, 0755, true);
}
file_put_contents($out, json_encode($json, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
echo 'Escritos '.count($json).' municipios en '.$out.PHP_EOL;

/**
 * @param  array<string, mixed>  $row
 * @return array{dep_dane: string, dane_code: string, nombre: string}|null
 */
function mapRow(array $row): ?array
{
    if (! isset($row['cod_dpto'], $row['cod_mpio'], $row['nom_mpio'])) {
        return null;
    }

    $dep = str_pad(preg_replace('/\D/', '', (string) $row['cod_dpto']), 2, '0', STR_PAD_LEFT);
    $mun = str_pad(preg_replace('/\D/', '', (string) $row['cod_mpio']), 5, '0', STR_PAD_LEFT);
    $nom = mb_strtoupper(trim((string) $row['nom_mpio']), 'UTF-8');

    if (strlen($dep) !== 2 || strlen($mun) !== 5 || $nom === '') {
        return null;
    }

    return [
        'dep_dane' => $dep,
        'dane_code' => $mun,
        'nombre' => $nom,
    ];
}
