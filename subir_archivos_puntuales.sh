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
    "packages/Reda/Integraciones/src/Models/MercadoLibre/EstadoMeli.php"
    "packages/Reda/Integraciones/database/migrations/2026_05_06_085221_rename_state_id_in_estados_melis_table.php"
)
