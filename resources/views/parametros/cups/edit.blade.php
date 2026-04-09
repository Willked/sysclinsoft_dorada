@extends('layouts.dashboard-shell')

@section('title', __('Editar CUPS'))

@section('page_title', __('Editar CUPS'))

@section('content')
    <div class="dashboard-content-header" style="margin-bottom:16px">
        <h1 style="margin:0;font-size:20px;font-weight:600">{{ __('Editar CUPS') }}</h1>
        <p style="margin:6px 0 0;font-size:13px;color:var(--dash-text-secondary)">
            <a href="{{ route('parametros.cups.index') }}" class="dashboard-link">{{ __('← Volver al listado') }}</a>
        </p>
    </div>

    <div class="dashboard-card">
        <div class="dashboard-card-header">
            <h3>{{ $cup->codigo }} <span style="font-weight:400;color:var(--dash-text-secondary)">· {{ $cup->nombre }}</span> <span style="font-weight:400;color:var(--dash-text-secondary)">#{{ $cup->id }}</span></h3>
        </div>
        <div style="padding:18px">
            @include('parametros.cups._form', [
                'cup' => $cup,
                'action' => route('parametros.cups.update', $cup),
                'method' => 'PUT',
                'submitLabel' => __('Guardar cambios'),
            ])
        </div>
    </div>
@endsection
