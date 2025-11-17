<?php

declare(strict_types=1);

namespace App\Http\Requests\Webhook;

use Illuminate\Foundation\Http\FormRequest;

class UpdateWebhookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'branch_id' => ['sometimes', 'uuid', 'exists:branches,id'],
            'name' => ['sometimes', 'string', 'max:255'],
            'url' => ['sometimes', 'url', 'max:500'],
            'events' => ['sometimes', 'array', 'min:1'],
            'events.*' => ['required_with:events', 'string'],
            'secret' => ['nullable', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
            'timeout' => ['sometimes', 'integer', 'min:1', 'max:120'],
            'max_retries' => ['sometimes', 'integer', 'min:0', 'max:5'],
            'headers' => ['nullable', 'array'],
            'headers.*' => ['nullable', 'string'],
        ];
    }
}
