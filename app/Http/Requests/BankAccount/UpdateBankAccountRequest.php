<?php

declare(strict_types=1);

namespace App\Http\Requests\BankAccount;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBankAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $bankAccountId = $this->route('bank_account');

        return [
            'branch_id' => ['sometimes', 'required', 'uuid', 'exists:branches,id'],
            'bank_name' => ['sometimes', 'required', 'string', 'max:255'],
            'account_name' => ['sometimes', 'required', 'string', 'max:255'],
            'account_number' => ['sometimes', 'required', 'string', 'max:100', "unique:bank_accounts,account_number,{$bankAccountId}"],
            'iban' => ['nullable', 'string', 'max:50', "unique:bank_accounts,iban,{$bankAccountId}"],
            'swift_code' => ['nullable', 'string', 'max:20'],
            'currency' => ['sometimes', 'required', 'string', 'max:3'],
            'current_balance' => ['nullable', 'numeric'],
            'is_active' => ['sometimes', 'boolean'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
