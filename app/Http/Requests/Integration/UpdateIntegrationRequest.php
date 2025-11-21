<?php

declare(strict_types=1);

namespace App\Http\Requests\Integration;

use Illuminate\Foundation\Http\FormRequest;

class UpdateIntegrationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'branch_id' => ['sometimes', 'uuid', 'exists:branches,id'],
            'integration_name' => ['sometimes', 'string', 'max:255'],
            'integration_type' => ['sometimes', 'string', 'in:payment,sms,email,calendar,accounting,crm'],
            'provider' => ['sometimes', 'string', 'max:100'],
            'credentials' => ['sometimes', 'array'],
            'settings' => ['nullable', 'array'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
