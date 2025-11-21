<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailGatewayLog extends Model
{
    use HasFactory;
    use HasUuid;

    protected $fillable = [
        'email_provider_id',
        'provider_name',
        'to_email',
        'from_email',
        'subject',
        'body_preview',
        'status',
        'provider_message_id',
        'provider_response',
        'sent_at',
        'delivered_at',
        'opened_at',
        'clicked_at',
        'error_message',
    ];

    protected $casts = [
        'provider_response' => 'array',
        'sent_at' => 'datetime',
        'delivered_at' => 'datetime',
        'opened_at' => 'datetime',
        'clicked_at' => 'datetime',
    ];
}
