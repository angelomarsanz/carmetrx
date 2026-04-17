#!/bin/bash
# Script para compilar selectivamente en el servidor

YELLOW='\033[1;33m'
GREEN='\033[0;32m'
NC='\033[0m'

echo -e "${YELLOW}--- COMPILACIÓN SELECTIVA EN SERVIDOR ---${NC}"

read -p "¿Compilar App Original? (S/N): " RESP_APP
read -p "¿Compilar Integraciones? (S/N): " RESP_INT

# Construir variables de entorno
CMD_VARS=""
[[ "$RESP_APP" =~ ^[Ss]$ ]] && CMD_VARS+="BUILD_APP=true " || CMD_VARS+="BUILD_APP=false "
[[ "$RESP_INT" =~ ^[Ss]$ ]] && CMD_VARS+="BUILD_INTEGRACIONES=true " || CMD_VARS+="BUILD_INTEGRACIONES=false "

echo -e "${YELLOW}Iniciando compilación de producción...${NC}"

# Ejecutar como el usuario del dominio
sudo -u devcarm env $CMD_VARS npm run prod

echo -e "${GREEN}✅ Proceso de compilación terminado.${NC}"