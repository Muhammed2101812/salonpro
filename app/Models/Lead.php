<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lead extends Model
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
        'first_name',
        'last_name',
        'email',
        'phone',
        'company',
        'status',
        'source',
        'source_details',
        'priority',
        'estimated_value',
        'score',
        'notes',
        'assigned_to',
        'last_contacted_at',
        'converted_at',
        'converted_to_customer_id',
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
            'estimated_value' => 'decimal:2',
            'score' => 'integer',
            'last_contacted_at' => 'datetime',
            'converted_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Get the branch that owns the lead.
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get the employee assigned to the lead.
     */
    public function assignedEmployee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'assigned_to');
    }

    /**
     * Get the customer this lead was converted to.
     */
    public function convertedCustomer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'converted_to_customer_id');
    }

    /**
     * Get the user who created the lead.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the activities for the lead.
     */
    public function activities(): HasMany
    {
        return $this->hasMany(LeadActivity::class);
    }

    /**
     * Get the lead's full name.
     */
    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    /**
     * Check if lead is new.
     */
    public function isNew(): bool
    {
        return $this->status === 'new';
    }

    /**
     * Check if lead is won.
     */
    public function isWon(): bool
    {
        return $this->status === 'won';
    }

    /**
     * Check if lead is lost.
     */
    public function isLost(): bool
    {
        return $this->status === 'lost';
    }

    /**
     * Check if lead is converted.
     */
    public function isConverted(): bool
    {
        return $this->converted_at !== null;
    }

    /**
     * Update lead status.
     */
    public function updateStatus(string $newStatus, ?string $userId = null): void
    {
        $oldStatus = $this->status;
        $this->status = $newStatus;
        $this->save();

        // Log activity
        $this->activities()->create([
            'activity_type' => 'status_change',
            'description' => "Status changed from {$oldStatus} to {$newStatus}",
            'activity_date' => now(),
            'user_id' => $userId,
        ]);
    }

    /**
     * Convert lead to customer.
     */
    public function convertToCustomer(string $customerId): void
    {
        $this->converted_to_customer_id = $customerId;
        $this->converted_at = now();
        $this->status = 'won';
        $this->save();
    }

    /**
     * Add activity.
     */
    public function addActivity(string $type, string $description, ?string $userId = null): void
    {
        $this->activities()->create([
            'activity_type' => $type,
            'description' => $description,
            'activity_date' => now(),
            'user_id' => $userId,
        ]);

        if (in_array($type, ['call', 'email', 'meeting'])) {
            $this->last_contacted_at = now();
            $this->save();
        }
    }

    /**
     * Calculate days since last contact.
     */
    public function getDaysSinceLastContact(): ?int
    {
        if (!$this->last_contacted_at) {
            return null;
        }

        return now()->diffInDays($this->last_contacted_at);
    }

    /**
     * Update lead score.
     */
    public function updateScore(int $score): void
    {
        $this->score = $score;
        $this->save();
    }
}
