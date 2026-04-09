<?php

namespace App\Http\Controllers\Parametros;

use App\Http\Controllers\Controller;
use App\Models\Cup;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CupController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));
        $cups = Cup::query()
            ->when($q !== '', function ($query) use ($q): void {
                $query->where(function ($sub) use ($q): void {
                    $sub->where('nombre', 'like', '%'.$q.'%')
                        ->orWhere('codigo', 'like', '%'.$q.'%');
                    if (ctype_digit($q)) {
                        $sub->orWhere('id', (int) $q);
                    }
                });
            })
            ->orderBy('codigo')
            ->paginate(15)
            ->withQueryString();

        return view('parametros.cups.index', [
            'cups' => $cups,
            'q' => $q,
        ]);
    }

    public function create(): View
    {
        return view('parametros.cups.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->rules());

        Cup::query()->create([
            'codigo' => strtoupper(trim($validated['codigo'])),
            'nombre' => $this->nombreMayusculas($validated['nombre']),
            'orden' => 0,
            'activo' => true,
        ]);

        return redirect()
            ->route('parametros.cups.index')
            ->with('status', __('CUPS registrado correctamente.'));
    }

    public function edit(Cup $cup): View
    {
        return view('parametros.cups.edit', [
            'cup' => $cup,
        ]);
    }

    public function update(Request $request, Cup $cup): RedirectResponse
    {
        $validated = $request->validate($this->rules($cup->id));

        $cup->codigo = strtoupper(trim($validated['codigo']));
        $cup->nombre = $this->nombreMayusculas($validated['nombre']);
        $cup->save();

        return redirect()
            ->route('parametros.cups.index')
            ->with('status', __('CUPS actualizado correctamente.'));
    }

    public function activar(Cup $cup): RedirectResponse
    {
        if ($cup->activo) {
            return redirect()
                ->route('parametros.cups.index')
                ->with('error', __('El CUPS ya está activo.'));
        }

        $cup->activo = true;
        $cup->save();

        return redirect()
            ->route('parametros.cups.index')
            ->with('status', __('CUPS activado.'));
    }

    public function desactivar(Cup $cup): RedirectResponse
    {
        if (! $cup->activo) {
            return redirect()
                ->route('parametros.cups.index')
                ->with('error', __('El CUPS ya está inactivo.'));
        }

        $cup->activo = false;
        $cup->save();

        return redirect()
            ->route('parametros.cups.index')
            ->with('status', __('CUPS dado de baja: queda inactivo y el registro se conserva.'));
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    private function rules(?int $ignoreId = null): array
    {
        $codigoRule = Rule::unique('cups', 'codigo');
        if ($ignoreId !== null) {
            $codigoRule = $codigoRule->ignore($ignoreId);
        }

        return [
            'codigo' => ['required', 'string', 'max:16', $codigoRule],
            'nombre' => ['required', 'string', 'max:255'],
        ];
    }

    private function nombreMayusculas(string $nombre): string
    {
        return mb_strtoupper(trim($nombre), 'UTF-8');
    }
}
