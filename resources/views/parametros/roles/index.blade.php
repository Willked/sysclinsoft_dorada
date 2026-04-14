@extends('layouts.dashboard-shell')

@section('title', __('Roles del sistema'))

@section('page_title', __('Roles del sistema'))

@section('content')
    <div class="dashboard-content-header" style="margin-bottom:16px">
        <h1 style="margin:0;font-size:20px;font-weight:600">{{ __('Roles del sistema') }}</h1>
        <p style="margin:6px 0 0;font-size:13px;color:var(--dash-text-secondary)">{{ __('Definición de perfiles y permisos asociados.') }}</p>
    </div>

    <div class="dashboard-card" style="margin-bottom:14px">
        <div class="dashboard-card-header" style="flex-wrap:wrap;gap:10px">
            <h3>{{ __('Listado de roles') }}</h3>
            <a href="{{ route('parametros.roles.create') }}" class="dashboard-btn-primary" style="text-decoration:none;display:inline-block">+ {{ __('Nuevo rol') }}</a>
        </div>
        <div style="padding:14px 18px;border-bottom:0.5px solid var(--dash-border-tertiary)">
            <form method="GET" action="{{ route('parametros.roles.index') }}" style="display:flex;gap:10px;flex-wrap:wrap;align-items:center">
                <input type="search" name="q" value="{{ $q }}" placeholder="{{ __('Buscar por nombre del rol…') }}" class="dashboard-input-search" style="flex:1;min-width:200px;padding:8px 12px;border:0.5px solid var(--dash-border-secondary);border-radius:var(--dash-radius-md);font-size:13px;font-family:inherit">
                <button type="submit" class="dashboard-btn-outline" style="font-size:13px">{{ __('Buscar') }}</button>
            </form>
        </div>
        <div class="dashboard-table-wrap">
            <table class="dashboard-table">
                <thead>
                    <tr>
                        <th>{{ __('Rol') }}</th>
                        <th style="width:100px;text-align:center">{{ __('Usuarios') }}</th>
                        <th style="width:100px;text-align:center">{{ __('Permisos') }}</th>
                        <th style="width:96px;text-align:right" aria-label="{{ __('Acciones') }}">{{ __('Acciones') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($roles as $r)
                        <tr>
                            <td><strong>{{ $r->name }}</strong></td>
                            <td style="text-align:center">{{ $r->users_count }}</td>
                            <td style="text-align:center">{{ $r->permissions_count }}</td>
                            <td style="text-align:right">
                                <div style="display:inline-flex;align-items:center;justify-content:flex-end;gap:6px">
                                    <a href="{{ route('parametros.roles.edit', $r) }}"
                                        class="dashboard-icon-action dashboard-icon-action--edit"
                                        title="{{ __('Editar') }}"
                                        aria-label="{{ __('Editar') }}">
                                        <x-lucide-pencil class="dashboard-icon-action-svg" aria-hidden="true" />
                                    </a>
                                    @if ($r->users_count === 0)
                                        <form method="POST" action="{{ route('parametros.roles.destroy', $r) }}" style="display:inline;margin:0">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="dashboard-icon-action dashboard-icon-action--danger"
                                                title="{{ __('Eliminar') }}"
                                                aria-label="{{ __('Eliminar') }}"
                                                onclick="return confirm(@json(__('¿Eliminar este rol? Esta acción no se puede deshacer.')))">
                                                <x-lucide-trash-2 class="dashboard-icon-action-svg" aria-hidden="true" />
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="color:var(--dash-text-secondary)">{{ __('No hay roles que coincidan.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($roles->hasPages())
            <div style="padding:12px 18px;border-top:0.5px solid var(--dash-border-tertiary)">
                {{ $roles->links('vendor.pagination.dashboard') }}
            </div>
        @endif
    </div>
@endsection
