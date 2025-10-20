<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketingCampaign extends Model
{
    use HasFactory;
    use HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'branch_id',
        'campaign_name',
        'campaign_code',
        'description',
        'campaign_type',
        'campaign_objective',
        'start_date',
        'end_date',
        'budget',
        'actual_cost',
        'target_audience_size',
        'reached_audience',
        'total_conversions',
        'conversion_rate',
        'roi',
        'status',
        'created_by',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'budget' => 'decimal:2',
            'actual_cost' => 'decimal:2',
            'target_audience_size' => 'integer',
            'reached_audience' => 'integer',
            'total_conversions' => 'integer',
            'conversion_rate' => 'decimal:2',
            'roi' => 'decimal:2',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Get the branch that owns the campaign.
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get the user who created the campaign.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Check if campaign is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if campaign is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Calculate and update conversion rate.
     */
    public function updateConversionRate(): void
    {
        if ($this->reached_audience > 0) {
            $this->conversion_rate = ($this->total_conversions / $this->reached_audience) * 100;
            $this->save();
        }
    }

    /**
     * Calculate and update ROI.
     */
    public function updateROI(): void
    {
        if ($this->actual_cost > 0) {
            $revenue = $this->total_conversions * 100; // Simplified calculation
            $this->roi = (($revenue - $this->actual_cost) / $this->actual_cost) * 100;
            $this->save();
        }
    }

    /**
     * Check if campaign is within date range.
     */
    public function isWithinDateRange(): bool
    {
        $today = now()->toDateString();
        $isAfterStart = $today >= $this->start_date->toDateString();
        $isBeforeEnd = !$this->end_date || $today <= $this->end_date->toDateString();

        return $isAfterStart && $isBeforeEnd;
    }

    /**
     * Get remaining budget.
     */
    public function getRemainingBudget(): ?float
    {
        if (!$this->budget) {
            return null;
        }

        return max(0, (float)$this->budget - (float)$this->actual_cost);
    }
}
