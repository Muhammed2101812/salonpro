<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;

class FileUpload extends Model
{
    use HasFactory, HasUuids;

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

    /**
     * Get the user who uploaded the file.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the parent uploadable model.
     */
    public function uploadable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scope to filter by file type.
     */
    public function scopeFileType($query, string $type)
    {
        return $query->where('file_type', $type);
    }

    /**
     * Scope to get images only.
     */
    public function scopeImages($query)
    {
        return $query->where('file_type', 'image');
    }

    /**
     * Scope to get documents only.
     */
    public function scopeDocuments($query)
    {
        return $query->where('file_type', 'document');
    }

    /**
     * Get the full URL to the file.
     */
    public function getUrlAttribute(): string
    {
        return Storage::disk($this->disk)->url($this->file_path);
    }

    /**
     * Get human-readable file size.
     */
    public function getHumanFileSizeAttribute(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Increment download count.
     */
    public function incrementDownloadCount(): void
    {
        $this->increment('download_count');
    }

    /**
     * Delete the physical file and record.
     */
    public function deleteFile(): bool
    {
        // Delete the physical file
        if (Storage::disk($this->disk)->exists($this->file_path)) {
            Storage::disk($this->disk)->delete($this->file_path);
        }

        // Delete the database record
        return $this->delete();
    }

    /**
     * Check if file exists on disk.
     */
    public function fileExists(): bool
    {
        return Storage::disk($this->disk)->exists($this->file_path);
    }

    /**
     * Get file contents.
     */
    public function getFileContents()
    {
        return Storage::disk($this->disk)->get($this->file_path);
    }

    /**
     * Download the file.
     */
    public function download()
    {
        $this->incrementDownloadCount();
        
        return Storage::disk($this->disk)->download($this->file_path, $this->original_name);
    }

    /**
     * Check if file is an image.
     */
    public function isImage(): bool
    {
        return $this->file_type === 'image';
    }

    /**
     * Check if file is a document.
     */
    public function isDocument(): bool
    {
        return $this->file_type === 'document';
    }

    /**
     * Check if file is a video.
     */
    public function isVideo(): bool
    {
        return $this->file_type === 'video';
    }
}
