<?php

namespace App\Http\Controllers\Parametros;

use App\Http\Controllers\Controller;
use App\Models\Cie10;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class Cie10Controller extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));
        $cie10List = Cie10::query()
            ->when($q !== '', function ($query) use ($q): void {
                $query->where(function ($sub) use ($q): void {
                    $sub->where('descripcion', 'like', '%'.$q.'%')
                        ->orWhere('codigo', 'like', '%'.$q.'%')
                        ->orWhere('capitulo', 'like', '%'.$q.'%');
                    if (ctype_digit($q)) {
                        $sub->orWhere('id', (int) $q);
                    }
                });
            })
            ->orderBy('codigo')
            ->paginate(15)
            ->withQueryString();

        return view('parametros.cie10.index', [
            'cie10List' => $cie10List,
            'q' => $q,
        ]);
    }

    public function create(): View
    {
        return view('parametros.cie10.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->rules());

        Cie10::query()->create([
            'codigo' => $this->normalizarCodigo($validated['codigo']),
            'descripcion' => $this->textoMayusculas($validated['descripcion']),
            'capitulo' => $this->nullableCapitulo($validated['capitulo'] ?? null),
            'orden' => 0,
            'activo' => true,
        ]);

        return redirect()
            ->route('parametros.cie10.index')
            ->with('status', __('Diagnóstico CIE-10 registrado correctamente.'));
    }

    public function edit(Cie10 $cie10): View
    {
        return view('parametros.cie10.edit', [
            'cie10' => $cie10,
        ]);
    }

    public function update(Request $request, Cie10 $cie10): RedirectResponse
    {
        $validated = $request->validate($this->rules($cie10->id));

        $cie10->codigo = $this->normalizarCodigo($validated['codigo']);
        $cie10->descripcion = $this->textoMayusculas($validated['descripcion']);
        $cie10->capitulo = $this->nullableCapitulo($validated['capitulo'] ?? null);
        $cie10->save();

        return redirect()
            ->route('parametros.cie10.index')
            ->with('status', __('Diagnóstico CIE-10 actualizado correctamente.'));
    }

    public function activar(Cie10 $cie10): RedirectResponse
    {
        if ($cie10->activo) {
            return redirect()
                ->route('parametros.cie10.index')
                ->with('error', __('El registro ya está activo.'));
        }

        $cie10->activo = true;
        $cie10->save();

        return redirect()
            ->route('parametros.cie10.index')
            ->with('status', __('CIE-10 activado.'));
    }

    public function desactivar(Cie10 $cie10): RedirectResponse
    {
        if (! $cie10->activo) {
            return redirect()
                ->route('parametros.cie10.index')
                ->with('error', __('El registro ya está inactivo.'));
        }

        $cie10->activo = false;
        $cie10->save();

        return redirect()
            ->route('parametros.cie10.index')
            ->with('status', __('CIE-10 dado de baja: queda inactivo y el registro se conserva.'));
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    private function rules(?int $ignoreId = null): array
    {
        $codigoRule = Rule::unique('cie_10', 'codigo');
        if ($ignoreId !== null) {
            $codigoRule = $codigoRule->ignore($ignoreId);
        }

        return [
            'codigo' => ['required', 'string', 'max:16', $codigoRule],
            'descripcion' => ['required', 'string', 'max:512'],
            'capitulo' => ['nullable', 'string', 'max:32'],
        ];
    }

    private function normalizarCodigo(string $codigo): string
    {
        $c = strtoupper(preg_replace('/\s+/', '', trim($codigo)));

        return $c;
    }

    private function textoMayusculas(string $texto): string
    {
        return mb_strtoupper(trim($texto), 'UTF-8');
    }

    private function nullableCapitulo(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }
        $t = trim($value);

        return $t === '' ? null : mb_strtoupper($t, 'UTF-8');
    }
}
