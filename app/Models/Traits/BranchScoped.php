<?php

declare(strict_types=1);

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait BranchScoped
{
    /**
     * Boot the BranchScoped trait for a model.
     */
    protected static function bootBranchScoped(): void
    {
        static::addGlobalScope('branch', function (Builder $builder): void {
            if (auth()->check() && auth()->user()->branch_id) {
                $builder->where(
                    $builder->getModel()->getTable() . '.branch_id',
                    auth()->user()->branch_id
                );
            }
        });

        static::creating(function (Model $model): void {
            if (! $model->branch_id && auth()->check() && auth()->user()->branch_id) {
                $model->branch_id = auth()->user()->branch_id;
            }
        });
    }

    /**
     * Get the branch relationship.
     */
    public function branch(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\Branch::class);
    }
}
