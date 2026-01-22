# 📮 Colección Postman - Prueba Técnica Makrosoft

## ✅ Cómo Importar la Colección

### Opción 1: Importar desde Archivo

1. **Abre Postman**
2. Haz clic en **Import** (esquina superior izquierda)
3. Selecciona **Upload Files**
4. Navega a: `postman_collection.json`
5. ¡Listo! La colección se importará automáticamente

### Opción 2: Copiar URL (Online)

1. En Postman, haz clic en **Import**
2. Selecciona la pestaña **Link**
3. Si tienes el archivo en GitHub, pega la URL

---

## 🚀 Antes de Ejecutar

### Asegúrate que el servidor esté corriendo:

```bash
cd "c:\Users\Daniel Calle\Documents\PruebaTecnicaMakrosoft\PruebaTecnicaMakroSoft"
php -S localhost:8000 -t public
```

Deberías ver:
```
Development Server (http://127.0.0.1:8000)
Press Ctrl-C to quit
```

---

## 📋 Peticiones Incluidas en la Colección

### **Grupo 1: Crear Contratos**
1. ✅ **Create Contract - PayPal** - $10,000
2. ✅ **Create Contract - PayOnline** - $25,000  
3. ✅ **Create Contract - Small Value** - $5,000

### **Grupo 2: Consultar Contratos**
4. ✅ **Get All Contracts** - Lista todos los contratos
5. ✅ **Get Contract by ID** - Obtiene un contrato específico

### **Grupo 3: Proyectar Cuotas**
6. ✅ **Project Installments - PayPal (12 months)** - $10,000 en 12 meses
7. ✅ **Project Installments - PayOnline (24 months)** - $25,000 en 24 meses
8. ✅ **Compare Payment Methods** - Mismo contrato, distinto método de pago
9. ✅ **Project - 6 months short term** - Corto plazo
10. ✅ **Project - 36 months long term** - Largo plazo

---

## 🎯 Flujo Recomendado de Prueba

### **Secuencia 1: Crear y Consultar**
```
1. Ejecuta: Create Contract - PayPal
   ↓ Guarda el ID de la respuesta
2. Ejecuta: Get All Contracts
   ↓ Verifica que aparezca el contrato
3. Ejecuta: Get Contract by ID
   ↓ Obtén los detalles del contrato
```

### **Secuencia 2: Comparar Métodos de Pago**
```
1. Ejecuta: Create Contract - PayPal ($10,000)
2. Ejecuta: Project - PayPal (12 meses)
   ↓ Anota el total (deberá ser ~$10,863)
3. Ejecuta: Compare Payment Methods
   ↓ Usa el mismo contrato con PayOnline
   ↓ Verifica que el total sea mayor (~$11,413)
```

### **Secuencia 3: Análisis de Plazos**
```
1. Ejecuta: Create Contract - Small Value ($5,000)
2. Ejecuta: Project - 6 months short term
   ↓ Observa cuotas más altas
3. Ejecuta: Project - 36 months long term
   ↓ Observa cuotas más bajas
```

---

## 📊 Variables de Entorno

La colección incluye una variable preconfigurada:

| Variable | Valor | Descripción |
|----------|-------|-------------|
| `baseUrl` | `http://localhost:8000` | URL base de la API |

**Para cambiar:** En Postman, haz clic en "Environment" → "Manage Environments" y edita `baseUrl`

---

## 💾 Estructura de los Archivos

```
postman_collection.json
├── 1. Create Contract - PayPal
├── 2. Create Contract - PayOnline
├── 3. Get All Contracts
├── 4. Get Contract by ID
├── 5. Project Installments - PayPal (12 months)
├── 6. Project Installments - PayOnline (24 months)
├── 7. Compare Payment Methods - Same Contract
├── 8. Create Contract - Small Value
├── 9. Project Installments - 6 months short term
└── 10. Project Installments - 36 months long term
```

---

## 🧪 Resultados Esperados

### PayPal ($10,000 / 12 meses)
- **Interés:** 1% sobre saldo restante
- **Tarifa:** 2% sobre cuota base
- **Total:** ~$10,863.00
- **Diferencia:** $863.00

### PayOnline ($10,000 / 12 meses)
- **Interés:** 2% sobre saldo restante
- **Tarifa:** 1% sobre cuota base
- **Total:** ~$11,413.00
- **Diferencia:** $1,413.00

### Comparación
```
PayPal:    $10,863.00  ✓ (Más barato)
PayOnline: $11,413.00
Diferencia: $550.00 (5.1% de ahorro con PayPal)
```

---

## 🐛 Solución de Problemas

### Error: "Connection refused"
```
❌ Problema: El servidor no está corriendo
✅ Solución: Ejecuta: php -S localhost:8000 -t public
```

### Error: "Contract not found"
```
❌ Problema: El ID del contrato no existe
✅ Solución: Primero ejecuta "Create Contract", luego usa ese ID
```

### Error: "Invalid payment method"
```
❌ Problema: paymentMethod debe ser "paypal" o "payonline"
✅ Solución: Verifica la ortografía exacta (minúsculas)
```

---

## 📝 Notas Importantes

1. **Los IDs cambian:** Cada vez que creas un contrato, obtienes un nuevo ID
2. **La BD persiste:** Los datos se guardan en `var/app.db`
3. **Puedes reintentar:** Ejecuta las peticiones múltiples veces
4. **Compara resultados:** Crea el mismo contrato con PayPal y PayOnline para ver la diferencia

---

## 🎓 Qué Prueba Esta Colección

✅ **CRUD Básico** - Crear, Leer contratos
✅ **Cálculos Complejos** - Proyección de cuotas con interés
✅ **Estrategias de Pago** - PayPal vs PayOnline
✅ **Validación de Datos** - Contratos válidos e inválidos
✅ **Manejo de Errores** - Respuestas de error apropiadas
✅ **JSON Response** - Formato de respuesta consistente

---

**¡Lista para probar!** 🚀
