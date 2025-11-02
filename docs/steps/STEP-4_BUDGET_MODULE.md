# Paso 4: Desarrollo del Módulo de Presupuestos

## Objetivos
- Crear el modelo de presupuestos
- Implementar CRUD de presupuestos
- Integrar con el sistema existente de Crater
- Crear interfaz de usuario para presupuestos

## Requisitos Previos
- Completar [STEP-3_OPENAI_INTEGRATION.md](STEP-3_OPENAI_INTEGRATION.md)
- Familiaridad con la estructura de Crater
- Entender el sistema de módulos de Crater

## Pasos de Implementación

### 1. Crear Migraciones
```bash
# Crear migración para presupuestos
php artisan make:migration create_budgets_table

# Crear migración para items de presupuesto
php artisan make:migration create_budget_items_table
```

```php
// database/migrations/xxxx_xx_xx_create_budgets_table.php
public function up()
{
    Schema::create('budgets', function (Blueprint $table) {
        $table->id();
        $table->foreignId('company_id')->constrained();
        $table->foreignId('customer_id')->constrained();
        $table->string('budget_number');
        $table->date('budget_date');
        $table->date('expiry_date');
        $table->decimal('sub_total', 15, 2);
        $table->decimal('total', 15, 2);
        $table->decimal('tax', 15, 2);
        $table->string('status')->default('draft');
        $table->text('notes')->nullable();
        $table->json('ai_analysis')->nullable();
        $table->timestamps();
        $table->softDeletes();
    });
}
```

### 2. Crear Modelos
```bash
# Crear modelo de presupuesto
php artisan make:model Budget

# Crear modelo de item de presupuesto
php artisan make:model BudgetItem
```

```php
// app/Models/Budget.php
class Budget extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id',
        'customer_id',
        'budget_number',
        'budget_date',
        'expiry_date',
        'sub_total',
        'total',
        'tax',
        'status',
        'notes',
        'ai_analysis'
    ];

    protected $casts = [
        'budget_date' => 'date',
        'expiry_date' => 'date',
        'ai_analysis' => 'array'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(BudgetItem::class);
    }
}
```

### 3. Crear Controladores y Requests
```bash
# Crear controlador
php artisan make:controller BudgetController --resource

# Crear requests
php artisan make:request Budget/StoreBudgetRequest
php artisan make:request Budget/UpdateBudgetRequest
```

### 4. Implementar Rutas
```php
// routes/api.php
Route::prefix('api')->group(function () {
    Route::apiResource('budgets', BudgetController::class);
    Route::post('budgets/{budget}/generate', [BudgetController::class, 'generateWithAI']);
    Route::post('budgets/{budget}/items', [BudgetItemController::class, 'store']);
});
```

### 5. Crear Servicios
```php
// app/Services/BudgetService.php
class BudgetService
{
    public function __construct(
        private AIServiceInterface $aiService
    ) {}

    public function create(array $data)
    {
        DB::transaction(function () use ($data) {
            $budget = Budget::create($data);
            $this->createItems($budget, $data['items']);
            return $budget;
        });
    }

    public function generateWithAI(string $description)
    {
        $dto = new BudgetRequestDTO($description);
        return $this->aiService->generateBudgetSuggestion($dto);
    }
}
```

### 6. Implementar Vistas
```bash
# Crear componentes Vue
mkdir -p resources/js/Components/Budgets
touch resources/js/Components/Budgets/BudgetForm.vue
touch resources/js/Components/Budgets/BudgetList.vue
touch resources/js/Components/Budgets/BudgetAIGenerator.vue
```

```vue
<!-- resources/js/Components/Budgets/BudgetForm.vue -->
<template>
  <div class="budget-form">
    <!-- Implementar formulario -->
  </div>
</template>

<script>
export default {
  // Implementar lógica
}
</script>
```

### 7. Implementar Políticas
```bash
# Crear política de presupuestos
php artisan make:policy BudgetPolicy
```

```php
// app/Policies/BudgetPolicy.php
class BudgetPolicy
{
    public function viewAny(User $user)
    {
        return $user->can('view budgets');
    }

    public function create(User $user)
    {
        return $user->can('create budgets');
    }
}
```

### 8. Crear Tests
```bash
# Crear tests
php artisan make:test BudgetTest
php artisan make:test BudgetControllerTest
```

## Verificación

### Lista de Comprobación
- [ ] Migraciones ejecutadas correctamente
- [ ] Modelos creados y relacionados
- [ ] API endpoints funcionando
- [ ] Interfaz de usuario implementada
- [ ] Tests pasando
- [ ] Integración con IA funcionando

## Pruebas Específicas

### 1. Pruebas de Unidad
```bash
php artisan test --filter=BudgetTest
```

### 2. Pruebas de API
```bash
php artisan test --filter=BudgetControllerTest
```

### 3. Pruebas de Integración con IA
```bash
php artisan test --filter=BudgetAIIntegrationTest
```

## Solución de Problemas

### Problemas Comunes

1. **Error: Migraciones Fallando**
   - Verificar orden de migraciones
   - Comprobar constrains de foreign keys

2. **Error: API Endpoints**
   - Verificar rutas y middleware
   - Comprobar políticas de acceso

3. **Error: Integración Frontend**
   - Limpiar caché de Vue
   - Recompilar assets

## Siguiente Paso
Una vez completado el módulo de presupuestos, proceder a [STEP-5_AI_IMPLEMENTATION.md](STEP-5_AI_IMPLEMENTATION.md)