<?php

namespace App\Services;

use App\Models\AccountLockout;
use App\Models\LoginAttempt;
use App\Models\User;
use Illuminate\Support\Facades\Request;

class LoginSecurityService
{
    /**
     * Record a login attempt.
     */
    public function recordAttempt(string $email, bool $successful, ?string $failureReason = null): LoginAttempt
    {
        return LoginAttempt::create([
            'email' => $email,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'successful' => $successful,
            'failure_reason' => $failureReason,
            'attempted_at' => now(),
        ]);
    }

    /**
     * Check if an email is currently locked out.
     */
    public function isLockedOut(string $email): bool
    {
        $lockout = AccountLockout::forEmail($email)
            ->active()
            ->first();

        if (! $lockout) {
            return false;
        }

        $lockoutDuration = config('auth.login_security.lockout_duration', 900);
        $lockoutExpiry = $lockout->locked_at->addSeconds($lockoutDuration);

        if ($lockoutExpiry->isPast()) {
            $lockout->unlock();

            return false;
        }

        return true;
    }

    /**
     * Get the number of failed login attempts for an email.
     */
    public function getFailedAttempts(string $email, int $minutes = 15): int
    {
        return LoginAttempt::forEmail($email)
            ->failed()
            ->recent($minutes)
            ->count();
    }

    /**
     * Check if the account should be locked out and lock it if necessary.
     */
    public function checkAndLockout(string $email): ?AccountLockout
    {
        $maxAttempts = config('auth.login_security.max_attempts', 5);
        $failedAttempts = $this->getFailedAttempts($email);

        if ($failedAttempts >= $maxAttempts) {
            return $this->lockoutAccount($email, $failedAttempts);
        }

        return null;
    }

    /**
     * Lock out an account.
     */
    protected function lockoutAccount(string $email, int $failedAttempts): AccountLockout
    {
        $user = User::where('email', $email)->first();

        return AccountLockout::create([
            'user_id' => $user?->id,
            'email' => $email,
            'ip_address' => Request::ip(),
            'locked_at' => now(),
            'failed_attempts' => $failedAttempts,
        ]);
    }

    /**
     * Clear failed attempts for an email (after successful login).
     */
    public function clearFailedAttempts(string $email): void
    {
        LoginAttempt::forEmail($email)
            ->failed()
            ->recent(60)
            ->delete();
    }

    /**
     * Get lockout time remaining in seconds.
     */
    public function getLockoutTimeRemaining(string $email): int
    {
        $lockout = AccountLockout::forEmail($email)
            ->active()
            ->first();

        if (! $lockout) {
            return 0;
        }

        $lockoutDuration = config('auth.login_security.lockout_duration', 900);
        $lockoutExpiry = $lockout->locked_at->addSeconds($lockoutDuration);

        return max(0, $lockoutExpiry->diffInSeconds(now()));
    }

    /**
     * Manually unlock an account.
     */
    public function unlockAccount(string $email): void
    {
        AccountLockout::forEmail($email)
            ->active()
            ->get()
            ->each(fn ($lockout) => $lockout->unlock());

        $this->clearFailedAttempts($email);
    }
}
