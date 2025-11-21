<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'branch_id' => $this->branch_id,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),

            // Relationships
            'branch' => BranchResource::make($this->whenLoaded('branch')),
            'roles' => $this->when(
                $this->relationLoaded('roles'),
                fn() => $this->roles->pluck('name')
            ),
            'permissions' => $this->when(
                $this->relationLoaded('permissions'),
                fn() => $this->permissions->pluck('name')
            ),
        ];
    }
}
