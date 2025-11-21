<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReportExecutionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'template_id' => $this->template_id,
            'schedule_id' => $this->schedule_id,
            'branch_id' => $this->branch_id,
            'parameters' => $this->parameters,
            'status' => $this->status,
            'started_at' => $this->started_at?->format('Y-m-d H:i:s'),
            'completed_at' => $this->completed_at?->format('Y-m-d H:i:s'),
            'execution_time_ms' => $this->execution_time_ms,
            'row_count' => $this->row_count,
            'output_file' => $this->output_file,
            'output_format' => $this->output_format,
            'file_size' => $this->file_size,
            'error_message' => $this->error_message,
            'executed_by' => $this->executed_by,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),

            // Relationships
            'template' => ReportTemplateResource::make($this->whenLoaded('template')),
            'schedule' => ReportScheduleResource::make($this->whenLoaded('schedule')),
            'branch' => BranchResource::make($this->whenLoaded('branch')),
            'executor' => UserResource::make($this->whenLoaded('executor')),

            // Computed fields
            'is_pending' => $this->status === 'pending',
            'is_completed' => $this->status === 'completed',
            'is_failed' => $this->status === 'failed',
            'status_badge' => $this->getStatusBadge(),
            'execution_time_seconds' => $this->execution_time_ms ? round($this->execution_time_ms / 1000, 2) : null,
            'execution_time_formatted' => $this->getExecutionTimeFormatted(),
            'file_size_formatted' => $this->getFileSizeFormatted(),
            'can_retry' => $this->status === 'failed',
            'can_download' => $this->status === 'completed' && $this->output_file,
            'elapsed_time' => $this->when(
                $this->status === 'pending' && $this->started_at,
                fn() => $this->started_at->diffInSeconds(now())
            ),
        ];
    }

    private function getStatusBadge(): array
    {
        return match($this->status) {
            'pending' => ['color' => 'warning', 'label' => 'Pending', 'icon' => 'clock'],
            'completed' => ['color' => 'success', 'label' => 'Completed', 'icon' => 'check'],
            'failed' => ['color' => 'danger', 'label' => 'Failed', 'icon' => 'x-circle'],
            default => ['color' => 'secondary', 'label' => ucfirst($this->status), 'icon' => 'circle'],
        };
    }

    private function getExecutionTimeFormatted(): ?string
    {
        if (!$this->execution_time_ms) {
            return null;
        }

        $seconds = $this->execution_time_ms / 1000;

        if ($seconds < 60) {
            return round($seconds, 2) . 's';
        }

        $minutes = floor($seconds / 60);
        $secs = $seconds % 60;

        return "{$minutes}m " . round($secs, 0) . 's';
    }

    private function getFileSizeFormatted(): ?string
    {
        if (!$this->file_size) {
            return null;
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $size = $this->file_size;
        $unitIndex = 0;

        while ($size >= 1024 && $unitIndex < count($units) - 1) {
            $size /= 1024;
            $unitIndex++;
        }

        return round($size, 2) . ' ' . $units[$unitIndex];
    }
}
