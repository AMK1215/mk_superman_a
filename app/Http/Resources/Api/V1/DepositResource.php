<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DepositResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'user_id' => $this->user_id,
            'agent_id' => $this->agent_id,
            'amount' => $this->amount,
            'bank' => $this->bank,
            'payment_type' => $this->bank->paymentType->name,
            'status' => $this->status,
        ];
    }
}
