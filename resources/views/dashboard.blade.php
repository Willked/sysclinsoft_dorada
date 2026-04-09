@php
    $user = auth()->user();
    $displayName = $user->name ?? 'Usuario';
    \Illuminate\Support\Carbon::setLocale(config('app.locale', 'es'));
    $todayLabel = now()->translatedFormat('d M Y');
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('Dashboard') }} — {{ config('app.name', 'SysClinSoft') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('styles.css') }}">
    <link rel="stylesheet" href="{{ asset('dashboard.css') }}">
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
                    <span class="dashboard-topbar-title">{{ __('Dashboard') }}</span>
                </div>
                <div class="dashboard-topbar-right">
                    <span style="font-size:12px;color:var(--dash-text-secondary)">{{ __('Hoy') }}, {{ $todayLabel }}</span>
                    <button type="button" class="dashboard-btn-outline">{{ __('Exportar') }}</button>
                    <a href="{{ route('atenciones.nueva') }}" class="dashboard-btn-primary" style="text-decoration:none;display:inline-block">+ {{ __('Nueva atención') }}</a>
                </div>
            </header>

            <div class="dashboard-content">
                @if (session('status'))
                    <p role="status" style="margin:0 0 16px;padding:10px 12px;border-radius:8px;background:#d1fae5;color:#065f46;font-size:14px;">{{ session('status') }}</p>
                @endif
                <div class="dashboard-content-header">
                    <h1>{{ __('Bienvenido') }} {{ $displayName }}</h1>
                </div>
                <div class="dashboard-kpi-grid">
                    <div class="dashboard-kpi ok">
                        <div class="dashboard-kpi-label">{{ __('Atenciones hoy') }}</div>
                        <div class="dashboard-kpi-value">{{ $atencionesHoy }}</div>
                        <div class="dashboard-kpi-sub">
                            @php
                                $diffHoy = $atencionesHoy - $atencionesAyer;
                            @endphp
                            @if ($diffHoy > 0)
                                +{{ $diffHoy }} {{ __('vs ayer') }}
                            @elseif ($diffHoy < 0)
                                {{ $diffHoy }} {{ __('vs ayer') }}
                            @else
                                {{ __('Igual que ayer') }}
                            @endif
                        </div>
                    </div>
                    <div class="dashboard-kpi warn">
                        <div class="dashboard-kpi-label">{{ __('En atención') }}</div>
                        <div class="dashboard-kpi-value">{{ $enAtencion }}</div>
                        <div class="dashboard-kpi-sub">{{ __('Estado en atención en sistema') }}</div>
                    </div>
                    <div class="dashboard-kpi">
                        <div class="dashboard-kpi-label">{{ __('Pacientes registrados') }}</div>
                        <div class="dashboard-kpi-value">1.248</div>
                        <div class="dashboard-kpi-sub">+12 {{ __('esta semana') }}</div>
                    </div>
                    <div class="dashboard-kpi warn">
                        <div class="dashboard-kpi-label">{{ __('RIPS pendientes') }}</div>
                        <div class="dashboard-kpi-value">7</div>
                        <div class="dashboard-kpi-sub">{{ __('Sin enviar a ADRES') }}</div>
                    </div>
                    <div class="dashboard-kpi danger">
                        <div class="dashboard-kpi-label">{{ __('RIPS rechazados') }}</div>
                        <div class="dashboard-kpi-value">2</div>
                        <div class="dashboard-kpi-sub">{{ __('Requieren corrección') }}</div>
                    </div>
                </div>

                <div class="dashboard-two-col">
                    <div class="dashboard-col-stack">
                        <div class="dashboard-card">
                            <div class="dashboard-card-header">
                                <h3>{{ __('Atenciones recientes') }}</h3>
                                <a href="{{ route('atenciones.nueva') }}">{{ __('Nueva atención') }}</a>
                            </div>
                            @forelse ($atenciones as $atencion)
                                @php
                                    $p = $atencion->paciente;
                                    $nombrePaciente = $p
                                        ? trim(implode(' ', array_filter([
                                            $p->primer_nombre,
                                            $p->segundo_nombre,
                                            $p->primer_apellido,
                                            $p->segundo_apellido,
                                        ])))
                                        : '—';
                                    $refAnio = $atencion->hora_llamada?->format('Y') ?? $atencion->created_at->format('Y');
                                    $ref = 'APH-'.$refAnio.'-'.str_pad((string) $atencion->id, 6, '0', STR_PAD_LEFT);
                                    $tipoServicio = $atencion->cup?->nombre ?? $atencion->tipo_servicio ?? __('Sin clasificar');
                                    $mins = null;
                                    if ($atencion->hora_llamada && $atencion->llegada_escena) {
                                        $mins = (int) $atencion->hora_llamada->diffInMinutes($atencion->llegada_escena);
                                    }
                                    $badgeClass = match ($atencion->estado) {
                                        'en_atencion' => 'en-atencion',
                                        'finalizado' => 'finalizado',
                                        default => 'pendiente',
                                    };
                                    $estadoLabel = match ($atencion->estado) {
                                        'en_atencion' => __('En atención'),
                                        'finalizado' => __('Finalizado'),
                                        default => $atencion->estado,
                                    };
                                    $tClass = $mins === null ? '' : ($mins <= 12 ? 'ok' : ($mins <= 18 ? 'warn' : 'crit'));
                                @endphp
                                <a href="{{ route('atenciones.show', $atencion) }}" class="dashboard-row-item dashboard-row-link">
                                    <div class="dashboard-row-main">
                                        <span>{{ $nombrePaciente }}</span>
                                        <small>{{ $ref }} · {{ $tipoServicio }}</small>
                                    </div>
                                    <div class="dashboard-row-right">
                                        <span class="dashboard-badge {{ $badgeClass }}">{{ $estadoLabel }}</span>
                                        @if ($mins !== null)
                                            <div class="dashboard-t-resp {{ $tClass }}">T. {{ __('respuesta') }}: {{ $mins }} min</div>
                                        @else
                                            <div class="dashboard-t-resp" style="color:var(--dash-text-secondary)">—</div>
                                        @endif
                                    </div>
                                </a>
                            @empty
                                <p style="margin:12px 0 0;font-size:14px;color:var(--dash-text-secondary)">{{ __('No hay atenciones registradas.') }}</p>
                            @endforelse
                        </div>

                        <div class="dashboard-card">
                            <div class="dashboard-card-header">
                                <h3>{{ __('Tiempo de respuesta promedio') }} — {{ __('hoy') }}</h3>
                                <span class="dashboard-link" style="font-size:12px;color:var(--dash-text-secondary)">{{ __('Meta') }}: ≤ 15 min</span>
                            </div>
                            <div class="dashboard-tiempo-bar">
                                <span class="nombre">{{ __('Traslado primario') }}</span>
                                <div class="dashboard-bar-track"><div class="dashboard-bar-fill" style="width:60%;background:var(--dash-brand)"></div></div>
                                <span class="val">9 min</span>
                            </div>
                            <div class="dashboard-tiempo-bar">
                                <span class="nombre">{{ __('Urgencias escena') }}</span>
                                <div class="dashboard-bar-track"><div class="dashboard-bar-fill" style="width:85%;background:#ef9f27"></div></div>
                                <span class="val">13 min</span>
                            </div>
                            <div class="dashboard-tiempo-bar">
                                <span class="nombre">{{ __('Traslado secundario') }}</span>
                                <div class="dashboard-bar-track"><div class="dashboard-bar-fill" style="width:100%;background:#e24b4a"></div></div>
                                <span class="val">22 min</span>
                            </div>
                            <div class="dashboard-tiempo-bar">
                                <span class="nombre">{{ __('Apoyo asistencial') }}</span>
                                <div class="dashboard-bar-track"><div class="dashboard-bar-fill" style="width:50%;background:#1d9e75"></div></div>
                                <span class="val">7 min</span>
                            </div>
                        </div>
                    </div>

                    <div class="dashboard-col-stack">
                        <div class="dashboard-card">
                            <div class="dashboard-card-header">
                                <h3>{{ __('Alertas RIPS') }}</h3>
                                <a href="#">{{ __('Ver RIPS') }}</a>
                            </div>
                            <div class="dashboard-alert-item">
                                <div class="dashboard-alert-dot red"></div>
                                <div class="dashboard-alert-text">
                                    <span>APH-2026-000009</span>
                                    <small>{{ __('Rechazado') }} — codDiagnosticoPrincipal {{ __('vacío') }}</small>
                                </div>
                            </div>
                            <div class="dashboard-alert-item">
                                <div class="dashboard-alert-dot red"></div>
                                <div class="dashboard-alert-text">
                                    <span>APH-2026-000007</span>
                                    <small>{{ __('Rechazado') }} — numFactura {{ __('duplicada') }}</small>
                                </div>
                            </div>
                            <div class="dashboard-alert-item">
                                <div class="dashboard-alert-dot amber"></div>
                                <div class="dashboard-alert-text">
                                    <span>APH-2026-000012</span>
                                    <small>{{ __('Pendiente') }} — {{ __('sin enviar a ADRES') }}</small>
                                </div>
                            </div>
                            <div class="dashboard-alert-item">
                                <div class="dashboard-alert-dot amber"></div>
                                <div class="dashboard-alert-text">
                                    <span>APH-2026-000011</span>
                                    <small>{{ __('Pendiente') }} — 3 {{ __('intentos fallidos') }}</small>
                                </div>
                            </div>
                        </div>

                        <div class="dashboard-card">
                            <div class="dashboard-card-header">
                                <h3>{{ __('Estado FHIR / MinSalud') }}</h3>
                            </div>
                            <div class="dashboard-fhir-padding">
                                <div class="dashboard-meta-row">
                                    <span class="dashboard-meta-label">{{ __('Sincronizados hoy') }}</span>
                                    <span class="dashboard-meta-value ok">12 / 14</span>
                                </div>
                                <div class="dashboard-meta-row">
                                    <span class="dashboard-meta-label">{{ __('Fallidos') }}</span>
                                    <span class="dashboard-meta-value crit">2</span>
                                </div>
                                <div class="dashboard-meta-row">
                                    <span class="dashboard-meta-label">{{ __('Cola pendiente') }}</span>
                                    <span class="dashboard-meta-value" style="color:var(--dash-text-primary)">0 jobs</span>
                                </div>
                                <div class="dashboard-fhir-box">
                                    <span>{{ __('Servidor FHIR activo') }}</span>
                                </div>
                            </div>
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
        })();
    </script>
</body>
</html>
