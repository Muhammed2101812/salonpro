<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExpenseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'branch_id' => $this->branch_id,
            'category' => $this->category,
            'title' => $this->title,
            'description' => $this->description,
            'amount' => $this->amount,
            'expense_date' => $this->expense_date?->format('Y-m-d'),
            'payment_method' => $this->payment_method,
            'receipt_number' => $this->receipt_number,
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
