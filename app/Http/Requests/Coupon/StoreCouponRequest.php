<?php

namespace App\Http\Requests\Coupon;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCouponRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', 'unique:coupons,code'],
            'type' => ['required', Rule::in(['fixed', 'percent'])],
            'value' => [
                'required',
                'numeric',
                'min:0',
                $this->type === 'percent' ? 'max:100' : 'max:999999.99'
            ],
            'min_spend' => ['sometimes', 'numeric', 'min:0'],
            'usage_limit' => ['nullable', 'integer', 'min:1'],
            'user_limit' => ['nullable', 'integer', 'min:1'],
            'is_active' => ['boolean'],
            'starts_at' => ['sometimes', 'date'],
            'expires_at' => ['sometimes', 'date', 'after_or_equal:starts_at'],
        ];
    }

    /**
     * Custom messages for better UX
     */
    public function messages(): array
    {
        return [
            'expires_at.after_or_equal' => 'The expiration date must be a date after or equal to the start date.',
            'value.max' => $this->type === 'percent'
                ? 'Percentage discount cannot exceed 100%.'
                : 'The discount value is too large.',
        ];
    }
}
