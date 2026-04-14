<?php

namespace App\Http\Controllers\Parametros;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolController extends Controller
{
    private const GUARD = 'web';

    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));
        $pivot = config('permission.table_names.model_has_roles');

        $roles = Role::query()
            ->select('roles.*')
            ->where('guard_name', self::GUARD)
            ->selectSub(function ($sub) use ($pivot): void {
                $sub->from($pivot)
                    ->whereColumn($pivot.'.role_id', 'roles.id')
                    ->where($pivot.'.model_type', User::class)
                    ->selectRaw('count(*)');
            }, 'users_count')
            ->withCount('permissions')
            ->when($q !== '', function ($query) use ($q): void {
                $query->where('name', 'like', '%'.$q.'%');
            })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('parametros.roles.index', [
            'roles' => $roles,
            'q' => $q,
        ]);
    }

    public function create(): View
    {
        return view('parametros.roles.create', [
            'permissions' => $this->permissionsForForm(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->merge([
            'name' => $this->normalizedRoleName((string) $request->input('name', '')),
        ]);

        $permissionNames = Permission::query()
            ->where('guard_name', self::GUARD)
            ->pluck('name')
            ->all();

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[\p{L}0-9_\-\s]+$/u',
                Rule::unique('roles', 'name')->where('guard_name', self::GUARD),
            ],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', Rule::in($permissionNames)],
        ]);

        $role = Role::query()->create([
            'name' => $validated['name'],
            'guard_name' => self::GUARD,
        ]);

        $role->syncPermissions($validated['permissions'] ?? []);

        return redirect()
            ->route('parametros.roles.index')
            ->with('status', __('Rol creado correctamente.'));
    }

    public function edit(Role $rol): View
    {
        if ($rol->guard_name !== self::GUARD) {
            abort(404);
        }

        $rol->load('permissions');

        return view('parametros.roles.edit', [
            'rol' => $rol,
            'permissions' => $this->permissionsForForm(),
        ]);
    }

    public function update(Request $request, Role $rol): RedirectResponse
    {
        if ($rol->guard_name !== self::GUARD) {
            abort(404);
        }

        $request->merge([
            'name' => $this->normalizedRoleName((string) $request->input('name', '')),
        ]);

        $permissionNames = Permission::query()
            ->where('guard_name', self::GUARD)
            ->pluck('name')
            ->all();

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[\p{L}0-9_\-\s]+$/u',
                Rule::unique('roles', 'name')
                    ->where('guard_name', self::GUARD)
                    ->ignore($rol->id),
            ],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', Rule::in($permissionNames)],
        ]);

        $rol->name = $validated['name'];
        $rol->save();

        $rol->syncPermissions($validated['permissions'] ?? []);

        return redirect()
            ->route('parametros.roles.index')
            ->with('status', __('Rol actualizado correctamente.'));
    }

    public function destroy(Role $rol): RedirectResponse
    {
        if ($rol->guard_name !== self::GUARD) {
            abort(404);
        }

        if ($this->assignedUsersCount($rol) > 0) {
            return redirect()
                ->route('parametros.roles.index')
                ->with('error', __('No se puede eliminar: hay usuarios asignados a este rol.'));
        }

        $rol->delete();

        return redirect()
            ->route('parametros.roles.index')
            ->with('status', __('Rol eliminado.'));
    }

    /**
     * Permisos agrupados por prefijo (parte antes del punto) para el formulario.
     *
     * @return Collection<string, \Illuminate\Database\Eloquent\Collection<int, Permission>>
     */
    private function permissionsForForm()
    {
        return Permission::query()
            ->where('guard_name', self::GUARD)
            ->orderBy('name')
            ->get()
            ->groupBy(function (Permission $p): string {
                $parts = explode('.', $p->name);

                return $parts[0] ?? $p->name;
            });
    }

    private function assignedUsersCount(Role $rol): int
    {
        $pivot = config('permission.table_names.model_has_roles');

        return (int) DB::table($pivot)
            ->where('role_id', $rol->id)
            ->where('model_type', User::class)
            ->count();
    }

    /** Primera letra de cada palabra en mayúscula (resto en minúsculas), UTF-8. */
    private function normalizedRoleName(string $value): string
    {
        $trimmed = trim($value);

        return $trimmed === '' ? '' : Str::title($trimmed);
    }
}
