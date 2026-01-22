# 🐳 GUÍA DE DOCKER

## 📋 Configuración Actual

Tu proyecto está **100% dockerizado** con:
- ✅ **Symfony PHP 8.2** en contenedor
- ✅ **MySQL 8.0** en contenedor
- ✅ **Docker Compose** para orquestar todo

---

## 🚀 INICIO RÁPIDO

### **1. Levantar todo con Docker**

```bash
# Primera vez (construye la imagen y levanta servicios)
docker-compose up --build

# Siguientes veces (solo levanta)
docker-compose up

# En background (sin ver logs)
docker-compose up -d
```

### **2. Acceder a la aplicación**

```
http://localhost:8000
```

### **3. Detener todo**

```bash
docker-compose down
```

---

## 📦 SERVICIOS DOCKERIZADOS

### **Servicio: app** (Aplicación Symfony)

```yaml
app:
  build: ./Dockerfile
  container_name: contratos_app
  ports: 8000:8000
  depends_on: database (espera a que esté listo)
  environment:
    - DATABASE_URL: mysql://contratos_user:contratos_pass@database:3306/contratos_db
    - APP_ENV: dev
  volumes:
    - .:/app (código sincronizado en tiempo real)
```

**¿Qué es?**
- Contenedor con PHP 8.2
- Tiene instalado Composer y todas las dependencias
- Corre el servidor Symfony en puerto 8000
- Valida que MySQL esté sano antes de iniciar

### **Servicio: database** (MySQL)

```yaml
database:
  image: mysql:8.0
  container_name: contratos_db
  ports: 3306:3306
  environment:
    - MYSQL_ROOT_PASSWORD: contratos_pass
    - MYSQL_DATABASE: contratos_db
    - MYSQL_USER: contratos_user
    - MYSQL_PASSWORD: contratos_pass
  volumes:
    - database_data:/var/lib/mysql (datos persistentes)
  healthcheck: (verifica que esté listo)
```

---

## 🎮 COMANDOS DOCKER COMPOSE

### **Levantar servicios**

```bash
# Levantar todos los servicios
docker-compose up

# Levantar sin ver logs
docker-compose up -d

# Levantar servicio específico
docker-compose up app
docker-compose up database

# Reconstruir imagen
docker-compose up --build

# Reconstruir sin caché
docker-compose up --build --no-cache
```

### **Ver estado**

```bash
# Ver contenedores corriendo
docker-compose ps

# Ver logs de todos los servicios
docker-compose logs

# Ver logs de servicio específico
docker-compose logs app
docker-compose logs database

# Ver logs en vivo (últimas 20 líneas)
docker-compose logs -f --tail=20

# Ver logs de servicio en vivo
docker-compose logs -f app
```

### **Ejecutar comandos en contenedor**

```bash
# Ejecutar comando en app
docker-compose exec app php bin/console doctrine:migrations:migrate

# Ejecutar comando en database
docker-compose exec database mysql -u root -p contratos_db

# Ejecutar bash interactivo en app
docker-compose exec app bash

# Ejecutar comandos de Composer
docker-compose exec app composer install
docker-compose exec app composer update
```

### **Detener y limpiar**

```bash
# Detener todos los servicios (mantiene datos)
docker-compose down

# Detener servicios específicos
docker-compose stop app
docker-compose stop database

# Reiniciar servicios
docker-compose restart

# Parar y eliminar volúmenes (BORRA BD)
docker-compose down -v

# Eliminar contenedores, redes, volúmenes y imágenes
docker-compose down -v --rmi all
```

---

## 🔧 ACCESO A LA BASE DE DATOS

### **Desde dentro del contenedor**

```bash
# Abrir MySQL
docker-compose exec database mysql -u root -p contratos_db
# Contraseña: contratos_pass

# Desde el contenedor de app
docker-compose exec app php bin/console dbal:run-sql "SELECT * FROM contracts"
```

### **Desde tu máquina local**

```bash
# Si tienes MySQL instalado localmente
mysql -h 127.0.0.1 -P 3306 -u contratos_user -p contratos_db
# Usuario: contratos_user
# Contraseña: contratos_pass

# Desde DBeaver, MySQL Workbench, etc:
- Host: 127.0.0.1
- Puerto: 3306
- Usuario: contratos_user
- Contraseña: contratos_pass
- Base de datos: contratos_db
```

---

## 📊 ARQUITECTURA CON DOCKER

```
┌─────────────────────────────────────────────────────┐
│             Docker Compose Network                   │
├─────────────────────────────────────────────────────┤
│                                                     │
│  ┌──────────────────────┐   ┌──────────────────┐  │
│  │  Contenedor: app     │   │ Contenedor: db   │  │
│  │  (contratos_app)     │──→│(contratos_db)    │  │
│  │                      │   │                  │  │
│  │ PHP 8.2 + Symfony    │   │ MySQL 8.0        │  │
│  │ Puerto: 8000         │   │ Puerto: 3306     │  │
│  │                      │   │                  │  │
│  │ /app (código)        │   │ database_data    │  │
│  │ var/log              │   │ (persistente)    │  │
│  │ vendor/              │   │                  │  │
│  └──────────────────────┘   └──────────────────┘  │
│         localhost:8000         localhost:3306     │
│                                                     │
└─────────────────────────────────────────────────────┘

Tu máquina (Windows)
        ↓
    Docker Desktop
        ↓
    Docker Compose
```

---

## 📝 WORKFLOW TÍPICO

### **Desarrollo diario**

```bash
# 1. Levantar todo
docker-compose up -d

# 2. Ver logs (para verificar que todo está bien)
docker-compose logs -f

# 3. Hacer cambios en el código (se sincronizan automáticamente)
# (editar archivos en tu IDE)

# 4. Si necesitas ejecutar comandos
docker-compose exec app php bin/console cache:clear
docker-compose exec app php bin/phpunit

# 5. Ver base de datos (opcional)
docker-compose exec database mysql -u root -p contratos_db

# 6. Cuando termines
docker-compose down
```

### **Cuando cambias dependencias (composer.json)**

```bash
# Reconstruir y levantar
docker-compose up --build

# O manualmente
docker-compose exec app composer install
docker-compose restart
```

### **Cuando cambias configuración (Dockerfile)**

```bash
# Reconstruir
docker-compose up --build --no-cache

# O desde cero
docker-compose down -v
docker-compose up --build
```

---

## 🐛 TROUBLESHOOTING

### **Puerto ya está en uso**

```bash
# Ver qué usa el puerto
netstat -ano | findstr :8000

# Cambiar puerto en compose.yaml
# Cambiar "8000:8000" a "8001:8000"
```

### **Contenedor no inicia**

```bash
# Ver logs detallados
docker-compose logs app

# Reconstruir sin caché
docker-compose up --build --no-cache

# Reset completo
docker-compose down -v
docker-compose up --build
```

### **Base de datos corrupta**

```bash
# Eliminar volumen y recrear
docker-compose down -v
docker-compose up

# Se ejecutarán migraciones automáticamente
```

### **Permisos en carpeta var/**

```bash
# Si hay problemas con var/log o var/cache
docker-compose exec app chmod -R 777 var/
```

### **Conexión a MySQL desde app fallando**

```bash
# Verificar que database esté healthy
docker-compose ps

# Ver logs de database
docker-compose logs database

# Esperar a que esté completamente listo
docker-compose down
docker-compose up
```

---

## 📁 ARCHIVOS DOCKER

```
Dockerfile              → Imagen de la aplicación
.dockerignore          → Archivos a ignorar al construir
compose.yaml           → Configuración de servicios
compose.override.yaml  → Overrides locales (desarrollo)
```

---

## 🔐 CREDENCIALES

| Componente | Usuario | Contraseña | BD |
|-----------|---------|-----------|-------|
| MySQL Root | root | contratos_pass | - |
| MySQL Usuario | contratos_user | contratos_pass | contratos_db |
| App | - | - | localhost:8000 |

---

## ✨ VENTAJAS DE ESTA SETUP

✅ **Todo containerizado** - Misma config en todos lados  
✅ **MySQL aislado** - No interfiere con tu sistema  
✅ **Código sincronizado** - Cambios en tiempo real  
✅ **Fácil reset** - `docker-compose down -v`  
✅ **Escalable** - Agregar más servicios es fácil  
✅ **CI/CD ready** - Funciona en pipelines  

---

## 🚀 SIGUIENTES PASOS

Para mejorar aún más:

```bash
# 1. Agregar Nginx como reverse proxy
# 2. Agregar Redis para caché
# 3. Agregar contenedor de tests
# 4. Agregar Adminer para acceso a BD por web
# 5. Configurar logging centralizado
```

---

## 📚 REFERENCIAS

- [Docker Documentation](https://docs.docker.com/)
- [Docker Compose Documentation](https://docs.docker.com/compose/)
- [Symfony Docker Guide](https://symfony.com/doc/current/setup/docker.html)

