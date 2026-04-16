<?php

namespace App\Http\Controllers\Parametros;

use App\Http\Controllers\Controller;
use App\Models\CausaExterna;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CausaExternaController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));
        $causasExternas = CausaExterna::query()
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

        return view('parametros.causas-externas.index', [
            'causasExternas' => $causasExternas,
            'q' => $q,
        ]);
    }

    public function create(): View
    {
        return view('parametros.causas-externas.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->rules());

        CausaExterna::query()->create([
            'codigo' => strtoupper(trim($validated['codigo'])),
            'nombre' => $this->nombreMayusculas($validated['nombre']),
            'orden' => 0,
            'activo' => true,
        ]);

        return redirect()
            ->route('parametros.causas-externas.index')
            ->with('status', __('Causa externa registrada correctamente.'));
    }

    public function edit(CausaExterna $causaExterna): View
    {
        return view('parametros.causas-externas.edit', [
            'causaExterna' => $causaExterna,
        ]);
    }

    public function update(Request $request, CausaExterna $causaExterna): RedirectResponse
    {
        $validated = $request->validate($this->rules($causaExterna->id));

        $causaExterna->codigo = strtoupper(trim($validated['codigo']));
        $causaExterna->nombre = $this->nombreMayusculas($validated['nombre']);
        $causaExterna->save();

        return redirect()
            ->route('parametros.causas-externas.index')
            ->with('status', __('Causa externa actualizada correctamente.'));
    }

    public function activar(CausaExterna $causaExterna): RedirectResponse
    {
        if ($causaExterna->activo) {
            return redirect()
                ->route('parametros.causas-externas.index')
                ->with('error', __('La causa externa ya está activa.'));
        }

        $causaExterna->activo = true;
        $causaExterna->save();

        return redirect()
            ->route('parametros.causas-externas.index')
            ->with('status', __('Causa externa activada.'));
    }

    public function desactivar(CausaExterna $causaExterna): RedirectResponse
    {
        if (! $causaExterna->activo) {
            return redirect()
                ->route('parametros.causas-externas.index')
                ->with('error', __('La causa externa ya está inactiva.'));
        }

        $causaExterna->activo = false;
        $causaExterna->save();

        return redirect()
            ->route('parametros.causas-externas.index')
            ->with('status', __('Causa externa dada de baja: queda inactiva y el registro se conserva.'));
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    private function rules(?int $ignoreId = null): array
    {
        $codigoRule = Rule::unique('causa_externas', 'codigo');
        if ($ignoreId !== null) {
            $codigoRule = $codigoRule->ignore($ignoreId);
        }

        return [
            'codigo' => ['required', 'string', 'max:8', $codigoRule],
            'nombre' => ['required', 'string', 'max:255'],
        ];
    }

    private function nombreMayusculas(string $nombre): string
    {
        return mb_strtoupper(trim($nombre), 'UTF-8');
    }
}
