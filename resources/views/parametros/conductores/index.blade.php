@extends('layouts.dashboard-shell')

@section('title', __('Conductores'))

@section('page_title', __('Conductores'))

@section('content')
    <div class="dashboard-content-header" style="margin-bottom:16px">
        <h1 style="margin:0;font-size:20px;font-weight:600">{{ __('Conductores') }}</h1>
        <p style="margin:6px 0 0;font-size:13px;color:var(--dash-text-secondary)">{{ __('Personal conductor disponible para asignar a las atenciones.') }}</p>
    </div>

    <div class="dashboard-card" style="margin-bottom:14px">
        <div class="dashboard-card-header" style="flex-wrap:wrap;gap:10px">
            <h3>{{ __('Listado') }}</h3>
            <a href="{{ route('parametros.conductores.create') }}" class="dashboard-btn-primary" style="text-decoration:none;display:inline-block">+ {{ __('Nuevo conductor') }}</a>
        </div>
        <div style="padding:14px 18px;border-bottom:0.5px solid var(--dash-border-tertiary)">
            <form method="GET" action="{{ route('parametros.conductores.index') }}" style="display:flex;gap:10px;flex-wrap:wrap;align-items:center">
                <input type="search" name="q" value="{{ $q }}" placeholder="{{ __('Buscar por documento, nombre o licencia…') }}" class="dashboard-input-search" style="flex:1;min-width:200px;padding:8px 12px;border:0.5px solid var(--dash-border-secondary);border-radius:var(--dash-radius-md);font-size:13px;font-family:inherit">
                <button type="submit" class="dashboard-btn-outline" style="font-size:13px">{{ __('Buscar') }}</button>
            </form>
        </div>
        <div class="dashboard-table-wrap">
            <table class="dashboard-table">
                <thead>
                    <tr>
                        <th>{{ __('Documento') }}</th>
                        <th>{{ __('Nombre') }}</th>
                        <th>{{ __('Teléfono') }}</th>
                        <th>{{ __('Licencia') }}</th>
                        <th>{{ __('Vence') }}</th>
                        <th>{{ __('Estado') }}</th>
                        <th style="width:120px;text-align:right" aria-label="{{ __('Acciones') }}">{{ __('Acciones') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($conductores as $conductor)
                        @php
                            $nombre = trim(implode(' ', array_filter([
                                $conductor->primer_nombre,
                                $conductor->segundo_nombre,
                                $conductor->primer_apellido,
                                $conductor->segundo_apellido,
                            ])));
                        @endphp
                        <tr @class(['dashboard-table-row-muted' => ! $conductor->activo])>
                            <td>
                                {{ $conductor->tipoDocumento?->codigo ?? '—' }}
                                {{ $conductor->numero_documento }}
                            </td>
                            <td><strong>{{ $nombre !== '' ? $nombre : '—' }}</strong></td>
                            <td>{{ $conductor->telefono ?: '—' }}</td>
                            <td>
                                @if ($conductor->numero_licencia)
                                    {{ $conductor->numero_licencia }}{{ $conductor->categoria_licencia ? ' · '.$conductor->categoria_licencia : '' }}
                                @else
                                    —
                                @endif
                            </td>
                            <td>{{ $conductor->fecha_vencimiento_licencia?->format('d/m/Y') ?? '—' }}</td>
                            <td>
                                @if ($conductor->activo)
                                    <span class="dashboard-badge dashboard-badge-active">{{ __('Activo') }}</span>
                                @else
                                    <span class="dashboard-badge dashboard-badge-inactive">{{ __('Inactivo') }}</span>
                                @endif
                            </td>
                            <td style="text-align:right">
                                <div style="display:inline-flex;align-items:center;justify-content:flex-end;gap:6px;flex-wrap:wrap">
                                    <a href="{{ route('parametros.conductores.edit', $conductor) }}"
                                        class="dashboard-icon-action dashboard-icon-action--edit"
                                        title="{{ __('Editar') }}"
                                        aria-label="{{ __('Editar') }}">
                                        <x-lucide-pencil class="dashboard-icon-action-svg" aria-hidden="true" />
                                    </a>
                                    @if ($conductor->activo)
                                        <form method="POST" action="{{ route('parametros.conductores.desactivar', $conductor) }}" style="display:inline;margin:0">
                                            @csrf
                                            <button type="submit"
                                                class="dashboard-icon-action dashboard-icon-action--danger"
                                                title="{{ __('Desactivar') }}"
                                                aria-label="{{ __('Desactivar') }}"
                                                onclick="return confirm(@json(__('¿Desactivar este conductor? Dejará de aparecer al crear atenciones.')))">
                                                <x-lucide-circle-off class="dashboard-icon-action-svg" aria-hidden="true" />
                                            </button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('parametros.conductores.activar', $conductor) }}" style="display:inline;margin:0">
                                            @csrf
                                            <button type="submit"
                                                class="dashboard-icon-action dashboard-icon-action--ok"
                                                title="{{ __('Activar') }}"
                                                aria-label="{{ __('Activar') }}"
                                                onclick="return confirm(@json(__('¿Activar este conductor?')))">
                                                <x-lucide-circle-check class="dashboard-icon-action-svg" aria-hidden="true" />
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="color:var(--dash-text-secondary)">{{ __('No hay conductores que coincidan.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($conductores->hasPages())
            <div style="padding:12px 18px;border-top:0.5px solid var(--dash-border-tertiary)">
                {{ $conductores->links('vendor.pagination.dashboard') }}
            </div>
        @endif
    </div>
@endsection
