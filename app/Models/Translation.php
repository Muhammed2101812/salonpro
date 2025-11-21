<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Translation extends Model
{
    use HasFactory;
    use HasUuid;

    protected $fillable = [
        'translatable_type',
        'translatable_id',
        'field',
        'locale',
        'value',
    ];

    public function translatable(): MorphTo
    {
        return $this->morphTo();
    }
}
