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
                    <span class="dashboard-topbar-title">{{ __('Dashboard') }}</span>
                </div>
                <div class="dashboard-topbar-right">
                    <span style="font-size:12px;color:var(--dash-text-secondary)">{{ __('Hoy') }}, {{ $todayLabel }}</span>
                    <button type="button" class="dashboard-btn-outline">{{ __('Exportar') }}</button>
                    <a href="{{ route('atenciones.nueva') }}" class="dashboard-btn-primary" style="text-decoration:none;display:inline-block">+ {{ __('Nueva atención') }}</a>
                </div>
            </header>

            <div class="dashboard-content">
                <div class="dashboard-content-header">
                    <h1>{{ __('Bienvenido') }} {{ $displayName }}</h1>
                </div>
                <div class="dashboard-kpi-grid">
                    <div class="dashboard-kpi ok">
                        <div class="dashboard-kpi-label">{{ __('Atenciones hoy') }}</div>
                        <div class="dashboard-kpi-value">14</div>
                        <div class="dashboard-kpi-sub">+3 {{ __('vs ayer') }}</div>
                    </div>
                    <div class="dashboard-kpi warn">
                        <div class="dashboard-kpi-label">{{ __('En atención') }}</div>
                        <div class="dashboard-kpi-value">3</div>
                        <div class="dashboard-kpi-sub">2 {{ __('traslados activos') }}</div>
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
                                <a href="#">{{ __('Ver todas') }}</a>
                            </div>
                            <div class="dashboard-row-item">
                                <div class="dashboard-row-main">
                                    <span>María García Rodríguez</span>
                                    <small>APH-2026-000014 · {{ __('Traslado primario') }}</small>
                                </div>
                                <div class="dashboard-row-right">
                                    <span class="dashboard-badge en-atencion">{{ __('En atención') }}</span>
                                    <div class="dashboard-t-resp ok">T. {{ __('respuesta') }}: 8 min</div>
                                </div>
                            </div>
                            <div class="dashboard-row-item">
                                <div class="dashboard-row-main">
                                    <span>Carlos Mendoza López</span>
                                    <small>APH-2026-000013 · {{ __('Urgencias en escena') }}</small>
                                </div>
                                <div class="dashboard-row-right">
                                    <span class="dashboard-badge en-atencion">{{ __('En atención') }}</span>
                                    <div class="dashboard-t-resp warn">T. {{ __('respuesta') }}: 18 min</div>
                                </div>
                            </div>
                            <div class="dashboard-row-item">
                                <div class="dashboard-row-main">
                                    <span>Lucía Herrera Pinto</span>
                                    <small>APH-2026-000012 · {{ __('Traslado secundario') }}</small>
                                </div>
                                <div class="dashboard-row-right">
                                    <span class="dashboard-badge finalizado">{{ __('Finalizado') }}</span>
                                    <div class="dashboard-t-resp ok">T. {{ __('respuesta') }}: 11 min</div>
                                </div>
                            </div>
                            <div class="dashboard-row-item">
                                <div class="dashboard-row-main">
                                    <span>Andrés Vargas Castro</span>
                                    <small>APH-2026-000011 · {{ __('Urgencias en escena') }}</small>
                                </div>
                                <div class="dashboard-row-right">
                                    <span class="dashboard-badge finalizado">{{ __('Finalizado') }}</span>
                                    <div class="dashboard-t-resp crit">T. {{ __('respuesta') }}: 24 min</div>
                                </div>
                            </div>
                            <div class="dashboard-row-item">
                                <div class="dashboard-row-main">
                                    <span>Paula Jiménez Torres</span>
                                    <small>APH-2026-000010 · {{ __('Apoyo asistencial') }}</small>
                                </div>
                                <div class="dashboard-row-right">
                                    <span class="dashboard-badge finalizado">{{ __('Finalizado') }}</span>
                                    <div class="dashboard-t-resp ok">T. {{ __('respuesta') }}: 9 min</div>
                                </div>
                            </div>
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
