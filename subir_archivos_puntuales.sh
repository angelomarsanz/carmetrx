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
    #"packages/Reda/Integraciones/resources/views/mercado_libre/configuraciones/index.blade.php"
    #"packages/Reda/Integraciones/resources/js/mercado_libre/configuraciones/indexConfiguracionesMercadoLibre.js"
    #"packages/Reda/Integraciones/resources/js/mercado_libre/generales/index.js"
    #"packages/Reda/Integraciones/resources/js/mercado_libre/generales/obtenerOrigenPrefijoBase.js"
    #"packages/Reda/Integraciones/resources/js/mercado_libre/generales/verificarUsuarioConectado.js"
    #"packages/Reda/Integraciones/resources/js/mercado_libre/generales/verificarTokenMeli.js"
    #"packages/Reda/Integraciones/src/Http/Controllers/General/UsuarioController.php"
    #"packages/Reda/Integraciones/src/Http/Controllers/MercadoLibre/ConfiguracionController.php"
    #"packages/Reda/Integraciones/src/Models/MercadoLibre/CategoriaMeli.php"
    #"packages/Reda/Integraciones/database/migrations/2026_04_25_210500_create_categorias_melis_table.php"
    #"packages/Reda/Integraciones/src/Models/MercadoLibre/AdminMeli.php"
    #"packages/Reda/Integraciones/database/migrations/2026_04_26_030133_create_admins_melis_table.php"
    #"packages/Reda/Integraciones/src/Models/MercadoLibre/UserMeli.php"
    #"packages/Reda/Integraciones/database/migrations/2026_04_26_033505_create_users_melis_table.php"
    #"packages/Reda/Integraciones/src/Models/MercadoLibre/AgentMeli.php"
    #"packages/Reda/Integraciones/database/migrations/2026_04_26_041021_create_agents_melis_table.php"
    #"packages/Reda/Integraciones/src/Models/MercadoLibre/PropiedadMeli.php"
    #"packages/Reda/Integraciones/database/migrations/2026_04_26_221833_create_propiedades_melis_table.php"
)
