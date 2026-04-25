<?php

use Illuminate\Support\Facades\Route;
use Reda\Integraciones\Http\Controllers\MercadoLibre\ImportadorController;
use Reda\Integraciones\Http\Controllers\MercadoLibre\ConfiguracionController;

// --- LÓGICA DE DETECCIÓN DE TENANT (Copiada de tenant_frontend.php) ---
$domain = env('WEBSITE_HOST');
$parsedUrl = parse_url(url()->current());
$host = isset($parsedUrl['host']) ? str_replace("www.", "", $parsedUrl['host']) : null;
$prefix = '';

if ($host) {
    if ($host == env('WEBSITE_HOST')) {
        $prefix = '/{username}';
    } else {
        $domain = (substr($_SERVER['HTTP_HOST'] ?? '', 0, 4) === 'www.') ? 'www.{domain}' : '{domain}';
    }
}

// Ruta de prueba para verificar que el plugin de Integraciones está funcionando correctamente
Route::get('test-integraciones', function () {
    return '¡El plugin de Integraciones está funcionando perfectamente!';
});

// Rutas para el panel de administración
Route::domain($domain)->group(function () use ($domain) {
    Route::prefix('admin')->middleware(['adminLang'])->group(function () use ($domain) {
        Route::group(['middleware' => ['auth:admin', 'checkstatus']], function ()  use ($domain) {
            Route::get('general/usuario/verificar', [UsuarioController::class, 'verificarUsuarioConectado'])->name('reda.integraciones.general.admin.usuario.verificar');
            Route::get('mercado-libre/importadores', [ImportadorController::class, 'index'])->name('reda.integraciones.mercado_libre.admin.importadores.index');
        });
    });
});

// Rutas para la agencia
Route::group(['prefix' => 'user', 'middleware' => ['auth:web', 'userstatus', 'TenantDashboardLang']], function () use ($domain) {
    Route::get('general/usuario/verificar', [UsuarioController::class, 'verificarUsuarioConectado'])->name('reda.integraciones.general.user.usuario.verificar');
    Route::get('mercado-libre/configuraciones', [ConfiguracionController::class, 'index'])->name('reda.integraciones.mercado_libre.user.configuraciones.index');
    Route::post('mercado-libre/configuraciones/verificar-token', [ConfiguracionController::class, 'verificarTokenMeli'])->name('reda.integraciones.mercado_libre.user.configuraciones.verificar_token');
});

// Rutas para el agente
Route::group([
    'domain' => $domain,
    'prefix' => $prefix,
    'middleware' => ['userMaintenance']
], function () use ($domain, $prefix) {
    Route::middleware(['frontend.language'])->group(function () use ($domain) {
        Route::group(['prefix' => 'agent', 'middleware' => ['auth:agent']], function () use ($domain) {
            Route::get('general/usuario/verificar', [UsuarioController::class, 'verificarUsuarioConectado'])->name('reda.integraciones.general.agent.usuario.verificar');
            Route::get('mercado-libre/configuraciones', [ConfiguracionController::class, 'index'])->name('reda.integraciones.mercado_libre.agent.configuraciones.index');
        });
    });
});
