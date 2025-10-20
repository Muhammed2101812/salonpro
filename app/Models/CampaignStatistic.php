<?php

namespace App\Models;

use App\Models\Traits\BranchScoped;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampaignStatistic extends Model
{
    use HasFactory, BranchScoped;

    protected $fillable = [
        'branch_id',
        'campaign_id',
        'total_sent',
        'total_delivered',
        'total_failed',
        'total_read',
        'total_clicked',
        'total_unsubscribed',
        'total_cost',
        'delivery_rate',
        'read_rate',
        'click_rate',
        'unsubscribe_rate',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'total_sent' => 'integer',
        'total_delivered' => 'integer',
        'total_failed' => 'integer',
        'total_read' => 'integer',
        'total_clicked' => 'integer',
        'total_unsubscribed' => 'integer',
        'total_cost' => 'decimal:4',
        'delivery_rate' => 'decimal:2',
        'read_rate' => 'decimal:2',
        'click_rate' => 'decimal:2',
        'unsubscribe_rate' => 'decimal:2',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(NotificationCampaign::class);
    }
}
