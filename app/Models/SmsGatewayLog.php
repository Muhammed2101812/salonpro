<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsGatewayLog extends Model
{
    use HasFactory;
    use HasUuid;

    protected $fillable = [
        'sms_provider_id',
        'provider_name',
        'to_number',
        'from_number',
        'message',
        'status',
        'provider_message_id',
        'provider_response',
        'cost',
        'message_parts',
        'sent_at',
        'delivered_at',
        'error_message',
    ];

    protected $casts = [
        'provider_response' => 'array',
        'cost' => 'decimal:4',
        'message_parts' => 'integer',
        'sent_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];
}
