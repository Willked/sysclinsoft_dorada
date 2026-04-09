<?php

namespace App\Http\Controllers\Parametros;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class UsuarioController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));
        $users = User::query()
            ->withTrashed()
            ->with('roles')
            ->when($q !== '', function ($query) use ($q): void {
                $query->where(function ($sub) use ($q): void {
                    $sub->where('name', 'like', '%'.$q.'%')
                        ->orWhere('email', 'like', '%'.$q.'%')
                        ->orWhere('registro_medico', 'like', '%'.$q.'%');
                });
            })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('parametros.usuarios.index', [
            'users' => $users,
            'q' => $q,
        ]);
    }

    public function create(): View
    {
        return view('parametros.usuarios.create', [
            'roles' => Role::query()->where('guard_name', 'web')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $roleNames = Role::query()->where('guard_name', 'web')->pluck('name')->all();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'registro_medico' => ['nullable', 'string', 'max:191'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'string', Rule::in($roleNames)],
        ]);

        $user = User::query()->create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'registro_medico' => $this->normalizedRegistroMedico($validated['registro_medico'] ?? null),
            'password' => $validated['password'],
            'email_verified_at' => now(),
        ]);

        $user->syncRoles([$validated['role']]);

        return redirect()
            ->route('parametros.usuarios.index')
            ->with('status', __('Usuario creado correctamente.'));
    }

    public function edit(User $usuario): View
    {
        $usuario->load('roles');

        return view('parametros.usuarios.edit', [
            'user' => $usuario,
            'roles' => Role::query()->where('guard_name', 'web')->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, User $usuario): RedirectResponse
    {
        $roleNames = Role::query()->where('guard_name', 'web')->pluck('name')->all();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($usuario->id)],
            'registro_medico' => ['nullable', 'string', 'max:191'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'string', Rule::in($roleNames)],
        ]);

        $usuario->name = $validated['name'];
        $usuario->email = $validated['email'];
        $usuario->registro_medico = $this->normalizedRegistroMedico($validated['registro_medico'] ?? null);
        if (! empty($validated['password'])) {
            $usuario->password = $validated['password'];
        }
        $usuario->save();

        $usuario->syncRoles([$validated['role']]);

        return redirect()
            ->route('parametros.usuarios.index')
            ->with('status', __('Usuario actualizado correctamente.'));
    }

    public function destroy(User $usuario): RedirectResponse
    {
        if ($usuario->id === auth()->id()) {
            return redirect()
                ->route('parametros.usuarios.index')
                ->with('error', __('No puede desactivar su propio usuario.'));
        }

        if ($usuario->trashed()) {
            return redirect()
                ->route('parametros.usuarios.index')
                ->with('error', __('El usuario ya está inactivo.'));
        }

        $usuario->delete();

        return redirect()
            ->route('parametros.usuarios.index')
            ->with('status', __('Usuario desactivado.'));
    }

    public function restore(int $usuario): RedirectResponse
    {
        $user = User::onlyTrashed()->findOrFail($usuario);

        if ($user->id === auth()->id()) {
            return redirect()
                ->route('parametros.usuarios.index')
                ->with('error', __('Operación no permitida.'));
        }

        $user->restore();

        return redirect()
            ->route('parametros.usuarios.index')
            ->with('status', __('Usuario activado.'));
    }

    private function normalizedRegistroMedico(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $trimmed = trim($value);

        return $trimmed === '' ? null : $trimmed;
    }
}
