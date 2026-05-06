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
    "packages/Reda/Integraciones/src/Models/MercadoLibre/MarcaAutoMeli.php"
    "packages/Reda/Integraciones/database/migrations/2026_05_06_085222_create_marcas_autos_melis_table.php"
    "app/Models/User/UserCarModel.php"
    "packages/Reda/Integraciones/database/migrations/2026_05_06_085223_create_modelos_autos_melis_table.php"
    "packages/Reda/Integraciones/src/Models/MercadoLibre/AmenidadMeli.php"
    "packages/Reda/Integraciones/src/Models/MercadoLibre/PaisMeli.php"
    "packages/Reda/Integraciones/src/Models/MercadoLibre/EstadoMeli.php"
    "packages/Reda/Integraciones/src/Models/MercadoLibre/CityMeli.php"
)