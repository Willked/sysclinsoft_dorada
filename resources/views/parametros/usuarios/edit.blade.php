@extends('layouts.dashboard-shell')

@section('title', __('Editar usuario'))

@section('page_title', __('Editar usuario'))

@section('content')
    <div class="dashboard-content-header" style="margin-bottom:16px">
        <h1 style="margin:0;font-size:20px;font-weight:600">{{ __('Editar usuario') }}</h1>
        <p style="margin:6px 0 0;font-size:13px;color:var(--dash-text-secondary)">
            <a href="{{ route('parametros.usuarios.index') }}" class="dashboard-link">{{ __('← Volver al listado') }}</a>
        </p>
    </div>

    <div class="dashboard-card">
        <div class="dashboard-card-header">
            <h3>{{ $user->name }}</h3>
        </div>
        <div style="padding:18px">
            @include('parametros.usuarios._form', [
                'user' => $user,
                'roles' => $roles,
                'action' => route('parametros.usuarios.update', $user),
                'method' => 'PUT',
                'submitLabel' => __('Guardar cambios'),
            ])
        </div>
    </div>
@endsection
