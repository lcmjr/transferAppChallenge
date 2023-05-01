<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
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
            'value' => $this->value,
            'was_authorized' => $this->was_authorized,
            'was_notified' => $this->was_notified,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'payer'=> UsersResource::make($this->payer),
            'payee'=> UsersResource::make($this->payee)
        ];
    }
}
