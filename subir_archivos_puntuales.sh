#!/bin/bash

# --- CONFIGURACIÓN DE SUBIDA PUNTUAL ---
# Lista aquí las rutas de TODOS los archivos específicos que quieres subir.
# Pueden ser del proyecto original o de los módulos REDA.
# Para no subir archivos, escribir la palabra "Ninguno".
# Ejemplo:
#   ARCHIVOS_PHP_PUNTUALES=(
#       "app/Http/Controllers/PaymentController.php"
#       "Ninguno"
#   )
ARCHIVOS_PHP_PUNTUALES=(
    #"Ninguno"
    "packages/Reda/Integraciones/resources/lang/es.json"
    "packages/Reda/Integraciones/routes/web.php"
    #"app/Http/Controllers/User/PropertyManagement/PropertyController.php"
    #"packages/Reda/Integraciones/src/Events/ModelsRequested.php"
    "packages/Reda/Integraciones/src/Listeners/SyncModelsWithMeli.php"
    #"packages/Reda/Integraciones/src/IntegracionesServiceProvider.php"
    "packages/Reda/Integraciones/src/Http/Controllers/MercadoLibre/ConfiguracionController.php"
    #"packages/Reda/Integraciones/src/Models/MercadoLibre/ModeloAutoMeli.php"
    #"packages/Reda/Integraciones/src/Http/Controllers/General/UsuarioController.php"
    #"packages/Reda/Integraciones/src/Http/Controllers/MercadoLibre/ConfiguracionController.php"
    #"packages/Reda/Integraciones/src/Traits/MercadoLibre/MeliRequestsTrait.php"
    "packages/Reda/Integraciones/resources/js/main.js"
    "packages/Reda/Integraciones/resources/js/mercado_libre/autos/index.js"
    "packages/Reda/Integraciones/resources/js/mercado_libre/autos/indexAutosAgregarEditar.js"
)
