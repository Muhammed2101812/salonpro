<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInventoryMovementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('inventory.update') ?? false;
    }

    public function rules(): array
    {
        return [
            'reason' => ['nullable', 'string', 'max:1000'],
            'movement_date' => ['sometimes', 'required', 'date'],
        ];
    }

    public function attributes(): array
    {
        return [
            'reason' => 'açıklama',
            'movement_date' => 'hareket tarihi',
        ];
    }
}
