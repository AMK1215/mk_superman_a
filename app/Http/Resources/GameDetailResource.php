<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GameDetailResource extends JsonResource
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
            'code' => $this->game_code,
            'name' => $this->name,
            // 'click_count' => $this->click_count,
            'game_type_id' => $this->game_type_id,
            'provider_id' => $this->product->id,
            'image' => $this->image_url,
        ];
    }
}
