<?php

use App\Http\Controllers\AtencionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DivipolaController;
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

    Route::get('/geo/departamentos', [DivipolaController::class, 'departamentos'])->name('geo.departamentos');
    Route::get('/geo/departamentos/{departamento}/municipios', [DivipolaController::class, 'municipios'])->name('geo.municipios');
});
