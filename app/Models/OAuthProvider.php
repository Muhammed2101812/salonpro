<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OAuthProvider extends Model
{
    use HasFactory;
    use HasUuid;

    protected $fillable = [
        'provider_name',
        'provider_key',
        'client_id',
        'client_secret',
        'redirect_uri',
        'scopes',
        'config',
        'is_active',
    ];

    protected $casts = [
        'scopes' => 'array',
        'config' => 'array',
        'is_active' => 'boolean',
    ];

    public function tokens(): HasMany
    {
        return $this->hasMany(OAuthToken::class, 'provider_id');
    }
}
