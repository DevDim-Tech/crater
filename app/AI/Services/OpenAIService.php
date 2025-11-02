<?php

namespace Crater\AI\Services;

use Crater\AI\Interfaces\AIServiceInterface;
use Crater\AI\DTOs\BudgetRequestDTO;
use OpenAI;
use Illuminate\Support\Facades\Log;
use Exception;

class OpenAIService implements AIServiceInterface
{
    protected $client;
    protected string $model;

    public function __construct()
    {
        $apiKey = config('openai.api_key', env('OPENAI_API_KEY'));
        $baseUri = config('openai.base_uri', env('OPENAI_BASE_URI', 'https://api.openai.com/v1'));
        $organization = config('openai.organization', env('OPENAI_ORGANIZATION'));

        if (!$apiKey) {
            throw new Exception('AI API key is not configured');
        }

        // Configure client with custom base URI if provided
        $clientConfig = ['api_key' => $apiKey];

        if ($baseUri && $baseUri !== 'https://api.openai.com/v1') {
            $clientConfig['base_uri'] = $baseUri;
        }

        if ($organization) {
            $clientConfig['organization'] = $organization;
        }

        $this->client = OpenAI::factory()
            ->withApiKey($apiKey)
            ->withBaseUri($baseUri)
            ->make();

        // Get model from config
        $this->model = config('openai.model', env('OPENAI_MODEL', 'gpt-4'));
    }

    /**
     * Generate budget suggestions based on requirements
     */
    public function generateBudgetSuggestion(BudgetRequestDTO $request): array
    {
        try {
            $prompt = $this->formatBudgetRequest($request);
            $maxTokens = config('openai.max_tokens', 2000);

            $response = $this->client->chat()->create([
                'model' => $this->model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'Eres un experto en estimación de presupuestos y consultoría financiera. Proporciona sugerencias de presupuesto detalladas y realistas basadas en los requisitos proporcionados. Formatea tu respuesta como un JSON estructurado con items, cantidades, precios unitarios y descripciones.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'temperature' => 0.7,
                'max_tokens' => $maxTokens,
            ]);

            return $this->parseResponse($response);
        } catch (Exception $e) {
            Log::error('AI budget suggestion error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Analyze budget requirements from description
     */
    public function analyzeBudgetRequirements(string $description): array
    {
        try {
            $maxTokens = config('openai.max_tokens', 1500);

            $response = $this->client->chat()->create([
                'model' => $this->model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'Eres un analista de negocios experto. Analiza la descripción del proyecto y extrae los requisitos clave, entregables y recursos necesarios. Devuelve un JSON estructurado con requisitos categorizados por tipo.'
                    ],
                    [
                        'role' => 'user',
                        'content' => "Analiza este proyecto y extrae los requisitos:\n\n{$description}"
                    ]
                ],
                'temperature' => 0.5,
                'max_tokens' => $maxTokens,
            ]);

            return $this->parseResponse($response);
        } catch (Exception $e) {
            Log::error('AI requirements analysis error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Estimate costs based on requirements
     */
    public function estimateCosts(array $requirements): array
    {
        try {
            $requirementsText = json_encode($requirements, JSON_PRETTY_PRINT);
            $maxTokens = config('openai.max_tokens', 1500);

            $response = $this->client->chat()->create([
                'model' => $this->model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'Eres un experto en estimación de costes. Basándote en los requisitos proporcionados, estima costes realistas incluyendo mano de obra, materiales y gastos generales. Proporciona costes itemizados con justificaciones.'
                    ],
                    [
                        'role' => 'user',
                        'content' => "Estima los costes para estos requisitos:\n\n{$requirementsText}"
                    ]
                ],
                'temperature' => 0.6,
                'max_tokens' => $maxTokens,
            ]);

            return $this->parseResponse($response);
        } catch (Exception $e) {
            Log::error('AI cost estimation error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Generate item descriptions for budget items
     */
    public function generateItemDescription(string $itemName, array $context = []): string
    {
        try {
            $contextText = !empty($context) ? "\nContexto: " . json_encode($context) : '';

            $response = $this->client->chat()->create([
                'model' => $this->model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'Eres un redactor profesional. Crea descripciones claras y concisas para items de presupuesto que expliquen qué está incluido y el valor proporcionado.'
                    ],
                    [
                        'role' => 'user',
                        'content' => "Genera una descripción profesional para este item de presupuesto: {$itemName}{$contextText}"
                    ]
                ],
                'temperature' => 0.7,
                'max_tokens' => 300,
            ]);

            $content = $response->choices[0]->message->content ?? '';
            return trim($content);
        } catch (Exception $e) {
            Log::error('AI description generation error: ' . $e->getMessage());
            return '';
        }
    }

    /**
     * Format budget request into a prompt
     */
    protected function formatBudgetRequest(BudgetRequestDTO $request): string
    {
        $prompt = "Generate a detailed budget estimate for the following:\n\n";
        $prompt .= "Description: {$request->description}\n";

        if ($request->requirements) {
            $prompt .= "\nRequirements:\n";
            foreach ($request->requirements as $requirement) {
                $prompt .= "- {$requirement}\n";
            }
        }

        if ($request->budget) {
            $prompt .= "\nMaximum Budget: $" . number_format($request->budget, 2) . "\n";
        }

        if ($request->industry) {
            $prompt .= "\nIndustry: {$request->industry}\n";
        }

        if ($request->timeframe) {
            $prompt .= "\nTimeframe: {$request->timeframe}\n";
        }

        if ($request->context) {
            $prompt .= "\nAdditional Context: " . json_encode($request->context) . "\n";
        }

        $prompt .= "\nProvide a structured budget with items, quantities, unit prices, and total costs.";

        return $prompt;
    }

    /**
     * Parse OpenAI API response
     */
    protected function parseResponse($response): array
    {
        $content = $response->choices[0]->message->content ?? '';

        // Try to parse as JSON first
        $decoded = json_decode($content, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            return $decoded;
        }

        // If not JSON, return structured response
        return [
            'raw_content' => $content,
            'parsed' => $this->extractStructuredData($content)
        ];
    }

    /**
     * Extract structured data from text response
     */
    protected function extractStructuredData(string $content): array
    {
        // Basic extraction logic - can be enhanced
        return [
            'content' => $content,
            'type' => 'text'
        ];
    }
}
