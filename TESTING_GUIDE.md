# 🧪 Guía Completa de Pruebas - Aplicación de Contratos

## Formas de Probar la Aplicación

### 1️⃣ DEMOSTRACIÓN RÁPIDA (Sin Base de Datos) ⭐ RECOMENDADO

La forma más rápida y fácil de ver cómo funciona la aplicación.

```bash
cd PruebaTecnicaMakroSoft
php demo.php
```

**Qué muestra:**
- ✓ Contrato de ejemplo: $10,000 en 12 meses
- ✓ Proyección con PayPal (1% interés + 2% comisión)
- ✓ Proyección con PayOnline (2% interés + 1% comisión)
- ✓ Tabla de 12 cuotas mensuales con detalles
- ✓ Comparativa de costos entre servicios

**Resultado esperado:**
```
PayPal:    $10,863 (costo: $863)
PayOnline: $11,413 (costo: $1,413)
Diferencia: $550 a favor de PayPal
```

---

### 2️⃣ PRUEBAS UNITARIAS (PHPUnit)

Ejecuta 5 casos de prueba para validar la lógica.

```bash
php vendor/bin/phpunit
```

**Casos de prueba:**
1. `testProjectInstallmentsWithPayPal()` - Proyecta 12 cuotas con PayPal
2. `testProjectInstallmentsWithPayOnline()` - Proyecta 6 cuotas con PayOnline
3. `testComparePaymentServices()` - Verifica que PayPal es más barato
4. `testInvalidNumberOfMonths()` - Valida que rechaza 0 meses
5. `testInvalidContractValue()` - Valida que rechaza valores negativos

**Ubicación del código:**
- `tests/InstallmentProjectionTest.php`

---

### 3️⃣ ENDPOINTS REST (Con Base de Datos)

Para probar los endpoints de la API REST, necesitas:

#### **Instalación inicial (una sola vez):**

```bash
# 1. Instalar dependencias
composer install

# 2. Configurar .env (editar archivo)
# Cambiar: DATABASE_URL="mysql://root:password@127.0.0.1:3306/makrosoft"

# 3. Crear la base de datos
php bin/console doctrine:database:create

# 4. Ejecutar migraciones
php bin/console doctrine:migrations:migrate
```

#### **Iniciar el servidor:**

```bash
php -S localhost:8000 -t public/
```

Accede a: `http://localhost:8000`

#### **Endpoint 1: Crear Contrato**

```bash
curl -X POST http://localhost:8000/api/contracts \
  -H "Content-Type: application/json" \
  -d '{
    "contractNumber": "CT-2025-001",
    "contractDate": "2025-01-22",
    "contractValue": 10000,
    "paymentMethod": "PayPal",
    "clientName": "Empresa ABC",
    "description": "Contrato de servicios profesionales"
  }'
```

**Respuesta esperada:**
```json
{
  "status": "success",
  "data": {
    "id": 1,
    "contractNumber": "CT-2025-001",
    "contractDate": "2025-01-22",
    "contractValue": 10000,
    "paymentMethod": "PayPal",
    "clientName": "Empresa ABC",
    "status": "PENDING"
  },
  "message": "Contrato creado exitosamente"
}
```

#### **Endpoint 2: Listar Contratos**

```bash
curl http://localhost:8000/api/contracts
```

**Respuesta esperada:**
```json
{
  "status": "success",
  "data": [
    {
      "id": 1,
      "contractNumber": "CT-2025-001",
      "contractValue": 10000,
      "paymentMethod": "PayPal",
      "status": "PENDING"
    }
  ]
}
```

#### **Endpoint 3: Obtener Contrato por ID**

```bash
curl http://localhost:8000/api/contracts/1
```

#### **Endpoint 4: Proyectar Cuotas**

```bash
curl -X POST http://localhost:8000/api/contracts/projection/calculate \
  -H "Content-Type: application/json" \
  -d '{
    "contractId": 1,
    "numberOfMonths": 12,
    "paymentMethod": "PayPal"
  }'
```

**Respuesta esperada:**
```json
{
  "status": "success",
  "data": {
    "contractNumber": "CT-2025-001",
    "contractValue": 10000,
    "numberOfMonths": 12,
    "totalAmount": 10863,
    "totalInterest": 650,
    "totalFee": 213,
    "installments": [
      {
        "month": 1,
        "dueDate": "2025-02-22",
        "baseAmount": 833.33,
        "interest": 100,
        "fee": 18.67,
        "totalValue": 952
      },
      ...
    ]
  }
}
```

---

### 4️⃣ VERIFICAR CÓDIGO FUENTE

#### **Controllers (Endpoints REST)**
- `src/Controller/ContractController.php` - 4 endpoints

#### **Servicios (Lógica de negocio)**
- `src/Service/InstallmentProjectionService.php` - Proyección de cuotas
- `src/Service/PaymentService/PaymentServiceInterface.php` - Interface
- `src/Service/PaymentService/PayPalService.php` - Implementación PayPal
- `src/Service/PaymentService/PayOnlineService.php` - Implementación PayOnline

#### **DTOs (Validación)**
- `src/DTO/CreateContractRequest.php` - Validación de entrada
- `src/DTO/InstallmentProjectionRequest.php` - Validación de proyección
- `src/DTO/InstallmentProjectionResponse.php` - Respuesta estructurada

#### **Entities (Base de Datos)**
- `src/Entity/Contract.php` - Entidad Doctrine

---

## 📊 Comparación de Servicios de Pago

### PayPal (1% interés + 2% comisión)

| Mes | Fecha | Base | Interés | Tarifa | Total |
|-----|-------|------|---------|--------|-------|
| 1 | 2025-02-22 | $833.33 | $100.00 | $18.67 | $952.00 |
| 2 | 2025-03-22 | $833.33 | $91.67 | $18.50 | $943.50 |
| ... | ... | ... | ... | ... | ... |
| 12 | 2026-01-22 | $833.33 | $8.33 | $16.83 | $858.50 |
| **TOTAL** | | **$10,000** | **$650** | **$213** | **$10,863** |

### PayOnline (2% interés + 1% comisión)

| Mes | Fecha | Base | Interés | Tarifa | Total |
|-----|-------|------|---------|--------|-------|
| 1 | 2025-02-22 | $833.33 | $200.00 | $10.33 | $1,043.67 |
| 2 | 2025-03-22 | $833.33 | $183.33 | $10.17 | $1,026.83 |
| ... | ... | ... | ... | ... | ... |
| 12 | 2026-01-22 | $833.33 | $16.67 | $8.50 | $858.50 |
| **TOTAL** | | **$10,000** | **$1,300** | **$113** | **$11,413** |

### Diferencia
- **PayPal es $550 más barato que PayOnline**
- PayPal: 8.63% de costo total
- PayOnline: 14.13% de costo total

---

## 🛠️ Herramientas Recomendadas

### Para probar REST API:

**Opción 1: curl (CLI)**
```bash
curl -X POST http://localhost:8000/api/contracts \
  -H "Content-Type: application/json" \
  -d '{"contractNumber":"CT-2025-001",...}'
```

**Opción 2: Postman (GUI)**
- Descargar: https://www.postman.com/downloads/
- Importar endpoints y crear requests visualmente

**Opción 3: VS Code REST Client**
- Extensión: `REST Client` de Huachao Mao
- Crear archivo `requests.http` con ejemplos

---

## 📋 Checklist de Pruebas

- [ ] Ejecutar `php demo.php` - Demostración
- [ ] Ejecutar `php vendor/bin/phpunit` - Pruebas unitarias
- [ ] Revisar código en `src/` - Arquitectura
- [ ] Revisar documentación - README, ARCHITECTURE, etc.
- [ ] (Opcional) Configurar BD y probar endpoints

---

## 🐛 Solución de Problemas

### Error: "Could not open input file: bin/phpunit"
```bash
# Usar el path completo:
php vendor/bin/phpunit
```

### Error: "database does not exist"
```bash
# Crear la BD primero:
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

### Error: "connection refused" en endpoints
```bash
# Asegúrate que el servidor está corriendo:
php -S localhost:8000 -t public/
```

---

## 📚 Archivos Clave para Entender el Proyecto

1. **README.md** - Descripción general
2. **ARCHITECTURE.md** - Patrones de diseño
3. **API_USAGE.md** - Ejemplos detallados de API
4. **demo.php** - Código de demostración
5. **src/Service/** - Lógica de negocio

---

**¿Necesitas ayuda?** Lee la documentación o revisa el código fuente en `src/`
