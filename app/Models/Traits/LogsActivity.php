<?php

declare(strict_types=1);

namespace App\Models\Traits;

use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity as SpatieLogsActivity;

trait LogsActivity
{
    use SpatieLogsActivity;

    /**
     * Get the activity log options.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['*'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn (string $eventName): string => "Model {$eventName}");
    }

    /**
     * Get the description for the activity log.
     */
    public function getDescriptionForEvent(string $eventName): string
    {
        $modelName = class_basename($this);

        return match ($eventName) {
            'created' => "{$modelName} created",
            'updated' => "{$modelName} updated",
            'deleted' => "{$modelName} deleted",
            default => "{$modelName} {$eventName}",
        };
    }
}
