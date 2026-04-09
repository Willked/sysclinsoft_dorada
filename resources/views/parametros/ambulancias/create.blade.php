@extends('layouts.dashboard-shell')

@section('title', __('Nueva ambulancia'))

@section('page_title', __('Nueva ambulancia'))

@section('content')
    <div class="dashboard-content-header" style="margin-bottom:16px">
        <h1 style="margin:0;font-size:20px;font-weight:600">{{ __('Nueva ambulancia') }}</h1>
        <p style="margin:6px 0 0;font-size:13px;color:var(--dash-text-secondary)">
            <a href="{{ route('parametros.ambulancias.index') }}" class="dashboard-link">{{ __('← Volver al listado') }}</a>
        </p>
    </div>

    <div class="dashboard-card">
        <div class="dashboard-card-header">
            <h3>{{ __('Datos de la unidad') }}</h3>
        </div>
        <div style="padding:18px">
            @include('parametros.ambulancias._form', [
                'ambulancia' => null,
                'action' => route('parametros.ambulancias.store'),
                'method' => 'POST',
                'submitLabel' => __('Registrar ambulancia'),
            ])
        </div>
    </div>
@endsection
