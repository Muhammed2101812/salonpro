<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppointmentGroupParticipant extends Model
{
    use HasFactory;
    use HasUuid;

    protected $fillable = [
        'group_id',
        'customer_id',
        'status',
        'joined_at',
        'left_at',
        'notes',
    ];

    protected $casts = [
        'joined_at' => 'datetime',
        'left_at' => 'datetime',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(AppointmentGroup::class, 'group_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
