# Arquitectura y Diseño

## Visión General

La aplicación sigue una arquitectura en capas basada en los principios SOLID y patrones de diseño reconocidos. Está diseñada para ser escalable, mantenible y fácil de extender.

## Capas de la Aplicación

```
┌─────────────────────────────────────────────────────────┐
│              API REST (Symfony Routing)                 │
├─────────────────────────────────────────────────────────┤
│                   Controllers                           │
│         (Manejo de HTTP Requests/Responses)             │
├─────────────────────────────────────────────────────────┤
│                     DTOs                                │
│   (Validación y transformación de datos)                │
├─────────────────────────────────────────────────────────┤
│                   Services                              │
│        (Lógica de negocio de la aplicación)             │
├─────────────────────────────────────────────────────────┤
│          Payment Services (Strategy Pattern)            │
│         (Diferentes formas de cálculo de cuotas)        │
├─────────────────────────────────────────────────────────┤
│                   Entities                              │
│            (Modelos de datos - Doctrine ORM)            │
├─────────────────────────────────────────────────────────┤
│                  Database                               │
│               (MySQL / SQLite)                          │
└─────────────────────────────────────────────────────────┘
```

## Componentes Principales

### 1. Controllers (API REST)

**Archivo:** `src/Controller/ContractController.php`

Responsabilidades:
- Recibir requests HTTP
- Validar datos de entrada
- Invocar servicios
- Retornar respuestas JSON estructuradas

```php
#[Route('/api/contracts')]
class ContractController extends AbstractController
{
    // Métodos:
    // POST   /api/contracts                    -> createContract()
    // GET    /api/contracts                    -> getAllContracts()
    // GET    /api/contracts/{id}               -> getContractById()
    // POST   /api/contracts/projection/calculate -> projectInstallments()
}
```

### 2. DTOs (Data Transfer Objects)

Ubicación: `src/DTO/`

**CreateContractRequest.php**
- Valida los datos al crear un contrato
- Usa Symfony Validator Constraints
- Propiedades: contractNumber, contractDate, contractValue, paymentMethod, etc.

**InstallmentProjectionRequest.php**
- Valida los parámetros para proyectar cuotas
- Propiedades: contractId, numberOfMonths, paymentMethod

**InstallmentProjectionResponse.php**
- Estructura la respuesta de proyección
- Propiedades: contractId, installments[], summary (totalAmount, totalInterest, totalFee)

### 3. Services

#### InstallmentProjectionService
**Archivo:** `src/Service/InstallmentProjectionService.php`

Responsabilidades:
- Proyectar cuotas basándose en:
  - Valor del contrato
  - Número de meses
  - Fecha del contrato
  - Servicio de pago seleccionado
- Calcular totales

```php
public function projectInstallments(
    float $contractValue,
    int $numberOfMonths,
    DateTime $contractDate,
    PaymentServiceInterface $paymentService
): array
```

Algoritmo:
1. Divide el valor del contrato entre número de meses (cuota base)
2. Para cada mes:
   - Calcula interés sobre el saldo pendiente
   - Suma interés a la cuota base
   - Calcula tarifa sobre el total (cuota + interés)
   - Almacena resultado
   - Deduce la cuota base del saldo pendiente

### 4. Payment Services (Strategy Pattern)

**Ubicación:** `src/Service/PaymentService/`

#### Interfaz: PaymentServiceInterface

Define el contrato que deben cumplir todos los servicios de pago:

```php
interface PaymentServiceInterface
{
    public function calculateInstallment(
        float $installmentValue,
        float $pendingBalance,
        int $installmentNumber
    ): float;
    
    public function getName(): string;
    public function getInterestRate(): float;
    public function getPaymentFee(): float;
}
```

#### Implementaciones

**PayPalService**
- Interés: 1% sobre saldo pendiente
- Tarifa: 2% sobre el total

**PayOnlineService**
- Interés: 2% sobre saldo pendiente
- Tarifa: 1% sobre el total

### 5. Entities (Doctrine ORM)

**Archivo:** `src/Entity/Contract.php`

Representa un contrato en la base de datos:

```php
@ORM\Entity
@ORM\Table(name="contracts")
class Contract
{
    - id: Integer (PK)
    - contractNumber: String (UNIQUE)
    - contractDate: DateTime
    - contractValue: Decimal(12,2)
    - paymentMethod: String (PayPal | PayOnline)
    - clientName: String (nullable)
    - description: Text (nullable)
    - status: String (PENDING | ACTIVE | COMPLETED | CANCELLED)
    - createdAt: DateTime
    - updatedAt: DateTime (nullable)
}
```

### 6. Resolver (Factory Pattern)

**Archivo:** `src/Controller/ContractPaymentServiceResolver.php`

Responsabilidad:
- Resolver y retornar la implementación correcta de PaymentService

```php
class ContractPaymentServiceResolver
{
    public function resolve(string $paymentMethodName): PaymentServiceInterface
    {
        return match($paymentMethodName) {
            'PayPal' => new PayPalService(),
            'PayOnline' => new PayOnlineService(),
            default => throw new InvalidArgumentException(...)
        };
    }
}
```

## Patrones de Diseño Implementados

### 1. Strategy Pattern

**Propósito:** Definir una familia de algoritmos, encapsular cada uno, y hacerlos intercambiables.

**Implementación:**
- `PaymentServiceInterface` define la estrategia
- `PayPalService` y `PayOnlineService` son estrategias concretas
- `InstallmentProjectionService` usa la estrategia sin importar cuál sea

**Ventaja:** Fácil agregar nuevos servicios de pago sin modificar código existente

### 2. Factory Pattern

**Propósito:** Crear objetos sin especificar sus clases exactas.

**Implementación:**
- `ContractPaymentServiceResolver` actúa como factory
- Crea la instancia correcta de PaymentService basado en un parámetro

**Ventaja:** Centraliza la creación de objetos

### 3. DTO Pattern

**Propósito:** Transferir datos entre capas con validación.

**Implementación:**
- Clases especializadas para cada operación (Create, Projection)
- Validación declarativa con Attributes

**Ventaja:** Separación de conceptos, validación centralizada

### 4. Repository Pattern

**Propósito:** Abstraer el acceso a datos.

**Implementación:**
- Doctrine ORM maneja automáticamente los queries
- EntityManager proporcionado por Symfony

**Ventaja:** Fácil cambiar la base de datos sin afectar la lógica de negocio

## Flujo de Solicitud

### Crear Contrato

```
1. HTTP POST /api/contracts
   ↓
2. ContractController::createContract()
   ↓
3. Deserializar JSON → CreateContractRequest (DTO)
   ↓
4. Validar DTO con Validator
   ↓
5. Si válido: Crear entidad Contract
   ↓
6. Persistir en BD con EntityManager
   ↓
7. Retornar respuesta JSON con status "success"
```

### Proyectar Cuotas

```
1. HTTP POST /api/contracts/projection/calculate
   ↓
2. ContractController::projectInstallments()
   ↓
3. Deserializar JSON → InstallmentProjectionRequest (DTO)
   ↓
4. Validar DTO
   ↓
5. Buscar Contract en BD
   ↓
6. Resolver PaymentService con ContractPaymentServiceResolver
   ↓
7. Llamar InstallmentProjectionService::projectInstallments()
   ↓
8. Servicio calcula cuotas usando la estrategia de pago
   ↓
9. Transformar a InstallmentProjectionResponse
   ↓
10. Retornar JSON con cuotas proyectadas
```

## Principios SOLID Aplicados

### Single Responsibility Principle (SRP)
Cada clase tiene una única razón para cambiar:
- `InstallmentProjectionService`: Solo proyecta cuotas
- `PayPalService`: Solo calcula según reglas de PayPal
- `ContractController`: Solo maneja requests HTTP

### Open/Closed Principle (OCP)
Las clases están abiertas para extensión, cerradas para modificación:
- Para agregar un nuevo servicio de pago, solo extendemos `PaymentServiceInterface`
- No necesitamos modificar `InstallmentProjectionService`

### Liskov Substitution Principle (LSP)
Las subclases pueden reemplazar a la clase base:
- `PayPalService` y `PayOnlineService` pueden usarse indistintamente donde se espera `PaymentServiceInterface`

### Interface Segregation Principle (ISP)
Interfaces específicas y no genéricas:
- `PaymentServiceInterface` es específica para servicios de pago
- No tiene métodos innecesarios

### Dependency Inversion Principle (DIP)
Depender de abstracciones, no de implementaciones concretas:
- `InstallmentProjectionService` depende de `PaymentServiceInterface`, no de PayPal específicamente
- `ContractController` inyecta sus dependencias

## Extensibilidad

### Agregar un nuevo Servicio de Pago

1. Crear nueva clase implementando `PaymentServiceInterface`:
```php
class StripeService implements PaymentServiceInterface { ... }
```

2. Agregar al resolver:
```php
'Stripe' => new StripeService()
```

3. El resto de la aplicación funciona sin cambios

### Agregar nuevas operaciones REST

1. Agregar método en `ContractController`
2. Crear DTO si es necesario
3. Implementar lógica usando servicios existentes

## Consideraciones de Rendimiento

1. **Caching:** Las proyecciones podrían cachearse si los datos no cambian
2. **Índices BD:** Tabla `contracts` tiene índices en:
   - `contract_number` (búsqueda)
   - `payment_method` (filtrado)
   - `status` (listado)

3. **Batch Processing:** Para múltiples proyecciones, optimizar queries

## Seguridad

1. **Validación de Entrada:** Todos los DTOs validan entrada
2. **Inyección de Dependencias:** Reduce acoplamiento
3. **Type Hints:** PHP 8 strict types
4. **Manejo de Excepciones:** Try-catch en endpoints críticos

## Testing

Hay pruebas unitarias en `tests/InstallmentProjectionTest.php`:
- Proyección con PayPal
- Proyección con PayOnline
- Comparación de servicios
- Validación de entrada (casos negativos)

Ejecutar pruebas:
```bash
php bin/phpunit
```

## Evolución Futura

1. **Caché Distribuido:** Redis para proyecciones frecuentes
2. **Event Sourcing:** Auditoría completa de cambios
3. **API Versioning:** Soportar múltiples versiones
4. **Webhooks:** Notificaciones cuando cambia el estado
5. **Rate Limiting:** Proteger endpoints
6. **Authentication:** Bearer tokens, OAuth2
7. **GraphQL:** Alternativa a REST
8. **Async Processing:** Cola para proyecciones complejas
