<form method="POST" action="{{ $action }}" style="max-width:560px">
    @csrf
    @if ($method === 'PUT')
        @method('PUT')
    @endif

    <p style="margin:0 0 14px;font-size:12px;color:var(--dash-text-secondary)">{{ __('Datos principales') }}</p>

    <div style="margin-bottom:14px">
        <label for="nombre" style="display:block;font-size:12px;font-weight:500;color:var(--dash-text-secondary);margin-bottom:5px">{{ __('Nombre') }}<span style="color:var(--dash-clinical-crit-text)">*</span></label>
        <input id="nombre" name="nombre" type="text" value="{{ old('nombre', $eps?->nombre ?? '') }}" required
            class="parametros-input" style="width:100%;box-sizing:border-box;padding:9px 12px;border:0.5px solid var(--dash-border-secondary);border-radius:var(--dash-radius-md);font-size:14px;font-family:inherit"
            maxlength="255" autocomplete="organization">
        @error('nombre')
            <p style="margin:6px 0 0;font-size:12px;color:var(--dash-clinical-crit-text)">{{ $message }}</p>
        @enderror
    </div>

    <div style="margin-bottom:14px">
        <label for="nit" style="display:block;font-size:12px;font-weight:500;color:var(--dash-text-secondary);margin-bottom:5px">{{ __('NIT') }}</label>
        <input id="nit" name="nit" type="text" value="{{ old('nit', $eps?->nit ?? '') }}"
            class="parametros-input" style="width:100%;box-sizing:border-box;padding:9px 12px;border:0.5px solid var(--dash-border-secondary);border-radius:var(--dash-radius-md);font-size:14px;font-family:inherit"
            maxlength="20" autocomplete="off">
        @error('nit')
            <p style="margin:6px 0 0;font-size:12px;color:var(--dash-clinical-crit-text)">{{ $message }}</p>
        @enderror
    </div>

    <div style="margin-bottom:14px">
        <label for="descripcion" style="display:block;font-size:12px;font-weight:500;color:var(--dash-text-secondary);margin-bottom:5px">{{ __('Descripción') }}</label>
        <input id="descripcion" name="descripcion" type="text" value="{{ old('descripcion', $eps?->descripcion ?? '') }}"
            class="parametros-input" style="width:100%;box-sizing:border-box;padding:9px 12px;border:0.5px solid var(--dash-border-secondary);border-radius:var(--dash-radius-md);font-size:14px;font-family:inherit"
            maxlength="255" autocomplete="off">
        @error('descripcion')
            <p style="margin:6px 0 0;font-size:12px;color:var(--dash-clinical-crit-text)">{{ $message }}</p>
        @enderror
    </div>

    <p style="margin:18px 0 14px;font-size:12px;color:var(--dash-text-secondary)">{{ __('Contacto institucional') }}</p>

    <div style="margin-bottom:14px">
        <label for="direccion" style="display:block;font-size:12px;font-weight:500;color:var(--dash-text-secondary);margin-bottom:5px">{{ __('Dirección') }}</label>
        <input id="direccion" name="direccion" type="text" value="{{ old('direccion', $eps?->direccion ?? '') }}"
            class="parametros-input" style="width:100%;box-sizing:border-box;padding:9px 12px;border:0.5px solid var(--dash-border-secondary);border-radius:var(--dash-radius-md);font-size:14px;font-family:inherit"
            maxlength="255" autocomplete="street-address">
        @error('direccion')
            <p style="margin:6px 0 0;font-size:12px;color:var(--dash-clinical-crit-text)">{{ $message }}</p>
        @enderror
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:14px">
        <div>
            <label for="telefono" style="display:block;font-size:12px;font-weight:500;color:var(--dash-text-secondary);margin-bottom:5px">{{ __('Teléfono') }}</label>
            <input id="telefono" name="telefono" type="text" value="{{ old('telefono', $eps?->telefono ?? '') }}"
                class="parametros-input" style="width:100%;box-sizing:border-box;padding:9px 12px;border:0.5px solid var(--dash-border-secondary);border-radius:var(--dash-radius-md);font-size:14px;font-family:inherit"
                maxlength="32" autocomplete="tel">
            @error('telefono')
                <p style="margin:6px 0 0;font-size:12px;color:var(--dash-clinical-crit-text)">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label for="email" style="display:block;font-size:12px;font-weight:500;color:var(--dash-text-secondary);margin-bottom:5px">{{ __('Correo') }}</label>
            <input id="email" name="email" type="email" value="{{ old('email', $eps?->email ?? '') }}"
                class="parametros-input" style="width:100%;box-sizing:border-box;padding:9px 12px;border:0.5px solid var(--dash-border-secondary);border-radius:var(--dash-radius-md);font-size:14px;font-family:inherit"
                maxlength="255" autocomplete="email">
            @error('email')
                <p style="margin:6px 0 0;font-size:12px;color:var(--dash-clinical-crit-text)">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:14px">
        <div>
            <label for="website" style="display:block;font-size:12px;font-weight:500;color:var(--dash-text-secondary);margin-bottom:5px">{{ __('Sitio web') }}</label>
            <input id="website" name="website" type="text" value="{{ old('website', $eps?->website ?? '') }}"
                class="parametros-input" style="width:100%;box-sizing:border-box;padding:9px 12px;border:0.5px solid var(--dash-border-secondary);border-radius:var(--dash-radius-md);font-size:14px;font-family:inherit"
                maxlength="255" placeholder="https://" autocomplete="url">
            @error('website')
                <p style="margin:6px 0 0;font-size:12px;color:var(--dash-clinical-crit-text)">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label for="logo" style="display:block;font-size:12px;font-weight:500;color:var(--dash-text-secondary);margin-bottom:5px">{{ __('Logo (URL o ruta)') }}</label>
            <input id="logo" name="logo" type="text" value="{{ old('logo', $eps?->logo ?? '') }}"
                class="parametros-input" style="width:100%;box-sizing:border-box;padding:9px 12px;border:0.5px solid var(--dash-border-secondary);border-radius:var(--dash-radius-md);font-size:14px;font-family:inherit"
                maxlength="255" autocomplete="off">
            @error('logo')
                <p style="margin:6px 0 0;font-size:12px;color:var(--dash-clinical-crit-text)">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <p style="margin:18px 0 14px;font-size:12px;color:var(--dash-text-secondary)">{{ __('Persona de contacto') }}</p>

    <div style="margin-bottom:14px">
        <label for="contacto" style="display:block;font-size:12px;font-weight:500;color:var(--dash-text-secondary);margin-bottom:5px">{{ __('Nombre') }}</label>
        <input id="contacto" name="contacto" type="text" value="{{ old('contacto', $eps?->contacto ?? '') }}"
            class="parametros-input" style="width:100%;box-sizing:border-box;padding:9px 12px;border:0.5px solid var(--dash-border-secondary);border-radius:var(--dash-radius-md);font-size:14px;font-family:inherit"
            maxlength="255" autocomplete="name">
        @error('contacto')
            <p style="margin:6px 0 0;font-size:12px;color:var(--dash-clinical-crit-text)">{{ $message }}</p>
        @enderror
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:18px">
        <div>
            <label for="contacto_telefono" style="display:block;font-size:12px;font-weight:500;color:var(--dash-text-secondary);margin-bottom:5px">{{ __('Teléfono') }}</label>
            <input id="contacto_telefono" name="contacto_telefono" type="text" value="{{ old('contacto_telefono', $eps?->contacto_telefono ?? '') }}"
                class="parametros-input" style="width:100%;box-sizing:border-box;padding:9px 12px;border:0.5px solid var(--dash-border-secondary);border-radius:var(--dash-radius-md);font-size:14px;font-family:inherit"
                maxlength="32" autocomplete="tel">
            @error('contacto_telefono')
                <p style="margin:6px 0 0;font-size:12px;color:var(--dash-clinical-crit-text)">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label for="contacto_email" style="display:block;font-size:12px;font-weight:500;color:var(--dash-text-secondary);margin-bottom:5px">{{ __('Correo') }}</label>
            <input id="contacto_email" name="contacto_email" type="email" value="{{ old('contacto_email', $eps?->contacto_email ?? '') }}"
                class="parametros-input" style="width:100%;box-sizing:border-box;padding:9px 12px;border:0.5px solid var(--dash-border-secondary);border-radius:var(--dash-radius-md);font-size:14px;font-family:inherit"
                maxlength="255" autocomplete="email">
            @error('contacto_email')
                <p style="margin:6px 0 0;font-size:12px;color:var(--dash-clinical-crit-text)">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <button type="submit" class="dashboard-btn-primary" style="border:none;cursor:pointer;font-family:inherit">{{ $submitLabel }}</button>
</form>
