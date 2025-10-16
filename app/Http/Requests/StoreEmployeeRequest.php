<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'branch_id' => ['required', 'uuid', 'exists:branches,id'],
            'user_id' => ['nullable', 'uuid', 'exists:users,id'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20', 'unique:employees,phone'],
            'email' => ['nullable', 'email', 'max:255'],
            'specialties' => ['nullable', 'array'],
            'specialties.*' => ['string', 'max:100'],
            'commission_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'is_active' => ['boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'branch_id' => 'şube',
            'user_id' => 'kullanıcı',
            'first_name' => 'ad',
            'last_name' => 'soyad',
            'phone' => 'telefon',
            'email' => 'e-posta',
            'specialties' => 'uzmanlık alanları',
            'commission_rate' => 'komisyon oranı',
            'is_active' => 'aktif durumu',
        ];
    }
}
