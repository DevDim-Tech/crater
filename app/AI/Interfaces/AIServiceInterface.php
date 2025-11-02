<?php

namespace Crater\AI\Interfaces;

use Crater\AI\DTOs\BudgetRequestDTO;

interface AIServiceInterface
{
    /**
     * Generate budget suggestions based on requirements
     *
     * @param BudgetRequestDTO $request
     * @return array
     */
    public function generateBudgetSuggestion(BudgetRequestDTO $request): array;

    /**
     * Analyze budget requirements from description
     *
     * @param string $description
     * @return array
     */
    public function analyzeBudgetRequirements(string $description): array;

    /**
     * Estimate costs based on requirements
     *
     * @param array $requirements
     * @return array
     */
    public function estimateCosts(array $requirements): array;

    /**
     * Generate item descriptions for budget items
     *
     * @param string $itemName
     * @param array $context
     * @return string
     */
    public function generateItemDescription(string $itemName, array $context = []): string;
}
