<?php

namespace App\Http\Controllers\Parametros;

use App\Http\Controllers\Controller;
use App\Models\Ambulancia;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AmbulanciaController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));
        $ambulancias = Ambulancia::query()
            ->withCount('atenciones')
            ->when($q !== '', function ($query) use ($q): void {
                $query->where(function ($sub) use ($q): void {
                    $sub->where('codigo', 'like', '%'.$q.'%')
                        ->orWhere('placa', 'like', '%'.$q.'%')
                        ->orWhere('descripcion', 'like', '%'.$q.'%')
                        ->orWhere('clasificacion_servicio', 'like', '%'.$q.'%');
                });
            })
            ->orderBy('codigo')
            ->paginate(15)
            ->withQueryString();

        return view('parametros.ambulancias.index', [
            'ambulancias' => $ambulancias,
            'q' => $q,
        ]);
    }

    public function create(): View
    {
        return view('parametros.ambulancias.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'codigo' => ['required', 'string', 'max:32', Rule::unique('ambulancias', 'codigo')],
            'placa' => ['nullable', 'string', 'max:16', Rule::unique('ambulancias', 'placa')],
            'descripcion' => ['nullable', 'string', 'max:255'],
            'clasificacion_servicio' => ['nullable', 'string', 'max:32'],
        ]);

        Ambulancia::query()->create([
            'codigo' => trim($validated['codigo']),
            'placa' => $this->normalizedPlaca($validated['placa'] ?? null),
            'descripcion' => $this->nullableTrim($validated['descripcion'] ?? null),
            'clasificacion_servicio' => $this->nullableTrim($validated['clasificacion_servicio'] ?? null),
            'activo' => true,
        ]);

        return redirect()
            ->route('parametros.ambulancias.index')
            ->with('status', __('Ambulancia registrada correctamente.'));
    }

    public function edit(Ambulancia $ambulancia): View
    {
        return view('parametros.ambulancias.edit', [
            'ambulancia' => $ambulancia,
        ]);
    }

    public function update(Request $request, Ambulancia $ambulancia): RedirectResponse
    {
        $validated = $request->validate([
            'codigo' => ['required', 'string', 'max:32', Rule::unique('ambulancias', 'codigo')->ignore($ambulancia->id)],
            'placa' => ['nullable', 'string', 'max:16', Rule::unique('ambulancias', 'placa')->ignore($ambulancia->id)],
            'descripcion' => ['nullable', 'string', 'max:255'],
            'clasificacion_servicio' => ['nullable', 'string', 'max:32'],
        ]);

        $ambulancia->codigo = trim($validated['codigo']);
        $ambulancia->placa = $this->normalizedPlaca($validated['placa'] ?? null);
        $ambulancia->descripcion = $this->nullableTrim($validated['descripcion'] ?? null);
        $ambulancia->clasificacion_servicio = $this->nullableTrim($validated['clasificacion_servicio'] ?? null);
        $ambulancia->save();

        return redirect()
            ->route('parametros.ambulancias.index')
            ->with('status', __('Ambulancia actualizada correctamente.'));
    }

    public function activar(Ambulancia $ambulancia): RedirectResponse
    {
        if ($ambulancia->activo) {
            return redirect()
                ->route('parametros.ambulancias.index')
                ->with('error', __('La unidad ya está activa.'));
        }

        $ambulancia->activo = true;
        $ambulancia->save();

        return redirect()
            ->route('parametros.ambulancias.index')
            ->with('status', __('Unidad activada.'));
    }

    public function desactivar(Ambulancia $ambulancia): RedirectResponse
    {
        if (! $ambulancia->activo) {
            return redirect()
                ->route('parametros.ambulancias.index')
                ->with('error', __('La unidad ya está inactiva.'));
        }

        $ambulancia->activo = false;
        $ambulancia->save();

        return redirect()
            ->route('parametros.ambulancias.index')
            ->with('status', __('Unidad desactivada.'));
    }

    public function destroy(Ambulancia $ambulancia): RedirectResponse
    {
        if ($ambulancia->atenciones()->exists()) {
            return redirect()
                ->route('parametros.ambulancias.index')
                ->with('error', __('No se puede eliminar: esta unidad tiene atenciones asociadas. Desactívela en su lugar.'));
        }

        $ambulancia->delete();

        return redirect()
            ->route('parametros.ambulancias.index')
            ->with('status', __('Ambulancia eliminada.'));
    }

    private function normalizedPlaca(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }
        $t = trim($value);

        return $t === '' ? null : mb_strtoupper($t, 'UTF-8');
    }

    private function nullableTrim(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }
        $t = trim($value);

        return $t === '' ? null : $t;
    }
}
