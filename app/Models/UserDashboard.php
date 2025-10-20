<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserDashboard extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'dashboard_name',
        'layout',
        'is_default',
    ];

    protected $casts = [
        'layout' => 'array',
        'is_default' => 'boolean',
    ];

    /**
     * Get the user who owns this dashboard
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to get default dashboards
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Set this dashboard as default for the user
     */
    public function setAsDefault(): bool
    {
        // Remove default flag from other dashboards
        static::where('user_id', $this->user_id)
            ->where('id', '!=', $this->id)
            ->update(['is_default' => false]);

        // Set this as default
        return $this->update(['is_default' => true]);
    }

    /**
     * Add widget to dashboard layout
     */
    public function addWidget(string $widgetId, array $position): bool
    {
        $layout = $this->layout ?? [];

        $layout[] = [
            'widget_id' => $widgetId,
            'position' => $position,
        ];

        return $this->update(['layout' => $layout]);
    }

    /**
     * Remove widget from dashboard layout
     */
    public function removeWidget(string $widgetId): bool
    {
        $layout = $this->layout ?? [];

        $layout = array_filter($layout, function ($item) use ($widgetId) {
            return $item['widget_id'] !== $widgetId;
        });

        return $this->update(['layout' => array_values($layout)]);
    }

    /**
     * Update widget position
     */
    public function updateWidgetPosition(string $widgetId, array $position): bool
    {
        $layout = $this->layout ?? [];

        foreach ($layout as &$item) {
            if ($item['widget_id'] === $widgetId) {
                $item['position'] = $position;
                break;
            }
        }

        return $this->update(['layout' => $layout]);
    }

    /**
     * Get widgets in this dashboard
     */
    public function getWidgets()
    {
        if (!$this->layout) {
            return collect([]);
        }

        $widgetIds = collect($this->layout)->pluck('widget_id')->toArray();

        return DashboardWidget::whereIn('id', $widgetIds)->get();
    }

    /**
     * Clone dashboard for current user
     */
    public function clone(string $newName): self
    {
        return static::create([
            'user_id' => $this->user_id,
            'dashboard_name' => $newName,
            'layout' => $this->layout,
            'is_default' => false,
        ]);
    }
}
