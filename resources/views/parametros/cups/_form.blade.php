<form method="POST" action="{{ $action }}" style="max-width:520px">
    @csrf
    @if ($method === 'PUT')
        @method('PUT')
    @endif

    <div style="margin-bottom:14px">
        <label for="codigo" style="display:block;font-size:12px;font-weight:500;color:var(--dash-text-secondary);margin-bottom:5px">{{ __('Código CUPS') }}<span style="color:var(--dash-clinical-crit-text)">*</span></label>
        <input id="codigo" name="codigo" type="text" value="{{ old('codigo', $cup?->codigo ?? '') }}" required
            class="parametros-input" style="width:100%;box-sizing:border-box;padding:9px 12px;border:0.5px solid var(--dash-border-secondary);border-radius:var(--dash-radius-md);font-size:14px;font-family:inherit;text-transform:uppercase"
            maxlength="16" autocomplete="off" placeholder="{{ __('Según manual vigente') }}">
        @error('codigo')
            <p style="margin:6px 0 0;font-size:12px;color:var(--dash-clinical-crit-text)">{{ $message }}</p>
        @enderror
    </div>

    <div style="margin-bottom:18px">
        <label for="nombre" style="display:block;font-size:12px;font-weight:500;color:var(--dash-text-secondary);margin-bottom:5px">{{ __('Nombre / descripción') }}<span style="color:var(--dash-clinical-crit-text)">*</span></label>
        <input id="nombre" name="nombre" type="text" value="{{ old('nombre', $cup?->nombre ?? '') }}" required
            class="parametros-input" style="width:100%;box-sizing:border-box;padding:9px 12px;border:0.5px solid var(--dash-border-secondary);border-radius:var(--dash-radius-md);font-size:14px;font-family:inherit"
            maxlength="255" autocomplete="off">
        @error('nombre')
            <p style="margin:6px 0 0;font-size:12px;color:var(--dash-clinical-crit-text)">{{ $message }}</p>
        @enderror
    </div>

    <button type="submit" class="dashboard-btn-primary" style="border:none;cursor:pointer;font-family:inherit">{{ $submitLabel }}</button>
</form>
