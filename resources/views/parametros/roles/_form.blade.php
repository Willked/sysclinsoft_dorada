@php
    $selectedPermissions = old('permissions', $rol ? $rol->permissions->pluck('name')->all() : []);
@endphp
<form method="POST" action="{{ $action }}" class="parametros-rol-form">
    @csrf
    @if ($method === 'PUT')
        @method('PUT')
    @endif

    <div style="margin-bottom:14px">
        <label for="name" style="display:block;font-size:12px;font-weight:500;color:var(--dash-text-secondary);margin-bottom:5px">{{ __('Nombre del rol') }}<span style="color:var(--dash-clinical-crit-text)">*</span></label>
        <input id="name" name="name" type="text" value="{{ old('name', $rol->name ?? '') }}" required
            class="parametros-input" style="width:100%;max-width:520px;box-sizing:border-box;padding:9px 12px;border:0.5px solid var(--dash-border-secondary);border-radius:var(--dash-radius-md);font-size:14px;font-family:inherit"
            maxlength="255" autocomplete="off" placeholder="{{ __('ej. Coordinador de turno') }}">
        <p style="margin:6px 0 0;font-size:12px;color:var(--dash-text-tertiary);max-width:520px">{{ __('Se guardará con la primera letra de cada palabra en mayúscula. Letras, números, espacios, guiones y guiones bajos.') }}</p>
        @error('name')
            <p style="margin:6px 0 0;font-size:12px;color:var(--dash-clinical-crit-text)">{{ $message }}</p>
        @enderror
    </div>

    <div style="margin-bottom:18px">
        <span style="display:block;font-size:12px;font-weight:600;color:var(--dash-text-primary);margin-bottom:10px">{{ __('Permisos del rol') }}</span>
        <p style="margin:0 0 12px;font-size:12px;color:var(--dash-text-secondary);max-width:640px">{{ __('Marque las acciones que podrá realizar quien tenga este rol. Si no marca ninguno, el rol no tendrá permisos hasta que los asigne.') }}</p>
        <div class="parametros-permisos-grid">
            @foreach ($permissions as $grupo => $items)
                <fieldset class="parametros-permisos-grupo">
                    <legend>{{ $grupo }}</legend>
                    <div class="parametros-permisos-list">
                        @foreach ($items as $permission)
                            <label class="parametros-perm-item">
                                <input type="checkbox" name="permissions[]" value="{{ $permission->name }}" @checked(in_array($permission->name, $selectedPermissions, true))>
                                <span>{{ $permission->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </fieldset>
            @endforeach
        </div>
        @error('permissions')
            <p style="margin:8px 0 0;font-size:12px;color:var(--dash-clinical-crit-text)">{{ $message }}</p>
        @enderror
        @error('permissions.*')
            <p style="margin:8px 0 0;font-size:12px;color:var(--dash-clinical-crit-text)">{{ $message }}</p>
        @enderror
    </div>

    <button type="submit" class="dashboard-btn-primary" style="border:none;cursor:pointer;font-family:inherit">{{ $submitLabel }}</button>
</form>
