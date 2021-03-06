<?php

namespace App\Http\Resources;

use App\Glicko\Glicko;
use Illuminate\Http\Resources\Json\JsonResource;

class UserMatchResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            "name" => $this->name,
            "rank" => Glicko::ratingToRank($this->pivot->rating),
            "result" => floatval($this->pivot->result),
            "rating" => $this->pivot->rating,
            "rating_deviation" => $this->pivot->rating_deviation,
            "rating_volatility" => $this->pivot->rating_volatility,
        ];
    }
}
