<?php

namespace Crater\Models;

use Carbon\Carbon;
use Crater\Traits\GeneratesPdfTrait;
use Crater\Traits\HasCustomFieldsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Budget extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;
    use GeneratesPdfTrait;
    use HasCustomFieldsTrait;

    // Status constants
    public const STATUS_DRAFT = 'DRAFT';
    public const STATUS_SENT = 'SENT';
    public const STATUS_VIEWED = 'VIEWED';
    public const STATUS_EXPIRED = 'EXPIRED';
    public const STATUS_ACCEPTED = 'ACCEPTED';
    public const STATUS_REJECTED = 'REJECTED';
    public const STATUS_CONVERTED = 'CONVERTED'; // Converted to invoice

    protected $dates = [
        'created_at',
        'updated_at',
        'budget_date',
        'expiry_date'
    ];

    protected $casts = [
        'total' => 'integer',
        'tax' => 'integer',
        'sub_total' => 'integer',
        'discount' => 'float',
        'discount_val' => 'integer',
        'ai_generated' => 'boolean',
        'sent' => 'boolean',
        'viewed' => 'boolean',
        'ai_context' => 'array',
    ];

    protected $guarded = [
        'id',
    ];

    protected $appends = [
        'formattedCreatedAt',
        'formattedBudgetDate',
        'formattedExpiryDate',
        'budgetPdfUrl',
        'isExpired',
    ];

    /**
     * Relationships
     */
    public function items()
    {
        return $this->hasMany(BudgetItem::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    /**
     * Accessors
     */
    public function getFormattedCreatedAtAttribute()
    {
        $dateFormat = CompanySetting::getSetting('carbon_date_format', $this->company_id);

        return Carbon::parse($this->created_at)->translatedFormat($dateFormat);
    }

    public function getFormattedBudgetDateAttribute()
    {
        $dateFormat = CompanySetting::getSetting('carbon_date_format', $this->company_id);

        return Carbon::parse($this->budget_date)->translatedFormat($dateFormat);
    }

    public function getFormattedExpiryDateAttribute()
    {
        if (!$this->expiry_date) {
            return null;
        }

        $dateFormat = CompanySetting::getSetting('carbon_date_format', $this->company_id);

        return Carbon::parse($this->expiry_date)->translatedFormat($dateFormat);
    }

    public function getBudgetPdfUrlAttribute()
    {
        return url('/budgets/pdf/'.$this->unique_hash);
    }

    public function getIsExpiredAttribute()
    {
        if (!$this->expiry_date) {
            return false;
        }

        return Carbon::now()->isAfter($this->expiry_date) && $this->status !== self::STATUS_ACCEPTED && $this->status !== self::STATUS_CONVERTED;
    }

    /**
     * Scopes
     */
    public function scopeWhereStatus($query, $status)
    {
        return $query->where('budgets.status', $status);
    }

    public function scopeWhereBudgetNumber($query, $budgetNumber)
    {
        return $query->where('budgets.budget_number', 'LIKE', '%'.$budgetNumber.'%');
    }

    public function scopeWhereSearch($query, $search)
    {
        return $query->where('budgets.budget_number', 'LIKE', '%'.$search.'%');
    }

    public function scopeAiGenerated($query)
    {
        return $query->where('budgets.ai_generated', true);
    }

    public function scopeWhereBudget($query, $budget_id)
    {
        return $query->orWhere('budgets.id', $budget_id);
    }

    public function scopeWhereCompany($query, $company_id)
    {
        return $query->where('budgets.company_id', $company_id);
    }

    public function scopeBudgetsBetween($query, $start, $end)
    {
        return $query->whereBetween(
            'budgets.budget_date',
            [$start->format('Y-m-d'), $end->format('Y-m-d')]
        );
    }

    public function scopeWhereOrder($query, $orderByField, $orderBy)
    {
        $query->orderBy($orderByField, $orderBy);
    }

    public function scopeApplyFilters($query, array $filters)
    {
        $filters = collect($filters);

        if ($filters->get('status')) {
            $query->whereStatus($filters->get('status'));
        }

        if ($filters->get('budget_number')) {
            $query->whereBudgetNumber($filters->get('budget_number'));
        }

        if ($filters->get('from_date') && $filters->get('to_date')) {
            $start = Carbon::createFromFormat('Y-m-d', $filters->get('from_date'));
            $end = Carbon::createFromFormat('Y-m-d', $filters->get('to_date'));
            $query->budgetsBetween($start, $end);
        }

        if ($filters->get('budget_id')) {
            $query->whereBudget($filters->get('budget_id'));
        }

        if ($filters->get('ai_generated')) {
            $query->aiGenerated();
        }

        if ($filters->get('search')) {
            $query->whereSearch($filters->get('search'));
        }

        if ($filters->get('orderByField') || $filters->get('orderBy')) {
            $field = $filters->get('orderByField') ? $filters->get('orderByField') : 'budget_number';
            $orderBy = $filters->get('orderBy') ? $filters->get('orderBy') : 'asc';
            $query->whereOrder($field, $orderBy);
        }
    }

    /**
     * Business Logic Methods
     */
    public function generateBudgetNumber($setting = 'budget')
    {
        $count = self::where('company_id', $this->company_id)->count();
        $prefix = CompanySetting::getSetting('budget_prefix', $this->company_id) ?? 'PRES';
        $padZero = CompanySetting::getSetting('budget_auto_generate', $this->company_id);
        $number = $count + 1;

        $budgetNumber = $prefix.'-'.sprintf('%06d', $number);

        $this->budget_number = $budgetNumber;
        $this->save();

        return $budgetNumber;
    }

    public function calculateTotals()
    {
        $this->sub_total = $this->items->sum('total');
        $this->total = $this->sub_total + $this->tax - ($this->discount_val ?? 0);
        $this->save();

        return $this;
    }

    public function updateStatus()
    {
        if ($this->expiry_date && Carbon::now()->isAfter($this->expiry_date)) {
            if ($this->status !== self::STATUS_ACCEPTED && $this->status !== self::STATUS_CONVERTED) {
                $this->status = self::STATUS_EXPIRED;
                $this->save();
            }
        }

        return $this;
    }

    public function markAsViewed()
    {
        if (!$this->viewed) {
            $this->viewed = true;
            if ($this->status === self::STATUS_SENT) {
                $this->status = self::STATUS_VIEWED;
            }
            $this->save();
        }

        return $this;
    }

    public function markAsAccepted()
    {
        $this->status = self::STATUS_ACCEPTED;
        $this->save();

        return $this;
    }

    public function markAsRejected()
    {
        $this->status = self::STATUS_REJECTED;
        $this->save();

        return $this;
    }

    public function convertToInvoice()
    {
        // This will be implemented when we integrate with invoices
        $this->status = self::STATUS_CONVERTED;
        $this->save();

        return $this;
    }

    /**
     * Static Methods
     */
    public static function createBudget($request)
    {
        $data = $request->validated();

        $budget = self::create($data);
        $budget->generateBudgetNumber();
        $budget->unique_hash = str_random(60);
        $budget->save();

        self::createItems($budget, $request->items);

        if ($request->has('taxes')) {
            self::createTaxes($budget, $request->taxes);
        }

        $budget = $budget->fresh();
        $budget->calculateTotals();

        return $budget;
    }

    public static function updateBudget($request, $budget)
    {
        $data = $request->validated();

        $budget->update($data);

        $budget->items()->delete();
        self::createItems($budget, $request->items);

        if ($request->has('taxes')) {
            $budget->taxes()->delete();
            self::createTaxes($budget, $request->taxes);
        }

        $budget = $budget->fresh();
        $budget->calculateTotals();

        return $budget;
    }

    public static function createItems($budget, $items)
    {
        foreach ($items as $item) {
            $item['company_id'] = $budget->company_id;
            $budgetItem = $budget->items()->create($item);
        }
    }

    public static function createTaxes($budget, $taxes)
    {
        foreach ($taxes as $tax) {
            $tax['company_id'] = $budget->company_id;
            $budget->taxes()->create($tax);
        }
    }

    public static function deleteAllBudgets($ids)
    {
        foreach ($ids as $id) {
            $budget = self::find($id);

            if ($budget) {
                $budget->delete();
            }
        }

        return true;
    }
}
