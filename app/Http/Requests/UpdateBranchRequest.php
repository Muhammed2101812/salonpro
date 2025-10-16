<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBranchRequest extends FormRequest
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
        $branchId = $this->route('branch'); // Get the branch ID from route parameter

        return [
            'name' => ['sometimes', 'array'],
            'name.tr' => ['required_with:name', 'string', 'max:255'],
            'name.en' => ['required_with:name', 'string', 'max:255'],
            'code' => ['sometimes', 'string', 'max:50', 'unique:branches,code,'.$branchId],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
            'city' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'size:2'],
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
            'name.tr' => 'branch name (Turkish)',
            'name.en' => 'branch name (English)',
            'code' => 'branch code',
            'phone' => 'phone number',
            'email' => 'email address',
            'address' => 'address',
            'city' => 'city',
            'country' => 'country code',
            'is_active' => 'active status',
        ];
    }
}
