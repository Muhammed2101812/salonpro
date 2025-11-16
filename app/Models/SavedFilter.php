<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SavedFilter extends Model
{
    use HasFactory;
    use HasUuid;

    protected $fillable = [
        'user_id',
        'filter_name',
        'filter_type',
        'filter_config',
        'is_default',
        'is_public',
    ];

    protected $casts = [
        'filter_config' => 'array',
        'is_default' => 'boolean',
        'is_public' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
