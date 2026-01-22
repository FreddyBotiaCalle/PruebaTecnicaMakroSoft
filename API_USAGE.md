# Guía de Uso de la API REST

## Requisitos Previos

1. La aplicación debe estar ejecutándose en `http://localhost:8000`
2. La base de datos debe estar configurada
3. Tener instalado `curl` en la terminal

## Ejecutar la Aplicación

```bash
php bin/console server:run
```

O con PHP built-in server:

```bash
php -S localhost:8000 -t public/
```

## Ejemplos de Uso

### 1. Crear un Contrato

**Endpoint:** `POST /api/contracts`

**cURL:**
```bash
curl -X POST http://localhost:8000/api/contracts \
  -H "Content-Type: application/json" \
  -d '{
    "contractNumber": "CNT-2025-001",
    "contractDate": "2025-01-22",
    "contractValue": 10000,
    "paymentMethod": "PayPal",
    "clientName": "Empresa ABC SAS",
    "description": "Contrato de servicios de consultoría"
  }'
```

**Respuesta Exitosa (201 Created):**
```json
{
  "status": "success",
  "message": "Contrato creado exitosamente",
  "data": {
    "id": 1,
    "contractNumber": "CNT-2025-001",
    "contractDate": "2025-01-22",
    "contractValue": "10000.00",
    "paymentMethod": "PayPal",
    "clientName": "Empresa ABC SAS"
  }
}
```

**Respuesta con Error de Validación (400 Bad Request):**
```json
{
  "status": "error",
  "message": "Datos inválidos",
  "errors": [
    {
      "field": "contractValue",
      "message": "El valor debe ser mayor a 0"
    }
  ]
}
```

**Parámetros:**

| Campo | Tipo | Requerido | Validación | Ejemplo |
|-------|------|-----------|-----------|---------|
| contractNumber | string | ✓ | 3-50 caracteres, único | CNT-2025-001 |
| contractDate | string | ✓ | Formato YYYY-MM-DD | 2025-01-22 |
| contractValue | number | ✓ | Mayor a 0 | 10000 |
| paymentMethod | string | ✓ | PayPal \| PayOnline | PayPal |
| clientName | string | ✗ | 3-100 caracteres | Empresa ABC |
| description | string | ✗ | Máx 500 caracteres | Descripción... |

---

### 2. Obtener Todos los Contratos

**Endpoint:** `GET /api/contracts`

**cURL:**
```bash
curl -X GET http://localhost:8000/api/contracts
```

**Respuesta (200 OK):**
```json
{
  "status": "success",
  "data": [
    {
      "id": 1,
      "contractNumber": "CNT-2025-001",
      "contractDate": "2025-01-22",
      "contractValue": "10000.00",
      "paymentMethod": "PayPal",
      "clientName": "Empresa ABC SAS",
      "status": "PENDING",
      "createdAt": "2025-01-22 14:30:00"
    },
    {
      "id": 2,
      "contractNumber": "CNT-2025-002",
      "contractDate": "2025-01-20",
      "contractValue": "5000.00",
      "paymentMethod": "PayOnline",
      "clientName": "Empresa XYZ",
      "status": "ACTIVE",
      "createdAt": "2025-01-20 10:15:00"
    }
  ],
  "total": 2
}
```

---

### 3. Obtener un Contrato por ID

**Endpoint:** `GET /api/contracts/{id}`

**cURL:**
```bash
curl -X GET http://localhost:8000/api/contracts/1
```

**Respuesta (200 OK):**
```json
{
  "status": "success",
  "data": {
    "id": 1,
    "contractNumber": "CNT-2025-001",
    "contractDate": "2025-01-22",
    "contractValue": "10000.00",
    "paymentMethod": "PayPal",
    "clientName": "Empresa ABC SAS",
    "description": "Contrato de servicios de consultoría",
    "status": "PENDING",
    "createdAt": "2025-01-22 14:30:00"
  }
}
```

**Respuesta - Contrato No Encontrado (404 Not Found):**
```json
{
  "status": "error",
  "message": "Contrato no encontrado"
}
```

**Parámetros:**

| Parámetro | Tipo | Requerido | Descripción |
|-----------|------|-----------|------------|
| id | integer | ✓ | ID del contrato |

---

### 4. Proyectar Cuotas de un Contrato

**Endpoint:** `POST /api/contracts/projection/calculate`

**cURL - Ejemplo 1: PayPal 12 meses**
```bash
curl -X POST http://localhost:8000/api/contracts/projection/calculate \
  -H "Content-Type: application/json" \
  -d '{
    "contractId": 1,
    "numberOfMonths": 12,
    "paymentMethod": "PayPal"
  }'
```

**cURL - Ejemplo 2: PayOnline 6 meses**
```bash
curl -X POST http://localhost:8000/api/contracts/projection/calculate \
  -H "Content-Type: application/json" \
  -d '{
    "contractId": 1,
    "numberOfMonths": 6,
    "paymentMethod": "PayOnline"
  }'
```

**Respuesta Exitosa (200 OK):**
```json
{
  "status": "success",
  "data": {
    "contractId": 1,
    "contractNumber": "CNT-2025-001",
    "contractDate": "2025-01-22",
    "contractValue": 10000,
    "paymentMethod": "PayPal",
    "clientName": "Empresa ABC SAS",
    "numberOfMonths": 12,
    "installments": [
      {
        "number": 1,
        "dueDate": "2025-02-22",
        "baseValue": 833.33,
        "interest": 100,
        "fee": 18.67,
        "totalValue": 952
      },
      {
        "number": 2,
        "dueDate": "2025-03-22",
        "baseValue": 833.33,
        "interest": 91.67,
        "fee": 18.5,
        "totalValue": 943.5
      },
      {
        "number": 3,
        "dueDate": "2025-04-22",
        "baseValue": 833.33,
        "interest": 83.33,
        "fee": 18.33,
        "totalValue": 935
      }
    ],
    "summary": {
      "baseTotal": 10000,
      "totalInterest": 650,
      "totalFee": 213,
      "totalAmount": 10863
    }
  }
}
```

**Respuesta - Error de Validación (400 Bad Request):**
```json
{
  "status": "error",
  "message": "Datos inválidos",
  "errors": [
    {
      "field": "numberOfMonths",
      "message": "El número de meses debe estar entre 1 y 360"
    }
  ]
}
```

**Parámetros:**

| Campo | Tipo | Requerido | Validación | Ejemplo |
|-------|------|-----------|-----------|---------|
| contractId | integer | ✓ | ID válido en BD | 1 |
| numberOfMonths | integer | ✓ | Entre 1-360 | 12 |
| paymentMethod | string | ✓ | PayPal \| PayOnline | PayPal |

**Campos de Respuesta:**

| Campo | Descripción |
|-------|------------|
| number | Número de la cuota (1-12) |
| dueDate | Fecha de vencimiento (YYYY-MM-DD) |
| baseValue | Valor base de la cuota |
| interest | Interés calculado para esta cuota |
| fee | Tarifa de pago para esta cuota |
| totalValue | Valor total a pagar (base + interés + tarifa) |

En **summary**:
| Campo | Descripción |
|-------|------------|
| baseTotal | Suma de valores base (= contractValue) |
| totalInterest | Suma de todos los intereses |
| totalFee | Suma de todas las tarifas |
| totalAmount | Total a pagar (baseTotal + totalInterest + totalFee) |

---

## Escenarios de Prueba

### Escenario 1: Comparar PayPal vs PayOnline

```bash
# Crear un contrato
CONTRACT_ID=$(curl -s -X POST http://localhost:8000/api/contracts \
  -H "Content-Type: application/json" \
  -d '{
    "contractNumber": "CNT-COMP-001",
    "contractDate": "2025-01-22",
    "contractValue": 10000,
    "paymentMethod": "PayPal",
    "clientName": "Test Comparación"
  }' | jq -r '.data.id')

# Proyectar con PayPal
echo "=== Proyección PayPal ==="
curl -s -X POST http://localhost:8000/api/contracts/projection/calculate \
  -H "Content-Type: application/json" \
  -d "{\"contractId\": $CONTRACT_ID, \"numberOfMonths\": 12, \"paymentMethod\": \"PayPal\"}" | jq '.data.summary'

# Proyectar con PayOnline
echo "=== Proyección PayOnline ==="
curl -s -X POST http://localhost:8000/api/contracts/projection/calculate \
  -H "Content-Type: application/json" \
  -d "{\"contractId\": $CONTRACT_ID, \"numberOfMonths\": 12, \"paymentMethod\": \"PayOnline\"}" | jq '.data.summary'
```

### Escenario 2: Diferentes períodos de pago

```bash
# Proyección a 6 meses
curl -s -X POST http://localhost:8000/api/contracts/projection/calculate \
  -H "Content-Type: application/json" \
  -d '{"contractId": 1, "numberOfMonths": 6, "paymentMethod": "PayPal"}' | jq '.data.summary'

# Proyección a 12 meses
curl -s -X POST http://localhost:8000/api/contracts/projection/calculate \
  -H "Content-Type: application/json" \
  -d '{"contractId": 1, "numberOfMonths": 12, "paymentMethod": "PayPal"}' | jq '.data.summary'

# Proyección a 24 meses
curl -s -X POST http://localhost:8000/api/contracts/projection/calculate \
  -H "Content-Type: application/json" \
  -d '{"contractId": 1, "numberOfMonths": 24, "paymentMethod": "PayPal"}' | jq '.data.summary'
```

---

## Códigos de Estado HTTP

| Código | Significado | Ejemplo |
|--------|------------|---------|
| 200 | OK - Solicitud exitosa | GET /api/contracts |
| 201 | Created - Recurso creado | POST /api/contracts |
| 400 | Bad Request - Datos inválidos | Validación fallida |
| 404 | Not Found - Recurso no existe | Contrato ID no existe |
| 500 | Server Error - Error interno | Excepción no manejada |

---

## Herramientas Útiles

### Usando Postman

1. Importar colección (crear manualmente o exportar cURL)
2. Configurar variables de entorno: `{{base_url}}` = `http://localhost:8000`
3. Crear requests para cada endpoint

### Usando jq para formatear JSON

```bash
# Obtener solo el resumen
curl -s http://localhost:8000/api/contracts/1 | jq '.data'

# Contar contratos
curl -s http://localhost:8000/api/contracts | jq '.total'

# Extraer números de contrato
curl -s http://localhost:8000/api/contracts | jq '.data[].contractNumber'
```

### Script Bash para pruebas

```bash
#!/bin/bash
BASE_URL="http://localhost:8000"

# Función para crear contrato
create_contract() {
  curl -s -X POST "$BASE_URL/api/contracts" \
    -H "Content-Type: application/json" \
    -d "$1"
}

# Función para proyectar
project_installments() {
  curl -s -X POST "$BASE_URL/api/contracts/projection/calculate" \
    -H "Content-Type: application/json" \
    -d "$1"
}

# Uso
create_contract '{"contractNumber":"CNT-001","contractDate":"2025-01-22","contractValue":5000,"paymentMethod":"PayPal","clientName":"Test"}'
```

---

## Solución de Problemas

### Error: "Contrato no encontrado"
- Verificar que el `contractId` existe en la BD
- Listar todos los contratos: `GET /api/contracts`

### Error: "Método de pago no válido"
- Usar solo: "PayPal" o "PayOnline"
- Verificar mayúsculas

### Error: "El número de meses debe estar entre 1 y 360"
- numberOfMonths debe ser un entero entre 1 y 360
- 360 meses = 30 años

### Error 500 - Sin base de datos
- Ejecutar migraciones: `php bin/console doctrine:migrations:migrate`
- Configurar DATABASE_URL en `.env`

---

## Notas sobre el Cálculo de Cuotas

### Fórmula General

Para cada cuota `n`:

1. **Valor base de la cuota:**
   ```
   baseValue = contractValue / numberOfMonths
   ```

2. **Interés (sobre saldo pendiente):**
   ```
   interest = pendingBalance × (interestRate / 100)
   ```

3. **Tarifa (sobre cuota + interés):**
   ```
   fee = (baseValue + interest) × (feeRate / 100)
   ```

4. **Valor total de la cuota:**
   ```
   totalValue = baseValue + interest + fee
   ```

5. **Actualizar saldo pendiente:**
   ```
   pendingBalance -= baseValue
   ```

### Ejemplo con PayPal (1% interés, 2% tarifa)

Para un contrato de $10,000 a 12 meses:

- Base por mes: $10,000 / 12 = $833.33
- **Cuota 1:**
  - Saldo pendiente: $10,000
  - Interés: $10,000 × 1% = $100
  - Tarifa: ($833.33 + $100) × 2% = $18.67
  - **Total: $952.00**

- **Cuota 2:**
  - Saldo pendiente: $10,000 - $833.33 = $9,166.67
  - Interés: $9,166.67 × 1% = $91.67
  - Tarifa: ($833.33 + $91.67) × 2% = $18.50
  - **Total: $943.50**

Y así sucesivamente...

**Total a pagar:** $10,863.00 (cuota base $10,000 + interés $650 + tarifa $213)
