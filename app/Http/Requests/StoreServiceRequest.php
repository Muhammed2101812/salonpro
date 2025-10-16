<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreServiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // TODO: Add authorization logic based on roles/permissions
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'service_category_id' => ['required', 'uuid', 'exists:service_categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'price' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'duration_minutes' => ['required', 'integer', 'min:1', 'max:1440'],
            'is_active' => ['boolean'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'service_category_id' => 'hizmet kategorisi',
            'name' => 'hizmet adı',
            'description' => 'açıklama',
            'price' => 'fiyat',
            'duration_minutes' => 'süre',
            'is_active' => 'aktif durumu',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'service_category_id.required' => ':attribute alanı zorunludur.',
            'service_category_id.exists' => 'Seçilen :attribute geçerli değil.',
            'price.min' => ':attribute en az :min olmalıdır.',
            'price.max' => ':attribute en fazla :max olabilir.',
            'duration_minutes.min' => ':attribute en az :min dakika olmalıdır.',
            'duration_minutes.max' => ':attribute en fazla :max dakika olabilir.',
        ];
    }
}
