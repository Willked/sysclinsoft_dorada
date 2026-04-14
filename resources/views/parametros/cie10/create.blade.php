@extends('layouts.dashboard-shell')

@section('title', __('Nuevo CIE-10'))

@section('page_title', __('Nuevo CIE-10'))

@section('content')
    <div class="dashboard-content-header" style="margin-bottom:16px">
        <h1 style="margin:0;font-size:20px;font-weight:600">{{ __('Nuevo CIE-10') }}</h1>
        <p style="margin:6px 0 0;font-size:13px;color:var(--dash-text-secondary)">
            <a href="{{ route('parametros.cie10.index') }}" class="dashboard-link">{{ __('← Volver al listado') }}</a>
        </p>
    </div>

    <div class="dashboard-card">
        <div class="dashboard-card-header">
            <h3>{{ __('Datos del diagnóstico') }}</h3>
        </div>
        <div style="padding:18px">
            @include('parametros.cie10._form', [
                'cie10' => null,
                'action' => route('parametros.cie10.store'),
                'method' => 'POST',
                'submitLabel' => __('Registrar'),
            ])
        </div>
    </div>
@endsection
