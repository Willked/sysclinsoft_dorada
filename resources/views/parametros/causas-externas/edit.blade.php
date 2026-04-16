@extends('layouts.dashboard-shell')

@section('title', __('Editar causa externa'))

@section('page_title', __('Editar causa externa'))

@section('content')
    <div class="dashboard-content-header" style="margin-bottom:16px">
        <h1 style="margin:0;font-size:20px;font-weight:600">{{ __('Editar causa externa') }}</h1>
        <p style="margin:6px 0 0;font-size:13px;color:var(--dash-text-secondary)">
            <a href="{{ route('parametros.causas-externas.index') }}" class="dashboard-link">{{ __('← Volver al listado') }}</a>
        </p>
    </div>

    <div class="dashboard-card">
        <div class="dashboard-card-header">
            <h3>{{ $causaExterna->codigo }} <span style="font-weight:400;color:var(--dash-text-secondary)">· {{ $causaExterna->nombre }}</span> <span style="font-weight:400;color:var(--dash-text-secondary)">#{{ $causaExterna->id }}</span></h3>
        </div>
        <div style="padding:18px">
            @include('parametros.causas-externas._form', [
                'causaExterna' => $causaExterna,
                'action' => route('parametros.causas-externas.update', $causaExterna),
                'method' => 'PUT',
                'submitLabel' => __('Guardar cambios'),
            ])
        </div>
    </div>
@endsection
