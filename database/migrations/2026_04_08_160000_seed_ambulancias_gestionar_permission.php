<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * Asegura el permiso ambulancias.gestionar y su asignación al rol administrador
 * en instalaciones que se sembraron antes de existir ese permiso en RolePermissionSeeder.
 */
return new class extends Migration
{
    public function up(): void
    {
        $permission = Permission::query()->firstOrCreate(
            ['name' => 'ambulancias.gestionar', 'guard_name' => 'web'],
        );

        $admin = Role::query()
            ->where('guard_name', 'web')
            ->whereIn('name', ['administrador', 'Administrador'])
            ->first();

        if ($admin !== null) {
            if (! $admin->hasPermissionTo($permission)) {
                $admin->givePermissionTo($permission);
            }
        }

        Cache::forget(config('permission.cache.key'));
    }

    public function down(): void
    {
        $permission = Permission::query()
            ->where('name', 'ambulancias.gestionar')
            ->where('guard_name', 'web')
            ->first();

        if ($permission === null) {
            return;
        }

        foreach (Role::query()->where('guard_name', 'web')->cursor() as $role) {
            $role->revokePermissionTo($permission);
        }

        $permission->delete();

        Cache::forget(config('permission.cache.key'));
    }
};
