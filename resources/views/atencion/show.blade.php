@php
    $user = auth()->user();
    $displayName = $user->name ?? 'Usuario';
    $parts = preg_split('/\s+/', trim($displayName));
    $initialsUser = '';
    foreach (array_slice($parts, 0, 2) as $p) {
        $initialsUser .= mb_strtoupper(mb_substr($p, 0, 1));
    }
    $initialsUser = $initialsUser !== '' ? $initialsUser : 'U';
    \Illuminate\Support\Carbon::setLocale(config('app.locale', 'es'));
    $todayLabel = now()->translatedFormat('d M Y');

    $p = $atencion->paciente;
    $nombrePaciente = $p
        ? trim(implode(' ', array_filter([
            $p->primer_nombre,
            $p->segundo_nombre,
            $p->primer_apellido,
            $p->segundo_apellido,
        ])))
        : '—';
    $iniParts = $p ? array_filter([$p->primer_nombre, $p->primer_apellido]) : [];
    $initialsPt = '';
    foreach (array_slice($iniParts, 0, 2) as $w) {
        $initialsPt .= mb_strtoupper(mb_substr($w, 0, 1));
    }
    $initialsPt = $initialsPt !== '' ? $initialsPt : '?';

    $refAnio = $atencion->hora_llamada?->format('Y') ?? $atencion->created_at->format('Y');
    $ref = 'APH-'.$refAnio.'-'.str_pad((string) $atencion->id, 6, '0', STR_PAD_LEFT);

    $sexoLabel = match ($p?->sexo) {
        'M' => __('Masculino'),
        'F' => __('Femenino'),
        'I' => __('Indeterminado'),
        default => $p?->sexo ?? '—',
    };
    $edad = $p?->fecha_nacimiento?->age;

    $tipoServicio = $atencion->cup?->nombre ?? $atencion->tipo_servicio ?? __('Sin clasificar');
    $fechaHoraServicio = $atencion->hora_llamada?->format('d/m/Y H:i') ?? '—';
    $medicoNombre = $atencion->medico?->name ?? __('Sin asignar');

    $estadoBadge = match ($atencion->estado) {
        'en_atencion' => 'en-atencion',
        'finalizado' => 'finalizado',
        default => 'en-atencion',
    };
    $estadoLabel = match ($atencion->estado) {
        'en_atencion' => __('En atención'),
        'finalizado' => __('Finalizado'),
        default => $atencion->estado,
    };

    $triagePill = match ($atencion->triage) {
        'I', '1' => __('Triage I · Rojo'),
        'II', '2' => __('Triage II · Naranja'),
        'III', '3' => __('Triage III · Amarillo'),
        'IV', '4' => __('Triage IV · Verde'),
        'V', '5' => __('Triage V · Negro'),
        default => $atencion->triage ? __('Triage').' '.$atencion->triage : null,
    };

    $signos = $atencion->signosVitales;
    $notasClinicas = $atencion->notasClinicas;
    $ultimaToma = $signos->last();
    $showSignosModal = $errors->signosVitales->any();
    $showNotaModal = $errors->notaClinica->any();
    $minsEscena = null;
    if ($atencion->hora_llamada && $atencion->llegada_escena) {
        $minsEscena = (int) $atencion->hora_llamada->diffInMinutes($atencion->llegada_escena);
    }

    $fmtH = static fn ($dt) => $dt ? $dt->format('H:i') : null;
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $ref }} — {{ __('Historia clínica') }} — {{ config('app.name', 'SysClinSoft') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('styles.css') }}">
    <link rel="stylesheet" href="{{ asset('dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('historia-clinica.css') }}">
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
            <a href="{{ route('atenciones.nueva') }}" class="dashboard-nav-item {{ request()->routeIs('atenciones.nueva', 'atenciones.show', 'atenciones.edit') ? 'active' : '' }}">
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
                    <div class="dashboard-avatar" aria-hidden="true">{{ $initialsUser }}</div>
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
                    <span class="dashboard-topbar-title">{{ __('Historia clínica') }}</span>
                </div>
                <div class="dashboard-topbar-right">
                    <span style="font-size:12px;color:var(--dash-text-secondary)">{{ __('Hoy') }}, {{ $todayLabel }}</span>
                </div>
            </header>

            <div class="dashboard-content hc-wrap">
                <div class="hc-topbar">
                    <div class="hc-topbar-left">
                        <a href="{{ route('dashboard') }}" class="hc-back-btn">
                            <x-lucide-chevron-left class="hc-back-icon" />
                            {{ __('Atenciones') }}
                        </a>
                        <div>
                            <div class="hc-ref-row">
                                <span class="hc-ref">{{ $ref }}</span>
                                <span class="hc-badge {{ $estadoBadge }} dot">{{ $estadoLabel }}</span>
                                @if ($triagePill)
                                    <span class="hc-triage-pill">{{ $triagePill }}</span>
                                @endif
                            </div>
                            <div class="hc-subtitle">{{ $tipoServicio }} · {{ $fechaHoraServicio }} · {{ $medicoNombre }}</div>
                        </div>
                    </div>
                    <div class="hc-actions">
                        <a href="{{ route('atenciones.edit', $atencion) }}" class="hc-btn-outline" style="text-decoration:none;display:inline-flex;align-items:center;justify-content:center">{{ __('Editar') }}</a>
                        @if ($atencion->estado !== 'finalizado')
                            <form method="POST" action="{{ route('atenciones.finalizar', $atencion) }}" class="hc-inline-form" onsubmit="return confirm(@json(__('¿Confirma finalizar esta atención?')));">
                                @csrf
                                <button type="submit" class="hc-btn-danger">{{ __('Finalizar atención') }}</button>
                            </form>
                        @endif
                    </div>
                </div>

                @if (session('status_atencion'))
                    <div class="hc-inline-alert success" style="margin:0 0 14px">{{ session('status_atencion') }}</div>
                @endif

                <div class="hc-grid">
                    <div>
                        <div class="hc-card">
                            <div class="hc-card-head">
                                <h3>{{ __('Paciente') }}</h3>
                                <span class="hc-link-muted" style="opacity:0.5;cursor:not-allowed">{{ __('Ver ficha') }}</span>
                            </div>
                            <div class="hc-card-body">
                                <div class="hc-patient-row">
                                    <div class="hc-avatar" aria-hidden="true">{{ $initialsPt }}</div>
                                    <div>
                                        <div class="hc-patient-name">{{ $nombrePaciente }}</div>
                                        <div class="hc-patient-meta">
                                            @if ($p?->tipoDocumento)
                                                {{ $p->tipoDocumento->nombre }} {{ $p->numero_documento }}
                                            @else
                                                —
                                            @endif
                                            @if ($edad !== null)
                                                · {{ $edad }} {{ __('años') }}
                                            @endif
                                            · {{ $sexoLabel }}
                                        </div>
                                    </div>
                                </div>
                                <dl class="hc-dl">
                                    <dt>{{ __('EPS') }}</dt>
                                    <dd>{{ $atencion->eps?->nombre ?? '—' }}</dd>
                                    <dt>{{ __('Tipo usuario') }}</dt>
                                    <dd>
                                        @if ($atencion->tipoUsuario)
                                            {{ $atencion->tipoUsuario->codigo }} — {{ $atencion->tipoUsuario->nombre }}
                                        @else
                                            —
                                        @endif
                                    </dd>
                                    <dt>{{ __('Municipio') }}</dt>
                                    <dd>
                                        @if ($atencion->municipio)
                                            {{ $atencion->municipio->nombre }} ({{ $atencion->municipio->dane_code }})
                                        @else
                                            —
                                        @endif
                                    </dd>
                                </dl>
                            </div>
                        </div>

                        <div class="hc-card">
                            <div class="hc-card-head"><h3>{{ __('Tiempos APH') }}</h3></div>
                            <div style="padding:0">
                                <div class="hc-time-row">
                                    <span class="hc-time-label">{{ __('Llamada') }}</span>
                                    <span class="hc-time-val">{{ $fmtH($atencion->hora_llamada) ?? '—' }}</span>
                                </div>
                                <div class="hc-time-row">
                                    <span class="hc-time-label">{{ __('Despacho') }}</span>
                                    <span class="hc-time-val">{{ $fmtH($atencion->hora_despacho) ?? '—' }}</span>
                                </div>
                                <div class="hc-time-row">
                                    <span class="hc-time-label">{{ __('Salida base') }}</span>
                                    <span class="hc-time-val">{{ $fmtH($atencion->salida_base) ?? '—' }}</span>
                                </div>
                                <div class="hc-time-row">
                                    <span class="hc-time-label">{{ __('Llegada escena') }}</span>
                                    <span class="hc-time-val {{ $minsEscena !== null ? 'ok' : '' }}">
                                        @if ($atencion->llegada_escena)
                                            {{ $fmtH($atencion->llegada_escena) }}
                                            @if ($minsEscena !== null)
                                                · {{ $minsEscena }} min ✓
                                            @endif
                                        @else
                                            —
                                        @endif
                                    </span>
                                </div>
                                <div class="hc-time-row">
                                    <span class="hc-time-label">{{ __('Salida escena') }}</span>
                                    <span class="hc-time-val muted">{{ $fmtH($atencion->salida_escena) ?? '—' }}</span>
                                </div>
                                <div class="hc-time-row">
                                    <span class="hc-time-label">{{ __('Llegada destino') }}</span>
                                    <span class="hc-time-val muted">{{ $fmtH($atencion->llegada_destino) ?? '—' }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="hc-card">
                            <div class="hc-card-head"><h3>{{ __('Integraciones') }}</h3></div>
                            <div class="hc-card-body">
                                <div class="hc-fhir-ok">{{ __('FHIR R4 — pendiente de sincronizar') }}</div>
                                <div class="hc-mono">—</div>
                                <div class="hc-rips-warn">{{ __('RIPS pendiente · Se generará al finalizar') }}</div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <div class="hc-card">
                            <div class="hc-section-tabs" role="tablist">
                                <button type="button" class="hc-tab active" role="tab" aria-selected="true" data-hc-tab="0">{{ __('Signos vitales') }}</button>
                                <button type="button" class="hc-tab" role="tab" aria-selected="false" data-hc-tab="1">{{ __('APH clínica') }}</button>
                                <button type="button" class="hc-tab" role="tab" aria-selected="false" data-hc-tab="2">{{ __('Diagnósticos') }}</button>
                                <button type="button" class="hc-tab" role="tab" aria-selected="false" data-hc-tab="3">{{ __('Procedimientos') }}</button>
                            </div>

                            <div class="hc-panel is-active" data-hc-panel="0" role="tabpanel">
                                <div class="hc-vitals-toolbar">
                                    <span style="font-size:12px;color:var(--dash-text-secondary)">
                                        {{ $signos->count() }}
                                        {{ $signos->count() === 1 ? __('toma registrada') : __('tomas registradas') }}
                                    </span>
                                    <button type="button" class="hc-btn-primary" style="font-size:12px;padding:6px 14px" id="open-signos-modal">+ {{ __('Registrar toma') }}</button>
                                </div>
                                @if (session('status_signos'))
                                    <div class="hc-inline-alert success">{{ session('status_signos') }}</div>
                                @endif
                                @if ($errors->signosVitales->any())
                                    <div class="hc-inline-alert error">{{ $errors->signosVitales->first() }}</div>
                                @endif
                                <div class="hc-vitals-table-wrap">
                                    <table class="hc-vitals-table">
                                        <thead>
                                            <tr>
                                                <th>{{ __('Hora') }}</th>
                                                <th>{{ __('TA (mmHg)') }}</th>
                                                <th>{{ __('FC (/min)') }}</th>
                                                <th>{{ __('FR (/min)') }}</th>
                                                <th>{{ __('Temp (°C)') }}</th>
                                                <th>{{ __('SpO₂ (%)') }}</th>
                                                <th>{{ __('Glucosa') }}</th>
                                                <th>{{ __('Obs.') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($signos as $sv)
                                                @php
                                                    $ta = ($sv->presion_sistolica && $sv->presion_diastolica)
                                                        ? $sv->presion_sistolica.'/'.$sv->presion_diastolica
                                                        : '—';
                                                    $taClass = 'hc-val-ok';
                                                    if ($sv->presion_sistolica && $sv->presion_diastolica) {
                                                        if ($sv->presion_sistolica >= 140 || $sv->presion_diastolica >= 90) {
                                                            $taClass = 'hc-val-warn';
                                                        }
                                                    }
                                                @endphp
                                                <tr>
                                                    <td style="font-weight:500">{{ $sv->medicion_en?->format('H:i') ?? '—' }}</td>
                                                    <td class="{{ $taClass }}">{{ $ta }}</td>
                                                    <td class="hc-val-ok">{{ $sv->frecuencia_cardiaca ?? '—' }}</td>
                                                    <td class="hc-val-ok">{{ $sv->frecuencia_respiratoria ?? '—' }}</td>
                                                    <td class="hc-val-ok">{{ $sv->temperatura !== null ? $sv->temperatura : '—' }}</td>
                                                    <td class="hc-val-ok">{{ $sv->saturacion_oxigeno !== null ? $sv->saturacion_oxigeno.'%' : '—' }}</td>
                                                    <td class="hc-val-ok">{{ $sv->glicemia ?? '—' }}</td>
                                                    <td>{{ $sv->observaciones ? \Illuminate\Support\Str::limit($sv->observaciones, 24) : '—' }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="8" class="hc-empty" style="padding:20px;text-align:center">{{ __('No hay signos vitales registrados.') }}</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                @if ($ultimaToma)
                                    <div class="hc-trend">
                                        <div class="hc-trend-title">{{ __('Última toma — tendencia') }}</div>
                                        <div class="hc-trend-chips">
                                            @if ($ultimaToma->presion_sistolica && $ultimaToma->presion_diastolica)
                                                <div class="hc-trend-chip">
                                                    <div class="big">{{ $ultimaToma->presion_sistolica }}/{{ $ultimaToma->presion_diastolica }}</div>
                                                    <div class="small">{{ __('Tensión arterial') }}</div>
                                                </div>
                                            @endif
                                            @if ($ultimaToma->saturacion_oxigeno !== null)
                                                <div class="hc-trend-chip">
                                                    <div class="big">{{ $ultimaToma->saturacion_oxigeno }}%</div>
                                                    <div class="small">{{ __('SpO₂') }}</div>
                                                </div>
                                            @endif
                                            @if ($ultimaToma->frecuencia_cardiaca !== null)
                                                <div class="hc-trend-chip">
                                                    <div class="big">{{ $ultimaToma->frecuencia_cardiaca }}</div>
                                                    <div class="small">{{ __('Frec. cardíaca') }}</div>
                                                </div>
                                            @endif
                                            @if ($ultimoGlasgow && $ultimoGlasgow->total !== null)
                                                <div class="hc-trend-chip">
                                                    <div class="big">{{ $ultimoGlasgow->total }}/15</div>
                                                    <div class="small">{{ __('Glasgow') }}</div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div class="hc-panel" data-hc-panel="1" role="tabpanel" hidden>
                                <div style="padding:14px 16px">
                                    <div class="hc-vitals-toolbar hc-panel-toolbar">
                                        <span style="font-size:12px;color:var(--dash-text-secondary)">
                                            {{ $notasClinicas->count() }}
                                            {{ $notasClinicas->count() === 1 ? __('nota registrada') : __('notas registradas') }}
                                        </span>
                                        <button type="button" class="hc-btn-primary" style="font-size:12px;padding:6px 14px" id="open-nota-modal">+ {{ __('Registrar nota') }}</button>
                                    </div>
                                    @if (session('status_nota'))
                                        <div class="hc-inline-alert success">{{ session('status_nota') }}</div>
                                    @endif
                                    @if ($errors->notaClinica->any())
                                        <div class="hc-inline-alert error">{{ $errors->notaClinica->first() }}</div>
                                    @endif
                                    <div class="hc-section-title">{{ __('Hallazgos / evaluación física') }}</div>
                                    @if (filled($atencion->evaluacion_fisica))
                                        <p class="hc-prose">{{ $atencion->evaluacion_fisica }}</p>
                                    @else
                                        <p class="hc-empty" style="padding:0">{{ __('Sin registro aún.') }}</p>
                                    @endif
                                    @if (filled($atencion->comentario))
                                        <div class="hc-section-title" style="margin-top:16px">{{ __('Comentarios') }}</div>
                                        <p class="hc-prose">{{ $atencion->comentario }}</p>
                                    @endif
                                    <div class="hc-section-title" style="margin-top:16px">{{ __('Historial de notas clínicas') }}</div>
                                    @forelse ($notasClinicas as $nota)
                                        <article class="hc-note-item">
                                            <div class="hc-note-meta">
                                                <strong>{{ $nota->usuario?->name ?? __('Usuario') }}</strong>
                                                · {{ $nota->tipo_redactor === 'medico' ? __('Médico') : __('Enfermería') }}
                                                · {{ $nota->created_at?->format('d/m/Y H:i') }}
                                            </div>
                                            <p class="hc-prose">{{ $nota->contenido }}</p>
                                        </article>
                                    @empty
                                        <p class="hc-empty" style="padding:0">{{ __('Sin notas registradas.') }}</p>
                                    @endforelse
                                </div>
                            </div>

                            <div class="hc-panel" data-hc-panel="2" role="tabpanel" hidden>
                                <div style="padding:14px 16px">
                                    <div class="hc-section-title">{{ __('Diagnósticos CIE-10') }}</div>
                                    <p class="hc-empty" style="padding:0">{{ __('No hay diagnósticos cargados.') }}</p>
                                    <button type="button" class="hc-add-btn" disabled>+ {{ __('Agregar diagnóstico') }}</button>
                                </div>
                            </div>

                            <div class="hc-panel" data-hc-panel="3" role="tabpanel" hidden>
                                <div style="padding:14px 16px">
                                    <p class="hc-empty" style="padding:0">{{ __('Sin procedimientos registrados.') }}</p>
                                    <button type="button" class="hc-add-btn" disabled>+ {{ __('Agregar procedimiento') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="hc-modal-backdrop{{ $showSignosModal ? ' is-open' : '' }}" id="signos-modal-backdrop" aria-hidden="{{ $showSignosModal ? 'false' : 'true' }}">
        <div class="hc-modal" role="dialog" aria-modal="true" aria-labelledby="signos-modal-title">
            <div class="hc-modal-head">
                <h3 id="signos-modal-title">{{ __('Registrar signos vitales') }}</h3>
                <button type="button" class="hc-modal-close" id="close-signos-modal" aria-label="{{ __('Cerrar') }}">&times;</button>
            </div>
            <form method="POST" action="{{ route('atenciones.signos-vitales.store', $atencion) }}" class="hc-modal-form">
                @csrf
                <div class="hc-modal-grid">
                    <label>
                        <span>{{ __('Fecha y hora') }}</span>
                        <input type="datetime-local" name="medicion_en" value="{{ old('medicion_en') }}" />
                    </label>
                    <label>
                        <span>{{ __('PA sistólica (mmHg)') }}</span>
                        <input type="number" name="presion_sistolica" min="40" max="300" value="{{ old('presion_sistolica') }}" />
                    </label>
                    <label>
                        <span>{{ __('PA diastólica (mmHg)') }}</span>
                        <input type="number" name="presion_diastolica" min="20" max="200" value="{{ old('presion_diastolica') }}" />
                    </label>
                    <label>
                        <span>{{ __('Frecuencia cardíaca') }}</span>
                        <input type="number" name="frecuencia_cardiaca" min="20" max="260" value="{{ old('frecuencia_cardiaca') }}" />
                    </label>
                    <label>
                        <span>{{ __('Frecuencia respiratoria') }}</span>
                        <input type="number" name="frecuencia_respiratoria" min="5" max="80" value="{{ old('frecuencia_respiratoria') }}" />
                    </label>
                    <label>
                        <span>{{ __('Temperatura (°C)') }}</span>
                        <input type="number" step="0.1" name="temperatura" min="25" max="45" value="{{ old('temperatura') }}" />
                    </label>
                    <label>
                        <span>{{ __('SpO₂ (%)') }}</span>
                        <input type="number" name="saturacion_oxigeno" min="0" max="100" value="{{ old('saturacion_oxigeno') }}" />
                    </label>
                    <label>
                        <span>{{ __('Glicemia (mg/dL)') }}</span>
                        <input type="number" name="glicemia" min="10" max="1000" value="{{ old('glicemia') }}" />
                    </label>
                    <label>
                        <span>{{ __('FiO2') }}</span>
                        <input type="text" name="fraccion_inspirada_oxigeno" maxlength="16" value="{{ old('fraccion_inspirada_oxigeno') }}" />
                    </label>
                </div>
                <label class="hc-modal-textarea">
                    <span>{{ __('Observaciones') }}</span>
                    <textarea name="observaciones" rows="3" maxlength="1000">{{ old('observaciones') }}</textarea>
                </label>
                <div class="hc-modal-actions">
                    <button type="button" class="hc-btn-outline" id="cancel-signos-modal">{{ __('Cancelar') }}</button>
                    <button type="submit" class="hc-btn-primary">{{ __('Guardar toma') }}</button>
                </div>
            </form>
        </div>
    </div>
    <div class="hc-modal-backdrop{{ $showNotaModal ? ' is-open' : '' }}" id="nota-modal-backdrop" aria-hidden="{{ $showNotaModal ? 'false' : 'true' }}">
        <div class="hc-modal hc-modal-sm" role="dialog" aria-modal="true" aria-labelledby="nota-modal-title">
            <div class="hc-modal-head">
                <h3 id="nota-modal-title">{{ __('Registrar nota clínica') }}</h3>
                <button type="button" class="hc-modal-close" id="close-nota-modal" aria-label="{{ __('Cerrar') }}">&times;</button>
            </div>
            <form method="POST" action="{{ route('atenciones.notas-clinicas.store', $atencion) }}" class="hc-modal-form">
                @csrf
                <label class="hc-modal-textarea" style="margin-top:0">
                    <span>{{ __('Nota clínica') }}</span>
                    <textarea name="contenido" rows="7" maxlength="5000" required>{{ old('contenido') }}</textarea>
                </label>
                <div class="hc-modal-actions">
                    <button type="button" class="hc-btn-outline" id="cancel-nota-modal">{{ __('Cancelar') }}</button>
                    <button type="submit" class="hc-btn-primary">{{ __('Guardar nota') }}</button>
                </div>
            </form>
        </div>
    </div>
    <script>
        (function () {
            var tabs = document.querySelectorAll('.hc-tab');
            var panels = document.querySelectorAll('.hc-panel');
            tabs.forEach(function (tab) {
                tab.addEventListener('click', function () {
                    var i = tab.getAttribute('data-hc-tab');
                    tabs.forEach(function (t) {
                        t.classList.toggle('active', t === tab);
                        t.setAttribute('aria-selected', t === tab ? 'true' : 'false');
                    });
                    panels.forEach(function (p) {
                        var on = p.getAttribute('data-hc-panel') === i;
                        p.classList.toggle('is-active', on);
                        p.hidden = !on;
                    });
                });
            });
        })();
    </script>
    <script>
        (function () {
            var backdrop = document.getElementById('signos-modal-backdrop');
            var openBtn = document.getElementById('open-signos-modal');
            var closeBtn = document.getElementById('close-signos-modal');
            var cancelBtn = document.getElementById('cancel-signos-modal');

            if (!backdrop) return;

            function openModal() {
                backdrop.classList.add('is-open');
                backdrop.setAttribute('aria-hidden', 'false');
                document.body.style.overflow = 'hidden';
            }

            function closeModal() {
                backdrop.classList.remove('is-open');
                backdrop.setAttribute('aria-hidden', 'true');
                document.body.style.overflow = '';
            }

            if (openBtn) openBtn.addEventListener('click', openModal);
            if (closeBtn) closeBtn.addEventListener('click', closeModal);
            if (cancelBtn) cancelBtn.addEventListener('click', closeModal);

            backdrop.addEventListener('click', function (e) {
                if (e.target === backdrop) closeModal();
            });

            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape' && backdrop.classList.contains('is-open')) closeModal();
            });
        })();
    </script>
    <script>
        (function () {
            var backdrop = document.getElementById('nota-modal-backdrop');
            var openBtn = document.getElementById('open-nota-modal');
            var closeBtn = document.getElementById('close-nota-modal');
            var cancelBtn = document.getElementById('cancel-nota-modal');

            if (!backdrop) return;

            function openModal() {
                backdrop.classList.add('is-open');
                backdrop.setAttribute('aria-hidden', 'false');
                document.body.style.overflow = 'hidden';
            }

            function closeModal() {
                backdrop.classList.remove('is-open');
                backdrop.setAttribute('aria-hidden', 'true');
                document.body.style.overflow = '';
            }

            if (openBtn) openBtn.addEventListener('click', openModal);
            if (closeBtn) closeBtn.addEventListener('click', closeModal);
            if (cancelBtn) cancelBtn.addEventListener('click', closeModal);

            backdrop.addEventListener('click', function (e) {
                if (e.target === backdrop) closeModal();
            });

            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape' && backdrop.classList.contains('is-open')) closeModal();
            });
        })();
    </script>
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
        })();
    </script>
</body>
</html>
