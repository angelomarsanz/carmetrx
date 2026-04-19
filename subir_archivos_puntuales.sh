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
        "resources/views/admin/layout.blade.php"
        "resources/views/user/layout.blade.php"
        "resources/views/agent/layout.blade.php"
        "packages/Reda/Integraciones/resources/views/layout_bridge.blade.php"
        "packages/Reda/Integraciones/resources/views/mercado_libre/importadores/index.blade.php"
)