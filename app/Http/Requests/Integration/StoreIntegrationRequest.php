<?php

declare(strict_types=1);

namespace App\Http\Requests\Integration;

use Illuminate\Foundation\Http\FormRequest;

class StoreIntegrationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'branch_id' => ['nullable', 'uuid', 'exists:branches,id'],
            'integration_name' => ['required', 'string', 'max:255'],
            'integration_type' => ['required', 'string', 'in:payment,sms,email,calendar,accounting,crm'],
            'provider' => ['required', 'string', 'max:100'],
            'credentials' => ['required', 'array'],
            'settings' => ['nullable', 'array'],
            'is_active' => ['sometimes', 'boolean'],
            'configured_by' => ['sometimes', 'uuid', 'exists:users,id'],
        ];
    }
}
