<form method="POST" action="{{ $action }}" style="max-width:560px">
    @csrf
    @if ($method === 'PUT')
        @method('PUT')
    @endif

    <div style="margin-bottom:14px">
        <label for="codigo" style="display:block;font-size:12px;font-weight:500;color:var(--dash-text-secondary);margin-bottom:5px">{{ __('Código CIE-10') }}<span style="color:var(--dash-clinical-crit-text)">*</span></label>
        <input id="codigo" name="codigo" type="text" value="{{ old('codigo', $cie10?->codigo ?? '') }}" required
            class="parametros-input" style="width:100%;box-sizing:border-box;padding:9px 12px;border:0.5px solid var(--dash-border-secondary);border-radius:var(--dash-radius-md);font-size:14px;font-family:inherit;text-transform:uppercase"
            maxlength="16" autocomplete="off" placeholder="{{ __('Ej. I10, E11.9, J18.9') }}">
        @error('codigo')
            <p style="margin:6px 0 0;font-size:12px;color:var(--dash-clinical-crit-text)">{{ $message }}</p>
        @enderror
    </div>

    <div style="margin-bottom:14px">
        <label for="descripcion" style="display:block;font-size:12px;font-weight:500;color:var(--dash-text-secondary);margin-bottom:5px">{{ __('Descripción') }}<span style="color:var(--dash-clinical-crit-text)">*</span></label>
        <textarea id="descripcion" name="descripcion" required rows="4"
            class="parametros-input" style="width:100%;box-sizing:border-box;padding:9px 12px;border:0.5px solid var(--dash-border-secondary);border-radius:var(--dash-radius-md);font-size:14px;font-family:inherit;resize:vertical"
            maxlength="512" autocomplete="off">{{ old('descripcion', $cie10?->descripcion ?? '') }}</textarea>
        @error('descripcion')
            <p style="margin:6px 0 0;font-size:12px;color:var(--dash-clinical-crit-text)">{{ $message }}</p>
        @enderror
    </div>

    <div style="margin-bottom:18px">
        <label for="capitulo" style="display:block;font-size:12px;font-weight:500;color:var(--dash-text-secondary);margin-bottom:5px">{{ __('Capítulo / agrupación') }}</label>
        <input id="capitulo" name="capitulo" type="text" value="{{ old('capitulo', $cie10?->capitulo ?? '') }}"
            class="parametros-input" style="width:100%;max-width:200px;box-sizing:border-box;padding:9px 12px;border:0.5px solid var(--dash-border-secondary);border-radius:var(--dash-radius-md);font-size:14px;font-family:inherit"
            maxlength="32" autocomplete="off" placeholder="{{ __('Ej. I, IX, X') }}">
        @error('capitulo')
            <p style="margin:6px 0 0;font-size:12px;color:var(--dash-clinical-crit-text)">{{ $message }}</p>
        @enderror
        <p style="margin:6px 0 0;font-size:11px;color:var(--dash-text-tertiary)">{{ __('Opcional. Útil para filtros y reportes alineados con la estructura del manual.') }}</p>
    </div>

    <button type="submit" class="dashboard-btn-primary" style="border:none;cursor:pointer;font-family:inherit">{{ $submitLabel }}</button>
</form>
