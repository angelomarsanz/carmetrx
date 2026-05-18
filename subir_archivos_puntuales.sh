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
    #"packages/Reda/Integraciones/routes/web.php"
    #"packages/Reda/Integraciones/resources/lang/es.json"

    #"app/Http/Controllers/User/PropertyManagement/StateController.php"
    #"packages/Reda/Integraciones/src/Events/StatesRequested.php"
    "packages/Reda/Integraciones/src/Listeners/SyncStatesWithMeli.php"
    
    #"app/Http/Controllers/User/PropertyManagement/CityController.php"
    #"packages/Reda/Integraciones/src/Events/CitiesRequested.php"
    "packages/Reda/Integraciones/src/Listeners/SyncCitiesWithMeli.php"

    "app/Http/Controllers/Agent/PropertyController.php"
    #"packages/Reda/Integraciones/resources/js/mercado_libre/autos/indexAutosAgregarEditar.js"
    #"packages/Reda/Integraciones/src/IntegracionesServiceProvider.php"
    #"packages/Reda/Integraciones/src/Http/Controllers/MercadoLibre/ConfiguracionController.php"
    #"packages/Reda/Integraciones/src/Models/MercadoLibre/CiudadMeli.php"
)