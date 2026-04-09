@extends('layouts.dashboard-shell')

@section('title', __('Nuevo rol'))

@section('page_title', __('Nuevo rol'))

@section('content')
    <div class="dashboard-content-header" style="margin-bottom:16px">
        <h1 style="margin:0;font-size:20px;font-weight:600">{{ __('Nuevo rol') }}</h1>
        <p style="margin:6px 0 0;font-size:13px;color:var(--dash-text-secondary)">
            <a href="{{ route('parametros.roles.index') }}" class="dashboard-link">{{ __('← Volver al listado') }}</a>
        </p>
    </div>

    <div class="dashboard-card">
        <div class="dashboard-card-header">
            <h3>{{ __('Definición del rol') }}</h3>
        </div>
        <div style="padding:18px">
            @include('parametros.roles._form', [
                'rol' => null,
                'permissions' => $permissions,
                'action' => route('parametros.roles.store'),
                'method' => 'POST',
                'submitLabel' => __('Crear rol'),
            ])
        </div>
    </div>
@endsection
