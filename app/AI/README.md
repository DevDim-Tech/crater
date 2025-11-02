# Configuración del Módulo de IA

## Descripción General
Este módulo permite la integración con APIs de modelos de IA compatibles con OpenAI, incluyendo:
- OpenAI (GPT-4, GPT-3.5-turbo)
- GPUStack
- LocalAI
- Ollama
- Cualquier endpoint compatible con la API de OpenAI

## Configuración

### Variables de Entorno (.env)

```bash
# API Key de tu servicio de IA
OPENAI_API_KEY=tu_api_key_aqui

# Base URI del endpoint (opcional, por defecto usa OpenAI)
# Para OpenAI: https://api.openai.com/v1 (default)
# Para GPUStack: http://tu-servidor:puerto/v1
# Para LocalAI: http://localhost:8080/v1
OPENAI_BASE_URI=http://78.40.111.120:8003/v1

# Nombre del modelo a utilizar
# OpenAI: gpt-4, gpt-3.5-turbo
# GPUStack: gemma3, llama3, mistral
# LocalAI: tu-modelo-local
OPENAI_MODEL=gemma3

# Organización (opcional, solo para OpenAI multi-org)
OPENAI_ORGANIZATION=

# Timeout de requests en segundos (opcional)
OPENAI_TIMEOUT=30

# Máximo de tokens por respuesta (opcional)
OPENAI_MAX_TOKENS=2000
```

## Ejemplos de Configuración

### OpenAI
```bash
OPENAI_API_KEY=sk-...
OPENAI_BASE_URI=https://api.openai.com/v1
OPENAI_MODEL=gpt-4
```

### GPUStack
```bash
OPENAI_API_KEY=gpustack_21060f706a0888c2_25e558a5128473cc56e19b15af2cd6cb
OPENAI_BASE_URI=http://78.40.111.120:8003/v1
OPENAI_MODEL=gemma3
```

### LocalAI
```bash
OPENAI_API_KEY=cualquier-cosa
OPENAI_BASE_URI=http://localhost:8080/v1
OPENAI_MODEL=llama3
```

### Ollama
```bash
OPENAI_API_KEY=ollama
OPENAI_BASE_URI=http://localhost:11434/v1
OPENAI_MODEL=llama3
```

## Uso del Servicio

### Inyección de Dependencias

```php
use Crater\AI\Interfaces\AIServiceInterface;

class MiController extends Controller
{
    protected $aiService;

    public function __construct(AIServiceInterface $aiService)
    {
        $this->aiService = $aiService;
    }

    public function generarPresupuesto(Request $request)
    {
        $dto = BudgetRequestDTO::fromArray($request->all());
        $resultado = $this->aiService->generateBudgetSuggestion($dto);
        
        return response()->json($resultado);
    }
}
```

### Uso Directo

```php
use Crater\AI\Services\OpenAIService;
use Crater\AI\DTOs\BudgetRequestDTO;

$aiService = app(OpenAIService::class);

$dto = new BudgetRequestDTO(
    description: 'Desarrollo de aplicación web',
    requirements: ['Frontend', 'Backend', 'Base de datos'],
    budget: 10000.00,
    industry: 'Tecnología',
    timeframe: '3 meses'
);

$sugerencias = $aiService->generateBudgetSuggestion($dto);
```

## Métodos Disponibles

### 1. generateBudgetSuggestion
Genera sugerencias de presupuesto completas basadas en requisitos.

```php
public function generateBudgetSuggestion(BudgetRequestDTO $request): array
```

### 2. analyzeBudgetRequirements
Analiza una descripción y extrae requisitos estructurados.

```php
public function analyzeBudgetRequirements(string $description): array
```

### 3. estimateCosts
Estima costes basándose en requisitos específicos.

```php
public function estimateCosts(array $requirements): array
```

### 4. generateItemDescription
Genera descripciones profesionales para items de presupuesto.

```php
public function generateItemDescription(string $itemName, array $context = []): string
```

## Testing

```bash
# Ejecutar tests del módulo de IA
php artisan test --filter=OpenAIServiceTest

# Ejecutar todos los tests
php artisan test
```

## Troubleshooting

### Error: "AI API key is not configured"
Asegúrate de tener configurado `OPENAI_API_KEY` en tu archivo `.env`.

### Error de conexión
Verifica que `OPENAI_BASE_URI` apunte a un endpoint accesible y que el servicio esté corriendo.

### Respuestas vacías o erróneas
- Ajusta el parámetro `OPENAI_MAX_TOKENS` si las respuestas se cortan
- Modifica la temperatura en el código si las respuestas son muy aleatorias o muy deterministas
- Verifica que el modelo especificado en `OPENAI_MODEL` esté disponible en tu endpoint

## Seguridad

- Nunca commitees tu `.env` con las API keys
- Usa variables de entorno en producción
- Considera usar servicios de gestión de secretos (AWS Secrets Manager, etc.)
- Implementa rate limiting en tus endpoints que usen IA

## Performance

- Las llamadas a la IA son asíncronas por naturaleza
- Considera implementar colas (Laravel Queues) para peticiones pesadas
- Implementa caché para respuestas frecuentes
- Monitoriza el uso de tokens para controlar costes
