<?php

namespace Crater\AI\DTOs;

class BudgetRequestDTO
{
    /**
     * Create a new BudgetRequestDTO instance
     *
     * @param string $description Main description of the budget/project
     * @param array|null $requirements List of specific requirements
     * @param float|null $budget Maximum budget available
     * @param string|null $industry Industry or sector
     * @param string|null $timeframe Expected timeframe for completion
     * @param array|null $context Additional context information
     */
    public function __construct(
        public string $description,
        public ?array $requirements = null,
        public ?float $budget = null,
        public ?string $industry = null,
        public ?string $timeframe = null,
        public ?array $context = null
    ) {}

    /**
     * Create DTO from array
     *
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            description: $data['description'] ?? '',
            requirements: $data['requirements'] ?? null,
            budget: isset($data['budget']) ? (float) $data['budget'] : null,
            industry: $data['industry'] ?? null,
            timeframe: $data['timeframe'] ?? null,
            context: $data['context'] ?? null
        );
    }

    /**
     * Convert DTO to array
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'description' => $this->description,
            'requirements' => $this->requirements,
            'budget' => $this->budget,
            'industry' => $this->industry,
            'timeframe' => $this->timeframe,
            'context' => $this->context,
        ];
    }
}
