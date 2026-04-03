<?php

namespace App\Http\Controllers;

use App\Models\Departamento;
use App\Models\Municipio;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class DivipolaController extends Controller
{
    public function departamentos(): JsonResponse
    {
        $rows = Departamento::query()
            ->where('country_code', 'CO')
            ->where('activo', true)
            ->orderBy('nombre')
            ->get(['id', 'dane_code', 'nombre']);

        $data = $rows->map(fn (Departamento $d) => [
            'id' => $d->id,
            'dane_code' => $d->dane_code,
            'nombre' => Str::title(Str::lower($d->nombre)),
        ]);

        return response()->json($data);
    }

    public function municipios(Departamento $departamento): JsonResponse
    {
        $rows = Municipio::query()
            ->where('departamento_id', $departamento->id)
            ->where('activo', true)
            ->orderBy('nombre')
            ->get(['id', 'dane_code', 'nombre']);

        $data = $rows->map(fn (Municipio $m) => [
            'id' => $m->id,
            'dane_code' => $m->dane_code,
            'nombre' => Str::title(Str::lower($m->nombre)),
        ]);

        return response()->json($data);
    }
}
