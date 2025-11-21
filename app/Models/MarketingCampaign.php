<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class MarketingCampaign extends Model
{
    use HasFactory;
    use HasUuid;
    use SoftDeletes;

    protected $fillable = [
        'branch_id',
        'name',
        'description',
        'campaign_type',
        'target_audience',
        'start_date',
        'end_date',
        'budget',
        'status',
        'channels',
        'template_id',
        'discount_percentage',
        'discount_amount',
        'metrics',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'budget' => 'decimal:2',
        'channels' => 'array',
        'discount_percentage' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'metrics' => 'array',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(NotificationTemplate::class, 'template_id');
    }
}
