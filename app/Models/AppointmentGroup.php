<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AppointmentGroup extends Model
{
    use HasFactory;
    use HasUuid;
    use SoftDeletes;

    protected $fillable = [
        'branch_id',
        'name',
        'description',
        'group_type',
        'max_participants',
        'appointment_date',
        'start_time',
        'end_time',
        'price_per_person',
        'total_price',
        'status',
        'notes',
    ];

    protected $casts = [
        'max_participants' => 'integer',
        'appointment_date' => 'date',
        'price_per_person' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function participants(): HasMany
    {
        return $this->hasMany(AppointmentGroupParticipant::class, 'group_id');
    }
}
