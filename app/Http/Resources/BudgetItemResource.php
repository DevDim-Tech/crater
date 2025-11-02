<?php

namespace Crater\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BudgetItemResource extends JsonResource
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
            'name' => $this->name,
            'description' => $this->description,
            'discount_type' => $this->discount_type,
            'price' => $this->price,
            'quantity' => $this->quantity,
            'discount' => $this->discount,
            'discount_val' => $this->discount_val,
            'tax' => $this->tax,
            'total' => $this->total,
            'ai_generated' => $this->ai_generated,
            'budget_id' => $this->budget_id,
            'item_id' => $this->item_id,
            'company_id' => $this->company_id,
            'taxes' => $this->when($this->taxes()->exists(), function () {
                return TaxResource::collection($this->taxes);
            }),
            'item' => $this->when($this->item()->exists(), function () {
                return new ItemResource($this->item);
            }),
        ];
    }
}
