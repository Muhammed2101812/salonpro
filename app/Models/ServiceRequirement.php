<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceRequirement extends Model
{
    use HasFactory;
    use HasUuid;

    protected $fillable = [
        'service_id',
        'requirement_type',
        'product_id',
        'requirement_name',
        'quantity',
        'is_mandatory',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'is_mandatory' => 'boolean',
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
