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
    @canany(['usuarios.gestionar', 'roles.gestionar', 'ambulancias.gestionar', 'conductores.gestionar', 'eps.gestionar', 'causasexternas.gestionar', 'cups.gestionar', 'cie10.gestionar'])
        <div class="dashboard-nav-section">{{ __('Parametrización') }}</div>
    @endcanany
    @can('usuarios.gestionar')
        <a href="{{ route('parametros.usuarios.index') }}" class="dashboard-nav-item {{ request()->routeIs('parametros.usuarios.*') ? 'active' : '' }}">
            <x-lucide-settings />
            {{ __('Usuarios del sistema') }}
        </a>
    @endcan
    @can('roles.gestionar')
        <a href="{{ route('parametros.roles.index') }}" class="dashboard-nav-item {{ request()->routeIs('parametros.roles.*') ? 'active' : '' }}">
            <x-lucide-shield />
            {{ __('Roles y permisos') }}
        </a>
    @endcan
    @can('ambulancias.gestionar')
        <a href="{{ route('parametros.ambulancias.index') }}" class="dashboard-nav-item {{ request()->routeIs('parametros.ambulancias.*') ? 'active' : '' }}">
            <x-lucide-ambulance />
            {{ __('Ambulancias') }}
        </a>
    @endcan
    @can('conductores.gestionar')
        <a href="{{ route('parametros.conductores.index') }}" class="dashboard-nav-item {{ request()->routeIs('parametros.conductores.*') ? 'active' : '' }}">
            <x-lucide-id-card />
            {{ __('Conductores') }}
        </a>
    @endcan
    @can('eps.gestionar')
        <a href="{{ route('parametros.eps.index') }}" class="dashboard-nav-item {{ request()->routeIs('parametros.eps.*') ? 'active' : '' }}">
            <x-lucide-building-2 />
            {{ __('Aseguradoras o EPS') }}
        </a>
    @endcan
    @can('causasexternas.gestionar')
        <a href="{{ route('parametros.causas-externas.index') }}" class="dashboard-nav-item {{ request()->routeIs('parametros.causas-externas.*') ? 'active' : '' }}">
            <x-lucide-file-text />
            {{ __('Causas externas') }}
        </a>
    @endcan
    @can('cups.gestionar')
        <a href="{{ route('parametros.cups.index') }}" class="dashboard-nav-item {{ request()->routeIs('parametros.cups.*') ? 'active' : '' }}">
            <x-lucide-file-digit />
            {{ __('CUPS') }}
        </a>
    @endcan
    @can('cie10.gestionar')
        <a href="{{ route('parametros.cie10.index') }}" class="dashboard-nav-item {{ request()->routeIs('parametros.cie10.*') ? 'active' : '' }}">
            <x-lucide-stethoscope />
            {{ __('CIE-10') }}
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
