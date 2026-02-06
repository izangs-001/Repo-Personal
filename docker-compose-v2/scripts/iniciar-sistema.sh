#!/bin/bash

# Colores para mensajes
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}============================================${NC}"
echo -e "${BLUE}   INICIANDO SISTEMA DOCKER COMPOSE v2${NC}"
echo -e "${BLUE}============================================${NC}"
echo ""

# Verificar Docker
if ! command -v docker &> /dev/null; then
    echo -e "${RED}❌ Docker no está instalado${NC}"
    exit 1
fi

# Verificar Docker Compose
if ! docker compose version &> /dev/null; then
    echo -e "${YELLOW}⚠️  Docker Compose no encontrado, intentando con docker-compose...${NC}"
    if ! command -v docker-compose &> /dev/null; then
        echo -e "${RED}❌ Docker Compose no está instalado${NC}"
        exit 1
    else
        COMPOSE_CMD="docker-compose"
    fi
else
    COMPOSE_CMD="docker compose"
fi

echo -e "${GREEN}✅ Docker encontrado: $(docker --version)${NC}"
echo -e "${GREEN}✅ Docker Compose encontrado${NC}"
echo ""

# Verificar archivo docker-compose.yml
if [ ! -f "docker-compose.yml" ]; then
    echo -e "${RED}❌ No se encuentra docker-compose.yml${NC}"
    echo "Ejecuta desde el directorio correcto"
    exit 1
fi

# Detener servicios previos si existen
echo -e "${YELLOW}🔄 Deteniendo servicios previos...${NC}"
$COMPOSE_CMD down 2>/dev/null

# Iniciar servicios
echo -e "${YELLOW}🚀 Iniciando servicios...${NC}"
$COMPOSE_CMD up -d --build

# Esperar a que los servicios estén saludables
echo -e "${YELLOW}⏳ Esperando que los servicios inicien...${NC}"
sleep 15

# Verificar estado
echo ""
echo -e "${BLUE}📊 ESTADO DE LOS SERVICIOS:${NC}"
echo "--------------------------------"
$COMPOSE_CMD ps

# Verificar conexiones
echo ""
echo -e "${BLUE}🔍 VERIFICANDO CONEXIONES:${NC}"
echo "--------------------------------"

# Verificar Apache
if curl -s -o /dev/null -w "%{http_code}" http://localhost | grep -q "200"; then
    echo -e "${GREEN}✅ Apache funcionando en http://localhost${NC}"
else
    echo -e "${RED}❌ Apache no responde${NC}"
fi

# Verificar phpMyAdmin
if curl -s -o /dev/null -w "%{http_code}" http://localhost:8080 | grep -q "200\|302"; then
    echo -e "${GREEN}✅ phpMyAdmin funcionando en http://localhost:8080${NC}"
else
    echo -e "${YELLOW}⚠️  phpMyAdmin puede tardar más en iniciar${NC}"
fi

# Verificar MariaDB
if docker exec mariadb-v2 mysqladmin ping -h localhost -u root -pAdmin123! &>/dev/null; then
    echo -e "${GREEN}✅ MariaDB funcionando en localhost:3306${NC}"
else
    echo -e "${RED}❌ MariaDB no responde${NC}"
fi

echo ""
echo -e "${BLUE}📋 ACCESOS DEL SISTEMA:${NC}"
echo "--------------------------------"
echo -e "🌐 ${GREEN}Apache (PHP):${NC}    http://localhost"
echo -e "📊 ${GREEN}phpMyAdmin:${NC}      http://localhost:8080"
echo -e "🗄️  ${GREEN}MariaDB:${NC}        localhost:3306"
echo -e "👤 ${GREEN}Usuario BD:${NC}      usuario_web"
echo -e "🔑 ${GREEN}Contraseña BD:${NC}   ClaveSegura456"
echo -e "💾 ${GREEN}Base de datos:${NC}   mi_empresa"
echo ""

echo -e "${BLUE}👤 TU INFORMACIÓN EN EL SISTEMA:${NC}"
echo "--------------------------------"
echo -e "Nombre: ${GREEN}Izan Gómez${NC}"
echo -e "Carnet: ${GREEN}SG001${NC}"
echo -e "Carrera: ${GREEN}Ingeniería en Sistemas${NC}"
echo ""

echo -e "${BLUE}🛠️  COMANDOS ÚTILES:${NC}"
echo "--------------------------------"
echo -e "${YELLOW}Ver logs:${NC}          $COMPOSE_CMD logs -f"
echo -e "${YELLOW}Detener:${NC}           $COMPOSE_CMD down"
echo -e "${YELLOW}Reiniciar:${NC}         $COMPOSE_CMD restart"
echo -e "${YELLOW}Ver estado:${NC}        $COMPOSE_CMD ps"
echo ""

echo -e "${GREEN}============================================${NC}"
echo -e "${GREEN}   ✅ SISTEMA INICIADO CORRECTAMENTE${NC}"
echo -e "${GREEN}============================================${NC}"
