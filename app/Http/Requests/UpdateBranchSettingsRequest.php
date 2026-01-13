<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBranchSettingsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('branches.update_settings') ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'settings' => ['required', 'array'],
            'settings.*.key' => ['required', 'string', 'max:255'],
            'settings.*.value' => ['required'],
            'settings.*.type' => ['sometimes', 'string', 'in:string,integer,float,boolean,json,array'],
            'settings.*.group' => ['sometimes', 'string', 'max:100'],
            'settings.*.is_encrypted' => ['sometimes', 'boolean'],
        ];
    }

    /**
     * Get custom validation messages
     */
    public function messages(): array
    {
        return [
            'settings.required' => 'Ayarlar gereklidir.',
            'settings.array' => 'Ayarlar bir dizi olmalıdır.',
            'settings.*.key.required' => 'Ayar anahtarı gereklidir.',
            'settings.*.value.required' => 'Ayar değeri gereklidir.',
            'settings.*.type.in' => 'Geçersiz ayar tipi.',
        ];
    }
}
