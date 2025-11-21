<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateNotificationTemplateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $templateId = $this->route('notification_template');

        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'slug' => ['sometimes', 'string', 'max:255', Rule::unique('notification_templates', 'slug')->ignore($templateId)],
            'channel' => ['sometimes', 'in:email,sms,push,whatsapp'],
            'event_type' => ['sometimes', 'string', 'max:255'],
            'subject' => ['nullable', 'string', 'max:255'],
            'body' => ['sometimes', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
