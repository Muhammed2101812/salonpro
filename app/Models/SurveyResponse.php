<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SurveyResponse extends Model
{
    use HasFactory;
    use HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'survey_id',
        'customer_id',
        'answers',
        'submitted_at',
        'ip_address',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'answers' => 'array',
            'submitted_at' => 'datetime',
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

        static::created(function ($response) {
            $response->survey->updateResponseStats();
        });

        static::deleted(function ($response) {
            $response->survey->updateResponseStats();
        });
    }

    /**
     * Get the survey.
     */
    public function survey(): BelongsTo
    {
        return $this->belongsTo(Survey::class);
    }

    /**
     * Get the customer.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get answer for a specific question.
     */
    public function getAnswer(string $questionId): mixed
    {
        return $this->answers[$questionId] ?? null;
    }

    /**
     * Set answer for a specific question.
     */
    public function setAnswer(string $questionId, mixed $answer): void
    {
        $answers = $this->answers ?? [];
        $answers[$questionId] = $answer;
        $this->answers = $answers;
        $this->save();
    }

    /**
     * Check if response is anonymous.
     */
    public function isAnonymous(): bool
    {
        return $this->customer_id === null;
    }

    /**
     * Get total answers count.
     */
    public function getTotalAnswers(): int
    {
        return count($this->answers ?? []);
    }
}
