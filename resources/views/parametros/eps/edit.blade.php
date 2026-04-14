@extends('layouts.dashboard-shell')

@section('title', __('Editar EPS'))

@section('page_title', __('Editar EPS'))

@section('content')
    <div class="dashboard-content-header" style="margin-bottom:16px">
        <h1 style="margin:0;font-size:20px;font-weight:600">{{ __('Editar EPS') }}</h1>
        <p style="margin:6px 0 0;font-size:13px;color:var(--dash-text-secondary)">
            <a href="{{ route('parametros.eps.index') }}" class="dashboard-link">{{ __('← Volver al listado') }}</a>
        </p>
    </div>

    <div class="dashboard-card">
        <div class="dashboard-card-header">
            <h3>{{ $eps->nombre }} <span style="font-weight:400;color:var(--dash-text-secondary)">#{{ $eps->id }}</span></h3>
        </div>
        <div style="padding:18px">
            @include('parametros.eps._form', [
                'eps' => $eps,
                'action' => route('parametros.eps.update', $eps),
                'method' => 'PUT',
                'submitLabel' => __('Guardar cambios'),
            ])
        </div>
    </div>
@endsection
