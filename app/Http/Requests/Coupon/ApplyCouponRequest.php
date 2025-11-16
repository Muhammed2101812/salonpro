<?php

declare(strict_types=1);

namespace App\Http\Requests\Coupon;

use Illuminate\Foundation\Http\FormRequest;

class ApplyCouponRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\CouponUsage::class);
    }

    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'max:50'],
            'customer_id' => ['required', 'uuid', 'exists:customers,id'],
            'amount' => ['required', 'numeric', 'min:0'],
            'appointment_id' => ['nullable', 'uuid', 'exists:appointments,id'],
            'sale_id' => ['nullable', 'uuid', 'exists:sales,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => 'Coupon code is required',
            'customer_id.required' => 'Customer is required',
            'customer_id.exists' => 'Selected customer does not exist',
            'amount.required' => 'Amount is required',
            'amount.min' => 'Amount must be greater than or equal to 0',
            'appointment_id.exists' => 'Selected appointment does not exist',
            'sale_id.exists' => 'Selected sale does not exist',
        ];
    }
}
