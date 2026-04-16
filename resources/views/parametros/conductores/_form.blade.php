<form method="POST" action="{{ $action }}" style="max-width:720px">
    @csrf
    @if ($method === 'PUT')
        @method('PUT')
    @endif

    <div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px">
        <div>
            <label for="tipo_documento_id" style="display:block;font-size:12px;font-weight:500;color:var(--dash-text-secondary);margin-bottom:5px">{{ __('Tipo de documento') }}<span style="color:var(--dash-clinical-crit-text)">*</span></label>
            <select id="tipo_documento_id" name="tipo_documento_id" required class="parametros-input" style="width:100%;box-sizing:border-box;padding:9px 12px;border:0.5px solid var(--dash-border-secondary);border-radius:var(--dash-radius-md);font-size:14px;font-family:inherit">
                <option value="">{{ __('Seleccione…') }}</option>
                @foreach ($tipoDocumentos as $tipoDocumento)
                    <option value="{{ $tipoDocumento->id }}" @selected((int) old('tipo_documento_id', $conductor?->tipo_documento_id) === $tipoDocumento->id)>{{ $tipoDocumento->nombre }}</option>
                @endforeach
            </select>
            @error('tipo_documento_id')
                <p style="margin:6px 0 0;font-size:12px;color:var(--dash-clinical-crit-text)">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="numero_documento" style="display:block;font-size:12px;font-weight:500;color:var(--dash-text-secondary);margin-bottom:5px">{{ __('Número de documento') }}<span style="color:var(--dash-clinical-crit-text)">*</span></label>
            <input id="numero_documento" name="numero_documento" type="text" value="{{ old('numero_documento', $conductor?->numero_documento ?? '') }}" required
                class="parametros-input" style="width:100%;box-sizing:border-box;padding:9px 12px;border:0.5px solid var(--dash-border-secondary);border-radius:var(--dash-radius-md);font-size:14px;font-family:inherit"
                maxlength="32" autocomplete="off">
            @error('numero_documento')
                <p style="margin:6px 0 0;font-size:12px;color:var(--dash-clinical-crit-text)">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="primer_nombre" style="display:block;font-size:12px;font-weight:500;color:var(--dash-text-secondary);margin-bottom:5px">{{ __('Primer nombre') }}<span style="color:var(--dash-clinical-crit-text)">*</span></label>
            <input id="primer_nombre" name="primer_nombre" type="text" value="{{ old('primer_nombre', $conductor?->primer_nombre ?? '') }}" required
                class="parametros-input" style="width:100%;box-sizing:border-box;padding:9px 12px;border:0.5px solid var(--dash-border-secondary);border-radius:var(--dash-radius-md);font-size:14px;font-family:inherit"
                maxlength="120" autocomplete="off">
            @error('primer_nombre')
                <p style="margin:6px 0 0;font-size:12px;color:var(--dash-clinical-crit-text)">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="segundo_nombre" style="display:block;font-size:12px;font-weight:500;color:var(--dash-text-secondary);margin-bottom:5px">{{ __('Segundo nombre') }}</label>
            <input id="segundo_nombre" name="segundo_nombre" type="text" value="{{ old('segundo_nombre', $conductor?->segundo_nombre ?? '') }}"
                class="parametros-input" style="width:100%;box-sizing:border-box;padding:9px 12px;border:0.5px solid var(--dash-border-secondary);border-radius:var(--dash-radius-md);font-size:14px;font-family:inherit"
                maxlength="120" autocomplete="off">
            @error('segundo_nombre')
                <p style="margin:6px 0 0;font-size:12px;color:var(--dash-clinical-crit-text)">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="primer_apellido" style="display:block;font-size:12px;font-weight:500;color:var(--dash-text-secondary);margin-bottom:5px">{{ __('Primer apellido') }}<span style="color:var(--dash-clinical-crit-text)">*</span></label>
            <input id="primer_apellido" name="primer_apellido" type="text" value="{{ old('primer_apellido', $conductor?->primer_apellido ?? '') }}" required
                class="parametros-input" style="width:100%;box-sizing:border-box;padding:9px 12px;border:0.5px solid var(--dash-border-secondary);border-radius:var(--dash-radius-md);font-size:14px;font-family:inherit"
                maxlength="120" autocomplete="off">
            @error('primer_apellido')
                <p style="margin:6px 0 0;font-size:12px;color:var(--dash-clinical-crit-text)">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="segundo_apellido" style="display:block;font-size:12px;font-weight:500;color:var(--dash-text-secondary);margin-bottom:5px">{{ __('Segundo apellido') }}</label>
            <input id="segundo_apellido" name="segundo_apellido" type="text" value="{{ old('segundo_apellido', $conductor?->segundo_apellido ?? '') }}"
                class="parametros-input" style="width:100%;box-sizing:border-box;padding:9px 12px;border:0.5px solid var(--dash-border-secondary);border-radius:var(--dash-radius-md);font-size:14px;font-family:inherit"
                maxlength="120" autocomplete="off">
            @error('segundo_apellido')
                <p style="margin:6px 0 0;font-size:12px;color:var(--dash-clinical-crit-text)">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="telefono" style="display:block;font-size:12px;font-weight:500;color:var(--dash-text-secondary);margin-bottom:5px">{{ __('Teléfono') }}</label>
            <input id="telefono" name="telefono" type="text" value="{{ old('telefono', $conductor?->telefono ?? '') }}"
                class="parametros-input" style="width:100%;box-sizing:border-box;padding:9px 12px;border:0.5px solid var(--dash-border-secondary);border-radius:var(--dash-radius-md);font-size:14px;font-family:inherit"
                maxlength="32" autocomplete="off">
            @error('telefono')
                <p style="margin:6px 0 0;font-size:12px;color:var(--dash-clinical-crit-text)">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="numero_licencia" style="display:block;font-size:12px;font-weight:500;color:var(--dash-text-secondary);margin-bottom:5px">{{ __('Número de licencia') }}</label>
            <input id="numero_licencia" name="numero_licencia" type="text" value="{{ old('numero_licencia', $conductor?->numero_licencia ?? '') }}"
                class="parametros-input" style="width:100%;box-sizing:border-box;padding:9px 12px;border:0.5px solid var(--dash-border-secondary);border-radius:var(--dash-radius-md);font-size:14px;font-family:inherit"
                maxlength="64" autocomplete="off">
            @error('numero_licencia')
                <p style="margin:6px 0 0;font-size:12px;color:var(--dash-clinical-crit-text)">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="categoria_licencia" style="display:block;font-size:12px;font-weight:500;color:var(--dash-text-secondary);margin-bottom:5px">{{ __('Categoría licencia') }}</label>
            <input id="categoria_licencia" name="categoria_licencia" type="text" value="{{ old('categoria_licencia', $conductor?->categoria_licencia ?? '') }}"
                class="parametros-input" style="width:100%;box-sizing:border-box;padding:9px 12px;border:0.5px solid var(--dash-border-secondary);border-radius:var(--dash-radius-md);font-size:14px;font-family:inherit"
                maxlength="16" autocomplete="off" placeholder="{{ __('ej. B2, C2') }}">
            @error('categoria_licencia')
                <p style="margin:6px 0 0;font-size:12px;color:var(--dash-clinical-crit-text)">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="fecha_vencimiento_licencia" style="display:block;font-size:12px;font-weight:500;color:var(--dash-text-secondary);margin-bottom:5px">{{ __('Vencimiento licencia') }}</label>
            <input id="fecha_vencimiento_licencia" name="fecha_vencimiento_licencia" type="date" value="{{ old('fecha_vencimiento_licencia', $conductor?->fecha_vencimiento_licencia?->format('Y-m-d') ?? '') }}"
                class="parametros-input" style="width:100%;box-sizing:border-box;padding:9px 12px;border:0.5px solid var(--dash-border-secondary);border-radius:var(--dash-radius-md);font-size:14px;font-family:inherit">
            @error('fecha_vencimiento_licencia')
                <p style="margin:6px 0 0;font-size:12px;color:var(--dash-clinical-crit-text)">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div style="margin-top:18px">
        <button type="submit" class="dashboard-btn-primary" style="border:none;cursor:pointer;font-family:inherit">{{ $submitLabel }}</button>
    </div>
</form>
