<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MatchUserResource extends JsonResource
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
            "game" => $this->game->name,
            "team" => $this->pivot->team,
            "result" => $this->pivot->result,
            "links" => [
                [
                    "rel" => "self",
                    "href" => "/api/games/{$this->game->slug}/matches/{$this->id}"
                ],
                [
                    "rel" => "game",
                    "href" => "/api/games/{$this->game->slug}"
                ]
            ]
        ];
    }
}
