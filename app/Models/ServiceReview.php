<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceReview extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'service_id',
        'customer_id',
        'appointment_id',
        'rating',
        'review',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'is_published' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    /**
     * Check if rating is positive (4 or 5 stars)
     */
    public function isPositive(): bool
    {
        return $this->rating >= 4;
    }

    /**
     * Check if rating is neutral (3 stars)
     */
    public function isNeutral(): bool
    {
        return $this->rating === 3;
    }

    /**
     * Check if rating is negative (1 or 2 stars)
     */
    public function isNegative(): bool
    {
        return $this->rating <= 2;
    }

    /**
     * Get rating stars as string
     */
    public function getStarsString(): string
    {
        return str_repeat('⭐', $this->rating);
    }

    /**
     * Get rating category in Turkish
     */
    public function getRatingCategory(): string
    {
        return match ($this->rating) {
            5 => 'Mükemmel',
            4 => 'Çok İyi',
            3 => 'İyi',
            2 => 'Orta',
            1 => 'Kötü',
            default => 'Belirsiz',
        };
    }

    /**
     * Scope to get only published reviews
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /**
     * Scope to get reviews with minimum rating
     */
    public function scopeMinimumRating($query, int $rating)
    {
        return $query->where('rating', '>=', $rating);
    }
}
