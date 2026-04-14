@php
    $selectedRole = old('role', $user && $user->roles->isNotEmpty() ? $user->roles->first()->name : '');
@endphp
<form method="POST" action="{{ $action }}" style="max-width:520px">
    @csrf
    @if ($method === 'PUT')
        @method('PUT')
    @endif

    <div style="margin-bottom:14px">
        <label for="name" style="display:block;font-size:12px;font-weight:500;color:var(--dash-text-secondary);margin-bottom:5px">{{ __('Nombre completo') }}<span style="color:var(--dash-clinical-crit-text)">*</span></label>
        <input id="name" name="name" type="text" value="{{ old('name', $user->name ?? '') }}" required
            class="parametros-input" style="width:100%;box-sizing:border-box;padding:9px 12px;border:0.5px solid var(--dash-border-secondary);border-radius:var(--dash-radius-md);font-size:14px;font-family:inherit">
        @error('name')
            <p style="margin:6px 0 0;font-size:12px;color:var(--dash-clinical-crit-text)">{{ $message }}</p>
        @enderror
    </div>

    <div style="margin-bottom:14px">
        <label for="email" style="display:block;font-size:12px;font-weight:500;color:var(--dash-text-secondary);margin-bottom:5px">{{ __('Correo electrónico') }}<span style="color:var(--dash-clinical-crit-text)">*</span></label>
        <input id="email" name="email" type="email" value="{{ old('email', $user->email ?? '') }}" required autocomplete="username"
            class="parametros-input" style="width:100%;box-sizing:border-box;padding:9px 12px;border:0.5px solid var(--dash-border-secondary);border-radius:var(--dash-radius-md);font-size:14px;font-family:inherit">
        @error('email')
            <p style="margin:6px 0 0;font-size:12px;color:var(--dash-clinical-crit-text)">{{ $message }}</p>
        @enderror
    </div>

    <div style="margin-bottom:14px">
        <label for="registro_medico" style="display:block;font-size:12px;font-weight:500;color:var(--dash-text-secondary);margin-bottom:5px">{{ __('Registro médico / profesional') }}</label>
        <input id="registro_medico" name="registro_medico" type="text" value="{{ old('registro_medico', $user?->registro_medico ?? '') }}"
            class="parametros-input" style="width:100%;box-sizing:border-box;padding:9px 12px;border:0.5px solid var(--dash-border-secondary);border-radius:var(--dash-radius-md);font-size:14px;font-family:inherit"
            maxlength="191" autocomplete="off" placeholder="{{ __('Opcional — médicos y paramédicos') }}">
        @error('registro_medico')
            <p style="margin:6px 0 0;font-size:12px;color:var(--dash-clinical-crit-text)">{{ $message }}</p>
        @enderror
    </div>

    <div style="margin-bottom:14px">
        <label for="password" style="display:block;font-size:12px;font-weight:500;color:var(--dash-text-secondary);margin-bottom:5px">{{ __('Contraseña') }} @if ($user)<span style="font-weight:400;color:var(--dash-text-tertiary)">({{ __('dejar en blanco para no cambiar') }})</span>@else<span style="color:var(--dash-clinical-crit-text)">*</span>@endif</label>
        <input id="password" name="password" type="password" @if (! $user) required @endif autocomplete="new-password"
            class="parametros-input" style="width:100%;box-sizing:border-box;padding:9px 12px;border:0.5px solid var(--dash-border-secondary);border-radius:var(--dash-radius-md);font-size:14px;font-family:inherit">
        @error('password')
            <p style="margin:6px 0 0;font-size:12px;color:var(--dash-clinical-crit-text)">{{ $message }}</p>
        @enderror
    </div>

    <div style="margin-bottom:18px">
        <label for="password_confirmation" style="display:block;font-size:12px;font-weight:500;color:var(--dash-text-secondary);margin-bottom:5px">{{ __('Confirmar contraseña') }} @if (! $user)<span style="color:var(--dash-clinical-crit-text)">*</span>@endif</label>
        <input id="password_confirmation" name="password_confirmation" type="password" @if (! $user) required @endif autocomplete="new-password"
            class="parametros-input" style="width:100%;box-sizing:border-box;padding:9px 12px;border:0.5px solid var(--dash-border-secondary);border-radius:var(--dash-radius-md);font-size:14px;font-family:inherit">
    </div>

    <div style="margin-bottom:18px">
        <label for="role" style="display:block;font-size:12px;font-weight:500;color:var(--dash-text-secondary);margin-bottom:5px">{{ __('Rol') }}<span style="color:var(--dash-clinical-crit-text)">*</span></label>
        <select id="role" name="role" required
            class="parametros-input" style="width:100%;box-sizing:border-box;padding:9px 12px;border:0.5px solid var(--dash-border-secondary);border-radius:var(--dash-radius-md);font-size:14px;font-family:inherit;background:var(--dash-bg-primary);color:var(--dash-text-primary)">
            <option value="" @selected($selectedRole === '')>{{ __('Seleccione un rol') }}</option>
            @foreach ($roles as $role)
                <option value="{{ $role->name }}" @selected($selectedRole === $role->name)>{{ $role->name }}</option>
            @endforeach
        </select>
        @error('role')
            <p style="margin:6px 0 0;font-size:12px;color:var(--dash-clinical-crit-text)">{{ $message }}</p>
        @enderror
    </div>

    <button type="submit" class="dashboard-btn-primary" style="border:none;cursor:pointer;font-family:inherit">{{ $submitLabel }}</button>
</form>
