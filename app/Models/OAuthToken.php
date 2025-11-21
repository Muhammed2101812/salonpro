<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OAuthToken extends Model
{
    use HasFactory;
    use HasUuid;

    protected $fillable = [
        'user_id',
        'provider_id',
        'access_token',
        'refresh_token',
        'token_type',
        'expires_in',
        'expires_at',
        'scopes',
        'metadata',
    ];

    protected $casts = [
        'expires_in' => 'integer',
        'expires_at' => 'datetime',
        'scopes' => 'array',
        'metadata' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(OAuthProvider::class);
    }
}
