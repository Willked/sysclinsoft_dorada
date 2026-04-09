<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            DepartamentoSeeder::class,
            MunicipioSeeder::class,
            TipoDocumentoSeeder::class,
            TipoUsuarioSeeder::class,
            CupSeeder::class,
            EpsSeeder::class,
            CausaExternaSeeder::class,
            AmbulanciaSeeder::class,
            ConductorSeeder::class,
            RolePermissionSeeder::class,
        ]);

        $adminUser = User::query()->firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'admin',
                'password' => Hash::make('12345678'),
                'email_verified_at' => now(),
            ]
        );
        $adminUser->syncRoles(['administrador']);
    }
}
