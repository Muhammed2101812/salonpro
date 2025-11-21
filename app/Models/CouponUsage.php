<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CouponUsage extends Model
{
    use HasFactory;
    use HasUuid;

    protected $fillable = [
        'coupon_id',
        'customer_id',
        'appointment_id',
        'sale_id',
        'used_at',
        'discount_amount',
        'original_amount',
        'final_amount',
    ];

    protected $casts = [
        'used_at' => 'datetime',
        'discount_amount' => 'decimal:2',
        'original_amount' => 'decimal:2',
        'final_amount' => 'decimal:2',
    ];

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }
}
