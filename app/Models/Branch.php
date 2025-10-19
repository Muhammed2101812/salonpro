<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Branch extends Model
{
    use HasFactory;
    use HasUuids;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'code',
        'phone',
        'email',
        'address',
        'city',
        'country',
        'is_active',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    /**
     * Get the users for the branch.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the appointments for the branch.
     */
    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    /**
     * Get the settings for the branch.
     */
    public function settings(): HasMany
    {
        return $this->hasMany(BranchSetting::class);
    }

    /**
     * Get a setting value by key
     */
    public function getSetting(string $key, $default = null)
    {
        return BranchSetting::get($this->id, $key, $default);
    }

    /**
     * Set a setting value
     */
    public function setSetting(string $key, $value, ?string $type = 'string', ?string $group = null, bool $isEncrypted = false)
    {
        return BranchSetting::set($this->id, $key, $value, $type, $group, $isEncrypted);
    }
}
