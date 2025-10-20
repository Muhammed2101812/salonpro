<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class JournalEntry extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'branch_id',
        'entry_number',
        'entry_date',
        'entry_type',
        'reference_id',
        'reference_type',
        'description',
        'total_debit',
        'total_credit',
        'status',
        'created_by',
        'posted_by',
        'posted_at',
    ];

    protected $casts = [
        'entry_date' => 'date',
        'total_debit' => 'decimal:2',
        'total_credit' => 'decimal:2',
        'posted_at' => 'datetime',
    ];

    // Relationships
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function poster(): BelongsTo
    {
        return $this->belongsTo(User::class, 'posted_by');
    }

    public function lines(): HasMany
    {
        return $this->hasMany(JournalEntryLine::class);
    }

    public function reference(): MorphTo
    {
        return $this->morphTo();
    }

    // Helper Methods
    public function post(User $user): bool
    {
        if ($this->status === 'posted') {
            throw new \Exception('Bu yevmiye kaydı zaten işlenmiş');
        }

        if ($this->status === 'voided') {
            throw new \Exception('İptal edilmiş yevmiye kaydı işlenemez');
        }

        if (!$this->isBalanced()) {
            throw new \Exception('Yevmiye kaydı dengeli değil');
        }

        // Update account balances
        foreach ($this->lines as $line) {
            if ($line->type === 'debit') {
                $line->account->debit($line->amount);
            } else {
                $line->account->credit($line->amount);
            }
        }

        $this->update([
            'status' => 'posted',
            'posted_by' => $user->id,
            'posted_at' => now(),
        ]);

        return true;
    }

    public function void(User $user): bool
    {
        if ($this->status !== 'posted') {
            throw new \Exception('Sadece işlenmiş kayıtlar iptal edilebilir');
        }

        // Reverse the account balance changes
        foreach ($this->lines as $line) {
            if ($line->type === 'debit') {
                $line->account->credit($line->amount); // Reverse
            } else {
                $line->account->debit($line->amount); // Reverse
            }
        }

        $this->status = 'voided';
        return $this->save();
    }

    public function addLine(array $data): JournalEntryLine
    {
        if ($this->status === 'posted') {
            throw new \Exception('İşlenmiş yevmiye kaydına satır eklenemez');
        }

        $line = $this->lines()->create($data);
        $this->recalculateTotals();

        return $line;
    }

    public function recalculateTotals(): void
    {
        $this->total_debit = $this->lines()->where('type', 'debit')->sum('amount');
        $this->total_credit = $this->lines()->where('type', 'credit')->sum('amount');
        $this->save();
    }

    public function isBalanced(): bool
    {
        return abs($this->total_debit - $this->total_credit) < 0.01;
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isPosted(): bool
    {
        return $this->status === 'posted';
    }

    public function isVoided(): bool
    {
        return $this->status === 'voided';
    }

    // Scopes
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopePosted($query)
    {
        return $query->where('status', 'posted');
    }

    public function scopeVoided($query)
    {
        return $query->where('status', 'voided');
    }

    public function scopeForBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('entry_type', $type);
    }

    public function scopeInDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('entry_date', [$startDate, $endDate]);
    }

    // Boot method
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($entry) {
            if (!$entry->entry_number) {
                $entry->entry_number = 'JE-' . now()->format('Ymd') . '-' . str_pad(
                    static::whereDate('created_at', now())->count() + 1,
                    4,
                    '0',
                    STR_PAD_LEFT
                );
            }
        });
    }
}
