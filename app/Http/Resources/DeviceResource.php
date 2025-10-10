<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DeviceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'ip_address' => $this->ip_address,
            'type' => $this->type,
            'hierarchy_level' => $this->hierarchy_level,
            'parent_id' => $this->parent_id,
            'location' => $this->location,
            'description' => $this->description,
            'status' => $this->status,
            'last_checked_at' => $this->last_checked_at?->format('Y-m-d H:i:s'),
            'is_active' => $this->is_active,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
