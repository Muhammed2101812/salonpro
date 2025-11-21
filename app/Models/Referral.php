<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Referral extends Model
{
    use HasFactory;
    use HasUuid;

    protected $fillable = [
        'referrer_id',
        'referred_id',
        'referral_code',
        'status',
        'reward_type',
        'reward_value',
        'completed_at',
        'rewarded_at',
    ];

    protected $casts = [
        'reward_value' => 'decimal:2',
        'completed_at' => 'datetime',
        'rewarded_at' => 'datetime',
    ];

    public function referrer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'referrer_id');
    }

    public function referred(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'referred_id');
    }
}
