<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WithdrawResource extends JsonResource
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
            'bank_id' => $this->bank,
            'agent_id' => $this->agent_id,
            'user_id' => $this->user_id,
            'account_name' => $this->account_name,
            'account_no' => $this->account_no,
            'amount' => $this->amount,
            'status' => $this->status
        ];
    }
}
