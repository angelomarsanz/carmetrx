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
    #"packages/Reda/Integraciones/src/Http/Controllers/General/UsuarioController.php"
    #"packages/Reda/Integraciones/src/Http/Controllers/General/AgenteController.php"
    #"packages/Reda/Integraciones/resources/js/mercado_libre/menus/index.js"
    #"packages/Reda/Integraciones/resources/js/mercado_libre/menus/indexMenuLateral.js"

    "packages/Reda/Integraciones/resources/js/main.js"
    "packages/Reda/Integraciones/resources/js/mercado_libre/configuraciones/index.js"
    "packages/Reda/Integraciones/resources/js/mercado_libre/configuraciones/indexConfiguracionesMercadoLibre.js"
    "packages/Reda/Integraciones/resources/js/mercado_libre/importadores/index.js"
    "packages/Reda/Integraciones/resources/js/mercado_libre/importadores/indexImportadores.js"
)
