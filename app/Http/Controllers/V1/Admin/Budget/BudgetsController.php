<?php

namespace Crater\Http\Controllers\V1\Admin\Budget;

use Crater\Http\Controllers\Controller;
use Crater\Http\Requests\BudgetRequest;
use Crater\Http\Requests\DeleteBudgetRequest;
use Crater\Http\Resources\BudgetResource;
use Crater\Models\Budget;
use Crater\AI\Interfaces\AIServiceInterface;
use Crater\AI\DTOs\BudgetRequestDTO;
use Illuminate\Http\Request;

class BudgetsController extends Controller
{
    protected $aiService;

    public function __construct(AIServiceInterface $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Budget::class);

        $limit = $request->has('limit') ? $request->limit : 10;

        $budgets = Budget::whereCompany()
            ->with(['customer', 'creator'])
            ->applyFilters($request->all())
            ->latest()
            ->paginateData($limit);

        return (BudgetResource::collection($budgets))
            ->additional(['meta' => [
                'budget_total_count' => Budget::whereCompany()->count(),
                'draft_count' => Budget::whereCompany()->whereStatus(Budget::STATUS_DRAFT)->count(),
                'sent_count' => Budget::whereCompany()->whereStatus(Budget::STATUS_SENT)->count(),
                'accepted_count' => Budget::whereCompany()->whereStatus(Budget::STATUS_ACCEPTED)->count(),
            ]]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  BudgetRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(BudgetRequest $request)
    {
        $this->authorize('create', Budget::class);

        $budget = Budget::createBudget($request);

        return new BudgetResource($budget);
    }

    /**
     * Display the specified resource.
     *
     * @param  Budget $budget
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, Budget $budget)
    {
        $this->authorize('view', $budget);

        // Update status if expired
        $budget->updateStatus();

        // Mark as viewed if accessed via public link
        if ($request->has('hash') && $request->hash === $budget->unique_hash) {
            $budget->markAsViewed();
        }

        return new BudgetResource($budget);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  BudgetRequest $request
     * @param  Budget $budget
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(BudgetRequest $request, Budget $budget)
    {
        $this->authorize('update', $budget);

        $budget = Budget::updateBudget($request, $budget);

        return new BudgetResource($budget);
    }

    /**
     * Delete the specified resources in storage.
     *
     * @param  DeleteBudgetRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(DeleteBudgetRequest $request)
    {
        $this->authorize('delete multiple budgets');

        Budget::deleteAllBudgets($request->ids);

        return response()->json([
            'success' => true,
        ]);
    }

    /**
     * Generate budget suggestion using AI
     *
     * @param  Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateWithAI(Request $request)
    {
        $this->authorize('create', Budget::class);

        $request->validate([
            'description' => 'required|string',
            'requirements' => 'nullable|array',
            'budget' => 'nullable|numeric',
            'industry' => 'nullable|string',
            'timeframe' => 'nullable|string',
            'context' => 'nullable|array',
        ]);

        $dto = BudgetRequestDTO::fromArray($request->all());

        try {
            $aiSuggestion = $this->aiService->generateBudgetSuggestion($dto);

            return response()->json([
                'success' => true,
                'suggestion' => $aiSuggestion,
                'dto' => $dto->toArray(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al generar sugerencia de presupuesto con IA',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Analyze budget requirements using AI
     *
     * @param  Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function analyzeRequirements(Request $request)
    {
        $this->authorize('create', Budget::class);

        $request->validate([
            'description' => 'required|string',
            'requirements' => 'nullable|array',
            'budget' => 'nullable|numeric',
            'industry' => 'nullable|string',
            'timeframe' => 'nullable|string',
            'context' => 'nullable|array',
        ]);

        $dto = BudgetRequestDTO::fromArray($request->all());

        try {
            $analysis = $this->aiService->analyzeBudgetRequirements($dto);

            return response()->json([
                'success' => true,
                'analysis' => $analysis,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al analizar requisitos con IA',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Estimate costs using AI
     *
     * @param  Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function estimateCosts(Request $request)
    {
        $this->authorize('create', Budget::class);

        $request->validate([
            'description' => 'required|string',
            'requirements' => 'nullable|array',
            'budget' => 'nullable|numeric',
            'industry' => 'nullable|string',
            'timeframe' => 'nullable|string',
            'context' => 'nullable|array',
        ]);

        $dto = BudgetRequestDTO::fromArray($request->all());

        try {
            $estimation = $this->aiService->estimateCosts($dto);

            return response()->json([
                'success' => true,
                'estimation' => $estimation,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al estimar costos con IA',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
