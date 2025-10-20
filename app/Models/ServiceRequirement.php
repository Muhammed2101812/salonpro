<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceRequirement extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'service_id',
        'requirement_type',
        'product_id',
        'requirement_name',
        'quantity',
        'is_mandatory',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
            'is_mandatory' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Check if requirement is a product
     */
    public function isProduct(): bool
    {
        return $this->requirement_type === 'product';
    }

    /**
     * Check if requirement is equipment
     */
    public function isEquipment(): bool
    {
        return $this->requirement_type === 'equipment';
    }

    /**
     * Check if requirement is a skill
     */
    public function isSkill(): bool
    {
        return $this->requirement_type === 'skill';
    }

    /**
     * Check if requirement is a certification
     */
    public function isCertification(): bool
    {
        return $this->requirement_type === 'certification';
    }

    /**
     * Get the display name of the requirement
     */
    public function getDisplayName(): string
    {
        if ($this->isProduct() && $this->product) {
            return $this->product->name;
        }

        return $this->requirement_name;
    }

    /**
     * Get requirement type label in Turkish
     */
    public function getTypeLabel(): string
    {
        return match ($this->requirement_type) {
            'product' => 'Ürün',
            'equipment' => 'Ekipman',
            'skill' => 'Yetenek',
            'certification' => 'Sertifika',
            default => $this->requirement_type,
        };
    }
}
