@extends('layouts.dashboard-shell')

@section('title', __('Nueva causa externa'))

@section('page_title', __('Nueva causa externa'))

@section('content')
    <div class="dashboard-content-header" style="margin-bottom:16px">
        <h1 style="margin:0;font-size:20px;font-weight:600">{{ __('Nueva causa externa') }}</h1>
        <p style="margin:6px 0 0;font-size:13px;color:var(--dash-text-secondary)">
            <a href="{{ route('parametros.causas-externas.index') }}" class="dashboard-link">{{ __('← Volver al listado') }}</a>
        </p>
    </div>

    <div class="dashboard-card">
        <div class="dashboard-card-header">
            <h3>{{ __('Datos de la causa externa') }}</h3>
        </div>
        <div style="padding:18px">
            @include('parametros.causas-externas._form', [
                'causaExterna' => null,
                'action' => route('parametros.causas-externas.store'),
                'method' => 'POST',
                'submitLabel' => __('Registrar causa externa'),
            ])
        </div>
    </div>
@endsection
