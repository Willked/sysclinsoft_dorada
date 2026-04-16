@extends('layouts.dashboard-shell')

@section('title', __('Editar conductor'))

@section('page_title', __('Editar conductor'))

@section('content')
    <div class="dashboard-content-header" style="margin-bottom:16px">
        <h1 style="margin:0;font-size:20px;font-weight:600">{{ __('Editar conductor') }}</h1>
        <p style="margin:6px 0 0;font-size:13px;color:var(--dash-text-secondary)">
            <a href="{{ route('parametros.conductores.index') }}" class="dashboard-link">{{ __('← Volver al listado') }}</a>
        </p>
    </div>

    <div class="dashboard-card">
        <div class="dashboard-card-header">
            <h3>{{ $conductor->primer_nombre }} {{ $conductor->primer_apellido }} <span style="font-weight:400;color:var(--dash-text-secondary)">#{{ $conductor->id }}</span></h3>
        </div>
        <div style="padding:18px">
            @include('parametros.conductores._form', [
                'conductor' => $conductor,
                'tipoDocumentos' => $tipoDocumentos,
                'action' => route('parametros.conductores.update', $conductor),
                'method' => 'PUT',
                'submitLabel' => __('Guardar cambios'),
            ])
        </div>
    </div>
@endsection
