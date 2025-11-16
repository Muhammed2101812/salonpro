<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerSegment extends Model
{
    use HasFactory;
    use HasUuid;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'criteria',
        'is_active',
        'auto_update',
        'last_updated_at',
    ];

    protected $casts = [
        'criteria' => 'array',
        'is_active' => 'boolean',
        'auto_update' => 'boolean',
        'last_updated_at' => 'datetime',
    ];

    public function customers(): BelongsToMany
    {
        return $this->belongsToMany(Customer::class, 'customer_segment_members')
            ->withTimestamps();
    }
}
