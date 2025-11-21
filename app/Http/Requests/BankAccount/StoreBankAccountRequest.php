<?php

declare(strict_types=1);

namespace App\Http\Requests\BankAccount;

use Illuminate\Foundation\Http\FormRequest;

class StoreBankAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'branch_id' => ['required', 'uuid', 'exists:branches,id'],
            'bank_name' => ['required', 'string', 'max:255'],
            'account_name' => ['required', 'string', 'max:255'],
            'account_number' => ['required', 'string', 'max:100', 'unique:bank_accounts,account_number'],
            'iban' => ['nullable', 'string', 'max:50', 'unique:bank_accounts,iban'],
            'swift_code' => ['nullable', 'string', 'max:20'],
            'currency' => ['required', 'string', 'max:3'],
            'current_balance' => ['nullable', 'numeric'],
            'is_active' => ['sometimes', 'boolean'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
