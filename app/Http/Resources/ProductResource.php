<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'product_code' => $this->product_code,
            'created_at' => $this->created_at,
            'created_by_name' => $this->whenLoaded('creator', fn () => $this->creator?->name),
            'updated_at' => $this->updated_at,
            'updated_by_name' => $this->whenLoaded('updater', fn () => $this->updater?->name),
        ];
    }
}
