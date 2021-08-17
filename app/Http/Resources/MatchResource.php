<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MatchResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'team_length' => $this->team_length,
            'users' => UserMatchResource::collection($this->users()->get()),
            'game' => new GameResource($this->game),
            'links' => [
                [
                    'rel' => 'self',
                    'href' => "/api/games/" .$this->game->slug. "/matches/$this->id",
                ],
                [
                    'rel' => 'game',
                    'href' => '/api/games/' .$this->game->slug,
                ],
            ],

        ];
    }
}
