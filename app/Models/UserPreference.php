<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPreference extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'language',
        'currency',
        'timezone',
        'date_format',
        'time_format',
        'theme',
        'notifications',
        'dashboard_layout',
        'custom_settings',
    ];

    protected $casts = [
        'notifications' => 'array',
        'dashboard_layout' => 'array',
        'custom_settings' => 'array',
    ];

    /**
     * Get the user that owns the preferences.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get or create preferences for a user.
     */
    public static function getForUser(User $user): self
    {
        return self::firstOrCreate(
            ['user_id' => $user->id],
            [
                'language' => 'tr',
                'currency' => 'TRY',
                'timezone' => 'Europe/Istanbul',
                'date_format' => 'd/m/Y',
                'time_format' => 'H:i',
                'theme' => 'light',
            ]
        );
    }

    /**
     * Get a specific preference value.
     */
    public function get(string $key, $default = null)
    {
        return data_get($this->custom_settings, $key, $default);
    }

    /**
     * Set a specific preference value.
     */
    public function set(string $key, $value): void
    {
        $settings = $this->custom_settings ?? [];
        data_set($settings, $key, $value);
        $this->update(['custom_settings' => $settings]);
    }

    /**
     * Get notification preference.
     */
    public function getNotificationPreference(string $key, bool $default = true): bool
    {
        return data_get($this->notifications, $key, $default);
    }

    /**
     * Set notification preference.
     */
    public function setNotificationPreference(string $key, bool $value): void
    {
        $notifications = $this->notifications ?? [];
        data_set($notifications, $key, $value);
        $this->update(['notifications' => $notifications]);
    }

    /**
     * Check if dark theme is enabled.
     */
    public function isDarkTheme(): bool
    {
        if ($this->theme === 'auto') {
            // Could check system time or other factors
            return now()->hour >= 18 || now()->hour < 6;
        }

        return $this->theme === 'dark';
    }

    /**
     * Format a date according to user preferences.
     */
    public function formatDate($date): string
    {
        if (!$date instanceof \Carbon\Carbon) {
            $date = \Carbon\Carbon::parse($date);
        }

        return $date->timezone($this->timezone)->format($this->date_format);
    }

    /**
     * Format a time according to user preferences.
     */
    public function formatTime($time): string
    {
        if (!$time instanceof \Carbon\Carbon) {
            $time = \Carbon\Carbon::parse($time);
        }

        return $time->timezone($this->timezone)->format($this->time_format);
    }

    /**
     * Format a datetime according to user preferences.
     */
    public function formatDateTime($datetime): string
    {
        if (!$datetime instanceof \Carbon\Carbon) {
            $datetime = \Carbon\Carbon::parse($datetime);
        }

        return $datetime->timezone($this->timezone)
            ->format($this->date_format . ' ' . $this->time_format);
    }
}
