<?php

use App\Http\Controllers\AtencionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DivipolaController;
use App\Http\Controllers\Parametros\AmbulanciaController;
use App\Http\Controllers\Parametros\CupController;
use App\Http\Controllers\Parametros\EpsController;
use App\Http\Controllers\Parametros\RolController;
use App\Http\Controllers\Parametros\UsuarioController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    // Route::get('/dashboardjetstream', function () {
    //     return view('dashboardjetstream');
    // })->name('dashboardantiguo');
    Route::get('/dashboard', [DashboardController::class, 'create'])->name('dashboard');

    Route::get('/atenciones/nueva', [AtencionController::class, 'create'])->name('atenciones.nueva');
    Route::post('/atenciones/nueva', [AtencionController::class, 'store'])->name('atenciones.nueva.store');
    Route::get('/atenciones/{atencion}', [AtencionController::class, 'show'])->name('atenciones.show');
    Route::get('/atenciones/{atencion}/editar', [AtencionController::class, 'edit'])->name('atenciones.edit');
    Route::put('/atenciones/{atencion}', [AtencionController::class, 'update'])->name('atenciones.update');
    Route::post('/atenciones/{atencion}/finalizar', [AtencionController::class, 'finalizar'])->name('atenciones.finalizar');
    Route::post('/atenciones/{atencion}/signos-vitales', [AtencionController::class, 'storeSignosVitales'])->name('atenciones.signos-vitales.store');
    Route::post('/atenciones/{atencion}/notas-clinicas', [AtencionController::class, 'storeNotaClinica'])->name('atenciones.notas-clinicas.store');

    Route::get('/geo/departamentos', [DivipolaController::class, 'departamentos'])->name('geo.departamentos');
    Route::get('/geo/departamentos/{departamento}/municipios', [DivipolaController::class, 'municipios'])->name('geo.municipios');

    Route::prefix('parametros')->name('parametros.')->group(function (): void {
        Route::middleware(['permission:usuarios.gestionar'])->group(function (): void {
            Route::post('usuarios/{usuario}/restore', [UsuarioController::class, 'restore'])
                ->name('usuarios.restore')
                ->whereNumber('usuario');
            Route::resource('usuarios', UsuarioController::class);
        });

        Route::middleware(['permission:roles.gestionar'])->group(function (): void {
            Route::resource('roles', RolController::class)
                ->parameters(['roles' => 'rol'])
                ->except(['show']);
        });

        Route::middleware(['permission:ambulancias.gestionar'])->group(function (): void {
            Route::post('ambulancias/{ambulancia}/activar', [AmbulanciaController::class, 'activar'])
                ->name('ambulancias.activar');
            Route::post('ambulancias/{ambulancia}/desactivar', [AmbulanciaController::class, 'desactivar'])
                ->name('ambulancias.desactivar');
            Route::resource('ambulancias', AmbulanciaController::class)->except(['show']);
        });

        Route::middleware(['permission:eps.gestionar'])->group(function (): void {
            Route::post('eps/{eps}/activar', [EpsController::class, 'activar'])
                ->name('eps.activar');
            Route::post('eps/{eps}/desactivar', [EpsController::class, 'desactivar'])
                ->name('eps.desactivar');
            Route::resource('eps', EpsController::class)
                ->parameters(['eps' => 'eps'])
                ->except(['show', 'destroy']);
        });

        Route::middleware(['permission:cups.gestionar'])->group(function (): void {
            Route::post('cups/{cup}/activar', [CupController::class, 'activar'])
                ->name('cups.activar');
            Route::post('cups/{cup}/desactivar', [CupController::class, 'desactivar'])
                ->name('cups.desactivar');
            Route::resource('cups', CupController::class)->except(['show', 'destroy']);
        });
    });
});
