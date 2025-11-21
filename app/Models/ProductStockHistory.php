<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductStockHistory extends Model
{
    use HasFactory;
    use HasUuid;

    protected $table = 'product_stock_history';

    protected $fillable = [
        'product_id',
        'branch_id',
        'movement_type',
        'quantity_before',
        'quantity_change',
        'quantity_after',
        'reference_id',
        'reference_type',
        'user_id',
        'notes',
        'movement_date',
    ];

    protected $casts = [
        'quantity_before' => 'integer',
        'quantity_change' => 'integer',
        'quantity_after' => 'integer',
        'movement_date' => 'datetime',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
