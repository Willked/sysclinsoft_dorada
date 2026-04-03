@php
    $user = auth()->user();
    $displayName = $user->name ?? 'Usuario';
    $parts = preg_split('/\s+/', trim($displayName));
    $initials = '';
    foreach (array_slice($parts, 0, 2) as $p) {
        $initials .= mb_strtoupper(mb_substr($p, 0, 1));
    }
    $initials = $initials !== '' ? $initials : 'U';
    \Illuminate\Support\Carbon::setLocale(config('app.locale', 'es'));
    $todayLabel = now()->translatedFormat('d M Y');
    $footerStamp = now()->translatedFormat('d/m/Y H:i');
    $caseRef = 'APH-' . now()->format('Y') . '-000015';
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('Nueva atención') }} — {{ config('app.name', 'SysClinSoft') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('styles.css') }}">
    <link rel="stylesheet" href="{{ asset('dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('atencion.css') }}">
</head>
<body class="dashboard-page">
    <div class="dashboard-sidebar-backdrop" id="dashboard-sidebar-backdrop" aria-hidden="true"></div>
    <div class="dashboard-app">
        <aside id="dashboard-sidebar" class="dashboard-sidebar" aria-label="{{ __('Navegación principal') }}">
            <div class="dashboard-sidebar-brand">
                <div class="dashboard-sidebar-logo">
                    <span>HC Ambulancias</span>
                    <small>Sistema prehospitalario</small>
                </div>
                <button type="button" class="dashboard-sidebar-close" id="dashboard-sidebar-close" aria-label="{{ __('Cerrar menú') }}">
                    <x-lucide-x />
                </button>
            </div>
            <div class="dashboard-nav-section">{{ __('Principal') }}</div>
            <a href="{{ route('dashboard') }}" class="dashboard-nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <x-lucide-home />
                {{ __('Dashboard') }}
            </a>
            <a href="{{ route('atenciones.nueva') }}" class="dashboard-nav-item {{ request()->routeIs('atenciones.nueva') ? 'active' : '' }}">
                <x-lucide-clipboard-list />
                {{ __('Atenciones') }}
            </a>
            <div class="dashboard-nav-item" role="presentation">
                <x-lucide-users />
                {{ __('Pacientes') }}
            </div>
            <div class="dashboard-nav-section">{{ __('Administrativo') }}</div>
            <div class="dashboard-nav-item" role="presentation">
                <x-lucide-file-text />
                RIPS / ADRES
            </div>
            <div class="dashboard-nav-item" role="presentation">
                <x-lucide-zap />
                FHIR / MinSalud
            </div>
            <div class="dashboard-sidebar-bottom">
                <div class="dashboard-user-row">
                    <div class="dashboard-avatar" aria-hidden="true">{{ $initials }}</div>
                    <div class="dashboard-user-info">
                        <span>{{ $displayName }}</span>
                        <small>{{ __('Paramédico') }}</small>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="dashboard-btn-outline">{{ __('Cerrar sesión') }}</button>
                </form>
            </div>
        </aside>

        <div class="dashboard-main">
            <header class="dashboard-topbar">
                <div class="dashboard-topbar-left">
                    <button type="button" class="dashboard-menu-toggle" id="dashboard-menu-toggle" aria-label="{{ __('Abrir menú') }}" aria-expanded="false" aria-controls="dashboard-sidebar">
                        <x-lucide-menu />
                    </button>
                    <span class="dashboard-topbar-title">{{ __('Nueva atención') }}</span>
                </div>
                <div class="dashboard-topbar-right">
                    <span style="font-size:12px;color:var(--dash-text-secondary)">{{ __('Hoy') }}, {{ $todayLabel }}</span>
                </div>
            </header>

            <div class="dashboard-content">
                <div class="atencion-wrap">
                    <div class="atencion-topbar">
                        <div class="atencion-topbar-left">
                            <a href="{{ route('dashboard') }}" class="atencion-back-btn">
                                <x-lucide-chevron-left />
                                {{ __('Atenciones') }}
                            </a>
                            <div>
                                <div class="atencion-page-title">{{ __('Nueva atención') }}</div>
                                <div class="atencion-page-sub">{{ $caseRef }} · {{ __('Generando…') }}</div>
                            </div>
                        </div>
                        <div class="atencion-topbar-actions">
                            <button type="button" class="dashboard-btn-outline">{{ __('Guardar borrador') }}</button>
                            <button type="button" class="dashboard-btn-primary">{{ __('Iniciar atención') }}</button>
                        </div>
                    </div>

                    <div class="atencion-steps" role="list">
                        <div class="atencion-step is-active" id="step-paciente" role="listitem">
                            <span class="atencion-step-num">1</span>
                            <span class="atencion-step-label">{{ __('Paciente') }}</span>
                        </div>
                        <div class="atencion-step" role="listitem">
                            <span class="atencion-step-num">2</span>
                            <span class="atencion-step-label">{{ __('Acompañante') }}</span>
                        </div>
                        <div class="atencion-step" role="listitem">
                            <span class="atencion-step-num">3</span>
                            <span class="atencion-step-label">{{ __('Servicio') }}</span>
                        </div>
                        <div class="atencion-step" role="listitem">
                            <span class="atencion-step-num">4</span>
                            <span class="atencion-step-label">{{ __('Tiempos APH') }}</span>
                        </div>
                        <div class="atencion-step" role="listitem">
                            <span class="atencion-step-num">4</span>
                            <span class="atencion-step-label">{{ __('Clínica') }}</span>
                        </div>
                        <div class="atencion-step" role="listitem">
                            <span class="atencion-step-num">5</span>
                            <span class="atencion-step-label">{{ __('Signos vitales') }}</span>
                        </div>
                    </div>

                    <div class="atencion-layout">
                        <div>
                            <div class="atencion-card">
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
                                            <select id="doc-type" class="atencion-select" name="tipo_documento">
                                                <option selected>{{ __('CC — Cédula de ciudadanía') }}</option>
                                                <option>{{ __('TI — Tarjeta de identidad') }}</option>
                                                <option>{{ __('CE — Cédula de extranjería') }}</option>
                                                <option>{{ __('PA — Pasaporte') }}</option>
                                                <option>{{ __('MS — Menor sin identificación') }}</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label for="doc-num">{{ __('Número de documento') }}<span class="atencion-req">*</span></label>
                                            <input id="doc-num" class="atencion-input is-highlight" type="text" name="numero_documento" value="1090456789" autocomplete="off">
                                        </div>
                                        <div>
                                            <label for="pri-nom">{{ __('Primer nombre') }}<span class="atencion-req">*</span></label>
                                            <input id="pri-nom" class="atencion-input is-highlight" type="text" name="primer_nombre" value="pedro" autocomplete="off">
                                        </div>
                                        <div>
                                            <label for="seg-nom">{{ __('Segundo nombre') }}</label>
                                            <input id="seg-nom" class="atencion-input" type="text" name="segundo_nombre" value="andres" autocomplete="off">
                                        </div>
                                        <div>
                                            <label for="pri-ape">{{ __('Primer apellido') }}<span class="atencion-req">*</span></label>
                                            <input id="pri-ape" class="atencion-input is-highlight" type="text" name="primer_apellido" value="garcia" autocomplete="off">
                                        </div>
                                        <div>
                                            <label for="seg-ape">{{ __('Segundo apellido') }}</label>
                                            <input id="seg-ape" class="atencion-input" type="text" name="segundo_apellido" value="rodriguez" autocomplete="off">
                                        </div>
                                        <div>
                                            <label for="fecha-nac">{{ __('Fecha de nacimiento') }}<span class="atencion-req">*</span></label>
                                            <input id="fecha-nac" class="atencion-input is-highlight" type="date" name="fecha_nacimiento" value="1990-01-01" autocomplete="off">
                                        </div>
                                        <div>
                                            <label for="sexo">{{ __('Sexo') }}<span class="atencion-req">*</span></label>
                                            <select id="sexo" class="atencion-select" name="sexo">
                                                <option selected>{{ __('Masculino') }}</option>
                                                <option>{{ __('Femenino') }}</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label for="estado-civil">{{ __('Estado civil') }}<span class="atencion-req">*</span></label>
                                            <select id="estado-civil" class="atencion-select" name="estado_civil">
                                                <option selected>{{ __('Soltero') }}</option>
                                                <option>{{ __('Casado') }}</option>
                                                <option>{{ __('Divorciado') }}</option>
                                                <option>{{ __('Viudo') }}</option>
                                                <option>{{ __('Unión libre') }}</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label for="direccion">{{ __('Dirección') }}<span class="atencion-req">*</span></label>
                                            <input id="direccion" class="atencion-input is-highlight" type="text" name="direccion" value="Calle 123 # 45-67" autocomplete="off">
                                        </div>
                                        <div>
                                            <label for="email">{{ __('Email') }}<span class="atencion-req">*</span></label>
                                            <input id="email" class="atencion-input is-highlight" type="email" name="email" value="mariaelena@gmail.com" autocomplete="off">
                                        </div>
                                        <div>
                                            <label for="telefono">{{ __('Teléfono') }}<span class="atencion-req">*</span></label>
                                            <input id="telefono" class="atencion-input is-highlight" type="text" name="telefono" value="3178901234" autocomplete="off">
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class="atencion-card">
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
                                            <input id="nombre-acompanante" class="atencion-input is-highlight" type="text" name="nombre_acompanante" value="Juan Perez" autocomplete="off">
                                        </div>
                                        <div>
                                            <label for="parentesco-acompanante">{{ __('Parentesco del acompañante') }}<span class="atencion-req">*</span></label>
                                            <select id="parentesco-acompanante" class="atencion-select" name="parentesco_acompanante">
                                                <option selected>{{ __('Padre') }}</option>
                                                <option>{{ __('Madre') }}</option>
                                                <option>{{ __('Hermano') }}</option>
                                                <option>{{ __('Hermana') }}</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label for="doc-type-acompanante">{{ __('Tipo de documento del acompañante') }}<span class="atencion-req">*</span></label>
                                            <select id="doc-type-acompanante" class="atencion-select" name="doc_type_acompanante">
                                                <option selected>{{ __('CC — Cédula de ciudadanía') }}</option>
                                                <option>{{ __('TI — Tarjeta de identidad') }}</option>
                                                <option>{{ __('CE — Cédula de extranjería') }}</option>
                                                <option>{{ __('PA — Pasaporte') }}</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label for="doc-num-acompanante">{{ __('Número de documento del acompañante') }}<span class="atencion-req">*</span></label>
                                            <input id="doc-num-acompanante" class="atencion-input is-highlight" type="text" name="doc_num_acompanante" value="1090456789" autocomplete="off">
                                        </div>
                                        <div>
                                            <label for="telefono-acompanante">{{ __('Teléfono del acompañante') }}<span class="atencion-req">*</span></label>
                                            <input id="telefono-acompanante" class="atencion-input is-highlight" type="text" name="telefono_acompanante" value="3178901234" autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="atencion-card">
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
                                            <select id="tipo-servicio" class="atencion-select" name="tipo_servicio">
                                                <option>{{ __('01 — Urgencias en escena') }}</option>
                                                <option selected>{{ __('02 — Traslado primario') }}</option>
                                                <option>{{ __('03 — Traslado secundario') }}</option>
                                                <option>{{ __('04 — Traslado neonatal') }}</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label for="causa-externa">{{ __('Causa externa') }}</label>
                                            <select id="causa-externa" class="atencion-select" name="causa_externa">
                                                <option selected>{{ __('01 — Accidente de tránsito') }}</option>
                                                <option>{{ __('30 — Enfermedad general') }}</option>
                                                <option>{{ __('02 — Lesión por agresión') }}</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label for="origen">{{ __('Institución origen') }}</label>
                                            <input id="origen" class="atencion-input" type="text" name="institucion_origen" placeholder="{{ __('Escena del accidente / IPS remitente') }}">
                                        </div>
                                        <div>
                                            <label for="destino">{{ __('Institución destino') }}</label>
                                            <input id="destino" class="atencion-input" type="text" name="institucion_destino" value="{{ __('Hospital Universitario de Ibagué') }}">
                                        </div>
                                        <div>
                                            <label for="departamento">{{ __('Departamento') }}<span class="atencion-req">*</span></label>
                                            <select id="departamento" class="atencion-select" name="departamento">
                                                <option selected>{{ __('Tolima') }}</option>
                                                <option>{{ __('Cundinamarca') }}</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label for="municipio">{{ __('Municipio') }}<span class="atencion-req">*</span></label>
                                            <select id="municipio" class="atencion-select" name="municipio">
                                                <option selected>{{ __('Ibagué') }}</option>
                                                <option>{{ __('Cali') }}</option>
                                            </select>
                                        </div>                                      
                                        <div>
                                            <label for="eps">{{ __('EPS') }}<span class="atencion-req">*</span></label>
                                            <select id="eps" class="atencion-select" name="eps">
                                                <option selected>{{ __('Sanitas') }}</option>
                                                <option>{{ __('Colmedica') }}</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label for="autorizacion">{{ __('Nro. autorización EPS') }}</label>
                                            <input id="autorizacion" class="atencion-input" type="text" name="autorizacion_eps" placeholder="{{ __('Opcional') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="atencion-card">
                                <div class="atencion-card-head">
                                    <h3>
                                        <span class="atencion-section-icon" aria-hidden="true"><x-lucide-clock /></span>
                                        {{ __('Tiempos operacionales APH') }}
                                    </h3>
                                    <span class="atencion-card-head-note">{{ __('Requeridos por RIPS') }}</span>
                                </div>
                                <div class="atencion-card-body">
                                    <div class="atencion-field-grid cols3">
                                        <div class="atencion-tiempo-row">
                                            <label>{{ __('Hora de llamada') }}<span class="atencion-req">*</span></label>
                                            <input class="atencion-input atencion-datetime" type="datetime-local" name="hora_llamada" value="2026-03-28T14:32">
                                            <button type="button" class="atencion-tiempo-now" data-atencion-now>{{ __('Usar hora actual') }}</button>
                                        </div>
                                        <div class="atencion-tiempo-row">
                                            <label>{{ __('Hora de despacho') }}</label>
                                            <input class="atencion-input atencion-datetime" type="datetime-local" name="hora_despacho" value="2026-03-28T14:35">
                                            <button type="button" class="atencion-tiempo-now" data-atencion-now>{{ __('Usar hora actual') }}</button>
                                        </div>
                                        <div class="atencion-tiempo-row">
                                            <label>{{ __('Salida de base') }}</label>
                                            <input class="atencion-input atencion-datetime" type="datetime-local" name="salida_base" value="2026-03-28T14:36">
                                            <button type="button" class="atencion-tiempo-now" data-atencion-now>{{ __('Usar hora actual') }}</button>
                                        </div>
                                        <div class="atencion-tiempo-row">
                                            <label>{{ __('Llegada a escena') }}</label>
                                            <input class="atencion-input atencion-datetime" type="datetime-local" name="llegada_escena" value="2026-03-28T14:44">
                                            <button type="button" class="atencion-tiempo-now" data-atencion-now>{{ __('Usar hora actual') }}</button>
                                        </div>
                                        <div class="atencion-tiempo-row">
                                            <label>{{ __('Salida de escena') }}</label>
                                            <input class="atencion-input atencion-datetime" type="datetime-local" name="salida_escena" value="">
                                            <button type="button" class="atencion-tiempo-now" data-atencion-now>{{ __('Usar hora actual') }}</button>
                                        </div>
                                        <div class="atencion-tiempo-row">
                                            <label>{{ __('Llegada a destino') }}</label>
                                            <input class="atencion-input atencion-datetime" type="datetime-local" name="llegada_destino" value="">
                                            <button type="button" class="atencion-tiempo-now" data-atencion-now>{{ __('Usar hora actual') }}</button>
                                        </div>
                                    </div>
                                    <div class="atencion-tiempos-summary">
                                        <span>{{ __('T. respuesta:') }} <strong>12 min</strong></span>
                                        <span>{{ __('T. en escena:') }} <strong>— min</strong></span>
                                        <span>{{ __('T. total:') }} <strong>— min</strong></span>
                                    </div>
                                </div>
                            </div>

                            <div class="atencion-card">
                                <div class="atencion-card-head">
                                    <h3>
                                        <span class="atencion-section-icon" aria-hidden="true"><x-lucide-heart /></span>
                                        {{ __('Clasificación de triage') }}
                                    </h3>
                                </div>
                                <div class="atencion-card-body">
                                    <div class="atencion-triage-grid" role="group" aria-label="{{ __('Triage') }}">
                                        <button type="button" class="atencion-triage-btn t1">{{ __('I') }}<span class="atencion-triage-label">{{ __('Rojo') }}</span></button>
                                        <button type="button" class="atencion-triage-btn t2">{{ __('II') }}<span class="atencion-triage-label">{{ __('Naranja') }}</span></button>
                                        <button type="button" class="atencion-triage-btn t3 is-selected">{{ __('III') }}<span class="atencion-triage-label">{{ __('Amarillo') }}</span></button>
                                        <button type="button" class="atencion-triage-btn t4">{{ __('IV') }}<span class="atencion-triage-label">{{ __('Verde') }}</span></button>
                                        <button type="button" class="atencion-triage-btn t5">{{ __('V') }}<span class="atencion-triage-label">{{ __('Negro') }}</span></button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div>
                            <div class="atencion-sidebar-card">
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

                            <div class="atencion-sidebar-card">
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

                            <div class="atencion-sidebar-card">
                                <div class="atencion-sidebar-card-head">{{ __('Resumen atención') }}</div>
                                <div class="atencion-sidebar-card-body" style="display:flex;flex-direction:column;gap:8px">
                                    <div class="atencion-resumen-row">
                                        <span>{{ __('Paciente') }}</span>
                                        <span>{{ __('M. García Rodríguez') }}</span>
                                    </div>
                                    <div class="atencion-resumen-row">
                                        <span>{{ __('Edad') }}</span>
                                        <span>{{ __('30 años') }}</span>
                                    </div>
                                    <div class="atencion-resumen-row">
                                        <span>{{ __('Servicio') }}</span>
                                        <span>{{ __('Traslado primario') }}</span>
                                    </div>
                                    <div class="atencion-resumen-row">
                                        <span>{{ __('T. respuesta') }}</span>
                                        <span class="atencion-ok">12 min ✓</span>
                                    </div>
                                    <div class="atencion-resumen-row">
                                        <span>{{ __('Triage') }}</span>
                                        <span class="atencion-triage-pill">{{ __('III — Amarillo') }}</span>
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
                        <span class="atencion-footer-info">{{ __('Atendiendo:') }} {{ $displayName }} · {{ __('Ambulancia') }} 03 · {{ $footerStamp }}</span>
                        <div class="atencion-footer-actions">
                            <button type="button" class="dashboard-btn-outline">{{ __('Cancelar') }}</button>
                            <button type="button" class="dashboard-btn-outline">{{ __('Guardar borrador') }}</button>
                            <button type="button" class="dashboard-btn-primary">{{ __('Iniciar atención') }} →</button>
                        </div>
                    </div>
                </div>
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
        })();
    </script>
</body>
</html>
