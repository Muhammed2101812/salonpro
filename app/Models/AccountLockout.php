<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccountLockout extends Model
{
    protected $fillable = [
        'user_id',
        'email',
        'ip_address',
        'locked_at',
        'unlocked_at',
        'failed_attempts',
    ];

    protected $casts = [
        'locked_at' => 'datetime',
        'unlocked_at' => 'datetime',
    ];

    /**
     * Get the user that owns the lockout.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Determine if the lockout is currently active.
     */
    public function isActive(): bool
    {
        return is_null($this->unlocked_at) && $this->locked_at->isFuture() === false;
    }

    /**
     * Unlock the account.
     */
    public function unlock(): void
    {
        $this->update(['unlocked_at' => now()]);
    }

    /**
     * Scope to get active lockouts.
     */
    public function scopeActive($query)
    {
        return $query->whereNull('unlocked_at')
            ->where('locked_at', '<=', now());
    }

    /**
     * Scope to get lockouts for email.
     */
    public function scopeForEmail($query, string $email)
    {
        return $query->where('email', $email);
    }
}
