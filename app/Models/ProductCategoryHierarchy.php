<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductCategoryHierarchy extends Model
{
    use HasFactory;
    use HasUuid;

    protected $table = 'product_category_hierarchy';

    public $incrementing = false;
    protected $keyType = 'string';
    protected $primaryKey = 'category_id';

    protected $fillable = [
        'category_id',
        'parent_category_id',
        'level',
        'path',
    ];

    protected $casts = [
        'level' => 'integer',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'category_id');
    }

    public function parentCategory(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'parent_category_id');
    }
}
