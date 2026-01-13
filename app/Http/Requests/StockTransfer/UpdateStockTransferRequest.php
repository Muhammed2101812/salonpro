<?php

declare(strict_types=1);

namespace App\Http\Requests\StockTransfer;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStockTransferRequest extends FormRequest
{
    public function authorize(): bool
    {
        $transfer = $this->route('transfer');
        return $this->user()->can('update', $transfer);
    }

    public function rules(): array
    {
        return [
            'from_branch_id' => ['sometimes', 'uuid', 'exists:branches,id', 'different:to_branch_id'],
            'to_branch_id' => ['sometimes', 'uuid', 'exists:branches,id'],
            'product_variant_id' => ['sometimes', 'uuid', 'exists:product_variants,id'],
            'quantity' => ['sometimes', 'integer', 'min:1'],
            'transfer_date' => ['sometimes', 'date'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'from_branch_id.different' => 'Source and destination branches must be different',
            'quantity.min' => 'Quantity must be at least 1',
        ];
    }
}
