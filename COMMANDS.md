# 📋 GUÍA COMPLETA DE COMANDOS

## 🚀 SYMFONY CONSOLE

### Servidor
```bash
# Iniciar servidor PHP
php -S localhost:8000 -t public

# Versión alternativa (Symfony)
php bin/console server:run

# Ver qué está usando el puerto 8000
netstat -ano | findstr :8000
```

### Base de Datos

#### Crear y Eliminar
```bash
# Crear base de datos
php bin/console doctrine:database:create

# Crear si no existe
php bin/console doctrine:database:create --if-not-exists

# Eliminar base de datos
php bin/console doctrine:database:drop

# Eliminar sin confirmación
php bin/console doctrine:database:drop --force
```

#### Migraciones
```bash
# Ejecutar todas las migraciones pendientes
php bin/console doctrine:migrations:migrate

# Sin pedir confirmación
php bin/console doctrine:migrations:migrate --no-interaction

# Ver estado de migraciones
php bin/console doctrine:migrations:status

# Detectar cambios en entidades
php bin/console doctrine:migrations:diff

# Generar nueva migración
php bin/console doctrine:migrations:generate

# Deshacer última migración
php bin/console doctrine:migrations:migrate prev

# Ver migraciones disponibles
php bin/console doctrine:migrations:list
```

#### Consultas Directas
```bash
# Ejecutar SQL directo
php bin/console dbal:run-sql "SELECT * FROM contracts LIMIT 5"

# Obtener información de tablas
php bin/console dbal:run-sql "DESCRIBE contracts"

# Contar registros
php bin/console dbal:run-sql "SELECT COUNT(*) as total FROM contracts"
```

### Caché
```bash
# Limpiar caché de desarrollo
php bin/console cache:clear

# Limpiar caché de producción
php bin/console cache:clear --env=prod

# Limpiar caché específico
php bin/console cache:clear --no-warmup
```

### Rutas y Servicios
```bash
# Ver todas las rutas disponibles
php bin/console debug:router

# Ver ruta específica
php bin/console debug:router nombre_ruta

# Buscar rutas que contengan texto
php bin/console debug:router api

# Ver todos los servicios registrados
php bin/console debug:container

# Ver servicio específico
php bin/console debug:container ContractStorage

# Ver configuración de Doctrine
php bin/console debug:config doctrine

# Ver configuración de validador
php bin/console debug:config validator
```

### Validación y Linting
```bash
# Validar archivos YAML
php bin/console lint:yaml config/

# Validar contenedor de servicios
php bin/console lint:container

# Validar rutas
php bin/console lint:routing
```

---

## 📦 COMPOSER

### Instalar y Actualizar
```bash
# Instalar todas las dependencias
composer install

# Instalar dependencias (sin dev)
composer install --no-dev

# Actualizar todas las dependencias
composer update

# Actualizar paquete específico
composer update symfony/console

# Agregar nuevo paquete
composer require symfony/mailer

# Agregar paquete de desarrollo
composer require --dev phpunit/phpunit

# Remover paquete
composer remove symfony/package
```

### Información
```bash
# Listar todos los paquetes instalados
composer show

# Ver versiones disponibles de un paquete
composer show symfony/console

# Ver paquetes desactualizados
composer outdated

# Ver información detallada de un paquete
composer info symfony/console

# Buscar paquete en Packagist
composer search contract
```

### Mantenimiento
```bash
# Validar composer.json
composer validate

# Regenerar autoloader
composer dump-autoload

# Regenerar con optimización
composer dump-autoload --optimize

# Ver dependencias
composer show --tree

# Ver árbol de dependencias
composer show --tree symfony/console
```

---

## 🧪 PRUEBAS UNITARIAS

### PHPUnit
```bash
# Ejecutar todos los tests
php bin/phpunit

# Ejecutar test específico
php bin/phpunit tests/InstallmentProjectionTest.php

# Ejecutar test específico con método
php bin/phpunit --filter testProjectInstallments tests/InstallmentProjectionTest.php

# Generar reporte de cobertura HTML
php bin/phpunit --coverage-html coverage/

# Generar reporte de cobertura en texto
php bin/phpunit --coverage-text

# Ejecutar con detalle verbose
php bin/phpunit --verbose

# Parar en primer error
php bin/phpunit --stop-on-failure

# Mostrar solo fallos
php bin/phpunit --no-coverage --testdox
```

### Validación PHP
```bash
# Validar sintaxis de archivo PHP
php -l src/Controller/ContractController.php

# Validar sintaxis de carpeta
php -l src/

# Mostrar errores y warnings
php -d error_reporting=E_ALL -l archivo.php
```

---

## 📝 GIT

### Status y Cambios
```bash
# Ver estado del repositorio
git status

# Ver cambios sin stagear
git diff

# Ver cambios ya stageados
git diff --cached

# Ver cambios de archivo específico
git diff src/Controller/ContractController.php

# Ver historial de commits
git log

# Ver historial compacto
git log --oneline

# Ver últimos 10 commits
git log -10

# Ver historial con cambios
git log -p

# Ver cambios de commit específico
git show commit-hash

# Ver quién modificó cada línea
git blame src/Controller/ContractController.php
```

### Agregar y Commitear
```bash
# Agregar todos los cambios
git add .

# Agregar archivo específico
git add src/Controller/ContractController.php

# Agregar cambios interactivos
git add -p

# Hacer commit
git commit -m "Mensaje descriptivo"

# Amend último commit
git commit --amend

# Ver cambios pendientes de commit
git status
```

### Push y Pull
```bash
# Subir cambios a repositorio remoto
git push

# Subir rama específica
git push origin main

# Forzar push (cuidado!)
git push --force

# Bajar cambios del repositorio
git pull

# Bajar de rama específica
git pull origin main

# Traer cambios sin fusionar
git fetch
```

### Ramas
```bash
# Ver todas las ramas
git branch

# Ver ramas incluida la actual
git branch -a

# Ver ramas remotas
git branch -r

# Crear nueva rama
git branch nombre-rama

# Crear y cambiar a nueva rama
git checkout -b nombre-rama

# Cambiar a rama existente
git checkout nombre-rama

# Cambiar a rama anterior
git checkout -

# Fusionar rama
git merge nombre-rama

# Eliminar rama
git branch -d nombre-rama

# Forzar eliminación
git branch -D nombre-rama

# Renombrar rama
git branch -m nombre-antiguo nombre-nuevo

# Ver qué ramas están fusionadas
git branch --merged

# Ver qué ramas NO están fusionadas
git branch --no-merged
```

### Stash (Guardar cambios temporalmente)
```bash
# Guardar cambios sin commitear
git stash

# Guardar con mensaje
git stash save "Descripción"

# Ver lista de stashes
git stash list

# Aplicar último stash
git stash apply

# Aplicar stash específico
git stash apply stash@{0}

# Aplicar y eliminar stash
git stash pop

# Eliminar stash
git stash drop

# Limpiar todos los stashes
git stash clear
```

### Deshacer Cambios
```bash
# Deshacer cambios de archivo
git checkout -- archivo.php

# Deshacer cambios de todos los archivos
git checkout -- .

# Deshacer agregar archivo (unstage)
git reset HEAD archivo.php

# Deshacer último commit (mantener cambios)
git reset --soft HEAD~1

# Deshacer último commit (descartar cambios)
git reset --hard HEAD~1

# Revertir commit (crea nuevo commit)
git revert commit-hash
```

### Configuración
```bash
# Ver configuración actual
git config --list

# Configurar nombre de usuario
git config --global user.name "Tu Nombre"

# Configurar correo
git config --global user.email "tu@email.com"

# Ver configuración de usuario
git config user.name
git config user.email
```

---

## 💾 BASE DE DATOS (MYSQL)

### Conectar y Usar
```bash
# Conectar a MySQL
mysql -u root -p

# Conectar a base de datos específica
mysql -u root -p contratos_db

# Conectar con host específico
mysql -h 127.0.0.1 -u root -p

# Ejecutar archivo SQL
mysql -u root -p < script.sql

# Ejecutar comando y salir
mysql -u root -p -e "SELECT * FROM contracts"
```

### Dentro de MySQL
```sql
-- Ver todas las bases de datos
SHOW DATABASES;

-- Seleccionar base de datos
USE contratos_db;

-- Ver todas las tablas
SHOW TABLES;

-- Ver estructura de tabla
DESCRIBE contracts;
DESC contracts;

-- Ver estructura detallada
SHOW CREATE TABLE contracts;

-- Ver registros
SELECT * FROM contracts;
SELECT * FROM contracts LIMIT 10;

-- Contar registros
SELECT COUNT(*) as total FROM contracts;

-- Ver por método de pago
SELECT * FROM contracts WHERE payment_method = 'PayPal';

-- Ver por ID
SELECT * FROM contracts WHERE id = 1;

-- Ver ordenado
SELECT * FROM contracts ORDER BY contract_date DESC;

-- Actualizar registro
UPDATE contracts SET status = 'ACTIVE' WHERE id = 1;

-- Eliminar registro
DELETE FROM contracts WHERE id = 1;

-- Ver índices
SHOW INDEXES FROM contracts;

-- Obtener última fecha de modificación
SELECT * FROM contracts ORDER BY updated_at DESC LIMIT 1;

-- Salir de MySQL
EXIT;
```

---

## 🔍 ANÁLISIS Y DEBUG

### PHP y Versiones
```bash
# Ver versión de PHP
php -v

# Ver módulos instalados
php -m

# Ver información específica
php -i | grep -i mysql
php -i | grep -i pdo

# Ver configuración de PHP
php -r "phpinfo();"

# Ver extensiones cargadas
php -m
```

### Procesos y Puertos
```bash
# Ver qué usa puerto 8000
netstat -ano | findstr :8000

# Ver qué usa puerto 3306 (MySQL)
netstat -ano | findstr :3306

# Ver todos los procesos PHP
tasklist | findstr php

# Ver procesos específicos
Get-Process php
```

### Logs
```bash
# Ver últimas líneas del log
tail -f var/log/dev.log

# Ver últimas 50 líneas
tail -50 var/log/dev.log

# Filtrar errores
grep ERROR var/log/dev.log

# Filtrar por fecha
grep "2026-01-22" var/log/dev.log

# Contar errores
grep -c ERROR var/log/dev.log

# Ver log completo
cat var/log/dev.log
```

---

## 📂 ARCHIVOS Y CARPETAS

### Windows PowerShell
```bash
# Listar archivos
Get-ChildItem
ls
dir

# Cambiar directorio
cd ruta
cd ..
cd \

# Crear carpeta
mkdir nombre_carpeta
New-Item -ItemType Directory -Name nombre_carpeta

# Crear archivo
New-Item -ItemType File -Name archivo.txt

# Copiar archivo
Copy-Item archivo.txt destino/

# Copiar carpeta
Copy-Item -Recurse carpeta/ destino/

# Mover archivo
Move-Item archivo.txt destino/

# Renombrar archivo
Rename-Item archivo.txt nuevoNombre.txt

# Eliminar archivo
Remove-Item archivo.txt
del archivo.txt

# Eliminar carpeta
Remove-Item -Recurse carpeta/

# Ver contenido de archivo
Get-Content archivo.txt
cat archivo.txt

# Ver ruta actual
Get-Location
pwd
```

### Linux/Mac
```bash
# Listar archivos
ls
ls -la
ls -lh

# Cambiar directorio
cd ruta
cd ..
cd ~
cd /

# Crear carpeta
mkdir nombre_carpeta

# Crear archivo
touch archivo.txt

# Copiar
cp archivo.txt destino/
cp -r carpeta/ destino/

# Mover/Renombrar
mv archivo.txt nuevoNombre.txt
mv archivo.txt destino/

# Eliminar
rm archivo.txt
rm -r carpeta/

# Ver contenido
cat archivo.txt
less archivo.txt

# Ver ruta actual
pwd

# Encontrar archivos
find . -name "*.php"
find . -type f -name "*.sql"
```

---

## 🛠️ COMANDOS DEL PROYECTO

### Setup Inicial
```bash
# Instalar todo desde cero
composer install
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate

# Setup completo con limpieza
php bin/console cache:clear
php bin/console doctrine:database:drop --force
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

### Desarrollo Diario
```bash
# Iniciar servidor
php -S localhost:8000 -t public

# Limpiar caché antes de cambios
php bin/console cache:clear

# Ver errores en los logs
tail -f var/log/dev.log

# Ejecutar tests
php bin/phpunit

# Commit y push
git add .
git commit -m "Descripción"
git push
```

### Debugging
```bash
# Ver todas las rutas
php bin/console debug:router

# Ver servicios
php bin/console debug:container

# Ver configuración
php bin/console debug:config doctrine

# Ver cambios pendientes
git status

# Ver diferencias
git diff
```

### Limpieza
```bash
# Limpiar caché
php bin/console cache:clear

# Limpiar logs
rm var/log/dev.log

# Regenerar autoloader
composer dump-autoload --optimize

# Limpiar archivos temporales
rm -rf var/cache/*
```

---

## 📊 TOP 10 COMANDOS MÁS USADOS

| # | Comando | Uso |
|---|---------|-----|
| 1 | `php -S localhost:8000 -t public` | Iniciar servidor |
| 2 | `git add . && git commit -m "msg"` | Guardar cambios |
| 3 | `git push` | Subir a repositorio |
| 4 | `php bin/console cache:clear` | Limpiar caché |
| 5 | `composer install` | Instalar dependencias |
| 6 | `php bin/console doctrine:migrations:migrate` | Ejecutar migraciones |
| 7 | `git status` | Ver cambios |
| 8 | `mysql -u root -p contratos_db` | Conectar a MySQL |
| 9 | `php bin/phpunit` | Ejecutar tests |
| 10 | `php bin/console debug:router` | Ver rutas |

---

## ⚡ ATAJOS PRÁCTICOS

### Reset Completo
```bash
php bin/console cache:clear && \
php bin/console doctrine:database:drop --force && \
php bin/console doctrine:database:create && \
php bin/console doctrine:migrations:migrate
```

### Validar Todo
```bash
composer validate && \
php -l src/Controller/ContractController.php && \
php bin/console lint:yaml config/ && \
php bin/phpunit
```

### Commit y Push Rápido
```bash
git add . && \
git commit -m "Cambios" && \
git push
```

### Ver Estado Completo
```bash
echo "=== GIT STATUS ===" && \
git status && \
echo -e "\n=== RUTAS ===" && \
php bin/console debug:router && \
echo -e "\n=== LOGS ===" && \
tail -20 var/log/dev.log
```

---

## 🎯 SOLUCIÓN DE PROBLEMAS

### Puerto en uso
```bash
# Ver qué usa el puerto
netstat -ano | findstr :8000

# Buscar por PID
tasklist | findstr PID_AQUI

# Matar proceso
taskkill /PID numero /F
```

### Caché corrupto
```bash
php bin/console cache:clear
php bin/console cache:warmup
```

### Base de datos corrupta
```bash
php bin/console doctrine:database:drop --force
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

### Problemas de permisos
```bash
# Linux/Mac
chmod -R 755 var/
chmod -R 755 public/
chmod -R 755 vendor/
```

---

## 📚 REFERENCIAS

- [Documentación Symfony](https://symfony.com/doc/current/console.html)
- [Documentación Doctrine](https://www.doctrine-project.org/)
- [Documentación Composer](https://getcomposer.org/doc/)
- [Documentación Git](https://git-scm.com/doc)
- [Documentación MySQL](https://dev.mysql.com/doc/)

