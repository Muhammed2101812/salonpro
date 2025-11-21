<?php

declare(strict_types=1);

namespace App\Http\Requests\Coupon;

use Illuminate\Foundation\Http\FormRequest;

class ValidateCouponRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Public endpoint for validation
    }

    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'max:50'],
            'customer_id' => ['nullable', 'uuid', 'exists:customers,id'],
            'amount' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => 'Coupon code is required',
            'customer_id.exists' => 'Selected customer does not exist',
            'amount.min' => 'Amount must be greater than or equal to 0',
        ];
    }
}
