<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SaleResource extends JsonResource
{
    public function toArray($request): array
    {
        return ['id' => $this->id, 'customer_id' => $this->customer_id, 'subtotal' => $this->subtotal, 'discount' => $this->discount, 'tax' => $this->tax, 'total' => $this->total, 'payment_method' => $this->payment_method, 'sale_date' => $this->sale_date?->format('Y-m-d'), 'created_at' => $this->created_at?->toISOString()];
    }
}
