<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerRfmAnalysis extends Model
{
    use HasFactory;
    use HasUuids;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'customer_rfm_analysis';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'customer_id',
        'branch_id',
        'last_visit_date',
        'days_since_last_visit',
        'recency_score',
        'total_visits',
        'frequency_score',
        'total_spent',
        'average_order_value',
        'monetary_score',
        'rfm_score',
        'customer_segment',
        'calculated_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'last_visit_date' => 'date',
            'days_since_last_visit' => 'integer',
            'recency_score' => 'integer',
            'total_visits' => 'integer',
            'frequency_score' => 'integer',
            'total_spent' => 'decimal:2',
            'average_order_value' => 'decimal:2',
            'monetary_score' => 'integer',
            'rfm_score' => 'integer',
            'calculated_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Get the customer.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the branch.
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Calculate recency score (1-5, 5 being most recent).
     */
    public function calculateRecencyScore(): int
    {
        if (!$this->days_since_last_visit) {
            return 5;
        }

        // Score based on days since last visit
        if ($this->days_since_last_visit <= 30) {
            return 5;
        } elseif ($this->days_since_last_visit <= 60) {
            return 4;
        } elseif ($this->days_since_last_visit <= 90) {
            return 3;
        } elseif ($this->days_since_last_visit <= 180) {
            return 2;
        } else {
            return 1;
        }
    }

    /**
     * Calculate frequency score (1-5, 5 being most frequent).
     */
    public function calculateFrequencyScore(): int
    {
        // Score based on total visits
        if ($this->total_visits >= 20) {
            return 5;
        } elseif ($this->total_visits >= 10) {
            return 4;
        } elseif ($this->total_visits >= 5) {
            return 3;
        } elseif ($this->total_visits >= 2) {
            return 2;
        } else {
            return 1;
        }
    }

    /**
     * Calculate monetary score (1-5, 5 being highest spender).
     */
    public function calculateMonetaryScore(): int
    {
        // Score based on total spent
        $total = (float)$this->total_spent;
        
        if ($total >= 5000) {
            return 5;
        } elseif ($total >= 2000) {
            return 4;
        } elseif ($total >= 1000) {
            return 3;
        } elseif ($total >= 500) {
            return 2;
        } else {
            return 1;
        }
    }

    /**
     * Update RFM scores.
     */
    public function updateScores(): void
    {
        $this->recency_score = $this->calculateRecencyScore();
        $this->frequency_score = $this->calculateFrequencyScore();
        $this->monetary_score = $this->calculateMonetaryScore();
        $this->rfm_score = $this->recency_score + $this->frequency_score + $this->monetary_score;
        $this->customer_segment = $this->determineSegment();
        $this->calculated_at = now();
        $this->save();
    }

    /**
     * Determine customer segment based on RFM scores.
     */
    protected function determineSegment(): string
    {
        $r = $this->recency_score;
        $f = $this->frequency_score;
        $m = $this->monetary_score;

        // Champions: High R, F, M
        if ($r >= 4 && $f >= 4 && $m >= 4) {
            return 'Champions';
        }

        // Loyal Customers: High F, M
        if ($f >= 4 && $m >= 4) {
            return 'Loyal';
        }

        // Potential Loyalists: Recent, frequent
        if ($r >= 4 && $f >= 3) {
            return 'Potential Loyalist';
        }

        // Recent Customers: High R, low F
        if ($r >= 4 && $f <= 2) {
            return 'New Customer';
        }

        // Promising: Recent with potential
        if ($r >= 3 && $f >= 2 && $m >= 2) {
            return 'Promising';
        }

        // Needs Attention: Average R, F, M
        if ($r >= 2 && $f >= 2 && $m >= 2) {
            return 'Needs Attention';
        }

        // About to Sleep: Low R, moderate F, M
        if ($r <= 2 && $f >= 3 && $m >= 3) {
            return 'About to Sleep';
        }

        // At Risk: Low R, high F, M
        if ($r <= 2 && $f >= 4) {
            return 'At Risk';
        }

        // Cannot Lose: Very low R, very high F, M
        if ($r == 1 && $f >= 4 && $m >= 4) {
            return 'Cannot Lose';
        }

        // Hibernating: Low R, F, M
        if ($r <= 2 && $f <= 2) {
            return 'Hibernating';
        }

        // Lost: Lowest scores
        if ($r == 1 && $f <= 2 && $m <= 2) {
            return 'Lost';
        }

        return 'Other';
    }

    /**
     * Check if customer is high value.
     */
    public function isHighValue(): bool
    {
        return in_array($this->customer_segment, ['Champions', 'Loyal', 'Potential Loyalist']);
    }

    /**
     * Check if customer is at risk.
     */
    public function isAtRisk(): bool
    {
        return in_array($this->customer_segment, ['At Risk', 'Cannot Lose', 'About to Sleep']);
    }

    /**
     * Check if customer is lost.
     */
    public function isLost(): bool
    {
        return in_array($this->customer_segment, ['Lost', 'Hibernating']);
    }

    /**
     * Get segment color for UI.
     */
    public function getSegmentColor(): string
    {
        return match($this->customer_segment) {
            'Champions', 'Loyal' => 'success',
            'Potential Loyalist', 'Promising', 'New Customer' => 'info',
            'Needs Attention', 'About to Sleep' => 'warning',
            'At Risk', 'Cannot Lose', 'Hibernating', 'Lost' => 'danger',
            default => 'secondary',
        };
    }
}
