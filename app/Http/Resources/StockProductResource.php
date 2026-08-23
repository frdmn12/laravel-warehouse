<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StockProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'product_name' => $this->product_name ?? $this->whenLoaded('product', fn () => $this->product?->name),
            'product_code' => $this->product_code ?? $this->whenLoaded('product', fn () => $this->product?->product_code),
            'location_id' => $this->location_id,
            'location_name' => $this->location_name ?? $this->whenLoaded('location', fn () => $this->location?->name),
            'stock' => $this->stock,
            'date_of_entry' => $this->date_of_entry,
        ];
    }
}
