#!/bin/bash

YELLOW='\033[1;33m'
GREEN='\033[0;32m'
NC='\033[0m'

# Carga variables .env (FTP_USER, etc)
set -o allexport
source .env
set +o allexport

echo -e "${YELLOW}--- SUBIDA DE ARCHIVOS FUENTE (PHP/JS/JSX) ---${NC}"

if [ -f "./subir_archivos_puntuales.sh" ]; then
    source ./subir_archivos_puntuales.sh
else
    echo "Error: No se encontró ./subir_archivos_puntuales.sh"
    exit 1
fi

if [ ${#ARCHIVOS_PHP_PUNTUALES[@]} -gt 0 ] && [ "${ARCHIVOS_PHP_PUNTUALES[0]}" != "Ninguno" ]; then
    lftp_commands="set ftp:ssl-allow no; open -u $FTP_USER,$FTP_PASSWORD $FTP_HOST;"
    
    for archivo in "${ARCHIVOS_PHP_PUNTUALES[@]}"; do
        if [ -f "$archivo" ]; then
            NOMBRE_ARCHIVO=$(basename "$archivo")
            CARPETA_PADRE=$(basename "$(dirname "$archivo")")
            NOMBRE_RESPALDO="${CARPETA_PADRE}_${NOMBRE_ARCHIVO}"
            URL_ORIGEN_FTP="/$archivo"
            URL_RESPALDO_FTP="$FTP_REMOTE_PATH_OLD/$NOMBRE_RESPALDO"
            DIRECTORIO_REMOTO=$(dirname "$URL_ORIGEN_FTP")

            echo "  -> Subiendo: $archivo"
            # Respalda el archivo anterior y sube el nuevo
            lftp_commands+="mv ${URL_ORIGEN_FTP} ${URL_RESPALDO_FTP}; mkdir -p ${DIRECTORIO_REMOTO}; put -O ${DIRECTORIO_REMOTO}/ ${archivo};"
        fi
    done

    lftp -c "$lftp_commands"
    echo -e "${GREEN}✅ Archivos fuentes actualizados en el servidor.${NC}"
else
    echo "Nada que subir."
fi