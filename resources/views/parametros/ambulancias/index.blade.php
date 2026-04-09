@extends('layouts.dashboard-shell')

@section('title', __('Ambulancias'))

@section('page_title', __('Ambulancias'))

@section('content')
    <div class="dashboard-content-header" style="margin-bottom:16px">
        <h1 style="margin:0;font-size:20px;font-weight:600">{{ __('Ambulancias') }}</h1>
        <p style="margin:6px 0 0;font-size:13px;color:var(--dash-text-secondary)">{{ __('Unidades disponibles para asignar a las atenciones.') }}</p>
    </div>

    <div class="dashboard-card" style="margin-bottom:14px">
        <div class="dashboard-card-header" style="flex-wrap:wrap;gap:10px">
            <h3>{{ __('Listado') }}</h3>
            <a href="{{ route('parametros.ambulancias.create') }}" class="dashboard-btn-primary" style="text-decoration:none;display:inline-block">+ {{ __('Nueva ambulancia') }}</a>
        </div>
        <div style="padding:14px 18px;border-bottom:0.5px solid var(--dash-border-tertiary)">
            <form method="GET" action="{{ route('parametros.ambulancias.index') }}" style="display:flex;gap:10px;flex-wrap:wrap;align-items:center">
                <input type="search" name="q" value="{{ $q }}" placeholder="{{ __('Buscar por código, placa o descripción…') }}" class="dashboard-input-search" style="flex:1;min-width:200px;padding:8px 12px;border:0.5px solid var(--dash-border-secondary);border-radius:var(--dash-radius-md);font-size:13px;font-family:inherit">
                <button type="submit" class="dashboard-btn-outline" style="font-size:13px">{{ __('Buscar') }}</button>
            </form>
        </div>
        <div class="dashboard-table-wrap">
            <table class="dashboard-table">
                <thead>
                    <tr>
                        <th>{{ __('Código') }}</th>
                        <th>{{ __('Placa') }}</th>
                        <th>{{ __('Descripción') }}</th>
                        <th>{{ __('Clasificación') }}</th>
                        <th>{{ __('Estado') }}</th>
                        <th style="width:90px;text-align:center">{{ __('Atenciones') }}</th>
                        <th style="width:168px;text-align:right" aria-label="{{ __('Acciones') }}">{{ __('Acciones') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($ambulancias as $a)
                        <tr @class(['dashboard-table-row-muted' => ! $a->activo])>
                            <td><strong>{{ $a->codigo }}</strong></td>
                            <td>{{ $a->placa ?: '—' }}</td>
                            <td>{{ $a->descripcion ?: '—' }}</td>
                            <td>{{ $a->clasificacion_servicio ?: '—' }}</td>
                            <td>
                                @if ($a->activo)
                                    <span class="dashboard-badge dashboard-badge-active">{{ __('Activa') }}</span>
                                @else
                                    <span class="dashboard-badge dashboard-badge-inactive">{{ __('Inactiva') }}</span>
                                @endif
                            </td>
                            <td style="text-align:center">{{ $a->atenciones_count }}</td>
                            <td style="text-align:right">
                                <div style="display:inline-flex;align-items:center;justify-content:flex-end;gap:6px;flex-wrap:wrap">
                                    <a href="{{ route('parametros.ambulancias.edit', $a) }}"
                                        class="dashboard-icon-action dashboard-icon-action--edit"
                                        title="{{ __('Editar') }}"
                                        aria-label="{{ __('Editar') }}">
                                        <x-lucide-pencil class="dashboard-icon-action-svg" aria-hidden="true" />
                                    </a>
                                    @if ($a->activo)
                                        <form method="POST" action="{{ route('parametros.ambulancias.desactivar', $a) }}" style="display:inline;margin:0">
                                            @csrf
                                            <button type="submit"
                                                class="dashboard-icon-action dashboard-icon-action--danger"
                                                title="{{ __('Desactivar') }}"
                                                aria-label="{{ __('Desactivar') }}"
                                                onclick="return confirm(@json(__('¿Desactivar esta unidad? Dejará de aparecer al crear atenciones.')))">
                                                <x-lucide-circle-off class="dashboard-icon-action-svg" aria-hidden="true" />
                                            </button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('parametros.ambulancias.activar', $a) }}" style="display:inline;margin:0">
                                            @csrf
                                            <button type="submit"
                                                class="dashboard-icon-action dashboard-icon-action--ok"
                                                title="{{ __('Activar') }}"
                                                aria-label="{{ __('Activar') }}"
                                                onclick="return confirm(@json(__('¿Activar esta unidad?')))">
                                                <x-lucide-circle-check class="dashboard-icon-action-svg" aria-hidden="true" />
                                            </button>
                                        </form>
                                    @endif
                                    @if ($a->atenciones_count === 0)
                                        <form method="POST" action="{{ route('parametros.ambulancias.destroy', $a) }}" style="display:inline;margin:0">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="dashboard-icon-action dashboard-icon-action--danger"
                                                title="{{ __('Eliminar') }}"
                                                aria-label="{{ __('Eliminar') }}"
                                                onclick="return confirm(@json(__('¿Eliminar esta ambulancia? Esta acción no se puede deshacer.')))">
                                                <x-lucide-trash-2 class="dashboard-icon-action-svg" aria-hidden="true" />
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="color:var(--dash-text-secondary)">{{ __('No hay unidades que coincidan.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($ambulancias->hasPages())
            <div style="padding:12px 18px;border-top:0.5px solid var(--dash-border-tertiary)">
                {{ $ambulancias->links('vendor.pagination.dashboard') }}
            </div>
        @endif
    </div>
@endsection
