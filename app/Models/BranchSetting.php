<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Crypt;

class BranchSetting extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'branch_id',
        'key',
        'value',
        'type',
        'group',
        'is_encrypted',
    ];

    protected $casts = [
        'is_encrypted' => 'boolean',
    ];

    /**
     * Get the branch that owns the setting
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get the setting value with automatic type casting
     */
    public function getValueAttribute($value)
    {
        // Decrypt if encrypted
        if ($this->is_encrypted && $value) {
            $value = Crypt::decryptString($value);
        }

        // Cast to appropriate type
        return match ($this->type) {
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'number', 'integer' => is_numeric($value) ? (int) $value : $value,
            'float', 'decimal' => is_numeric($value) ? (float) $value : $value,
            'json' => is_string($value) ? json_decode($value, true) : $value,
            'array' => is_string($value) ? json_decode($value, true) : $value,
            default => $value,
        };
    }

    /**
     * Set the setting value with automatic encryption if needed
     */
    public function setValueAttribute($value)
    {
        // Convert to JSON if array or object
        if (in_array($this->type, ['json', 'array']) && is_array($value)) {
            $value = json_encode($value);
        }

        // Encrypt if marked as encrypted
        if ($this->is_encrypted && $value) {
            $value = Crypt::encryptString($value);
        }

        $this->attributes['value'] = $value;
    }

    /**
     * Get setting by key for a branch
     */
    public static function get(string $branchId, string $key, $default = null)
    {
        $setting = static::where('branch_id', $branchId)
            ->where('key', $key)
            ->first();

        return $setting ? $setting->value : $default;
    }

    /**
     * Set setting for a branch
     */
    public static function set(string $branchId, string $key, $value, ?string $type = 'string', ?string $group = null, bool $isEncrypted = false)
    {
        return static::updateOrCreate(
            [
                'branch_id' => $branchId,
                'key' => $key,
            ],
            [
                'value' => $value,
                'type' => $type,
                'group' => $group,
                'is_encrypted' => $isEncrypted,
            ]
        );
    }

    /**
     * Get all settings for a branch
     */
    public static function getAllForBranch(string $branchId, ?string $group = null)
    {
        $query = static::where('branch_id', $branchId);

        if ($group) {
            $query->where('group', $group);
        }

        return $query->get()->pluck('value', 'key')->toArray();
    }

    /**
     * Delete setting for a branch
     */
    public static function remove(string $branchId, string $key): bool
    {
        return static::where('branch_id', $branchId)
            ->where('key', $key)
            ->delete();
    }
}
