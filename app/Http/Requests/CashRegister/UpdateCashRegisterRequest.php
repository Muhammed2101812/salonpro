<?php

declare(strict_types=1);

namespace App\Http\Requests\CashRegister;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCashRegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'branch_id' => ['sometimes', 'required', 'uuid', 'exists:branches,id'],
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:500'],
            'opening_balance' => ['nullable', 'numeric', 'min:0'],
            'current_balance' => ['nullable', 'numeric', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
            'location' => ['nullable', 'string', 'max:255'],
        ];
    }
}
