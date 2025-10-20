<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductCategoryHierarchy extends Model
{
    use HasFactory;

    protected $table = 'product_category_hierarchy';

    public $incrementing = false;

    protected $primaryKey = 'category_id';

    protected $keyType = 'string';

    protected $fillable = [
        'category_id',
        'parent_category_id',
        'level',
        'path',
    ];

    protected function casts(): array
    {
        return [
            'level' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Get the category.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'category_id');
    }

    /**
     * Get the parent category.
     */
    public function parentCategory(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'parent_category_id');
    }

    /**
     * Check if this is a root category.
     */
    public function isRoot(): bool
    {
        return $this->level === 0 || $this->parent_category_id === null;
    }

    /**
     * Get breadcrumb path as array.
     */
    public function getPathArray(): array
    {
        if (!$this->path) {
            return [$this->category_id];
        }

        return explode('/', $this->path);
    }

    /**
     * Build path string from category IDs.
     */
    public static function buildPath(?string $parentPath, string $categoryId): string
    {
        if (!$parentPath) {
            return $categoryId;
        }

        return $parentPath . '/' . $categoryId;
    }

    /**
     * Scope to root categories.
     */
    public function scopeRoot($query)
    {
        return $query->where('level', 0)->whereNull('parent_category_id');
    }

    /**
     * Scope by level.
     */
    public function scopeByLevel($query, int $level)
    {
        return $query->where('level', $level);
    }

    /**
     * Scope to children of a specific category.
     */
    public function scopeChildrenOf($query, string $categoryId)
    {
        return $query->where('parent_category_id', $categoryId);
    }
}
