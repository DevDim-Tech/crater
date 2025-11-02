<?php

namespace Tests\Feature\AI\Services;

use Tests\TestCase;
use Crater\AI\Services\OpenAIService;
use Crater\AI\DTOs\BudgetRequestDTO;
use Crater\AI\Interfaces\AIServiceInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OpenAIServiceTest extends TestCase
{
    /**
     * Test that AI service can be instantiated
     */
    public function test_ai_service_can_be_instantiated()
    {
        if (!env('OPENAI_API_KEY')) {
            $this->markTestSkipped('OpenAI API key not configured');
        }

        $service = app(AIServiceInterface::class);
        $this->assertInstanceOf(OpenAIService::class, $service);
    }

    /**
     * Test budget request DTO creation
     */
    public function test_budget_request_dto_can_be_created()
    {
        $dto = new BudgetRequestDTO(
            description: 'Website development project',
            requirements: ['Frontend', 'Backend', 'Database'],
            budget: 10000.00,
            industry: 'Technology',
            timeframe: '3 months'
        );

        $this->assertEquals('Website development project', $dto->description);
        $this->assertCount(3, $dto->requirements);
        $this->assertEquals(10000.00, $dto->budget);
    }

    /**
     * Test budget request DTO from array
     */
    public function test_budget_request_dto_from_array()
    {
        $data = [
            'description' => 'Mobile app development',
            'requirements' => ['iOS', 'Android'],
            'budget' => 15000,
            'industry' => 'Technology',
            'timeframe' => '6 months'
        ];

        $dto = BudgetRequestDTO::fromArray($data);

        $this->assertEquals('Mobile app development', $dto->description);
        $this->assertIsArray($dto->requirements);
        $this->assertEquals(15000.0, $dto->budget);
    }

    /**
     * Test DTO to array conversion
     */
    public function test_dto_to_array_conversion()
    {
        $dto = new BudgetRequestDTO(
            description: 'Test project',
            requirements: ['Requirement 1'],
            budget: 5000.00
        );

        $array = $dto->toArray();

        $this->assertIsArray($array);
        $this->assertArrayHasKey('description', $array);
        $this->assertArrayHasKey('requirements', $array);
        $this->assertArrayHasKey('budget', $array);
    }
}

