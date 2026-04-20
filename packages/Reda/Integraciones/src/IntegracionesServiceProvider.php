<?php

namespace Reda\Integraciones;

use Illuminate\Support\ServiceProvider;

class IntegracionesServiceProvider extends ServiceProvider
{
    public function register(): void
    {
       $this->mergeConfigFrom(
            __DIR__.'/../config/integraciones.php', 'reda-integraciones'
        );
    }

    public function boot(): void
    {
        // 1. Carga de Rutas
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        // 2. Carga de Vistas con el namespace 'reda-integraciones'
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'reda-integraciones');

        // 3. Carga las migraciones
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

       // 4. PUBLICACIÓN DE CONFIGURACIÓN
        $this->publishes([
            __DIR__.'/../config/integraciones.php' => config_path('integraciones.php'),
        // Nueva etiqueta
        ], 'integraciones-config');

        // Traducciones PHP (Mantén esto por si usas archivos PHP en el futuro)
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'reda-integraciones');

        // ¡NUEVO!: Carga de traducciones JSON
        // Esto permite que {{ __('Texto con espacios') }} funcione buscando en tu es.json
        $this->loadJsonTranslationsFrom(__DIR__.'/../resources/lang');

        // Publicación (Opcional, para que el usuario pueda sobrescribirlas)
        $this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/reda-integraciones'),
        ], 'integraciones-lang');
    }
}
