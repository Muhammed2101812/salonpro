<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Survey extends Model
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
        'survey_name',
        'description',
        'questions',
        'start_date',
        'end_date',
        'total_sent',
        'total_responses',
        'response_rate',
        'is_active',
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
            'questions' => 'array',
            'start_date' => 'date',
            'end_date' => 'date',
            'total_sent' => 'integer',
            'total_responses' => 'integer',
            'response_rate' => 'decimal:2',
            'is_active' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Get the branch that owns the survey.
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get the user who created the survey.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the responses for this survey.
     */
    public function responses(): HasMany
    {
        return $this->hasMany(SurveyResponse::class);
    }

    /**
     * Check if survey is currently active.
     */
    public function isCurrentlyActive(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $today = now()->toDateString();
        
        if ($today < $this->start_date->toDateString()) {
            return false;
        }

        if ($this->end_date && $today > $this->end_date->toDateString()) {
            return false;
        }

        return true;
    }

    /**
     * Update response statistics.
     */
    public function updateResponseStats(): void
    {
        $this->total_responses = $this->responses()->count();
        
        if ($this->total_sent > 0) {
            $this->response_rate = ($this->total_responses / $this->total_sent) * 100;
        }
        
        $this->save();
    }

    /**
     * Increment sent count.
     */
    public function incrementSent(int $count = 1): void
    {
        $this->increment('total_sent', $count);
        $this->updateResponseStats();
    }

    /**
     * Add question to survey.
     */
    public function addQuestion(array $question): void
    {
        $questions = $this->questions ?? [];
        $questions[] = $question;
        $this->questions = $questions;
        $this->save();
    }

    /**
     * Remove question from survey.
     */
    public function removeQuestion(int $index): void
    {
        $questions = $this->questions ?? [];
        
        if (isset($questions[$index])) {
            array_splice($questions, $index, 1);
            $this->questions = $questions;
            $this->save();
        }
    }

    /**
     * Get question by index.
     */
    public function getQuestion(int $index): ?array
    {
        return $this->questions[$index] ?? null;
    }

    /**
     * Get total questions count.
     */
    public function getTotalQuestions(): int
    {
        return count($this->questions ?? []);
    }
}
