@extends('layouts.dashboard-shell')

@section('title', __('Editar CIE-10'))

@section('page_title', __('Editar CIE-10'))

@section('content')
    <div class="dashboard-content-header" style="margin-bottom:16px">
        <h1 style="margin:0;font-size:20px;font-weight:600">{{ __('Editar CIE-10') }}</h1>
        <p style="margin:6px 0 0;font-size:13px;color:var(--dash-text-secondary)">
            <a href="{{ route('parametros.cie10.index') }}" class="dashboard-link">{{ __('← Volver al listado') }}</a>
        </p>
    </div>

    <div class="dashboard-card">
        <div class="dashboard-card-header">
            <h3>{{ $cie10->codigo }} <span style="font-weight:400;color:var(--dash-text-secondary)">#{{ $cie10->id }}</span></h3>
        </div>
        <div style="padding:18px">
            @include('parametros.cie10._form', [
                'cie10' => $cie10,
                'action' => route('parametros.cie10.update', $cie10),
                'method' => 'PUT',
                'submitLabel' => __('Guardar cambios'),
            ])
        </div>
    </div>
@endsection
