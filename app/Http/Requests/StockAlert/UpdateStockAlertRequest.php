<?php

declare(strict_types=1);

namespace App\Http\Requests\StockAlert;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStockAlertRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'branch_id' => ['sometimes', 'uuid', 'exists:branches,id'],
            'product_id' => ['sometimes', 'uuid', 'exists:products,id'],
            'alert_type' => ['sometimes', 'string', 'in:low_stock,out_of_stock,overstock,expiring_soon'],
            'threshold_quantity' => ['sometimes', 'numeric', 'min:0'],
            'current_quantity' => ['sometimes', 'numeric', 'min:0'],
            'priority' => ['sometimes', 'integer', 'min:1', 'max:5'],
            'status' => ['sometimes', 'string', 'in:active,resolved'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
