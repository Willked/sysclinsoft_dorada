<?php

namespace App\Http\Controllers\Parametros;

use App\Http\Controllers\Controller;
use App\Models\Conductor;
use App\Models\TipoDocumento;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ConductorController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));
        $conductores = Conductor::query()
            ->with('tipoDocumento')
            ->when($q !== '', function ($query) use ($q): void {
                $query->where(function ($sub) use ($q): void {
                    $sub->where('numero_documento', 'like', '%'.$q.'%')
                        ->orWhere('primer_nombre', 'like', '%'.$q.'%')
                        ->orWhere('segundo_nombre', 'like', '%'.$q.'%')
                        ->orWhere('primer_apellido', 'like', '%'.$q.'%')
                        ->orWhere('segundo_apellido', 'like', '%'.$q.'%')
                        ->orWhere('numero_licencia', 'like', '%'.$q.'%');
                });
            })
            ->orderBy('primer_apellido')
            ->orderBy('primer_nombre')
            ->paginate(15)
            ->withQueryString();

        return view('parametros.conductores.index', [
            'conductores' => $conductores,
            'q' => $q,
        ]);
    }

    public function create(): View
    {
        return view('parametros.conductores.create', [
            'tipoDocumentos' => TipoDocumento::query()->activosOrdenados()->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->rules());

        Conductor::query()->create([
            'tipo_documento_id' => (int) $validated['tipo_documento_id'],
            'numero_documento' => trim($validated['numero_documento']),
            'primer_nombre' => $this->textoMayusculas($validated['primer_nombre']),
            'segundo_nombre' => $this->nullableMayusculas($validated['segundo_nombre'] ?? null),
            'primer_apellido' => $this->textoMayusculas($validated['primer_apellido']),
            'segundo_apellido' => $this->nullableMayusculas($validated['segundo_apellido'] ?? null),
            'telefono' => $this->nullableTrim($validated['telefono'] ?? null),
            'numero_licencia' => $this->nullableUpper($validated['numero_licencia'] ?? null),
            'categoria_licencia' => $this->nullableUpper($validated['categoria_licencia'] ?? null),
            'fecha_vencimiento_licencia' => $validated['fecha_vencimiento_licencia'] ?? null,
            'activo' => true,
        ]);

        return redirect()
            ->route('parametros.conductores.index')
            ->with('status', __('Conductor registrado correctamente.'));
    }

    public function edit(Conductor $conductor): View
    {
        return view('parametros.conductores.edit', [
            'conductor' => $conductor,
            'tipoDocumentos' => TipoDocumento::query()->activosOrdenados()->get(),
        ]);
    }

    public function update(Request $request, Conductor $conductor): RedirectResponse
    {
        $validated = $request->validate($this->rules($conductor->id));

        $conductor->tipo_documento_id = (int) $validated['tipo_documento_id'];
        $conductor->numero_documento = trim($validated['numero_documento']);
        $conductor->primer_nombre = $this->textoMayusculas($validated['primer_nombre']);
        $conductor->segundo_nombre = $this->nullableMayusculas($validated['segundo_nombre'] ?? null);
        $conductor->primer_apellido = $this->textoMayusculas($validated['primer_apellido']);
        $conductor->segundo_apellido = $this->nullableMayusculas($validated['segundo_apellido'] ?? null);
        $conductor->telefono = $this->nullableTrim($validated['telefono'] ?? null);
        $conductor->numero_licencia = $this->nullableUpper($validated['numero_licencia'] ?? null);
        $conductor->categoria_licencia = $this->nullableUpper($validated['categoria_licencia'] ?? null);
        $conductor->fecha_vencimiento_licencia = $validated['fecha_vencimiento_licencia'] ?? null;
        $conductor->save();

        return redirect()
            ->route('parametros.conductores.index')
            ->with('status', __('Conductor actualizado correctamente.'));
    }

    public function activar(Conductor $conductor): RedirectResponse
    {
        if ($conductor->activo) {
            return redirect()
                ->route('parametros.conductores.index')
                ->with('error', __('El conductor ya está activo.'));
        }

        $conductor->activo = true;
        $conductor->save();

        return redirect()
            ->route('parametros.conductores.index')
            ->with('status', __('Conductor activado.'));
    }

    public function desactivar(Conductor $conductor): RedirectResponse
    {
        if (! $conductor->activo) {
            return redirect()
                ->route('parametros.conductores.index')
                ->with('error', __('El conductor ya está inactivo.'));
        }

        $conductor->activo = false;
        $conductor->save();

        return redirect()
            ->route('parametros.conductores.index')
            ->with('status', __('Conductor desactivado.'));
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    private function rules(?int $ignoreId = null): array
    {
        $documentoRule = Rule::unique('conductores', 'numero_documento')
            ->where(fn ($query) => $query->where('tipo_documento_id', request('tipo_documento_id')));

        if ($ignoreId !== null) {
            $documentoRule = $documentoRule->ignore($ignoreId);
        }

        return [
            'tipo_documento_id' => ['required', 'integer', 'exists:tipo_documentos,id'],
            'numero_documento' => ['required', 'string', 'max:32', $documentoRule],
            'primer_nombre' => ['required', 'string', 'max:120'],
            'segundo_nombre' => ['nullable', 'string', 'max:120'],
            'primer_apellido' => ['required', 'string', 'max:120'],
            'segundo_apellido' => ['nullable', 'string', 'max:120'],
            'telefono' => ['nullable', 'string', 'max:32'],
            'numero_licencia' => ['nullable', 'string', 'max:64'],
            'categoria_licencia' => ['nullable', 'string', 'max:16'],
            'fecha_vencimiento_licencia' => ['nullable', 'date'],
        ];
    }

    private function textoMayusculas(string $value): string
    {
        return mb_strtoupper(trim($value), 'UTF-8');
    }

    private function nullableMayusculas(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $trimmed = trim($value);

        return $trimmed === '' ? null : mb_strtoupper($trimmed, 'UTF-8');
    }

    private function nullableTrim(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $trimmed = trim($value);

        return $trimmed === '' ? null : $trimmed;
    }

    private function nullableUpper(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $trimmed = trim($value);

        return $trimmed === '' ? null : mb_strtoupper($trimmed, 'UTF-8');
    }
}
