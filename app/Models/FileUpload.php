<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class FileUpload extends Model
{
    use HasFactory;
    use HasUuid;

    protected $fillable = [
        'user_id',
        'uploadable_type',
        'uploadable_id',
        'file_name',
        'original_name',
        'file_path',
        'disk',
        'mime_type',
        'file_size',
        'file_type',
        'metadata',
        'download_count',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'metadata' => 'array',
        'download_count' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function uploadable(): MorphTo
    {
        return $this->morphTo();
    }
}
