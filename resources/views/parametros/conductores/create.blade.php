@extends('layouts.dashboard-shell')

@section('title', __('Nuevo conductor'))

@section('page_title', __('Nuevo conductor'))

@section('content')
    <div class="dashboard-content-header" style="margin-bottom:16px">
        <h1 style="margin:0;font-size:20px;font-weight:600">{{ __('Nuevo conductor') }}</h1>
        <p style="margin:6px 0 0;font-size:13px;color:var(--dash-text-secondary)">
            <a href="{{ route('parametros.conductores.index') }}" class="dashboard-link">{{ __('← Volver al listado') }}</a>
        </p>
    </div>

    <div class="dashboard-card">
        <div class="dashboard-card-header">
            <h3>{{ __('Datos del conductor') }}</h3>
        </div>
        <div style="padding:18px">
            @include('parametros.conductores._form', [
                'conductor' => null,
                'tipoDocumentos' => $tipoDocumentos,
                'action' => route('parametros.conductores.store'),
                'method' => 'POST',
                'submitLabel' => __('Registrar conductor'),
            ])
        </div>
    </div>
@endsection
