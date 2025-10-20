<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerFeedback extends Model
{
    use HasFactory;
    use HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'customer_id',
        'branch_id',
        'appointment_id',
        'employee_id',
        'overall_rating',
        'service_quality_rating',
        'cleanliness_rating',
        'staff_friendliness_rating',
        'value_rating',
        'comment',
        'suggestions',
        'would_recommend',
        'sentiment',
        'is_published',
        'responded_by',
        'response',
        'responded_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'overall_rating' => 'integer',
            'service_quality_rating' => 'integer',
            'cleanliness_rating' => 'integer',
            'staff_friendliness_rating' => 'integer',
            'value_rating' => 'integer',
            'would_recommend' => 'boolean',
            'is_published' => 'boolean',
            'responded_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($feedback) {
            if (!$feedback->sentiment) {
                $feedback->sentiment = $feedback->calculateSentiment();
            }
        });

        static::updating(function ($feedback) {
            if ($feedback->isDirty('overall_rating')) {
                $feedback->sentiment = $feedback->calculateSentiment();
            }
        });
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
     * Get the appointment.
     */
    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    /**
     * Get the employee.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Get the user who responded.
     */
    public function responder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responded_by');
    }

    /**
     * Calculate sentiment based on overall rating.
     */
    protected function calculateSentiment(): string
    {
        if ($this->overall_rating >= 4) {
            return 'positive';
        } elseif ($this->overall_rating >= 3) {
            return 'neutral';
        } else {
            return 'negative';
        }
    }

    /**
     * Check if feedback is positive.
     */
    public function isPositive(): bool
    {
        return $this->sentiment === 'positive';
    }

    /**
     * Check if feedback is negative.
     */
    public function isNegative(): bool
    {
        return $this->sentiment === 'negative';
    }

    /**
     * Check if feedback has been responded to.
     */
    public function hasResponse(): bool
    {
        return $this->response !== null;
    }

    /**
     * Add response to feedback.
     */
    public function addResponse(string $response, string $userId): void
    {
        $this->response = $response;
        $this->responded_by = $userId;
        $this->responded_at = now();
        $this->save();
    }

    /**
     * Calculate average rating.
     */
    public function getAverageRating(): float
    {
        $ratings = array_filter([
            $this->overall_rating,
            $this->service_quality_rating,
            $this->cleanliness_rating,
            $this->staff_friendliness_rating,
            $this->value_rating,
        ]);

        if (empty($ratings)) {
            return 0;
        }

        return array_sum($ratings) / count($ratings);
    }

    /**
     * Publish feedback.
     */
    public function publish(): void
    {
        $this->is_published = true;
        $this->save();
    }

    /**
     * Unpublish feedback.
     */
    public function unpublish(): void
    {
        $this->is_published = false;
        $this->save();
    }
}
