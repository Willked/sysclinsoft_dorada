<?php

namespace App\Http\Controllers;

use App\Models\Atencion;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function create(): View
    {
        $atenciones = Atencion::query()
            ->with(['paciente', 'cup'])
            ->orderByDesc('hora_llamada')
            ->orderByDesc('id')
            ->limit(20)
            ->get();

        $atencionesHoy = Atencion::query()
            ->whereDate('hora_llamada', today())
            ->count();

        $enAtencion = Atencion::query()
            ->where('estado', 'en_atencion')
            ->count();

        $atencionesAyer = Atencion::query()
            ->whereDate('hora_llamada', today()->subDay())
            ->count();

        return view('dashboard', [
            'atenciones' => $atenciones,
            'atencionesHoy' => $atencionesHoy,
            'atencionesAyer' => $atencionesAyer,
            'enAtencion' => $enAtencion,
        ]);
    }
}
