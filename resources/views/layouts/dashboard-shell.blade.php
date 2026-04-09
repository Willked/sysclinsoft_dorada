@php
    \Illuminate\Support\Carbon::setLocale(config('app.locale', 'es'));
    $shellTodayLabel = now()->translatedFormat('d M Y');
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') — {{ config('app.name', 'SysClinSoft') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('styles.css') }}">
    <link rel="stylesheet" href="{{ asset('dashboard.css') }}">
    @stack('styles')
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
                    <span class="dashboard-topbar-title">@yield('page_title')</span>
                </div>
                <div class="dashboard-topbar-right">
                    <span style="font-size:12px;color:var(--dash-text-secondary)">{{ __('Hoy') }}, {{ $shellTodayLabel }}</span>
                    @yield('topbar_actions')
                </div>
            </header>
            <div class="dashboard-content">
                @if (session('status'))
                    <p role="status" style="margin:0 0 16px;padding:10px 12px;border-radius:8px;background:#d1fae5;color:#065f46;font-size:14px;">{{ session('status') }}</p>
                @endif
                @if (session('error'))
                    <p role="alert" style="margin:0 0 16px;padding:10px 12px;border-radius:8px;background:#fee2e2;color:#991b1b;font-size:14px;">{{ session('error') }}</p>
                @endif
                @yield('content')
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
    @stack('scripts')
</body>
</html>
