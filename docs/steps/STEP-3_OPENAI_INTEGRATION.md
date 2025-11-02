# Paso 3: Integración con OpenAI

## Objetivos
- Configurar la integración con OpenAI
- Implementar el servicio base de IA
- Crear la estructura para el módulo de IA

## Requisitos Previos
- Completar [STEP-2_CRATER_INSTALLATION.md](STEP-2_CRATER_INSTALLATION.md)
- Cuenta activa en OpenAI con API key
- Crater funcionando correctamente

## Pasos de Implementación

### 1. Instalar Dependencias
```bash
# Instalar SDK de OpenAI
composer require openai-php/laravel

# Publicar configuración
php artisan vendor:publish --provider="OpenAI\Laravel\ServiceProvider"
```

### 2. Configurar OpenAI
```bash
# Añadir a .env
OPENAI_API_KEY=tu_api_key
OPENAI_ORGANIZATION=tu_org_id # Opcional
```

### 3. Crear Estructura de Módulo AI

```bash
# Crear directorios
mkdir -p app/AI/{Services,Interfaces,DTOs,Config}

# Crear archivos base
touch app/AI/Interfaces/AIServiceInterface.php
touch app/AI/Services/OpenAIService.php
touch app/AI/DTOs/BudgetRequestDTO.php
touch app/AI/Config/PromptConfig.php
```

### 4. Implementar Interface de IA
```php
<?php
// app/AI/Interfaces/AIServiceInterface.php

namespace App\AI\Interfaces;

use App\AI\DTOs\BudgetRequestDTO;

interface AIServiceInterface
{
    public function generateBudgetSuggestion(BudgetRequestDTO $request): array;
    public function analyzeBudgetRequirements(string $description): array;
    public function estimateCosts(array $requirements): array;
}
```

### 5. Implementar DTO
```php
<?php
// app/AI/DTOs/BudgetRequestDTO.php

namespace App\AI\DTOs;

class BudgetRequestDTO
{
    public function __construct(
        public string $description,
        public ?array $requirements = null,
        public ?float $budget = null,
        public ?string $industry = null,
        public ?string $timeframe = null
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            description: $data['description'],
            requirements: $data['requirements'] ?? null,
            budget: $data['budget'] ?? null,
            industry: $data['industry'] ?? null,
            timeframe: $data['timeframe'] ?? null
        );
    }
}
```

### 6. Implementar Servicio OpenAI
```php
<?php
// app/AI/Services/OpenAIService.php

namespace App\AI\Services;

use App\AI\Interfaces\AIServiceInterface;
use App\AI\DTOs\BudgetRequestDTO;
use OpenAI\Laravel\Facades\OpenAI;

class OpenAIService implements AIServiceInterface
{
    public function generateBudgetSuggestion(BudgetRequestDTO $request): array
    {
        $response = OpenAI::chat()->create([
            'model' => 'gpt-4',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are a professional budget estimator.'
                ],
                [
                    'role' => 'user',
                    'content' => $this->formatBudgetRequest($request)
                ]
            ]
        ]);

        return $this->parseResponse($response);
    }

    private function formatBudgetRequest(BudgetRequestDTO $request): string
    {
        // Implementar lógica de formateo
    }

    private function parseResponse($response): array
    {
        // Implementar lógica de parsing
    }
}
```

### 7. Configurar Service Provider
```bash
# Crear provider
php artisan make:provider AIServiceProvider
```

```php
<?php
// app/Providers/AIServiceProvider.php

namespace App\Providers;

use App\AI\Interfaces\AIServiceInterface;
use App\AI\Services\OpenAIService;
use Illuminate\Support\ServiceProvider;

class AIServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(AIServiceInterface::class, OpenAIService::class);
    }
}
```

### 8. Registrar Provider
Añadir a `config/app.php`:
```php
'providers' => [
    // ...
    App\Providers\AIServiceProvider::class,
],
```

## Verificación

### Lista de Comprobación
- [ ] OpenAI SDK instalado correctamente
- [ ] Variables de entorno configuradas
- [ ] Estructura de módulos creada
- [ ] Interface implementada
- [ ] DTO implementado
- [ ] Servicio OpenAI implementado
- [ ] Provider registrado

## Pruebas

### 1. Crear Test Básico
```bash
php artisan make:test AI/Services/OpenAIServiceTest
```

```php
<?php
// tests/Feature/AI/Services/OpenAIServiceTest.php

namespace Tests\Feature\AI\Services;

use Tests\TestCase;
use App\AI\Services\OpenAIService;
use App\AI\DTOs\BudgetRequestDTO;

class OpenAIServiceTest extends TestCase
{
    public function test_can_generate_budget_suggestion()
    {
        $service = app(OpenAIService::class);
        $dto = new BudgetRequestDTO(
            description: 'Website development project'
        );

        $result = $service->generateBudgetSuggestion($dto);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('suggestions', $result);
    }
}
```

## Solución de Problemas

### Problemas Comunes

1. **Error: OpenAI API Key Invalid**
   - Verificar que la API key está correctamente configurada en .env
   - Comprobar que la key tiene permisos suficientes

2. **Error: Rate Limiting**
   - Implementar sistema de caché para respuestas
   - Añadir retry logic para peticiones fallidas

3. **Error: Respuesta Malformada**
   - Implementar mejor manejo de errores
   - Añadir logging detallado

## Siguiente Paso
Una vez completada la integración con OpenAI, proceder a [STEP-4_BUDGET_MODULE.md](STEP-4_BUDGET_MODULE.md)