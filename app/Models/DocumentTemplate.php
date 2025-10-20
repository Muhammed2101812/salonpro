<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentTemplate extends Model
{
    use HasFactory, HasUuids;

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

    /**
     * Get the user who created the template.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope to get active templates.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get system templates.
     */
    public function scopeSystem($query)
    {
        return $query->where('is_system', true);
    }

    /**
     * Scope to get custom templates.
     */
    public function scopeCustom($query)
    {
        return $query->where('is_system', false);
    }

    /**
     * Scope to filter by template type.
     */
    public function scopeType($query, string $type)
    {
        return $query->where('template_type', $type);
    }

    /**
     * Render template with data.
     */
    public function render(array $data): string
    {
        $content = $this->template_content;

        // Replace variables in template
        foreach ($data as $key => $value) {
            $content = str_replace("{{" . $key . "}}", $value, $content);
        }

        return $content;
    }

    /**
     * Get available variables for this template.
     */
    public function getAvailableVariables(): array
    {
        return $this->variables ?? [];
    }

    /**
     * Check if template is editable.
     */
    public function isEditable(): bool
    {
        return !$this->is_system;
    }

    /**
     * Clone this template.
     */
    public function duplicate(string $newName): self
    {
        $template = $this->replicate();
        $template->template_name = $newName;
        $template->template_code = $this->template_code . '_copy';
        $template->is_system = false;
        $template->created_by = auth()->id();
        $template->save();

        return $template;
    }
}
