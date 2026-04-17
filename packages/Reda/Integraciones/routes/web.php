<?php

use Illuminate\Support\Facades\Route;
use Reda\Integraciones\Http\Controllers\MercadoLibre\ImportadorController;

Route::get('test-integraciones', function () {
    return '¡El plugin de Integraciones está funcionando perfectamente!';
});

// Proteger la ruta y exponerla en '/user/mercado-libre/importadores'
Route::group(['prefix' => 'user', 'middleware' => ['auth:web', 'userstatus', 'TenantDashboardLang']], function () {
    Route::get('mercado-libre/importadores', [ImportadorController::class, 'index'])->name('reda.mercado_libre.importadores.index');
});