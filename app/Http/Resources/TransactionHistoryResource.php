<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionHistoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'batch' => $this->batch,
            'date' => $this->date,
            'hour' => $this->date ? Carbon::parse($this->date)->format('H:i:s') : null,
            'date_of_transaction' => $this->date_of_transaction ? Carbon::parse($this->date_of_transaction)->format('j F Y') : null,
            'location_name' => $this->whenLoaded('location', fn () => $this->location?->name),
            'product_code' => $this->whenLoaded('product', fn () => $this->product?->product_code),
            'product_name' => $this->whenLoaded('product', fn () => $this->product?->name),
            'qty' => $this->qty,
        ];
    }
}
