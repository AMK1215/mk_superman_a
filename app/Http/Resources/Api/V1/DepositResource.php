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
            'bank_id' => $this->bank,
            'amount' => $this->amount,
            'refrence_no' => $this->refrence_no,
            'status' => $this->status,
        ];
    }
}
