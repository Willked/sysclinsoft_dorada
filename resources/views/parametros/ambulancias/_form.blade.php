<form method="POST" action="{{ $action }}" style="max-width:520px">
    @csrf
    @if ($method === 'PUT')
        @method('PUT')
    @endif

    <div style="margin-bottom:14px">
        <label for="codigo" style="display:block;font-size:12px;font-weight:500;color:var(--dash-text-secondary);margin-bottom:5px">{{ __('Código / número de unidad') }}<span style="color:var(--dash-clinical-crit-text)">*</span></label>
        <input id="codigo" name="codigo" type="text" value="{{ old('codigo', $ambulancia?->codigo ?? '') }}" required
            class="parametros-input" style="width:100%;box-sizing:border-box;padding:9px 12px;border:0.5px solid var(--dash-border-secondary);border-radius:var(--dash-radius-md);font-size:14px;font-family:inherit"
            maxlength="32" autocomplete="off" placeholder="{{ __('ej. 01, APH-12') }}">
        @error('codigo')
            <p style="margin:6px 0 0;font-size:12px;color:var(--dash-clinical-crit-text)">{{ $message }}</p>
        @enderror
    </div>

    <div style="margin-bottom:14px">
        <label for="placa" style="display:block;font-size:12px;font-weight:500;color:var(--dash-text-secondary);margin-bottom:5px">{{ __('Placa') }}</label>
        <input id="placa" name="placa" type="text" value="{{ old('placa', $ambulancia?->placa ?? '') }}"
            class="parametros-input" style="width:100%;box-sizing:border-box;padding:9px 12px;border:0.5px solid var(--dash-border-secondary);border-radius:var(--dash-radius-md);font-size:14px;font-family:inherit"
            maxlength="16" autocomplete="off" placeholder="{{ __('Opcional — se guardará en mayúsculas') }}">
        @error('placa')
            <p style="margin:6px 0 0;font-size:12px;color:var(--dash-clinical-crit-text)">{{ $message }}</p>
        @enderror
    </div>

    <div style="margin-bottom:14px">
        <label for="descripcion" style="display:block;font-size:12px;font-weight:500;color:var(--dash-text-secondary);margin-bottom:5px">{{ __('Descripción') }}</label>
        <input id="descripcion" name="descripcion" type="text" value="{{ old('descripcion', $ambulancia?->descripcion ?? '') }}"
            class="parametros-input" style="width:100%;box-sizing:border-box;padding:9px 12px;border:0.5px solid var(--dash-border-secondary);border-radius:var(--dash-radius-md);font-size:14px;font-family:inherit"
            maxlength="255" autocomplete="off">
        @error('descripcion')
            <p style="margin:6px 0 0;font-size:12px;color:var(--dash-clinical-crit-text)">{{ $message }}</p>
        @enderror
    </div>

    <div style="margin-bottom:18px">
        <label for="clasificacion_servicio" style="display:block;font-size:12px;font-weight:500;color:var(--dash-text-secondary);margin-bottom:5px">{{ __('Clasificación del servicio') }}</label>
        <input id="clasificacion_servicio" name="clasificacion_servicio" type="text" value="{{ old('clasificacion_servicio', $ambulancia?->clasificacion_servicio ?? '') }}"
            class="parametros-input" style="width:100%;box-sizing:border-box;padding:9px 12px;border:0.5px solid var(--dash-border-secondary);border-radius:var(--dash-radius-md);font-size:14px;font-family:inherit"
            maxlength="32" autocomplete="off" placeholder="{{ __('ej. SVB, SVA') }}">
        @error('clasificacion_servicio')
            <p style="margin:6px 0 0;font-size:12px;color:var(--dash-clinical-crit-text)">{{ $message }}</p>
        @enderror
    </div>

    <button type="submit" class="dashboard-btn-primary" style="border:none;cursor:pointer;font-family:inherit">{{ $submitLabel }}</button>
</form>
