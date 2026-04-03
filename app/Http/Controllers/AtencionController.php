<?php

namespace App\Http\Controllers;

use App\Models\TipoDocumento;
use App\Models\TipoUsuario;
use Illuminate\View\View;

class AtencionController extends Controller
{
    public function create(): View
    {
        return view('atencion', [
            'sexos' => [
                'M' => __('Masculino'),
                'F' => __('Femenino'),
                'I' => __('Indeterminado o Intersexual'),
            ],
            'estadosCiviles' => [
                'S' => __('Soltero'),
                'C' => __('Casado'),
                'D' => __('Divorciado'),
                'V' => __('Viudo'),
                'U' => __('Unión libre'),
            ],
            'parentescos' => [
                'P' => __('Padre'),
                'M' => __('Madre'),
                'H' => __('Hermano'),
                'H' => __('Hermana'),
                'A' => __('Amigo'),
                'O' => __('Otro'),
            ],
            'tipoDocumentos' => TipoDocumento::query()->activosOrdenados()->get(),
            'tipoUsuarios' => TipoUsuario::query()->activosOrdenados()->get(),
            'zonas' => [
                'U' => __('Urbana'),
                'R' => __('Rural'),
            ],
            'tipoServicios' => [
                '01' => __('01 — Urgencias en escena'),
                '02' => __('02 — Traslado primario'),
                '03' => __('03 — Traslado secundario'),
                '04' => __('04 — Traslado neonatal'),
            ],
            'causaExternas' => [
                '01' => __('01 — Accidente de tránsito'),
                '30' => __('30 — Enfermedad general'),
                '02' => __('02 — Lesión por agresión'),
            ],
            'eps' => [
                '1' => __('Sanitas'),
                '2' => __('Colmedica'),
            ],
        ]);
    }
}
