@extends('layouts.dashboard-shell')

@section('title', __('Causas externas'))

@section('page_title', __('Causas externas'))

@section('content')
    <div class="dashboard-content-header" style="margin-bottom:16px">
        <h1 style="margin:0;font-size:20px;font-weight:600">{{ __('Causas externas') }}</h1>
        <p style="margin:6px 0 0;font-size:13px;color:var(--dash-text-secondary)">{{ __('Catálogo de causas externas para atenciones y RIPS.') }}</p>
    </div>

    <div class="dashboard-card" style="margin-bottom:14px">
        <div class="dashboard-card-header" style="flex-wrap:wrap;gap:10px">
            <h3>{{ __('Listado') }}</h3>
            <a href="{{ route('parametros.causas-externas.create') }}" class="dashboard-btn-primary" style="text-decoration:none;display:inline-block">+ {{ __('Nueva causa externa') }}</a>
        </div>
        <div style="padding:14px 18px;border-bottom:0.5px solid var(--dash-border-tertiary)">
            <form method="GET" action="{{ route('parametros.causas-externas.index') }}" style="display:flex;gap:10px;flex-wrap:wrap;align-items:center">
                <input type="search" name="q" value="{{ $q }}" placeholder="{{ __('Buscar por código, nombre o ID…') }}" class="dashboard-input-search" style="flex:1;min-width:200px;padding:8px 12px;border:0.5px solid var(--dash-border-secondary);border-radius:var(--dash-radius-md);font-size:13px;font-family:inherit">
                <button type="submit" class="dashboard-btn-outline" style="font-size:13px">{{ __('Buscar') }}</button>
            </form>
        </div>
        <div class="dashboard-table-wrap">
            <table class="dashboard-table">
                <thead>
                    <tr>
                        <th>{{ __('Código') }}</th>
                        <th>{{ __('Nombre') }}</th>
                        <th>{{ __('Estado') }}</th>
                        <th style="width:120px;text-align:right" aria-label="{{ __('Acciones') }}">{{ __('Acciones') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($causasExternas as $causaExterna)
                        <tr @class(['dashboard-table-row-muted' => ! $causaExterna->activo])>
                            <td><strong>{{ $causaExterna->codigo }}</strong></td>
                            <td>{{ $causaExterna->nombre }}</td>
                            <td>
                                @if ($causaExterna->activo)
                                    <span class="dashboard-badge dashboard-badge-active">{{ __('Activo') }}</span>
                                @else
                                    <span class="dashboard-badge dashboard-badge-inactive">{{ __('Inactivo') }}</span>
                                @endif
                            </td>
                            <td style="text-align:right">
                                <div style="display:inline-flex;align-items:center;justify-content:flex-end;gap:6px;flex-wrap:wrap">
                                    <a href="{{ route('parametros.causas-externas.edit', $causaExterna) }}"
                                        class="dashboard-icon-action dashboard-icon-action--edit"
                                        title="{{ __('Editar') }}"
                                        aria-label="{{ __('Editar') }}">
                                        <x-lucide-pencil class="dashboard-icon-action-svg" aria-hidden="true" />
                                    </a>
                                    @if ($causaExterna->activo)
                                        <form method="POST" action="{{ route('parametros.causas-externas.desactivar', $causaExterna) }}" style="display:inline;margin:0">
                                            @csrf
                                            <button type="submit"
                                                class="dashboard-icon-action dashboard-icon-action--danger"
                                                title="{{ __('Desactivar') }}"
                                                aria-label="{{ __('Desactivar') }}"
                                                onclick="return confirm(@json(__('¿Dar de baja esta causa externa? Quedará inactiva (no se elimina el registro) y dejará de aparecer al crear atenciones.')))">
                                                <x-lucide-circle-off class="dashboard-icon-action-svg" aria-hidden="true" />
                                            </button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('parametros.causas-externas.activar', $causaExterna) }}" style="display:inline;margin:0">
                                            @csrf
                                            <button type="submit"
                                                class="dashboard-icon-action dashboard-icon-action--ok"
                                                title="{{ __('Activar') }}"
                                                aria-label="{{ __('Activar') }}"
                                                onclick="return confirm(@json(__('¿Activar esta causa externa?')))">
                                                <x-lucide-circle-check class="dashboard-icon-action-svg" aria-hidden="true" />
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="color:var(--dash-text-secondary)">{{ __('No hay causas externas que coincidan.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($causasExternas->hasPages())
            <div style="padding:12px 18px;border-top:0.5px solid var(--dash-border-tertiary)">
                {{ $causasExternas->links('vendor.pagination.dashboard') }}
            </div>
        @endif
    </div>
@endsection
