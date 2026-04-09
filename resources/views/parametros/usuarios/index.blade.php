@extends('layouts.dashboard-shell')

@section('title', __('Usuarios del sistema'))

@section('page_title', __('Usuarios del sistema'))

@section('content')
    <div class="dashboard-content-header" style="margin-bottom:16px">
        <h1 style="margin:0;font-size:20px;font-weight:600">{{ __('Usuarios del sistema') }}</h1>
        <p style="margin:6px 0 0;font-size:13px;color:var(--dash-text-secondary)">{{ __('Alta, edición, roles y activación o desactivación de cuentas.') }}</p>
    </div>

    <div class="dashboard-card" style="margin-bottom:14px">
        <div class="dashboard-card-header" style="flex-wrap:wrap;gap:10px">
            <h3>{{ __('Listado de usuarios') }}</h3>
            <a href="{{ route('parametros.usuarios.create') }}" class="dashboard-btn-primary" style="text-decoration:none;display:inline-block">+ {{ __('Nuevo usuario') }}</a>
        </div>
        <div style="padding:14px 18px;border-bottom:0.5px solid var(--dash-border-tertiary)">
            <form method="GET" action="{{ route('parametros.usuarios.index') }}" style="display:flex;gap:10px;flex-wrap:wrap;align-items:center">
                <input type="search" name="q" value="{{ $q }}" placeholder="{{ __('Buscar por nombre, correo o registro…') }}" class="dashboard-input-search" style="flex:1;min-width:200px;padding:8px 12px;border:0.5px solid var(--dash-border-secondary);border-radius:var(--dash-radius-md);font-size:13px;font-family:inherit">
                <button type="submit" class="dashboard-btn-outline" style="font-size:13px">{{ __('Buscar') }}</button>
            </form>
        </div>
        <div class="dashboard-table-wrap">
            <table class="dashboard-table">
                <thead>
                    <tr>
                        <th>{{ __('Nombre') }}</th>
                        <th>{{ __('Correo') }}</th>
                        <th>{{ __('Registro médico /') }}<br />{{ __('profesional') }}</th>
                        <th>{{ __('Roles') }}</th>
                        <th>{{ __('Estado') }}</th>
                        <th style="width:96px;text-align:right" aria-label="{{ __('Acciones') }}">{{ __('Acciones') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $u)
                        <tr @class(['dashboard-table-row-muted' => $u->trashed()])>
                            <td>{{ $u->name }}</td>
                            <td>{{ $u->email }}</td>
                            <td>{{ $u->registro_medico ?: '—' }}</td>
                            <td>
                                @forelse ($u->roles as $role)
                                    <span class="dashboard-badge pendiente" style="margin-right:4px;font-size:11px">{{ $role->name }}</span>
                                @empty
                                    <span style="color:var(--dash-text-tertiary)">—</span>
                                @endforelse
                            </td>
                            <td>
                                @if ($u->trashed())
                                    <span class="dashboard-badge dashboard-badge-inactive">{{ __('Inactivo') }}</span>
                                @else
                                    <span class="dashboard-badge dashboard-badge-active">{{ __('Activo') }}</span>
                                @endif
                            </td>
                            <td style="text-align:right">
                                <div style="display:inline-flex;align-items:center;justify-content:flex-end;gap:6px">
                                    @unless ($u->trashed())
                                        <a href="{{ route('parametros.usuarios.edit', $u) }}"
                                            class="dashboard-icon-action dashboard-icon-action--edit"
                                            title="{{ __('Editar') }}"
                                            aria-label="{{ __('Editar') }}">
                                            <x-lucide-pencil class="dashboard-icon-action-svg" aria-hidden="true" />
                                        </a>
                                    @endunless
                                    @if ($u->id !== auth()->id())
                                        @if ($u->trashed())
                                            <form method="POST" action="{{ route('parametros.usuarios.restore', $u->id) }}" style="display:inline;margin:0">
                                                @csrf
                                                <button type="submit"
                                                    class="dashboard-icon-action dashboard-icon-action--ok"
                                                    title="{{ __('Activar') }}"
                                                    aria-label="{{ __('Activar') }}"
                                                    onclick="return confirm(@json(__('¿Activar este usuario?')))">
                                                    <x-lucide-user-check class="dashboard-icon-action-svg" aria-hidden="true" />
                                                </button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('parametros.usuarios.destroy', $u) }}" style="display:inline;margin:0">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="dashboard-icon-action dashboard-icon-action--danger"
                                                    title="{{ __('Desactivar') }}"
                                                    aria-label="{{ __('Desactivar') }}"
                                                    onclick="return confirm(@json(__('¿Desactivar este usuario? Podrá volver a activarse más adelante.')))">
                                                    <x-lucide-user-x class="dashboard-icon-action-svg" aria-hidden="true" />
                                                </button>
                                            </form>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="color:var(--dash-text-secondary)">{{ __('No hay usuarios que coincidan.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($users->hasPages())
            <div style="padding:12px 18px;border-top:0.5px solid var(--dash-border-tertiary)">
                {{ $users->links('vendor.pagination.dashboard') }}
            </div>
        @endif
    </div>
@endsection
