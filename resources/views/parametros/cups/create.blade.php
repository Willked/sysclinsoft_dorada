@extends('layouts.dashboard-shell')

@section('title', __('Nuevo CUPS'))

@section('page_title', __('Nuevo CUPS'))

@section('content')
    <div class="dashboard-content-header" style="margin-bottom:16px">
        <h1 style="margin:0;font-size:20px;font-weight:600">{{ __('Nuevo CUPS') }}</h1>
        <p style="margin:6px 0 0;font-size:13px;color:var(--dash-text-secondary)">
            <a href="{{ route('parametros.cups.index') }}" class="dashboard-link">{{ __('← Volver al listado') }}</a>
        </p>
    </div>

    <div class="dashboard-card">
        <div class="dashboard-card-header">
            <h3>{{ __('Datos del procedimiento') }}</h3>
        </div>
        <div style="padding:18px">
            @include('parametros.cups._form', [
                'cup' => null,
                'action' => route('parametros.cups.store'),
                'method' => 'POST',
                'submitLabel' => __('Registrar CUPS'),
            ])
        </div>
    </div>
@endsection
