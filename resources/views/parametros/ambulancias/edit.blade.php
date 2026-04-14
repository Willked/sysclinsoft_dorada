@extends('layouts.dashboard-shell')

@section('title', __('Editar ambulancia'))

@section('page_title', __('Editar ambulancia'))

@section('content')
    <div class="dashboard-content-header" style="margin-bottom:16px">
        <h1 style="margin:0;font-size:20px;font-weight:600">{{ __('Editar ambulancia') }}</h1>
        <p style="margin:6px 0 0;font-size:13px;color:var(--dash-text-secondary)">
            <a href="{{ route('parametros.ambulancias.index') }}" class="dashboard-link">{{ __('← Volver al listado') }}</a>
        </p>
    </div>

    <div class="dashboard-card">
        <div class="dashboard-card-header">
            <h3>{{ $ambulancia->codigo }} @if ($ambulancia->placa)<span style="font-weight:400;color:var(--dash-text-secondary)">· {{ $ambulancia->placa }}</span>@endif</h3>
        </div>
        <div style="padding:18px">
            @include('parametros.ambulancias._form', [
                'ambulancia' => $ambulancia,
                'action' => route('parametros.ambulancias.update', $ambulancia),
                'method' => 'PUT',
                'submitLabel' => __('Guardar cambios'),
            ])
        </div>
    </div>
@endsection
