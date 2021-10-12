<?php

namespace App\Http\Resources;

use App\Glicko\Glicko;
use Illuminate\Http\Resources\Json\JsonResource;

class RankingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            "name" => $this->name,
            "rating" => $this->pivot->rating,
            "rank" => Glicko::ratingToRank($this->pivot->rating),
        ];
    }
}
