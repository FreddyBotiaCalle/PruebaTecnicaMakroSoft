# Aplicación de Tramitación de Contratos - Symfony 8.0

## Descripción

Aplicación REST desarrollada en Symfony 8.0 para automatizar la tramitación de contratos, incluyendo la proyección de cuotas de pago según diferentes servicios de pago en línea.

## Características

- ✅ Crear contratos con información básica
- ✅ Proyectar cuotas de pago automáticamente
- ✅ Soporte para múltiples servicios de pago (PayPal, PayOnline)
- ✅ API REST con validación
- ✅ Implementación de patrones SOLID
- ✅ Clean Code
- ✅ Patrones de diseño (Strategy, Factory)

## Servicios de Pago Soportados

### PayPal
- Interés: 1% sobre saldo pendiente
- Tarifa: 2% por pago

### PayOnline
- Interés: 2% sobre saldo pendiente
- Tarifa: 1% por pago

## Requisitos

- PHP 8.2+
- Composer
- MySQL 8.0+ (o cualquier base de datos compatible con Doctrine)

## Instalación

1. **Clonar el repositorio**
```bash
git clone <url-repositorio>
cd PruebaTecnicaMakroSoft
```

2. **Instalar dependencias**
```bash
composer install
```

3. **Configurar la base de datos**

Edita el archivo `.env` y configura la URL de la base de datos:

```env
# Para MySQL
DATABASE_URL="mysql://usuario:password@127.0.0.1:3306/contratos_db?serverVersion=8.0.32&charset=utf8mb4"
```

4. **Crear la base de datos**
```bash
php bin/console doctrine:database:create
```

5. **Ejecutar migraciones**
```bash
php bin/console doctrine:migrations:migrate
```

## Estructura del Proyecto

```
src/
├── Controller/
│   ├── ContractController.php          # Controlador REST
│   └── ContractPaymentServiceResolver.php  # Resolver de servicios de pago
├── Entity/
│   └── Contract.php                    # Entidad de contrato
├── Service/
│   ├── InstallmentProjectionService.php  # Servicio de proyección
│   └── PaymentService/
│       ├── PaymentServiceInterface.php   # Interfaz
│       ├── PayPalService.php             # Implementación PayPal
│       └── PayOnlineService.php          # Implementación PayOnline
├── DTO/
│   ├── CreateContractRequest.php       # DTO para crear contrato
│   ├── InstallmentProjectionRequest.php # DTO para proyección
│   └── InstallmentProjectionResponse.php # DTO de respuesta
└── ...
```

## API Endpoints

### 1. Crear un Contrato
**POST** `/api/contracts`

Request:
```json
{
  "contractNumber": "CNT-2025-001",
  "contractDate": "2025-01-22",
  "contractValue": 10000,
  "paymentMethod": "PayPal",
  "clientName": "Empresa ABC",
  "description": "Contrato de servicios"
}
```

Response:
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
    "clientName": "Empresa ABC"
  }
}
```

### 2. Obtener Todos los Contratos
**GET** `/api/contracts`

Response:
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
      "clientName": "Empresa ABC",
      "status": "PENDING",
      "createdAt": "2025-01-22 10:30:00"
    }
  ],
  "total": 1
}
```

### 3. Obtener un Contrato por ID
**GET** `/api/contracts/{id}`

Response:
```json
{
  "status": "success",
  "data": {
    "id": 1,
    "contractNumber": "CNT-2025-001",
    "contractDate": "2025-01-22",
    "contractValue": "10000.00",
    "paymentMethod": "PayPal",
    "clientName": "Empresa ABC",
    "description": "Contrato de servicios",
    "status": "PENDING",
    "createdAt": "2025-01-22 10:30:00"
  }
}
```

### 4. Proyectar Cuotas de un Contrato
**POST** `/api/contracts/projection/calculate`

Request:
```json
{
  "contractId": 1,
  "numberOfMonths": 12,
  "paymentMethod": "PayPal"
}
```

Response:
```json
{
  "status": "success",
  "data": {
    "contractId": 1,
    "contractNumber": "CNT-2025-001",
    "contractDate": "2025-01-22",
    "contractValue": 10000,
    "paymentMethod": "PayPal",
    "clientName": "Empresa ABC",
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
        "fee": 18.33,
        "totalValue": 943.33
      }
    ],
    "summary": {
      "baseTotal": 10000,
      "totalInterest": 1050,
      "totalFee": 220.5,
      "totalAmount": 11270.5
    }
  }
}
```

## Ejecutar la Aplicación

### Servidor de Desarrollo Symfony
```bash
php bin/console server:run
```

La aplicación estará disponible en: `http://localhost:8000`

### Usando PHP Built-in Server
```bash
php -S localhost:8000 -t public/
```

## Ejemplos de Prueba

### Crear un contrato
```bash
curl -X POST http://localhost:8000/api/contracts \
  -H "Content-Type: application/json" \
  -d '{
    "contractNumber": "CNT-2025-001",
    "contractDate": "2025-01-22",
    "contractValue": 10000,
    "paymentMethod": "PayPal",
    "clientName": "Empresa Test"
  }'
```

### Proyectar cuotas
```bash
curl -X POST http://localhost:8000/api/contracts/projection/calculate \
  -H "Content-Type: application/json" \
  -d '{
    "contractId": 1,
    "numberOfMonths": 12,
    "paymentMethod": "PayPal"
  }'
```

### Obtener todos los contratos
```bash
curl http://localhost:8000/api/contracts
```

## Patrones y Principios Implementados

### SOLID
- **S**ingle Responsibility: Cada clase tiene una única responsabilidad
- **O**pen/Closed: Las clases están abiertas para extensión pero cerradas para modificación
- **L**iskov Substitution: Las implementaciones de PaymentService son intercambiables
- **I**nterface Segregation: Interfaces específicas y bien definidas
- **D**ependency Inversion: Inyección de dependencias en los servicios

### Patrones de Diseño
- **Strategy Pattern**: Diferentes estrategias de pago (PayPal, PayOnline)
- **Factory Pattern**: Resolver para crear servicios de pago
- **DTO Pattern**: Objetos de transferencia de datos para validación
- **Repository Pattern**: Doctrine maneja el acceso a datos

### Clean Code
- Nombres descriptivos
- Métodos cortos y enfocados
- Comentarios significativos
- Validación de entrada
- Manejo de excepciones

## Base de Datos

### Script SQL para crear la tabla (si es necesario)

```sql
CREATE TABLE contracts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    contract_number VARCHAR(50) NOT NULL UNIQUE,
    contract_date DATETIME NOT NULL,
    contract_value DECIMAL(12, 2) NOT NULL,
    payment_method VARCHAR(20) NOT NULL,
    client_name VARCHAR(100),
    description TEXT,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME,
    status VARCHAR(20) NOT NULL DEFAULT 'PENDING',
    INDEX idx_contract_number (contract_number),
    INDEX idx_payment_method (payment_method),
    INDEX idx_status (status)
);
```

## Desarrollo Futuro

- Agregar autenticación y autorización
- Implementar caché para proyecciones
- Agregar pruebas unitarias y de integración
- Implementar pagos reales
- Agregar más métodos de pago
- Dashboard de administración
- Reportes de contratos

## Licencia

Este proyecto es de uso privado para fines educativos.

## Autor

Desarrollado por: Daniel Calle
Fecha: Enero 2025
