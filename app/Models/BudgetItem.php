<?php

namespace Crater\Models;

use Carbon\Carbon;
use Crater\Traits\HasCustomFieldsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class BudgetItem extends Model
{
    use HasFactory;
    use HasCustomFieldsTrait;

    protected $guarded = [
        'id'
    ];

    protected $casts = [
        'price' => 'integer',
        'total' => 'integer',
        'discount' => 'float',
        'quantity' => 'float',
        'discount_val' => 'integer',
        'tax' => 'integer',
        'ai_generated' => 'boolean',
    ];

    /**
     * Relationships
     */
    public function budget()
    {
        return $this->belongsTo(Budget::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function taxes()
    {
        return $this->hasMany(Tax::class, 'budget_item_id');
    }

    /**
     * Scopes
     */
    public function scopeWhereCompany($query, $company_id)
    {
        return $query->where('company_id', $company_id);
    }

    public function scopeBudgetsBetween($query, $start, $end)
    {
        return $query->whereHas('budget', function ($query) use ($start, $end) {
            $query->whereBetween(
                'budget_date',
                [$start->format('Y-m-d'), $end->format('Y-m-d')]
            );
        });
    }

    public function scopeApplyBudgetFilters($query, array $filters)
    {
        $filters = collect($filters);

        if ($filters->get('from_date') && $filters->get('to_date')) {
            $start = Carbon::createFromFormat('Y-m-d', $filters->get('from_date'));
            $end = Carbon::createFromFormat('Y-m-d', $filters->get('to_date'));
            $query->budgetsBetween($start, $end);
        }
    }

    public function scopeItemAttributes($query)
    {
        return $query->select(
            DB::raw('sum(quantity) as total_quantity, sum(total) as total_amount, budget_items.name')
        )->groupBy('budget_items.name');
    }

    /**
     * Business Logic Methods
     */
    public function calculateTotal()
    {
        $subtotal = $this->price * $this->quantity;
        $discountAmount = $this->discount_val ?? 0;
        $this->total = $subtotal - $discountAmount + ($this->tax ?? 0);
        $this->save();

        return $this;
    }
}
