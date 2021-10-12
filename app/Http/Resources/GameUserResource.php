<?php

namespace App\Http\Resources;

use App\Glicko\Glicko;
use Illuminate\Http\Resources\Json\JsonResource;

class GameUserResource extends JsonResource
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
            "rating" => $this->pivot->rating,
            "rating_deviation" => $this->pivot->rating_deviation,
            "rating_volatility" => $this->pivot->rating_volatility,
            "wins" => $this->pivot->wins,
            "loses" => $this->pivot->loses,
            'links' => [
                [
                    'rel' => 'self',
                    'href' => '/games/' . $this->slug,
                ],
            ]
        ];
    }
}
