<?php

namespace Crater\Http\Requests;

use Crater\Models\Budget;
use Crater\Models\CompanySetting;
use Crater\Models\Customer;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BudgetRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'budget_date' => [
                'required',
                'date',
            ],
            'expiry_date' => [
                'nullable',
                'date',
                'after_or_equal:budget_date',
            ],
            'customer_id' => [
                'required',
                'exists:customers,id',
            ],
            'budget_number' => [
                'nullable',
                Rule::unique('budgets')->where('company_id', $this->header('company'))
            ],
            'status' => [
                'required',
                Rule::in([
                    Budget::STATUS_DRAFT,
                    Budget::STATUS_SENT,
                    Budget::STATUS_VIEWED,
                    Budget::STATUS_EXPIRED,
                    Budget::STATUS_ACCEPTED,
                    Budget::STATUS_REJECTED,
                    Budget::STATUS_CONVERTED,
                ]),
            ],
            'notes' => [
                'nullable',
                'string',
            ],
            'discount' => [
                'required',
            ],
            'discount_val' => [
                'required',
            ],
            'sub_total' => [
                'required',
            ],
            'total' => [
                'required',
            ],
            'tax' => [
                'required',
            ],
            'template_name' => [
                'nullable',
                'string',
            ],
            'items' => [
                'required',
                'array',
            ],
            'items.*.description' => [
                'nullable',
            ],
            'items.*.name' => [
                'required',
            ],
            'items.*.quantity' => [
                'required',
                'numeric',
                'min:0.01',
            ],
            'items.*.price' => [
                'required',
                'numeric',
                'min:0',
            ],
            'items.*.discount_val' => [
                'nullable',
                'numeric',
                'min:0',
            ],
            'items.*.tax' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            // AI Integration fields
            'ai_generated' => [
                'nullable',
                'boolean',
            ],
            'ai_context' => [
                'nullable',
                'array',
            ],
            'ai_description' => [
                'nullable',
                'string',
            ],
            'industry' => [
                'nullable',
                'string',
                'max:255',
            ],
            'timeframe' => [
                'nullable',
                'string',
                'max:255',
            ],
        ];

        if ($this->isMethod('PUT')) {
            $rules['budget_number'] = [
                'nullable',
                Rule::unique('budgets')->ignore($this->route('budget')->id, 'id')->where('company_id', $this->header('company'))
            ];
        }

        return $rules;
    }
}
