<?php

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
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/atenciones/nueva', function () {
        return view('atencion');
    })->name('atenciones.nueva');

    Route::get('/geo/departamentos', [DivipolaController::class, 'departamentos'])->name('geo.departamentos');
    Route::get('/geo/departamentos/{departamento}/municipios', [DivipolaController::class, 'municipios'])->name('geo.municipios');
});
