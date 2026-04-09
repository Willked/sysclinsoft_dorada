<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Permisos y roles iniciales (SysClinSoft — parametrización).
     * Ajuste de nombres: usar siempre guard `web`.
     */
    public function run(): void
    {
        Cache::forget(config('permission.cache.key'));

        $permisos = [
            'dashboard.acceder',
            'atenciones.ver',
            'atenciones.crear',
            'atenciones.editar',
            'atenciones.finalizar',
            'atenciones.signos_vitales',
            'atenciones.notas_clinicas',
            'usuarios.gestionar',
            'roles.gestionar',
            'ambulancias.gestionar',
            'eps.gestionar',
            'cups.gestionar',
            'sistema.parametros',
        ];

        foreach ($permisos as $nombre) {
            Permission::query()->firstOrCreate(
                ['name' => $nombre, 'guard_name' => 'web'],
            );
        }

        $todos = Permission::query()->where('guard_name', 'web')->get();

        $rolAdmin = Role::query()->firstOrCreate(
            ['name' => 'administrador', 'guard_name' => 'web'],
            ['name' => 'administrador', 'guard_name' => 'web'],
        );
        $rolAdmin->syncPermissions($todos);

        $rolMedico = Role::query()->firstOrCreate(
            ['name' => 'medico', 'guard_name' => 'web'],
            ['name' => 'medico', 'guard_name' => 'web'],
        );
        $rolMedico->syncPermissions([
            'dashboard.acceder',
            'atenciones.ver',
            'atenciones.crear',
            'atenciones.editar',
            'atenciones.finalizar',
            'atenciones.signos_vitales',
            'atenciones.notas_clinicas',
        ]);

        $rolParamedico = Role::query()->firstOrCreate(
            ['name' => 'paramedico', 'guard_name' => 'web'],
            ['name' => 'paramedico', 'guard_name' => 'web'],
        );
        $rolParamedico->syncPermissions([
            'dashboard.acceder',
            'atenciones.ver',
            'atenciones.crear',
            'atenciones.editar',
            'atenciones.finalizar',
            'atenciones.signos_vitales',
            'atenciones.notas_clinicas',
        ]);

        $rolVisor = Role::query()->firstOrCreate(
            ['name' => 'visor', 'guard_name' => 'web'],
            ['name' => 'visor', 'guard_name' => 'web'],
        );
        $rolVisor->syncPermissions([
            'dashboard.acceder',
            'atenciones.ver',
        ]);
    }
}
