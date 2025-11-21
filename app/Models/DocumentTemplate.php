<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentTemplate extends Model
{
    use HasFactory;
    use HasUuid;

    protected $fillable = [
        'template_name',
        'template_code',
        'template_type',
        'description',
        'template_content',
        'variables',
        'paper_size',
        'orientation',
        'header_html',
        'footer_html',
        'is_system',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'variables' => 'array',
        'header_html' => 'array',
        'footer_html' => 'array',
        'is_system' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
