<?php

namespace Crater\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BudgetResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'budget_date' => $this->budget_date,
            'expiry_date' => $this->expiry_date,
            'budget_number' => $this->budget_number,
            'reference_number' => $this->reference_number,
            'status' => $this->status,
            'tax_per_item' => $this->tax_per_item,
            'discount_per_item' => $this->discount_per_item,
            'notes' => $this->notes,
            'discount_type' => $this->discount_type,
            'discount' => $this->discount,
            'discount_val' => $this->discount_val,
            'sub_total' => $this->sub_total,
            'total' => $this->total,
            'tax' => $this->tax,
            'sent' => $this->sent,
            'viewed' => $this->viewed,
            'unique_hash' => $this->unique_hash,
            'template_name' => $this->template_name ?? 'budget1',
            
            // AI Integration fields
            'ai_generated' => $this->ai_generated,
            'ai_context' => $this->ai_context,
            'ai_description' => $this->ai_description,
            'industry' => $this->industry,
            'timeframe' => $this->timeframe,
            
            // Foreign keys
            'customer_id' => $this->customer_id,
            'user_id' => $this->user_id,
            'creator_id' => $this->creator_id,
            'company_id' => $this->company_id,
            'currency_id' => $this->currency_id,
            
            // Formatted dates
            'formatted_created_at' => $this->formattedCreatedAt,
            'formatted_budget_date' => $this->formattedBudgetDate,
            'formatted_expiry_date' => $this->formattedExpiryDate,
            
            // Computed attributes
            'budget_pdf_url' => $this->budgetPdfUrl,
            'is_expired' => $this->isExpired,
            
            // Relationships
            'items' => $this->when($this->items()->exists(), function () {
                return BudgetItemResource::collection($this->items);
            }),
            'customer' => $this->when($this->customer()->exists(), function () {
                return new CustomerResource($this->customer);
            }),
            'creator' => $this->when($this->creator()->exists(), function () {
                return new UserResource($this->creator);
            }),
            'user' => $this->when($this->user()->exists(), function () {
                return new UserResource($this->user);
            }),
            'company' => $this->when($this->company()->exists(), function () {
                return new CompanyResource($this->company);
            }),
            'currency' => $this->when($this->currency()->exists(), function () {
                return new CurrencyResource($this->currency);
            }),
        ];
    }
}
