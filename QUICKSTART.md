## 🚀 Inicio Rápido

### 1. Verificar Requisitos
```bash
php -v                    # PHP 8.2+
composer --version        # Composer
git --version            # Git
```

### 2. Descargar Dependencias
```bash
composer install
```

### 3. Ejecutar Demostración (Sin BD)
```bash
php demo.php
```

**Salida esperada:**
```
Proyección de 12 cuotas con PayPal: $10,863.00
Proyección de 12 cuotas con PayOnline: $11,413.00
Diferencia: $550.00 a favor de PayPal
```

### 4. Ejecutar Pruebas Unitarias
```bash
php bin/phpunit
```

### 5. Ejecutar la API (Requiere BD)

#### 5a. Configurar Base de Datos

Editar `.env` y descomentar una opción:

```env
# MySQL
DATABASE_URL="mysql://root:@127.0.0.1:3306/contratos_db?serverVersion=8.0.32&charset=utf8mb4"

# O SQLite
DATABASE_URL="sqlite:///%kernel.project_dir%/var/data.db"
```

#### 5b. Crear BD y Tablas
```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

#### 5c. Iniciar Servidor
```bash
php -S localhost:8000 -t public/
```

#### 5d. Probar API
```bash
# Crear contrato
curl -X POST http://localhost:8000/api/contracts \
  -H "Content-Type: application/json" \
  -d '{
    "contractNumber":"TEST-001",
    "contractDate":"2025-01-22",
    "contractValue":10000,
    "paymentMethod":"PayPal",
    "clientName":"Test"
  }'

# Proyectar cuotas
curl -X POST http://localhost:8000/api/contracts/projection/calculate \
  -H "Content-Type: application/json" \
  -d '{"contractId":1,"numberOfMonths":12,"paymentMethod":"PayPal"}'
```

## 📖 Documentación

- **[README.md](README.md)** - Visión general
- **[ARCHITECTURE.md](ARCHITECTURE.md)** - Diseño técnico
- **[API_USAGE.md](API_USAGE.md)** - Ejemplos de API
- **[INSTALLATION.md](INSTALLATION.md)** - Instalación completa
- **[PROJECT_SUMMARY.md](PROJECT_SUMMARY.md)** - Resumen ejecutivo

## 🎯 Características Principales

✅ API REST para gestión de contratos  
✅ Proyección automática de cuotas  
✅ Múltiples servicios de pago (PayPal, PayOnline)  
✅ Patrones SOLID y Clean Code  
✅ Pruebas unitarias  
✅ Documentación completa  

## 🏗️ Stack Tecnológico

- Symfony 8.0
- Doctrine ORM
- PHP 8.4
- MySQL/SQLite
- PHPUnit

---

¿Preguntas? Consulta la [documentación completa](README.md)
