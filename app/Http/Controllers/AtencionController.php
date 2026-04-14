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
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AtencionController extends Controller
{
    public function create(): View
    {
        return view('atencion', $this->formData(null));
    }

    public function edit(Atencion $atencion): View
    {
        $atencion->load([
            'paciente.tipoDocumento',
            'acompanante.tipoDocumento',
            'municipio.departamento',
            'departamento',
            'cup',
            'causaExterna',
            'eps',
            'tipoUsuario',
        ]);

        return view('atencion', $this->formData($atencion));
    }

    /**
     * @return array<string, mixed>
     */
    private function formData(?Atencion $atencion): array
    {
        return [
            'atencion' => $atencion,
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
        ];
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

        $signo = new SignoVital;
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
        $request->validate(
            $this->reglasNuevaAtencion($request),
            [],
            $this->atributosValidacionNuevaAtencion(),
        );

        $horaLlamada = $this->parseDatetimeLocal($request->input('hora_llamada'));
        if ($horaLlamada === null) {
            return redirect()
                ->route('atenciones.nueva')
                ->withInput()
                ->withErrors(['hora_llamada' => __('La hora de llamada no es válida.')]);
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

    public function update(Request $request, Atencion $atencion): RedirectResponse
    {
        $horaLlamada = $this->parseDatetimeLocal($request->input('hora_llamada'));
        if ($horaLlamada === null) {
            return redirect()
                ->route('atenciones.edit', $atencion)
                ->withInput()
                ->withErrors(['hora_llamada' => __('La hora de llamada es obligatoria.')]);
        }

        try {
            DB::transaction(function () use ($request, $atencion, $horaLlamada): void {
                $paciente = $atencion->paciente;
                if ($paciente === null) {
                    throw new \InvalidArgumentException('Atención sin paciente asociado.');
                }
                $this->updatePaciente($request, $paciente);
                $acompanante = $this->syncAcompanante($request, $paciente, $atencion);
                $this->applyAtencionUpdate($request, $atencion, $acompanante, $horaLlamada);
            });
        } catch (\Throwable $e) {
            report($e);

            return redirect()
                ->route('atenciones.edit', $atencion)
                ->withInput()
                ->with('error', __('No se pudo actualizar la atención. Intente de nuevo.'));
        }

        return redirect()
            ->route('atenciones.show', $atencion)
            ->with('status_atencion', __('Atención actualizada correctamente.'));
    }

    public function finalizar(Atencion $atencion): RedirectResponse
    {
        if ($atencion->estado === 'finalizado') {
            return redirect()
                ->route('dashboard')
                ->with('status', __('La atención ya estaba finalizada.'));
        }

        $atencion->forceFill(['estado' => 'finalizado'])->save();

        return redirect()
            ->route('dashboard')
            ->with('status', __('Atención finalizada correctamente.'));
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

    private function updatePaciente(Request $request, Paciente $paciente): void
    {
        $tipoDocumentoId = TipoDocumento::query()
            ->where('codigo', $request->input('tipo_documento'))
            ->value('id');

        if ($tipoDocumentoId === null) {
            throw new \InvalidArgumentException('Tipo de documento del paciente no reconocido.');
        }

        $paciente->forceFill([
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
        ])->save();
    }

    private function syncAcompanante(Request $request, Paciente $paciente, Atencion $atencion): ?Acompanante
    {
        if (! filled($request->input('nombre_acompanante'))) {
            $oldId = $atencion->acompanante_id;
            if ($oldId !== null) {
                $atencion->forceFill(['acompanante_id' => null])->save();
                $stillUsed = Atencion::query()->where('acompanante_id', $oldId)->exists();
                if (! $stillUsed) {
                    Acompanante::query()->whereKey($oldId)->delete();
                }
            }

            return null;
        }

        $tipoDocId = TipoDocumento::query()
            ->where('codigo', $request->input('doc_type_acompanante'))
            ->value('id');

        if ($tipoDocId === null) {
            throw new \InvalidArgumentException('Tipo de documento del acompañante no reconocido.');
        }

        $payload = [
            'tipo_documento_id' => $tipoDocId,
            'numero_documento' => $request->input('doc_num_acompanante'),
            'nombre' => $request->input('nombre_acompanante'),
            'parentesco' => $request->input('parentesco_acompanante'),
            'telefono' => $request->input('telefono_acompanante') ?: null,
        ];

        if ($atencion->acompanante_id !== null) {
            $existente = Acompanante::query()
                ->whereKey($atencion->acompanante_id)
                ->where('paciente_id', $paciente->id)
                ->first();
            if ($existente !== null) {
                $existente->forceFill($payload)->save();

                return $existente;
            }
        }

        return Acompanante::query()->create(array_merge($payload, [
            'paciente_id' => $paciente->id,
        ]));
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

    private function applyAtencionUpdate(
        Request $request,
        Atencion $atencion,
        ?Acompanante $acompanante,
        Carbon $horaLlamada,
    ): void {
        $cupsId = Cup::query()->where('codigo', $request->input('tipo_servicio'))->value('id');
        $causaExternaId = CausaExterna::query()->where('codigo', $request->input('causa_externa'))->value('id');
        $epsId = Eps::query()->where('codigo', $request->input('eps'))->value('id');
        $tipoUsuarioId = TipoUsuario::query()->where('codigo', $request->input('tipo_usuario'))->value('id');

        $atencion->forceFill([
            'acompanante_id' => $acompanante?->id,
            'ambulancia_id' => $request->filled('ambulancia_id') ? $request->integer('ambulancia_id') : null,
            'conductor_id' => $request->filled('conductor_id') ? $request->integer('conductor_id') : null,
            'medico_id' => $request->filled('medico_id') ? $request->integer('medico_id') : null,
            'hora_llamada' => $horaLlamada,
            'hora_despacho' => $this->parseDatetimeLocalOptional($request->input('hora_despacho')),
            'salida_base' => $this->parseDatetimeLocalOptional($request->input('salida_base')),
            'llegada_escena' => $this->parseDatetimeLocalOptional($request->input('llegada_escena')),
            'salida_escena' => $this->parseDatetimeLocalOptional($request->input('salida_escena')),
            'llegada_destino' => $this->parseDatetimeLocalOptional($request->input('llegada_destino')),
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
            'triage' => $request->filled('triage') ? $request->input('triage') : null,
        ])->save();
    }

    /**
     * Reglas alineadas con los campos marcados como obligatorios (*) en el formulario de nueva atención.
     *
     * @return array<string, mixed>
     */
    private function reglasNuevaAtencion(Request $request): array
    {
        return [
            'hora_llamada' => ['required', 'date'],
            'tipo_documento' => ['required', 'string', Rule::exists('tipo_documentos', 'codigo')],
            'numero_documento' => ['required', 'string', 'max:64'],
            'primer_nombre' => ['required', 'string', 'max:120'],
            'segundo_nombre' => ['nullable', 'string', 'max:120'],
            'primer_apellido' => ['required', 'string', 'max:120'],
            'segundo_apellido' => ['nullable', 'string', 'max:120'],
            'fecha_nacimiento' => ['required', 'date'],
            'sexo' => ['required', 'in:M,F,I'],
            'estado_civil' => ['required', 'in:S,C,D,V,U'],
            'direccion' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'telefono' => ['required', 'string', 'max:32'],
            'nombre_acompanante' => ['required', 'string', 'max:255'],
            'parentesco_acompanante' => ['required', 'string', 'max:8'],
            'doc_type_acompanante' => ['required', 'string', Rule::exists('tipo_documentos', 'codigo')],
            'doc_num_acompanante' => ['required', 'string', 'max:64'],
            'telefono_acompanante' => ['required', 'string', 'max:32'],
            'tipo_servicio' => ['required', 'string', Rule::exists('cups', 'codigo')],
            'causa_externa' => ['nullable', 'string'],
            'institucion_origen' => ['nullable', 'string', 'max:255'],
            'institucion_destino' => ['nullable', 'string', 'max:255'],
            'departamento_id' => ['required', 'integer', 'exists:departamentos,id'],
            'municipio_id' => [
                'required',
                'integer',
                Rule::exists('municipios', 'id')->where(function ($query) use ($request) {
                    $query->where('departamento_id', (int) $request->input('departamento_id'));
                }),
            ],
            'eps' => ['required', 'string', Rule::exists('eps', 'codigo')],
            'autorizacion_eps' => ['nullable', 'string', 'max:64'],
            'tipo_usuario' => ['required', 'string', Rule::exists('tipo_usuarios', 'codigo')],
            'zona' => ['required', 'in:U,R'],
            'ambulancia_id' => ['required', 'integer', 'exists:ambulancias,id'],
            'conductor_id' => ['required', 'integer', 'exists:conductores,id'],
            'medico_id' => ['nullable', 'integer', 'exists:users,id'],
        ];
    }

    /**
     * @return array<string, string>
     */
    private function atributosValidacionNuevaAtencion(): array
    {
        return [
            'hora_llamada' => __('hora de llamada'),
            'tipo_documento' => __('tipo de documento del paciente'),
            'numero_documento' => __('número de documento del paciente'),
            'primer_nombre' => __('primer nombre'),
            'segundo_nombre' => __('segundo nombre'),
            'primer_apellido' => __('primer apellido'),
            'segundo_apellido' => __('segundo apellido'),
            'fecha_nacimiento' => __('fecha de nacimiento'),
            'sexo' => __('sexo'),
            'estado_civil' => __('estado civil'),
            'direccion' => __('dirección'),
            'email' => __('correo electrónico'),
            'telefono' => __('teléfono'),
            'nombre_acompanante' => __('nombre del acompañante'),
            'parentesco_acompanante' => __('parentesco del acompañante'),
            'doc_type_acompanante' => __('tipo de documento del acompañante'),
            'doc_num_acompanante' => __('número de documento del acompañante'),
            'telefono_acompanante' => __('teléfono del acompañante'),
            'tipo_servicio' => __('tipo de servicio'),
            'causa_externa' => __('causa externa'),
            'institucion_origen' => __('institución origen'),
            'institucion_destino' => __('institución destino'),
            'departamento_id' => __('departamento'),
            'municipio_id' => __('municipio'),
            'eps' => __('EPS'),
            'autorizacion_eps' => __('número de autorización EPS'),
            'tipo_usuario' => __('tipo de usuario'),
            'zona' => __('zona'),
            'ambulancia_id' => __('móvil'),
            'conductor_id' => __('conductor'),
            'medico_id' => __('médico'),
        ];
    }

    private function parseDatetimeLocalOptional(?string $value): ?Carbon
    {
        if ($value === null || $value === '') {
            return null;
        }

        return Carbon::parse($value);
    }

    private function parseDatetimeLocal(?string $value): ?Carbon
    {
        if ($value === null || $value === '') {
            return null;
        }

        return Carbon::parse($value);
    }
}
