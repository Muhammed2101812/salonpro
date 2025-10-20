<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServicePriceHistory extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'service_price_history';

    protected $fillable = [
        'service_id',
        'old_price',
        'new_price',
        'price_change',
        'price_change_percentage',
        'changed_by',
        'reason',
        'changed_at',
    ];

    protected function casts(): array
    {
        return [
            'old_price' => 'decimal:2',
            'new_price' => 'decimal:2',
            'price_change' => 'decimal:2',
            'price_change_percentage' => 'decimal:2',
            'changed_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    /**
     * Check if price increased
     */
    public function isPriceIncrease(): bool
    {
        return $this->price_change > 0;
    }

    /**
     * Check if price decreased
     */
    public function isPriceDecrease(): bool
    {
        return $this->price_change < 0;
    }

    /**
     * Get formatted price change with sign
     */
    public function getFormattedPriceChange(): string
    {
        $sign = $this->price_change >= 0 ? '+' : '';
        return $sign . number_format($this->price_change, 2) . ' TL';
    }
}
