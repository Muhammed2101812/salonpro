<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImportHistory extends Model
{
    use HasFactory;
    use HasUuid;

    protected $table = 'import_history';

    protected $fillable = [
        'user_id',
        'import_type',
        'file_name',
        'file_path',
        'total_rows',
        'successful_rows',
        'failed_rows',
        'status',
        'mapping',
        'errors',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'total_rows' => 'integer',
        'successful_rows' => 'integer',
        'failed_rows' => 'integer',
        'mapping' => 'array',
        'errors' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
