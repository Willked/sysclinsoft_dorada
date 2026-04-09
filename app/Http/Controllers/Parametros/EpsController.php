<?php

namespace App\Http\Controllers\Parametros;

use App\Http\Controllers\Controller;
use App\Models\Eps;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class EpsController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));
        $epsList = Eps::query()
            ->when($q !== '', function ($query) use ($q): void {
                $query->where(function ($sub) use ($q): void {
                    $sub->where('nombre', 'like', '%'.$q.'%')
                        ->orWhere('nit', 'like', '%'.$q.'%');
                    if (ctype_digit($q)) {
                        $sub->orWhere('id', (int) $q);
                    }
                });
            })
            ->orderBy('nombre')
            ->paginate(15)
            ->withQueryString();

        return view('parametros.eps.index', [
            'epsList' => $epsList,
            'q' => $q,
        ]);
    }

    public function create(): View
    {
        return view('parametros.eps.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->normalizeOptionalStrings($request);
        $validated = $request->validate($this->rules());

        DB::transaction(function () use ($validated): void {
            $eps = Eps::query()->create([
                'codigo' => 'p'.bin2hex(random_bytes(8)),
                'nombre' => trim($validated['nombre']),
                'nit' => $this->nullableTrim($validated['nit'] ?? null),
                'direccion' => $this->nullableTrim($validated['direccion'] ?? null),
                'telefono' => $this->nullableTrim($validated['telefono'] ?? null),
                'email' => $this->nullableTrim($validated['email'] ?? null),
                'website' => $this->nullableTrim($validated['website'] ?? null),
                'logo' => $this->nullableTrim($validated['logo'] ?? null),
                'descripcion' => $this->nullableTrim($validated['descripcion'] ?? null),
                'contacto' => $this->nullableTrim($validated['contacto'] ?? null),
                'contacto_telefono' => $this->nullableTrim($validated['contacto_telefono'] ?? null),
                'contacto_email' => $this->nullableTrim($validated['contacto_email'] ?? null),
                'orden' => 0,
                'activo' => true,
            ]);
            $candidate = (string) $eps->id;
            if (Eps::query()->where('codigo', $candidate)->where('id', '!=', $eps->id)->exists()) {
                $candidate = 'id'.$eps->id;
            }
            $eps->codigo = $candidate;
            $eps->save();
        });

        return redirect()
            ->route('parametros.eps.index')
            ->with('status', __('EPS registrada correctamente.'));
    }

    public function edit(Eps $eps): View
    {
        return view('parametros.eps.edit', [
            'eps' => $eps,
        ]);
    }

    public function update(Request $request, Eps $eps): RedirectResponse
    {
        $this->normalizeOptionalStrings($request);
        $validated = $request->validate($this->rules($eps->id));

        $eps->nombre = trim($validated['nombre']);
        $eps->nit = $this->nullableTrim($validated['nit'] ?? null);
        $eps->direccion = $this->nullableTrim($validated['direccion'] ?? null);
        $eps->telefono = $this->nullableTrim($validated['telefono'] ?? null);
        $eps->email = $this->nullableTrim($validated['email'] ?? null);
        $eps->website = $this->nullableTrim($validated['website'] ?? null);
        $eps->logo = $this->nullableTrim($validated['logo'] ?? null);
        $eps->descripcion = $this->nullableTrim($validated['descripcion'] ?? null);
        $eps->contacto = $this->nullableTrim($validated['contacto'] ?? null);
        $eps->contacto_telefono = $this->nullableTrim($validated['contacto_telefono'] ?? null);
        $eps->contacto_email = $this->nullableTrim($validated['contacto_email'] ?? null);
        $eps->save();

        return redirect()
            ->route('parametros.eps.index')
            ->with('status', __('EPS actualizada correctamente.'));
    }

    public function activar(Eps $eps): RedirectResponse
    {
        if ($eps->activo) {
            return redirect()
                ->route('parametros.eps.index')
                ->with('error', __('La EPS ya está activa.'));
        }

        $eps->activo = true;
        $eps->save();

        return redirect()
            ->route('parametros.eps.index')
            ->with('status', __('EPS activada.'));
    }

    public function desactivar(Eps $eps): RedirectResponse
    {
        if (! $eps->activo) {
            return redirect()
                ->route('parametros.eps.index')
                ->with('error', __('La EPS ya está inactiva.'));
        }

        $eps->activo = false;
        $eps->save();

        return redirect()
            ->route('parametros.eps.index')
            ->with('status', __('EPS dada de baja: queda inactiva y el registro se conserva.'));
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    private function rules(?int $ignoreId = null): array
    {
        $nitRule = Rule::unique('eps', 'nit');
        if ($ignoreId !== null) {
            $nitRule = $nitRule->ignore($ignoreId);
        }

        return [
            'nombre' => ['required', 'string', 'max:255'],
            'nit' => ['nullable', 'string', 'max:20', $nitRule],
            'direccion' => ['nullable', 'string', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:32'],
            'email' => ['nullable', 'email', 'max:255'],
            'website' => ['nullable', 'string', 'max:255'],
            'logo' => ['nullable', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string', 'max:255'],
            'contacto' => ['nullable', 'string', 'max:255'],
            'contacto_telefono' => ['nullable', 'string', 'max:32'],
            'contacto_email' => ['nullable', 'email', 'max:255'],
        ];
    }

    private function nullableTrim(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }
        $t = trim($value);

        return $t === '' ? null : $t;
    }

    private function normalizeOptionalStrings(Request $request): void
    {
        $keys = [
            'nit', 'direccion', 'telefono', 'email', 'website', 'logo', 'descripcion',
            'contacto', 'contacto_telefono', 'contacto_email',
        ];
        foreach ($keys as $key) {
            if (! $request->has($key)) {
                continue;
            }
            $v = $request->input($key);
            if (is_string($v) && trim($v) === '') {
                $request->merge([$key => null]);
            }
        }
    }
}
