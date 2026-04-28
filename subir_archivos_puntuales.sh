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
    "packages/Reda/Integraciones/resources/js/mercado_libre/configuraciones/indexConfiguracionesMercadoLibre.js"
    "packages/Reda/Integraciones/resources/js/mercado_libre/generales/verificarTokenMeli.js"
    "packages/Reda/Integraciones/src/Http/Controllers/MercadoLibre/ConfiguracionController.php"
)
