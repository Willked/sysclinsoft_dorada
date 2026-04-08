<?php

namespace App\Http\Controllers;

use App\Models\Acompanante;
use App\Models\Ambulancia;
use App\Models\Atencion;
use App\Models\CausaExterna;
use App\Models\Conductor;
use App\Models\Cup;
use App\Models\Eps;
use App\Models\NotaClinica;
use App\Models\Paciente;
use App\Models\SignoVital;
use App\Models\TipoDocumento;
use App\Models\TipoUsuario;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
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
                'R' => __('Hermana'),
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
            'ambulancias' => Ambulancia::activosOrdenados()->get(),
            'conductores' => Conductor::activosOrdenados()->get(),
            'medicos' => User::all(),
        ]);
    }

    public function show(Atencion $atencion): View
    {
        $atencion->load([
            'paciente.tipoDocumento',
            'eps',
            'tipoUsuario',
            'municipio',
            'cup',
            'medico',
            'signosVitales' => fn ($q) => $q->orderBy('medicion_en')->orderBy('id'),
            'notasClinicas' => fn ($q) => $q->with('usuario')->orderByDesc('created_at')->orderByDesc('id'),
        ]);

        $ultimoGlasgow = $atencion->glasgowRegistros()
            ->orderByDesc('medicion_en')
            ->orderByDesc('id')
            ->first();

        return view('atencion.show', [
            'atencion' => $atencion,
            'ultimoGlasgow' => $ultimoGlasgow,
        ]);
    }

    public function storeSignosVitales(Request $request, Atencion $atencion): RedirectResponse
    {
        $validated = $request->validateWithBag('signosVitales', [
            'medicion_en' => ['nullable', 'date'],
            'presion_sistolica' => ['nullable', 'integer', 'min:40', 'max:300'],
            'presion_diastolica' => ['nullable', 'integer', 'min:20', 'max:200'],
            'frecuencia_cardiaca' => ['nullable', 'integer', 'min:20', 'max:260'],
            'frecuencia_respiratoria' => ['nullable', 'integer', 'min:5', 'max:80'],
            'temperatura' => ['nullable', 'numeric', 'min:25', 'max:45'],
            'saturacion_oxigeno' => ['nullable', 'integer', 'min:0', 'max:100'],
            'glicemia' => ['nullable', 'integer', 'min:10', 'max:1000'],
            'fraccion_inspirada_oxigeno' => ['nullable', 'string', 'max:16'],
            'observaciones' => ['nullable', 'string', 'max:1000'],
        ]);

        if (! collect($validated)->except(['medicion_en', 'observaciones'])->filter(static fn ($v) => $v !== null && $v !== '')->isNotEmpty()) {
            return redirect()
                ->route('atenciones.show', $atencion)
                ->withInput()
                ->withErrors(['medicion_en' => __('Debe registrar al menos un signo vital.')], 'signosVitales');
        }

        $signo = new SignoVital();
        $signo->fill($validated);
        $signo->atencion_id = $atencion->id;
        $signo->registrado_por_id = auth()->id();
        $signo->medicion_en = filled($validated['medicion_en'] ?? null)
            ? Carbon::parse($validated['medicion_en'])
            : now();
        $signo->save();

        $atencion->forceFill(['signos_vitales_id' => $signo->id])->save();

        return redirect()
            ->route('atenciones.show', $atencion)
            ->with('status_signos', __('Toma de signos vitales registrada correctamente.'));
    }

    public function storeNotaClinica(Request $request, Atencion $atencion): RedirectResponse
    {
        $validated = $request->validateWithBag('notaClinica', [
            'contenido' => ['required', 'string', 'min:5', 'max:5000'],
        ]);

        $tipoRedactor = auth()->id() === $atencion->medico_id
            ? NotaClinica::TIPO_MEDICO
            : NotaClinica::TIPO_ENFERMERIA;

        NotaClinica::query()->create([
            'atencion_id' => $atencion->id,
            'usuario_id' => auth()->id(),
            'tipo_redactor' => $tipoRedactor,
            'contenido' => trim($validated['contenido']),
        ]);

        return redirect()
            ->route('atenciones.show', $atencion)
            ->with('status_nota', __('Nota clínica registrada correctamente.'));
    }

    public function store(Request $request): RedirectResponse
    {
        
        $horaLlamada = $this->parseDatetimeLocal($request->input('hora_llamada'));
        if ($horaLlamada === null) {
            return redirect()
                ->route('atenciones.nueva')
                ->withInput()
                ->withErrors(['hora_llamada' => __('La hora de llamada es obligatoria.')]);
        }

        try {
            DB::transaction(function () use ($request, $horaLlamada): void {
                $paciente = $this->createPaciente($request);
                $acompanante = $this->createAcompanante($request, $paciente);
                $this->createAtencion($request, $paciente, $acompanante, $horaLlamada);
            });
        } catch (\Throwable $e) {
            report($e);

            return redirect()
                ->route('atenciones.nueva')
                ->withInput()
                ->with('error', __('No se pudo guardar la atención. Intente de nuevo.'));
        }

        return redirect()
            ->route('dashboard')
            ->with('status', __('Atención registrada correctamente.'));
    }

    private function createPaciente(Request $request): Paciente
    {
        $tipoDocumentoId = TipoDocumento::query()
            ->where('codigo', $request->input('tipo_documento'))
            ->value('id');

        if ($tipoDocumentoId === null) {
            throw new \InvalidArgumentException('Tipo de documento del paciente no reconocido.');
        }

        return Paciente::query()->create([
            'tipo_documento_id' => $tipoDocumentoId,
            'numero_documento' => $request->input('numero_documento'),
            'primer_nombre' => $request->input('primer_nombre'),
            'segundo_nombre' => $request->input('segundo_nombre') ?: null,
            'primer_apellido' => $request->input('primer_apellido'),
            'segundo_apellido' => $request->input('segundo_apellido') ?: null,
            'fecha_nacimiento' => $request->input('fecha_nacimiento'),
            'sexo' => $request->input('sexo'),
            'estado_civil' => $request->input('estado_civil'),
            'direccion' => $request->input('direccion'),
            'email' => $request->input('email'),
            'telefono' => $request->input('telefono'),
        ]);
    }

    private function createAcompanante(Request $request, Paciente $paciente): ?Acompanante
    {
        if (! filled($request->input('nombre_acompanante'))) {
            return null;
        }

        $tipoDocId = TipoDocumento::query()
            ->where('codigo', $request->input('doc_type_acompanante'))
            ->value('id');

        if ($tipoDocId === null) {
            throw new \InvalidArgumentException('Tipo de documento del acompañante no reconocido.');
        }

        return Acompanante::query()->create([
            'paciente_id' => $paciente->id,
            'tipo_documento_id' => $tipoDocId,
            'numero_documento' => $request->input('doc_num_acompanante'),
            'nombre' => $request->input('nombre_acompanante'),
            'parentesco' => $request->input('parentesco_acompanante'),
            'telefono' => $request->input('telefono_acompanante') ?: null,
        ]);
    }

    private function createAtencion(
        Request $request,
        Paciente $paciente,
        ?Acompanante $acompanante,
        Carbon $horaLlamada,
    ): Atencion {
        $cupsId = Cup::query()->where('codigo', $request->input('tipo_servicio'))->value('id');
        $causaExternaId = CausaExterna::query()->where('codigo', $request->input('causa_externa'))->value('id');
        $epsId = Eps::query()->where('codigo', $request->input('eps'))->value('id');
        $tipoUsuarioId = TipoUsuario::query()->where('codigo', $request->input('tipo_usuario'))->value('id');

        return Atencion::query()->create([
            'paciente_id' => $paciente->id,
            'acompanante_id' => $acompanante?->id,
            'ambulancia_id' => $request->filled('ambulancia_id') ? $request->integer('ambulancia_id') : null,
            'conductor_id' => $request->filled('conductor_id') ? $request->integer('conductor_id') : null,
            'enfermero_id' => auth()->id(),
            'medico_id' => $request->filled('medico_id') ? $request->integer('medico_id') : null,
            'hora_llamada' => $horaLlamada,
            // 'hora_despacho' => $this->parseDatetimeLocal($request->input('hora_despacho')),
            // 'salida_base' => $this->parseDatetimeLocal($request->input('salida_base')),
            // 'llegada_escena' => $this->parseDatetimeLocal($request->input('llegada_escena')),
            // 'salida_escena' => $this->parseDatetimeLocal($request->input('salida_escena')),
            // 'llegada_destino' => $this->parseDatetimeLocal($request->input('llegada_destino')),
            'cups_id' => $cupsId,
            'tipo_servicio' => $request->input('tipo_servicio'),
            'causa_externa_id' => $causaExternaId,
            'institucion_origen' => $request->input('institucion_origen') ?: null,
            'institucion_destino' => $request->input('institucion_destino') ?: null,
            'departamento_id' => $request->filled('departamento_id') ? $request->integer('departamento_id') : null,
            'municipio_id' => $request->filled('municipio_id') ? $request->integer('municipio_id') : null,
            'eps_id' => $epsId,
            'autorizacion_eps' => $request->input('autorizacion_eps') ?: null,
            'tipo_usuario_id' => $tipoUsuarioId,
            'zona' => $request->input('zona'),
            'evaluacion_fisica' => null,
            'comentario' => null,
            'estado' => 'en_atencion',
            'triage' => null,
        ]);
    }

    private function parseDatetimeLocal(?string $value): ?Carbon
    {
        if ($value === null || $value === '') {
            return null;
        }

        return Carbon::parse($value);
    }
}
