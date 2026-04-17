<?php

namespace Reda\Integraciones;

use Illuminate\Support\ServiceProvider;

class IntegracionesServiceProvider extends ServiceProvider
{
    public function register(): void
    {
       $this->mergeConfigFrom(
            __DIR__.'/../config/integracion.php', 'integracion'
        );
    }

    public function boot(): void
    {
        // 1. Carga de Rutas
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        
        // 2. Carga de Vistas con el namespace 'reda-integraciones'
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'integraciones');

        // 3. Carga las migraciones
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

       // 4. PUBLICACIÓN DE CONFIGURACIÓN
        $this->publishes([
            __DIR__.'/../config/integracion.php' => config_path('integracion.php'),
        // Nueva etiqueta
        ], 'integracion-config');
    }
}