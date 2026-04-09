@php
    $user = auth()->user();
    $displayName = $user->name ?? 'Usuario';
    \Illuminate\Support\Carbon::setLocale(config('app.locale', 'es'));
    $todayLabel = now()->translatedFormat('d M Y');
    $footerStamp = now()->translatedFormat('d/m/Y H:i');
    $atencionModel = $atencion ?? null;
    $pacienteModel = $atencionModel?->paciente;
    $acompananteModel = $atencionModel?->acompanante;
    $editing = $atencionModel !== null;
    $caseRef = $editing
        ? 'APH-' . ($atencionModel->hora_llamada?->format('Y') ?? $atencionModel->created_at->format('Y')) . '-' . str_pad((string) $atencionModel->id, 6, '0', STR_PAD_LEFT)
        : 'APH-' . now()->format('Y') . ' · ' . __('Sin numerar');
    $fmtDtLocal = static function ($value) {
        if ($value === null) {
            return '';
        }

        return $value instanceof \Illuminate\Support\Carbon
            ? $value->format('Y-m-d\TH:i')
            : '';
    };
    $defaultDepDane = $editing
        ? (string) ($atencionModel->departamento?->dane_code ?? $atencionModel->municipio?->departamento?->dane_code ?? '17')
        : '17';
    $defaultMunDane = $editing
        ? (string) ($atencionModel->municipio?->dane_code ?? '')
        : '17380';
    $triageCurrent = old('triage', $atencionModel?->triage);
    $triageKey = match ($triageCurrent) {
        'I', '1' => 't1',
        'II', '2' => 't2',
        'III', '3' => 't3',
        'IV', '4' => 't4',
        'V', '5' => 't5',
        default => '',
    };
    $tipoDocPac = old('tipo_documento', $pacienteModel?->tipoDocumento?->codigo ?? 'CC');
    $sexoVal = old('sexo', $pacienteModel?->sexo);
    $estadoCivilVal = old('estado_civil', $pacienteModel?->estado_civil);
    $parentescoVal = old('parentesco_acompanante', $acompananteModel?->parentesco);
    $tipoDocAco = old('doc_type_acompanante', $acompananteModel?->tipoDocumento?->codigo ?? 'CC');
    $tipoServicioVal = old('tipo_servicio', $atencionModel?->tipo_servicio);
    $causaVal = old('causa_externa', $atencionModel?->causaExterna?->codigo);
    $epsVal = old('eps', $atencionModel?->eps?->codigo);
    $tipoUsuarioVal = old('tipo_usuario', $atencionModel?->tipoUsuario?->codigo);
    $zonaVal = old('zona', $atencionModel?->zona);
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $editing ? __('Editar atención') : __('Nueva atención') }} — {{ config('app.name', 'SysClinSoft') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('styles.css') }}">
    <link rel="stylesheet" href="{{ asset('dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('atencion.css') }}">
</head>
<body class="dashboard-page">
    <div class="dashboard-sidebar-backdrop" id="dashboard-sidebar-backdrop" aria-hidden="true"></div>
    <div class="dashboard-app">
        @include('partials.dashboard-sidebar')

        <div class="dashboard-main">
            <header class="dashboard-topbar">
                <div class="dashboard-topbar-left">
                    <button type="button" class="dashboard-menu-toggle" id="dashboard-menu-toggle" aria-label="{{ __('Abrir menú') }}" aria-expanded="false" aria-controls="dashboard-sidebar">
                        <x-lucide-menu />
                    </button>
                    <span class="dashboard-topbar-title">{{ $editing ? __('Editar atención') : __('Nueva atención') }}</span>
                </div>
                <div class="dashboard-topbar-right">
                    <span style="font-size:12px;color:var(--dash-text-secondary)">{{ __('Hoy') }}, {{ $todayLabel }}</span>
                </div>
            </header>

            <div class="dashboard-content">
                <form id="atencion-form-main" method="POST" action="{{ $editing ? route('atenciones.update', $atencionModel) : route('atenciones.nueva.store') }}">
                    @csrf
                    @if ($editing)
                        @method('PUT')
                    @endif
                    @if (session('status'))
                        <p role="status" style="margin:0 0 12px;padding:10px 12px;border-radius:8px;background:#d1fae5;color:#065f46;font-size:14px;">{{ session('status') }}</p>
                    @endif
                    @if (session('error'))
                        <p role="alert" style="margin:0 0 12px;padding:10px 12px;border-radius:8px;background:#fee2e2;color:#991b1b;font-size:14px;">{{ session('error') }}</p>
                    @endif
                    @if ($errors->any())
                        <p class="atencion-form-summary-error" role="alert" style="margin:0 0 12px;padding:10px 12px;border-radius:8px;background:#fee2e2;color:#991b1b;font-size:14px;">{{ __('Faltan datos obligatorios o hay errores en el formulario. Revise los campos indicados abajo.') }}</p>
                    @endif
                <div class="atencion-wrap">
                    <div class="atencion-topbar">
                        <div class="atencion-topbar-left">
                            <a href="{{ $editing ? route('atenciones.show', $atencionModel) : route('dashboard') }}" class="atencion-back-btn">
                                <x-lucide-chevron-left />
                                {{ __('Atenciones') }}
                            </a>
                            <div>
                                <div class="atencion-page-title">{{ $editing ? __('Editar atención') : __('Nueva atención') }}</div>
                                <div class="atencion-page-sub">{{ $caseRef }} · {{ $editing ? __('Actualizar datos iniciales') : __('Generando…') }}</div>
                            </div>
                        </div>
                        <div class="atencion-topbar-actions">
                            <button type="submit" name="action" value="iniciar" class="dashboard-btn-primary">{{ $editing ? __('Guardar cambios') : __('Iniciar atención') }}</button>
                        </div>
                    </div>

                    <nav class="atencion-steps" aria-label="{{ __('Secciones del formulario') }}">
                        <a href="#atencion-seccion-paciente" class="atencion-step is-active" data-atencion-step aria-current="true">
                            <span class="atencion-step-num">1</span>
                            <span class="atencion-step-label">{{ __('Identificación del paciente') }}</span>
                        </a>
                        <a href="#atencion-seccion-acompanante" class="atencion-step" data-atencion-step>
                            <span class="atencion-step-num">2</span>
                            <span class="atencion-step-label">{{ __('Identificación del acompañante') }}</span>
                        </a>
                        <a href="#atencion-seccion-servicio" class="atencion-step" data-atencion-step>
                            <span class="atencion-step-num">3</span>
                            <span class="atencion-step-label">{{ __('Datos del servicio') }}</span>
                        </a>
                        <a href="#atencion-seccion-tiempos" class="atencion-step" data-atencion-step>
                            <span class="atencion-step-num">4</span>
                            <span class="atencion-step-label">{{ __('Tiempos operacionales APH') }}</span>
                        </a>
                        <a href="#atencion-seccion-triage" class="atencion-step" data-atencion-step>
                            <span class="atencion-step-num">5</span>
                            <span class="atencion-step-label">{{ __('Clasificación de triage') }}</span>
                        </a>
                    </nav>


                    <div class="atencion-layout">
                        <div>
                            <div class="atencion-card" id="atencion-seccion-paciente" tabindex="-1">
                                <div class="atencion-card-head">
                                    <h3>
                                        <span class="atencion-section-icon" aria-hidden="true"><x-lucide-user /></span>
                                        {{ __('Identificación del paciente') }}
                                    </h3>
                                </div>
                                <div class="atencion-card-body">
                                    <div class="atencion-field-grid cols2" style="margin-bottom:12px">
                                        <div>
                                            <label for="doc-type">{{ __('Tipo de documento') }}<span class="atencion-req">*</span></label>
                                            <select id="doc-type" class="atencion-select @error('tipo_documento') atencion-input-invalid @enderror" name="tipo_documento">
                                                @forelse ($tipoDocumentos as $tipoDocumento)
                                                    <option value="{{ $tipoDocumento->codigo }}" @selected($tipoDocPac === $tipoDocumento->codigo)>{{ $tipoDocumento->nombre }}</option>
                                                @empty
                                                    <option value="" disabled>{{ __('No hay tipos de documento') }}</option>
                                                @endforelse
                                            </select>
                                            @error('tipo_documento')
                                                <p class="atencion-field-error" role="alert">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div>
                                            <label for="doc-num">{{ __('Número de documento') }}<span class="atencion-req">*</span></label>
                                            <input id="doc-num" class="atencion-input @error('numero_documento') atencion-input-invalid @enderror" type="text" name="numero_documento" value="{{ old('numero_documento', $pacienteModel?->numero_documento ?? '') }}" autocomplete="off">
                                            @error('numero_documento')
                                                <p class="atencion-field-error" role="alert">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div>
                                            <label for="pri-nom">{{ __('Primer nombre') }}<span class="atencion-req">*</span></label>
                                            <input id="pri-nom" class="atencion-input @error('primer_nombre') atencion-input-invalid @enderror" type="text" name="primer_nombre" value="{{ old('primer_nombre', $pacienteModel?->primer_nombre ?? '') }}" autocomplete="off">
                                            @error('primer_nombre')
                                                <p class="atencion-field-error" role="alert">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div>
                                            <label for="seg-nom">{{ __('Segundo nombre') }}</label>
                                            <input id="seg-nom" class="atencion-input @error('segundo_nombre') atencion-input-invalid @enderror" type="text" name="segundo_nombre" value="{{ old('segundo_nombre', $pacienteModel?->segundo_nombre ?? '') }}" autocomplete="off">
                                            @error('segundo_nombre')
                                                <p class="atencion-field-error" role="alert">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div>
                                            <label for="pri-ape">{{ __('Primer apellido') }}<span class="atencion-req">*</span></label>
                                            <input id="pri-ape" class="atencion-input @error('primer_apellido') atencion-input-invalid @enderror" type="text" name="primer_apellido" value="{{ old('primer_apellido', $pacienteModel?->primer_apellido ?? '') }}" autocomplete="off">
                                            @error('primer_apellido')
                                                <p class="atencion-field-error" role="alert">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div>
                                            <label for="seg-ape">{{ __('Segundo apellido') }}</label>
                                            <input id="seg-ape" class="atencion-input @error('segundo_apellido') atencion-input-invalid @enderror" type="text" name="segundo_apellido" value="{{ old('segundo_apellido', $pacienteModel?->segundo_apellido ?? '') }}" autocomplete="off">
                                            @error('segundo_apellido')
                                                <p class="atencion-field-error" role="alert">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div>
                                            <label for="fecha-nac">{{ __('Fecha de nacimiento') }}<span class="atencion-req">*</span></label>
                                            <input id="fecha-nac" class="atencion-input @error('fecha_nacimiento') atencion-input-invalid @enderror" type="date" name="fecha_nacimiento" value="{{ old('fecha_nacimiento', $pacienteModel?->fecha_nacimiento?->format('Y-m-d') ?? '') }}" autocomplete="off">
                                            @error('fecha_nacimiento')
                                                <p class="atencion-field-error" role="alert">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div>
                                            <label for="sexo">{{ __('Sexo') }}<span class="atencion-req">*</span></label>
                                            <select id="sexo" class="atencion-select @error('sexo') atencion-input-invalid @enderror" name="sexo">
                                                <option value="" @selected(! filled($sexoVal))>{{ __('Seleccione…') }}</option>
                                                @foreach ($sexos as $sexo => $nombre)
                                                    <option value="{{ $sexo }}" @selected(filled($sexoVal) && $sexoVal === $sexo)>{{ $nombre }}</option>
                                                @endforeach
                                            </select>
                                            @error('sexo')
                                                <p class="atencion-field-error" role="alert">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div>
                                            <label for="estado-civil">{{ __('Estado civil') }}<span class="atencion-req">*</span></label>
                                            <select id="estado-civil" class="atencion-select @error('estado_civil') atencion-input-invalid @enderror" name="estado_civil">
                                                <option value="" @selected(! filled($estadoCivilVal))>{{ __('Seleccione…') }}</option>
                                                @foreach ($estadosCiviles as $estadoCivil => $nombre)
                                                    <option value="{{ $estadoCivil }}" @selected(filled($estadoCivilVal) && $estadoCivilVal === $estadoCivil)>{{ $nombre }}</option>
                                                @endforeach
                                            </select>
                                            @error('estado_civil')
                                                <p class="atencion-field-error" role="alert">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div>
                                            <label for="direccion">{{ __('Dirección') }}<span class="atencion-req">*</span></label>
                                            <input id="direccion" class="atencion-input @error('direccion') atencion-input-invalid @enderror" type="text" name="direccion" value="{{ old('direccion', $pacienteModel?->direccion ?? '') }}" autocomplete="off">
                                            @error('direccion')
                                                <p class="atencion-field-error" role="alert">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div>
                                            <label for="email">{{ __('Email') }}<span class="atencion-req">*</span></label>
                                            <input id="email" class="atencion-input @error('email') atencion-input-invalid @enderror" type="email" name="email" value="{{ old('email', $pacienteModel?->email ?? '') }}" autocomplete="off">
                                            @error('email')
                                                <p class="atencion-field-error" role="alert">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div>
                                            <label for="telefono">{{ __('Teléfono') }}<span class="atencion-req">*</span></label>
                                            <input id="telefono" class="atencion-input @error('telefono') atencion-input-invalid @enderror" type="text" name="telefono" value="{{ old('telefono', $pacienteModel?->telefono ?? '') }}" autocomplete="off">
                                            @error('telefono')
                                                <p class="atencion-field-error" role="alert">{{ $message }}</p>
                                            @enderror
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class="atencion-card" id="atencion-seccion-acompanante" tabindex="-1">
                                <div class="atencion-card-head">
                                    <h3>
                                        <span class="atencion-section-icon" aria-hidden="true"><x-lucide-user /></span>
                                        {{ __('Identificación del acompañante') }}
                                    </h3>
                                </div>
                                <div class="atencion-card-body">
                                    <div class="atencion-field-grid cols2">
                                        <div>
                                            <label for="nombre-acompanante">{{ __('Nombre del acompañante') }}<span class="atencion-req">*</span></label>
                                            <input id="nombre-acompanante" class="atencion-input @error('nombre_acompanante') atencion-input-invalid @enderror" type="text" name="nombre_acompanante" value="{{ old('nombre_acompanante', $acompananteModel?->nombre ?? '') }}" autocomplete="off">
                                            @error('nombre_acompanante')
                                                <p class="atencion-field-error" role="alert">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div>
                                            <label for="parentesco-acompanante">{{ __('Parentesco del acompañante') }}<span class="atencion-req">*</span></label>
                                            <select id="parentesco-acompanante" class="atencion-select @error('parentesco_acompanante') atencion-input-invalid @enderror" name="parentesco_acompanante">
                                                <option value="" @selected(! filled($parentescoVal))>{{ __('Seleccione…') }}</option>
                                                @foreach ($parentescos as $parentesco => $nombre)
                                                    <option value="{{ $parentesco }}" @selected(filled($parentescoVal) && $parentescoVal === $parentesco)>{{ $nombre }}</option>
                                                @endforeach
                                            </select>
                                            @error('parentesco_acompanante')
                                                <p class="atencion-field-error" role="alert">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div>
                                            <label for="doc-type-acompanante">{{ __('Tipo de documento del acompañante') }}<span class="atencion-req">*</span></label>
                                            <select id="doc-type-acompanante" class="atencion-select @error('doc_type_acompanante') atencion-input-invalid @enderror" name="doc_type_acompanante">
                                                @forelse ($tipoDocumentos as $tipoDocumento)
                                                    <option value="{{ $tipoDocumento->codigo }}" @selected($tipoDocAco === $tipoDocumento->codigo)>{{ $tipoDocumento->nombre }}</option>
                                                @empty
                                                    <option value="" disabled>{{ __('No hay tipos de documento') }}</option>
                                                @endforelse
                                            </select>
                                            @error('doc_type_acompanante')
                                                <p class="atencion-field-error" role="alert">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div>
                                            <label for="doc-num-acompanante">{{ __('Número de documento del acompañante') }}<span class="atencion-req">*</span></label>
                                            <input id="doc-num-acompanante" class="atencion-input @error('doc_num_acompanante') atencion-input-invalid @enderror" type="text" name="doc_num_acompanante" value="{{ old('doc_num_acompanante', $acompananteModel?->numero_documento ?? '') }}" autocomplete="off">
                                            @error('doc_num_acompanante')
                                                <p class="atencion-field-error" role="alert">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div>
                                            <label for="telefono-acompanante">{{ __('Teléfono del acompañante') }}<span class="atencion-req">*</span></label>
                                            <input id="telefono-acompanante" class="atencion-input @error('telefono_acompanante') atencion-input-invalid @enderror" type="text" name="telefono_acompanante" value="{{ old('telefono_acompanante', $acompananteModel?->telefono ?? '') }}" autocomplete="off">
                                            @error('telefono_acompanante')
                                                <p class="atencion-field-error" role="alert">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="atencion-card" id="atencion-seccion-servicio" tabindex="-1">
                                <div class="atencion-card-head">
                                    <h3>
                                        <span class="atencion-section-icon" aria-hidden="true"><x-lucide-clipboard-list /></span>
                                        {{ __('Datos del servicio') }}
                                    </h3>
                                </div>
                                <div class="atencion-card-body">
                                    <div class="atencion-field-grid cols2">
                                        <div>
                                            <label for="tipo-servicio">{{ __('Tipo de servicio') }}<span class="atencion-req">*</span></label>
                                            <select id="tipo-servicio" class="atencion-select @error('tipo_servicio') atencion-input-invalid @enderror" name="tipo_servicio">
                                                <option value="" @selected(! filled($tipoServicioVal))>{{ __('Seleccione…') }}</option>
                                                @foreach ($cups as $cup)
                                                    <option value="{{ $cup->codigo }}" @selected(filled($tipoServicioVal) && $tipoServicioVal === $cup->codigo)>{{ $cup->nombre }}</option>
                                                @endforeach
                                            </select>
                                            @error('tipo_servicio')
                                                <p class="atencion-field-error" role="alert">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div>
                                            <label for="causa-externa">{{ __('Causa externa') }}</label>
                                            <select id="causa-externa" class="atencion-select @error('causa_externa') atencion-input-invalid @enderror" name="causa_externa">
                                                <option value="" @selected(! filled($causaVal))>{{ __('Seleccione…') }}</option>
                                                @foreach ($causaExternas as $causaExterna)
                                                    <option value="{{ $causaExterna->codigo }}" @selected(filled($causaVal) && $causaVal === $causaExterna->codigo)>{{ $causaExterna->nombre }}</option>
                                                @endforeach
                                            </select>
                                            @error('causa_externa')
                                                <p class="atencion-field-error" role="alert">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div>
                                            <label for="origen">{{ __('Institución origen') }}</label>
                                            <input id="origen" class="atencion-input @error('institucion_origen') atencion-input-invalid @enderror" type="text" name="institucion_origen" value="{{ old('institucion_origen', $atencionModel?->institucion_origen ?? '') }}" placeholder="{{ __('Escena del accidente / IPS remitente') }}">
                                            @error('institucion_origen')
                                                <p class="atencion-field-error" role="alert">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div>
                                            <label for="destino">{{ __('Institución destino') }}</label>
                                            <input id="destino" class="atencion-input @error('institucion_destino') atencion-input-invalid @enderror" type="text" name="institucion_destino" value="{{ old('institucion_destino', $atencionModel?->institucion_destino ?? '') }}" placeholder="{{ __('Hospital / IPS destino') }}">
                                            @error('institucion_destino')
                                                <p class="atencion-field-error" role="alert">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div>
                                            <label for="departamento">{{ __('Departamento') }}<span class="atencion-req">*</span></label>
                                            <select id="departamento" class="atencion-select @error('departamento_id') atencion-input-invalid @enderror" name="departamento_id" data-atencion-geo-base="{{ url('/geo/departamentos') }}" data-atencion-default-dep-dane="{{ $defaultDepDane }}">
                                                <option value="">{{ __('Cargando…') }}</option>
                                            </select>
                                            @error('departamento_id')
                                                <p class="atencion-field-error" role="alert">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div>
                                            <label for="municipio">{{ __('Municipio') }}<span class="atencion-req">*</span></label>
                                            <select id="municipio" class="atencion-select @error('municipio_id') atencion-input-invalid @enderror" name="municipio_id" disabled data-atencion-default-mun-dane="{{ $defaultMunDane }}">
                                                <option value="">{{ __('Seleccione un departamento') }}</option>
                                            </select>
                                            @error('municipio_id')
                                                <p class="atencion-field-error" role="alert">{{ $message }}</p>
                                            @enderror
                                        </div>                                      
                                        <div>
                                            <label for="eps">{{ __('EPS') }}<span class="atencion-req">*</span></label>
                                            <select id="eps" class="atencion-select @error('eps') atencion-input-invalid @enderror" name="eps">
                                                <option value="" @selected(! filled($epsVal))>{{ __('Seleccione…') }}</option>
                                                @foreach ($eps as $epsItem)
                                                    <option value="{{ $epsItem->codigo }}" @selected(filled($epsVal) && $epsVal === $epsItem->codigo)>{{ $epsItem->nombre }}</option>
                                                @endforeach
                                            </select>
                                            @error('eps')
                                                <p class="atencion-field-error" role="alert">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div>
                                            <label for="autorizacion">{{ __('Nro. autorización EPS') }}</label>
                                            <input id="autorizacion" class="atencion-input @error('autorizacion_eps') atencion-input-invalid @enderror" type="text" name="autorizacion_eps" value="{{ old('autorizacion_eps', $atencionModel?->autorizacion_eps ?? '') }}" placeholder="{{ __('Opcional') }}">
                                            @error('autorizacion_eps')
                                                <p class="atencion-field-error" role="alert">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div>
                                            <label for="tipo-usuario">{{ __('Tipo de usuario') }}<span class="atencion-req">*</span></label>
                                            <select id="tipo-usuario" class="atencion-select @error('tipo_usuario') atencion-input-invalid @enderror" name="tipo_usuario">
                                                <option value="" @selected(! filled($tipoUsuarioVal))>{{ __('Seleccione…') }}</option>
                                                @forelse ($tipoUsuarios as $tipoUsuario)
                                                    <option value="{{ $tipoUsuario->codigo }}" @selected(filled($tipoUsuarioVal) && $tipoUsuarioVal === $tipoUsuario->codigo)>{{ $tipoUsuario->nombre }}</option>
                                                @empty
                                                    <option value="" disabled>{{ __('No hay tipos de usuario') }}</option>
                                                @endforelse
                                            </select>
                                            @error('tipo_usuario')
                                                <p class="atencion-field-error" role="alert">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div>
                                            <label for="zona">{{ __('Zona') }}<span class="atencion-req">*</span></label>
                                            <select id="zona" class="atencion-select @error('zona') atencion-input-invalid @enderror" name="zona">
                                                <option value="" @selected(! filled($zonaVal))>{{ __('Seleccione…') }}</option>
                                                @foreach ($zonas as $zonaKey => $nombre)
                                                    <option value="{{ $zonaKey }}" @selected(filled($zonaVal) && $zonaVal === $zonaKey)>{{ $nombre }}</option>
                                                @endforeach
                                            </select>
                                            @error('zona')
                                                <p class="atencion-field-error" role="alert">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="atencion-card" id="atencion-seccion-tiempos" tabindex="-1">
                                <div class="atencion-card-head">
                                    <h3>
                                        <span class="atencion-section-icon" aria-hidden="true"><x-lucide-clock /></span>
                                        {{ __('Tiempos operacionales APH') }}
                                    </h3>
                                    <span class="atencion-card-head-note">{{ __('Requeridos por RIPS') }}</span>
                                </div>
                                <div class="atencion-card-body">
                                    <div class="atencion-field-grid cols3">
                                        <div>
                                            <label for="ambulancia-placa">{{ __('movil') }}<span class="atencion-req">*</span></label>
                                            <select id="ambulancia-placa" class="atencion-select @error('ambulancia_id') atencion-input-invalid @enderror" name="ambulancia_id">
                                                <option value="">{{ __('Seleccione una movil') }}</option>
                                                @foreach ($ambulancias as $ambulancia)
                                                    <option value="{{ $ambulancia->id }}" @selected(old('ambulancia_id', $atencionModel?->ambulancia_id) == $ambulancia->id)>{{ $ambulancia->placa ?: $ambulancia->codigo }}</option>
                                                @endforeach
                                            </select>
                                            @error('ambulancia_id')
                                                <p class="atencion-field-error" role="alert">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div>
                                            <label for="conductor-nombre">{{ __('Conductor') }}<span class="atencion-req">*</span></label>                                                
                                            <select id="conductor-nombre" class="atencion-select @error('conductor_id') atencion-input-invalid @enderror" name="conductor_id">
                                                <option value="">{{ __('Seleccione un conductor') }}</option>
                                                @foreach ($conductores as $conductor)
                                                    <option value="{{ $conductor->id }}" @selected(old('conductor_id', $atencionModel?->conductor_id) == $conductor->id)>{{ $conductor->nombre }}</option>
                                                @endforeach
                                            </select>
                                            @error('conductor_id')
                                                <p class="atencion-field-error" role="alert">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div>
                                            <label for="medico-nombre">{{ __('Médico') }}</label>
                                            <select id="medico-nombre" class="atencion-select @error('medico_id') atencion-input-invalid @enderror" name="medico_id">
                                                <option value="">{{ __('Seleccione un médico') }}</option>
                                                @foreach ($medicos as $medico)
                                                    <option value="{{ $medico->id }}" @selected(old('medico_id', $atencionModel?->medico_id) == $medico->id)>{{ $medico->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('medico_id')
                                                <p class="atencion-field-error" role="alert">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="atencion-field-grid cols3">
                                        <div>
                                            <div class="atencion-tiempo-row">
                                                <label>{{ __('Hora de llamada') }}<span class="atencion-req">*</span></label>
                                                <input class="atencion-input atencion-datetime @error('hora_llamada') atencion-input-invalid @enderror" type="datetime-local" name="hora_llamada" value="{{ old('hora_llamada', $editing ? $fmtDtLocal($atencionModel->hora_llamada) : '') }}">
                                                <button type="button" class="atencion-tiempo-now" data-atencion-now>{{ __('Usar hora actual') }}</button>
                                            </div>
                                            @error('hora_llamada')
                                                <p class="atencion-field-error" role="alert">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div class="atencion-tiempo-row">
                                            <label>{{ __('Hora de despacho') }}</label>
                                            <input class="atencion-input atencion-datetime" type="datetime-local" name="hora_despacho" value="{{ old('hora_despacho', $editing ? $fmtDtLocal($atencionModel->hora_despacho) : '') }}">
                                            <button type="button" class="atencion-tiempo-now" data-atencion-now>{{ __('Usar hora actual') }}</button>
                                        </div>
                                        <div class="atencion-tiempo-row">
                                            <label>{{ __('Salida de base') }}</label>
                                            <input class="atencion-input atencion-datetime" type="datetime-local" name="salida_base" value="{{ old('salida_base', $editing ? $fmtDtLocal($atencionModel->salida_base) : '') }}">
                                            <button type="button" class="atencion-tiempo-now" data-atencion-now>{{ __('Usar hora actual') }}</button>
                                        </div>
                                        <div class="atencion-tiempo-row">
                                            <label>{{ __('Llegada a escena') }}</label>
                                            <input class="atencion-input atencion-datetime" type="datetime-local" name="llegada_escena" value="{{ old('llegada_escena', $editing ? $fmtDtLocal($atencionModel->llegada_escena) : '') }}">
                                            <button type="button" class="atencion-tiempo-now" data-atencion-now>{{ __('Usar hora actual') }}</button>
                                        </div>
                                        <div class="atencion-tiempo-row">
                                            <label>{{ __('Salida de escena') }}</label>
                                            <input class="atencion-input atencion-datetime" type="datetime-local" name="salida_escena" value="{{ old('salida_escena', $editing ? $fmtDtLocal($atencionModel->salida_escena) : '') }}">
                                            <button type="button" class="atencion-tiempo-now" data-atencion-now>{{ __('Usar hora actual') }}</button>
                                        </div>
                                        <div class="atencion-tiempo-row">
                                            <label>{{ __('Llegada a destino') }}</label>
                                            <input class="atencion-input atencion-datetime" type="datetime-local" name="llegada_destino" value="{{ old('llegada_destino', $editing ? $fmtDtLocal($atencionModel->llegada_destino) : '') }}">
                                            <button type="button" class="atencion-tiempo-now" data-atencion-now>{{ __('Usar hora actual') }}</button>
                                        </div>
                                    </div>
                                    <div class="atencion-tiempos-summary">
                                        <span>{{ __('T. respuesta:') }} <strong>—</strong></span>
                                        <span>{{ __('T. en escena:') }} <strong>—</strong></span>
                                        <span>{{ __('T. total:') }} <strong>—</strong></span>
                                    </div>
                                </div>
                            </div>

                            <div class="atencion-card" id="atencion-seccion-triage" tabindex="-1">
                                <div class="atencion-card-head">
                                    <h3>
                                        <span class="atencion-section-icon" aria-hidden="true"><x-lucide-heart /></span>
                                        {{ __('Clasificación de triage') }}
                                    </h3>
                                </div>
                                <div class="atencion-card-body">
                                    <input type="hidden" name="triage" id="atencion-triage-input" value="{{ $triageCurrent ?? '' }}">
                                    <div class="atencion-triage-grid" role="group" aria-label="{{ __('Triage') }}">
                                        <button type="button" class="atencion-triage-btn t1{{ $triageKey === 't1' ? ' is-selected' : '' }}" data-triage="I">{{ __('I') }}<span class="atencion-triage-label">{{ __('Rojo') }}</span></button>
                                        <button type="button" class="atencion-triage-btn t2{{ $triageKey === 't2' ? ' is-selected' : '' }}" data-triage="II">{{ __('II') }}<span class="atencion-triage-label">{{ __('Naranja') }}</span></button>
                                        <button type="button" class="atencion-triage-btn t3{{ $triageKey === 't3' ? ' is-selected' : '' }}" data-triage="III">{{ __('III') }}<span class="atencion-triage-label">{{ __('Amarillo') }}</span></button>
                                        <button type="button" class="atencion-triage-btn t4{{ $triageKey === 't4' ? ' is-selected' : '' }}" data-triage="IV">{{ __('IV') }}<span class="atencion-triage-label">{{ __('Verde') }}</span></button>
                                        <button type="button" class="atencion-triage-btn t5{{ $triageKey === 't5' ? ' is-selected' : '' }}" data-triage="V">{{ __('V') }}<span class="atencion-triage-label">{{ __('Negro') }}</span></button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div>
                            <div class="atencion-sidebar-card" id="atencion-seccion-manejo" tabindex="-1">
                                <div class="atencion-sidebar-card-head">{{ __('Manejo prehospitalario') }}</div>
                                <div class="atencion-sidebar-card-body">
                                    <div class="atencion-check-row" role="button" tabindex="0" data-atencion-check>
                                        <div class="atencion-check-box is-checked" aria-hidden="true"></div>
                                        <span class="atencion-check-label">{{ __('Soporte vital básico') }}</span>
                                    </div>
                                    <div class="atencion-check-row" role="button" tabindex="0" data-atencion-check>
                                        <div class="atencion-check-box" aria-hidden="true"></div>
                                        <span class="atencion-check-label">{{ __('Soporte vital avanzado') }}</span>
                                    </div>
                                    <div class="atencion-check-row" role="button" tabindex="0" data-atencion-check>
                                        <div class="atencion-check-box is-checked" aria-hidden="true"></div>
                                        <span class="atencion-check-label">{{ __('Inmovilización') }}</span>
                                    </div>
                                    <div class="atencion-check-row" role="button" tabindex="0" data-atencion-check>
                                        <div class="atencion-check-box" aria-hidden="true"></div>
                                        <span class="atencion-check-label">{{ __('Vía aérea avanzada') }}</span>
                                    </div>
                                    <div class="atencion-check-row" role="button" tabindex="0" data-atencion-check>
                                        <div class="atencion-check-box" aria-hidden="true"></div>
                                        <span class="atencion-check-label">{{ __('RCP aplicado') }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="atencion-sidebar-card" id="atencion-seccion-glasgow" tabindex="-1">
                                <div class="atencion-sidebar-card-head">{{ __('Escala de Glasgow') }}</div>
                                <div class="atencion-sidebar-card-body">
                                    <div class="atencion-glasgow-grid">
                                        <span class="atencion-glasgow-label">{{ __('Ocular (1–4)') }}</span>
                                        <select id="atencion-glasgow-ocular" class="atencion-select" name="glasgow_ocular" aria-label="{{ __('Glasgow ocular') }}">
                                            @foreach (['4', '3', '2', '1'] as $v)
                                                <option value="{{ $v }}">{{ $v }}</option>
                                            @endforeach
                                        </select>
                                        <span class="atencion-glasgow-label">{{ __('Verbal (1–5)') }}</span>
                                        <select id="atencion-glasgow-verbal" class="atencion-select" name="glasgow_verbal" aria-label="{{ __('Glasgow verbal') }}">
                                            @foreach (['5', '4', '3', '2', '1'] as $v)
                                                <option value="{{ $v }}">{{ $v }}</option>
                                            @endforeach
                                        </select>
                                        <span class="atencion-glasgow-label">{{ __('Motor (1–6)') }}</span>
                                        <select id="atencion-glasgow-motor" class="atencion-select" name="glasgow_motor" aria-label="{{ __('Glasgow motor') }}">
                                            @foreach (['6', '5', '4', '3', '2', '1'] as $v)
                                                <option value="{{ $v }}">{{ $v }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="atencion-glasgow-total">
                                        <span>{{ __('Total Glasgow') }}</span>
                                        <strong id="atencion-glasgow-total">15 / 15</strong>
                                    </div>
                                </div>
                            </div>

                            <div class="atencion-sidebar-card" id="atencion-seccion-resumen" tabindex="-1">
                                <div class="atencion-sidebar-card-head">{{ __('Resumen atención') }}</div>
                                <div class="atencion-sidebar-card-body" style="display:flex;flex-direction:column;gap:8px">
                                    <div class="atencion-resumen-row">
                                        <span>{{ __('Paciente') }}</span>
                                        <span class="atencion-resumen-muted">—</span>
                                    </div>
                                    <div class="atencion-resumen-row">
                                        <span>{{ __('Edad') }}</span>
                                        <span class="atencion-resumen-muted">—</span>
                                    </div>
                                    <div class="atencion-resumen-row">
                                        <span>{{ __('Servicio') }}</span>
                                        <span class="atencion-resumen-muted">—</span>
                                    </div>
                                    <div class="atencion-resumen-row">
                                        <span>{{ __('T. respuesta') }}</span>
                                        <span class="atencion-resumen-muted">—</span>
                                    </div>
                                    <div class="atencion-resumen-row">
                                        <span>{{ __('Triage') }}</span>
                                        <span class="atencion-resumen-muted">—</span>
                                    </div>
                                    <div class="atencion-resumen-row">
                                        <span>{{ __('Glasgow') }}</span>
                                        <span id="atencion-glasgow-resumen">15/15</span>
                                    </div>
                                    <div class="atencion-resumen-divider">
                                        <div class="atencion-resumen-row">
                                            <span>FHIR</span>
                                            <span class="atencion-resumen-muted">{{ __('Pendiente sincronización') }}</span>
                                        </div>
                                        <div class="atencion-resumen-row" style="margin-top:4px">
                                            <span>RIPS</span>
                                            <span class="atencion-resumen-muted">{{ __('Se generará al finalizar') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="atencion-footer-bar">
                        <span class="atencion-footer-info">{{ __('Atendiendo:') }} {{ $displayName }} · {{ $footerStamp }}</span>
                        <div class="atencion-footer-actions">
                            <button type="submit" name="action" value="iniciar" class="dashboard-btn-primary">{{ $editing ? __('Guardar cambios') : __('Iniciar atención') }} →</button>
                        </div>
                    </div>
                </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        (function () {
            var sidebar = document.getElementById('dashboard-sidebar');
            var backdrop = document.getElementById('dashboard-sidebar-backdrop');
            var toggle = document.getElementById('dashboard-menu-toggle');
            var closeBtn = document.getElementById('dashboard-sidebar-close');
            var mq = window.matchMedia('(max-width: 900px)');

            function openMenu() {
                if (!mq.matches) return;
                sidebar.classList.add('is-open');
                backdrop.classList.add('is-visible');
                backdrop.setAttribute('aria-hidden', 'false');
                document.body.style.overflow = 'hidden';
                if (toggle) toggle.setAttribute('aria-expanded', 'true');
            }

            function closeMenu() {
                sidebar.classList.remove('is-open');
                backdrop.classList.remove('is-visible');
                backdrop.setAttribute('aria-hidden', 'true');
                document.body.style.overflow = '';
                if (toggle) toggle.setAttribute('aria-expanded', 'false');
            }

            function onToggle() {
                if (sidebar.classList.contains('is-open')) closeMenu();
                else openMenu();
            }

            if (toggle) toggle.addEventListener('click', onToggle);
            if (closeBtn) closeBtn.addEventListener('click', closeMenu);
            if (backdrop) backdrop.addEventListener('click', closeMenu);

            window.addEventListener('resize', function () {
                if (!mq.matches) closeMenu();
            });

            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape' && mq.matches) closeMenu();
            });

            if (sidebar) {
                sidebar.querySelectorAll('a.dashboard-nav-item').forEach(function (link) {
                    link.addEventListener('click', function () {
                        if (mq.matches) closeMenu();
                    });
                });
            }

            var atencionWrap = document.querySelector('.atencion-wrap');
            var stepLinks = atencionWrap ? atencionWrap.querySelectorAll('a[data-atencion-step]') : [];
            if (atencionWrap && stepLinks.length) {
                var sectionIds = [];
                stepLinks.forEach(function (a) {
                    var hid = (a.getAttribute('href') || '').replace(/^#/, '');
                    if (hid) sectionIds.push(hid);
                });
                function setActiveStep(activeAnchor) {
                    stepLinks.forEach(function (a) {
                        var on = a === activeAnchor;
                        a.classList.toggle('is-active', on);
                        if (on) {
                            a.setAttribute('aria-current', 'true');
                        } else {
                            a.removeAttribute('aria-current');
                        }
                    });
                }
                stepLinks.forEach(function (a) {
                    a.addEventListener('click', function (e) {
                        var id = (a.getAttribute('href') || '').replace(/^#/, '');
                        var el = id ? document.getElementById(id) : null;
                        if (!el) return;
                        e.preventDefault();
                        setActiveStep(a);
                        el.scrollIntoView({ behavior: 'smooth', block: 'start' });
                        try {
                            el.focus({ preventScroll: true });
                        } catch (err) { /* ignore */ }
                    });
                });
                var scrollSyncTimer;
                function syncActiveStepFromScroll() {
                    var offset = 96;
                    var y = window.scrollY + offset;
                    var bestAnchor = stepLinks[0];
                    sectionIds.forEach(function (id, i) {
                        var sec = document.getElementById(id);
                        if (!sec) return;
                        var top = sec.getBoundingClientRect().top + window.scrollY;
                        if (top <= y && stepLinks[i]) {
                            bestAnchor = stepLinks[i];
                        }
                    });
                    if (bestAnchor) {
                        setActiveStep(bestAnchor);
                    }
                }
                window.addEventListener('scroll', function () {
                    clearTimeout(scrollSyncTimer);
                    scrollSyncTimer = setTimeout(syncActiveStepFromScroll, 60);
                }, { passive: true });
                syncActiveStepFromScroll();
                var hash = window.location.hash.replace(/^#/, '');
                if (hash) {
                    stepLinks.forEach(function (a) {
                        if ((a.getAttribute('href') || '') === '#' + hash) {
                            setActiveStep(a);
                        }
                    });
                }
            }

            function localDatetimeValue() {
                var d = new Date();
                d.setMinutes(d.getMinutes() - d.getTimezoneOffset());
                return d.toISOString().slice(0, 16);
            }

            document.querySelectorAll('[data-atencion-now]').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    var row = btn.closest('.atencion-tiempo-row');
                    var input = row && row.querySelector('.atencion-datetime');
                    if (input) input.value = localDatetimeValue();
                });
            });

            document.querySelectorAll('[data-atencion-check]').forEach(function (row) {
                function flip() {
                    var box = row.querySelector('.atencion-check-box');
                    if (box) box.classList.toggle('is-checked');
                }
                row.addEventListener('click', flip);
                row.addEventListener('keydown', function (e) {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        flip();
                    }
                });
            });

            document.querySelectorAll('.atencion-triage-btn').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    document.querySelectorAll('.atencion-triage-btn').forEach(function (b) {
                        b.classList.remove('is-selected');
                    });
                    btn.classList.add('is-selected');
                    var tri = document.getElementById('atencion-triage-input');
                    if (tri) tri.value = btn.getAttribute('data-triage') || '';
                });
            });

            function parseGlasgowPart(id) {
                var el = document.getElementById(id);
                if (!el) return 0;
                var n = parseInt(el.value, 10);
                return isNaN(n) ? 0 : n;
            }

            function updateGlasgowTotal() {
                var sum = parseGlasgowPart('atencion-glasgow-ocular') + parseGlasgowPart('atencion-glasgow-verbal') + parseGlasgowPart('atencion-glasgow-motor');
                var totalEl = document.getElementById('atencion-glasgow-total');
                if (totalEl) totalEl.textContent = sum + ' / 15';
                var resumenEl = document.getElementById('atencion-glasgow-resumen');
                if (resumenEl) resumenEl.textContent = sum + '/15';
            }

            ['atencion-glasgow-ocular', 'atencion-glasgow-verbal', 'atencion-glasgow-motor'].forEach(function (id) {
                var el = document.getElementById(id);
                if (el) {
                    el.addEventListener('change', updateGlasgowTotal);
                }
            });
            updateGlasgowTotal();

            var depSel = document.getElementById('departamento');
            var munSel = document.getElementById('municipio');
            if (depSel && munSel) {
                var geoBase = (depSel.getAttribute('data-atencion-geo-base') || '').replace(/\/$/, '');
                var defaultDepDane = depSel.getAttribute('data-atencion-default-dep-dane') || '';
                var defaultMunDane = munSel.getAttribute('data-atencion-default-mun-dane') || '';
                var msgSelDep = @json(__('Seleccione un departamento'));
                var msgSelMun = @json(__('Seleccione un municipio'));
                var msgLoading = @json(__('Cargando…'));
                var msgErrDep = @json(__('No se pudieron cargar los departamentos'));
                var msgErrMun = @json(__('No se pudieron cargar los municipios'));

                function setMunicipiosPlaceholder(msg) {
                    munSel.innerHTML = '';
                    var o = document.createElement('option');
                    o.value = '';
                    o.textContent = msg;
                    munSel.appendChild(o);
                    munSel.value = '';
                    munSel.disabled = true;
                }

                function loadMunicipios(depId, selectDane) {
                    if (!depId) {
                        setMunicipiosPlaceholder(msgSelDep);
                        return;
                    }
                    munSel.disabled = true;
                    setMunicipiosPlaceholder(msgLoading);
                    fetch(geoBase + '/' + encodeURIComponent(depId) + '/municipios', {
                        headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                        credentials: 'same-origin',
                    })
                        .then(function (r) {
                            if (!r.ok) throw new Error('municipios');
                            return r.json();
                        })
                        .then(function (list) {
                            if (!Array.isArray(list)) throw new Error('municipios');
                            munSel.innerHTML = '';
                            var ph = document.createElement('option');
                            ph.value = '';
                            ph.textContent = msgSelMun;
                            munSel.appendChild(ph);
                            list.forEach(function (m) {
                                var opt = document.createElement('option');
                                opt.value = m.id;
                                opt.textContent = m.nombre;
                                opt.setAttribute('data-dane-code', m.dane_code);
                                munSel.appendChild(opt);
                            });
                            munSel.disabled = false;
                            if (selectDane) {
                                for (var i = 0; i < munSel.options.length; i++) {
                                    if (munSel.options[i].getAttribute('data-dane-code') === selectDane) {
                                        munSel.selectedIndex = i;
                                        break;
                                    }
                                }
                            }
                        })
                        .catch(function () {
                            setMunicipiosPlaceholder(msgErrMun);
                        });
                }

                fetch(geoBase, {
                    headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    credentials: 'same-origin',
                })
                    .then(function (r) {
                        if (!r.ok) throw new Error('dep');
                        return r.json();
                    })
                    .then(function (list) {
                        if (!Array.isArray(list)) throw new Error('dep');
                        depSel.innerHTML = '';
                        var ph = document.createElement('option');
                        ph.value = '';
                        ph.textContent = msgSelDep;
                        depSel.appendChild(ph);
                        var defaultId = '';
                        list.forEach(function (d) {
                            var opt = document.createElement('option');
                            opt.value = d.id;
                            opt.textContent = d.nombre;
                            opt.setAttribute('data-dane-code', d.dane_code);
                            depSel.appendChild(opt);
                            if (defaultDepDane && d.dane_code === defaultDepDane) {
                                defaultId = String(d.id);
                            }
                        });
                        if (defaultId) {
                            depSel.value = defaultId;
                            loadMunicipios(defaultId, defaultMunDane);
                        } else {
                            setMunicipiosPlaceholder(msgSelDep);
                        }
                    })
                    .catch(function () {
                        depSel.innerHTML = '';
                        var o = document.createElement('option');
                        o.value = '';
                        o.textContent = msgErrDep;
                        depSel.appendChild(o);
                        setMunicipiosPlaceholder(msgSelDep);
                    });

                depSel.addEventListener('change', function () {
                    loadMunicipios(depSel.value, '');
                });

                var atencionMainForm = document.getElementById('atencion-form-main');
                if (atencionMainForm) {
                    atencionMainForm.addEventListener('submit', function () {
                        /* Los <select disabled> no se envían: hay que habilitar para incluir municipio_id. */
                        munSel.disabled = false;
                    });
                }
            }
        })();
    </script>
</body>
</html>
