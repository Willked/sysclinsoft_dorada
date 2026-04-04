<?php

namespace App\Http\Controllers;

use App\Models\CausaExterna;
use App\Models\Cup;
use App\Models\Paciente;
use App\Models\TipoDocumento;
use App\Models\TipoUsuario;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
            'cups' => Cup::query()->activosOrdenados()->get(),
            'causaExternas' => CausaExterna::query()->activosOrdenados()->get(),
            'eps' => Eps::query()->activosOrdenados()->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        // $tipoDocumentoId = TipoDocumento::query()
        //     ->where('codigo', $request->input('tipo_documento'))
        //     ->value('id');

        // if ($tipoDocumentoId === null) {
        //     return redirect()
        //         ->route('atenciones.nueva')
        //         ->withInput()
        //         ->with('error', __('Tipo de documento no reconocido.'));
        // }

        // Paciente::create([
        //     'tipo_documento_id' => $tipoDocumentoId,
        //     'numero_documento' => $request->input('numero_documento'),
        //     'primer_nombre' => $request->input('primer_nombre'),
        //     'segundo_nombre' => $request->input('segundo_nombre') ?: null,
        //     'primer_apellido' => $request->input('primer_apellido'),
        //     'segundo_apellido' => $request->input('segundo_apellido') ?: null,
        //     'fecha_nacimiento' => $request->input('fecha_nacimiento'),
        //     'sexo' => $request->input('sexo'),
        //     'estado_civil' => $request->input('estado_civil'),
        //     'direccion' => $request->input('direccion'),
        //     'email' => $request->input('email'),
        //     'telefono' => $request->input('telefono'),
        // ]);

        // return redirect()
        //     ->route('atenciones.nueva')
        //     ->with('status', __('Paciente guardado (prueba).'));

        dd($request->all());
    }
}
