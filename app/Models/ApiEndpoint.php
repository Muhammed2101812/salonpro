<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiEndpoint extends Model
{
    use HasFactory;
    use HasUuid;

    protected $fillable = [
        'method',
        'path',
        'name',
        'description',
        'parameters',
        'request_body',
        'response_examples',
        'scopes_required',
        'is_public',
        'is_deprecated',
        'version',
    ];

    protected $casts = [
        'parameters' => 'array',
        'request_body' => 'array',
        'response_examples' => 'array',
        'scopes_required' => 'array',
        'is_public' => 'boolean',
        'is_deprecated' => 'boolean',
    ];
}
