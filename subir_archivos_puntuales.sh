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
        "packages/Reda/Integraciones/config/integraciones.php"
        "packages/Reda/Integraciones/resources/lang/es.json"
        "packages/Reda/Integraciones/src/IntegracionesServiceProvider.php"
        "packages/Reda/Integraciones/resources/views/mercado_libre/importadores/index.blade.php"
        "packages/Reda/Integraciones/src/Http/Controllers/MercadoLibre/ImportadorController.php"
)
