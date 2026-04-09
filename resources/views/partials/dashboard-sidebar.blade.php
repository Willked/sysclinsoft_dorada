@php
    $sbUser = auth()->user();
    $sbDisplayName = $sbUser->name ?? 'Usuario';
    $sbParts = preg_split('/\s+/', trim($sbDisplayName));
    $sbInitials = '';
    foreach (array_slice($sbParts, 0, 2) as $sbP) {
        $sbInitials .= mb_strtoupper(mb_substr($sbP, 0, 1));
    }
    $sbInitials = $sbInitials !== '' ? $sbInitials : 'U';
@endphp
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
    <a href="{{ route('atenciones.nueva') }}" class="dashboard-nav-item {{ request()->routeIs('atenciones.*') ? 'active' : '' }}">
        <x-lucide-clipboard-list />
        {{ __('Atenciones') }}
    </a>
    <div class="dashboard-nav-item" role="presentation">
        <x-lucide-users />
        {{ __('Pacientes') }}
    </div>
    @can('usuarios.gestionar')
        <div class="dashboard-nav-section">{{ __('Parametrización') }}</div>
        <a href="{{ route('parametros.usuarios.index') }}" class="dashboard-nav-item {{ request()->routeIs('parametros.usuarios.*') ? 'active' : '' }}">
            <x-lucide-settings />
            {{ __('Usuarios del sistema') }}
        </a>
    @endcan
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
            <div class="dashboard-avatar" aria-hidden="true">{{ $sbInitials }}</div>
            <div class="dashboard-user-info">
                <span>{{ $sbDisplayName }}</span>
                <small>{{ __('Paramédico') }}</small>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="dashboard-btn-outline">{{ __('Cerrar sesión') }}</button>
        </form>
    </div>
</aside>
