# Guía de Instalación y Configuración

## Tabla de Contenidos

1. [Requisitos del Sistema](#requisitos-del-sistema)
2. [Instalación](#instalación)
3. [Configuración de Base de Datos](#configuración-de-base-de-datos)
4. [Ejecutar la Aplicación](#ejecutar-la-aplicación)
5. [Pruebas](#pruebas)
6. [Troubleshooting](#troubleshooting)

## Requisitos del Sistema

### Software Requerido

- **PHP:** 8.2 o superior
- **Composer:** Última versión
- **Git:** Para control de versiones
- **Base de Datos:** MySQL 8.0+ o PostgreSQL 13+

### Verificar Instalaciones

```bash
# Verificar PHP
php -v

# Verificar Composer
composer --version

# Verificar Git
git --version
```

### Extensiones PHP Requeridas

```bash
# Ver extensiones instaladas
php -m

# Requeridas:
# - json
# - pdo
# - pdo_mysql (o pdo_pgsql para PostgreSQL)
```

## Instalación

### 1. Clonar el Repositorio

```bash
git clone <url-repositorio>
cd PruebaTecnicaMakroSoft
```

### 2. Instalar Dependencias

```bash
composer install
```

Esto descargará:
- Symfony 8.0
- Doctrine ORM
- PHPUnit (para pruebas)
- Y otras dependencias

### 3. Generar APP_SECRET

Si el `.env` tiene `APP_SECRET` vacío, generar uno:

```bash
php -r "echo bin2hex(random_bytes(16));"
```

Copiar el resultado en `.env`:

```env
APP_SECRET=<tu-secret-generado>
```

## Configuración de Base de Datos

### Opción A: MySQL (Recomendado para Producción)

1. **Crear la base de datos:**

```bash
mysql -u root -p
```

```sql
CREATE DATABASE contratos_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

2. **Configurar `.env`:**

```env
DATABASE_URL="mysql://root:password@127.0.0.1:3306/contratos_db?serverVersion=8.0.32&charset=utf8mb4"
```

Reemplazar:
- `root` con tu usuario MySQL
- `password` con tu contraseña
- `127.0.0.1` con tu host
- `3306` con tu puerto

3. **Crear tablas:**

```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

### Opción B: PostgreSQL

1. **Crear la base de datos:**

```bash
psql -U postgres
```

```sql
CREATE DATABASE contratos_db;
\q
```

2. **Configurar `.env`:**

```env
DATABASE_URL="postgresql://postgres:password@127.0.0.1:5432/contratos_db?serverVersion=15&charset=utf8"
```

3. **Crear tablas:**

```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

### Opción C: SQLite (Desarrollo Local)

1. **Configurar `.env`:**

```env
DATABASE_URL="sqlite:///%kernel.project_dir%/var/data.db"
```

2. **Crear base de datos:**

```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

El archivo `var/data.db` se creará automáticamente.

## Ejecutar la Aplicación

### Opción A: Symfony Server (Recomendado)

```bash
# Instalar Symfony CLI (si no está instalado)
# https://symfony.com/download

# Ejecutar servidor
symfony server:start
```

La aplicación estará en: `http://localhost:8000`

### Opción B: PHP Built-in Server

```bash
php -S localhost:8000 -t public/
```

### Opción C: Docker Compose (si está configurado)

```bash
docker-compose up -d
```

## Pruebas

### Ejecutar Pruebas Unitarias

```bash
php bin/phpunit
```

Esto ejecutará todas las pruebas en `tests/`

### Ejecutar Script de Demostración

```bash
php demo.php
```

Muestra la proyección de cuotas sin necesidad de base de datos

### Verificar Endpoints

Crear un contrato:

```bash
curl -X POST http://localhost:8000/api/contracts \
  -H "Content-Type: application/json" \
  -d '{
    "contractNumber": "TEST-001",
    "contractDate": "2025-01-22",
    "contractValue": 5000,
    "paymentMethod": "PayPal",
    "clientName": "Test Client"
  }'
```

Obtener todos los contratos:

```bash
curl http://localhost:8000/api/contracts
```

Proyectar cuotas:

```bash
curl -X POST http://localhost:8000/api/contracts/projection/calculate \
  -H "Content-Type: application/json" \
  -d '{
    "contractId": 1,
    "numberOfMonths": 12,
    "paymentMethod": "PayPal"
  }'
```

## Estructura de Directorios

```
.
├── bin/                          # Scripts ejecutables
│   └── console                   # Consola Symfony
├── config/                       # Configuración
│   ├── packages/                 # Paquetes configurables
│   ├── routes.yaml              # Rutas de la aplicación
│   └── services.yaml            # Servicios inyectables
├── migrations/                   # Migraciones de BD
│   └── Version*.php             # Scripts de migración
├── public/                       # Documentos raíz web
│   ├── index.php                # Punto de entrada
│   └── .htaccess                # Configuración Apache
├── src/                          # Código fuente
│   ├── Controller/              # Controladores
│   ├── Entity/                  # Entidades Doctrine
│   ├── Service/                 # Servicios de negocio
│   ├── DTO/                     # Data Transfer Objects
│   └── Resolver/                # Resolvers
├── tests/                        # Pruebas unitarias
├── var/                          # Archivos generados
│   ├── cache/                   # Caché de aplicación
│   ├── log/                     # Archivos de registro
│   └── data.db                  # BD SQLite (si aplica)
├── vendor/                       # Dependencias Composer
├── .env                          # Variables de entorno
├── .env.dev                      # Variables de desarrollo
├── .gitignore                    # Archivos ignorados por Git
├── composer.json                 # Dependencias del proyecto
├── composer.lock                 # Versiones exactas
├── README.md                     # Documentación principal
├── ARCHITECTURE.md               # Documentación de arquitectura
├── API_USAGE.md                  # Guía de uso de API
├── INSTALLATION.md               # Esta guía
├── demo.php                      # Script de demostración
└── symfony.lock                  # Lock Symfony flex

```

## Acceso a la Base de Datos

### MySQL

```bash
# Conectar
mysql -u root -p contratos_db

# Ver tablas
SHOW TABLES;

# Ver estructura de contratos
DESCRIBE contracts;

# Ver contratos
SELECT * FROM contracts;
```

### PostgreSQL

```bash
# Conectar
psql -U postgres contratos_db

# Ver tablas
\dt

# Ver estructura
\d contracts

# Ver contratos
SELECT * FROM contracts;
```

### SQLite

```bash
# Conectar
sqlite3 var/data.db

# Ver tablas
.tables

# Ver estructura
.schema contracts

# Ver contratos
SELECT * FROM contracts;

# Salir
.quit
```

## Variables de Entorno Importantes

```env
# Entorno (dev, test, prod)
APP_ENV=dev

# Secret para seguridad
APP_SECRET=tu_secret_aqui

# URL Base
DEFAULT_URI=http://localhost

# Base de Datos
DATABASE_URL="mysql://user:pass@host:port/db?charset=utf8mb4"

# Debug (solo desarrollo)
APP_DEBUG=true
```

## Comandos Útiles de Symfony

```bash
# Ver todas las rutas
php bin/console debug:routes

# Ver todos los servicios
php bin/console debug:container

# Limpiar caché
php bin/console cache:clear

# Ver logs
tail -f var/log/dev.log

# Validar configuración
php bin/console config:dump

# Ejecutar migraciones pendientes
php bin/console doctrine:migrations:migrate

# Revertir última migración
php bin/console doctrine:migrations:migrate prev

# Crear nueva migración
php bin/console make:migration

# Generar código
php bin/console make:controller
php bin/console make:entity
```

## Troubleshooting

### Error: "Could not find driver"

**Problema:** PDO Driver para MySQL/PostgreSQL no está habilitado

**Solución:**

```bash
# Ver extensiones
php -m | grep pdo

# Habilitar en php.ini (Windows):
# extension=pdo_mysql
# o
# extension=pdo_pgsql

# Reiniciar el servidor
```

### Error: "Access denied for user"

**Problema:** Credenciales incorrectas en DATABASE_URL

**Solución:**

```bash
# Verificar credenciales
mysql -u root -p  # Probar conexión

# Actualizar .env con credenciales correctas
DATABASE_URL="mysql://root:tunombre@127.0.0.1:3306/contratos_db?serverVersion=8.0.32&charset=utf8mb4"
```

### Error: "Database does not exist"

**Problema:** Base de datos no fue creada

**Solución:**

```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

### Error: "No connection could be made because the target machine actively refused"

**Problema:** Servidor MySQL/PostgreSQL no está corriendo

**Solución:**

```bash
# Windows - Iniciar MySQL
net start MySQL80

# Linux - Iniciar MySQL
sudo systemctl start mysql

# macOS
brew services start mysql
```

### Error 404 en los endpoints

**Problema:** Las rutas no están registradas

**Solución:**

```bash
# Limpiar caché
php bin/console cache:clear

# Verificar rutas
php bin/console debug:routes
```

### Error: "Serialization is not enabled"

**Problema:** Falta configuración de Serializer

**Solución:**

```bash
composer require symfony/serializer
```

## Configuración para Producción

### 1. Variables de Entorno

```env
APP_ENV=prod
APP_DEBUG=false
APP_SECRET=<un-secret-seguro-generado>
```

### 2. Compilar Caché

```bash
php bin/console cache:warmup
```

### 3. Instalar sin dependencias de desarrollo

```bash
composer install --no-dev --optimize-autoloader
```

### 4. Actualizar permisos

```bash
chmod -R 755 public/
chmod -R 777 var/
```

### 5. Configurar servidor web

**Apache:**

```apache
<Directory /path/to/app/public>
    AllowOverride All
    Order Allow,Deny
    Allow from All
</Directory>
```

**Nginx:**

```nginx
location / {
    try_files $uri @rewrite;
}

location @rewrite {
    rewrite ^(.*)$ /index.php/$1 last;
}
```

## Siguientes Pasos

1. Leer [README.md](README.md) para una visión general
2. Revisar [ARCHITECTURE.md](ARCHITECTURE.md) para entender el diseño
3. Consultar [API_USAGE.md](API_USAGE.md) para ejemplos de API
4. Explorar el código en `src/`

¡Listo! La aplicación está lista para usar.
