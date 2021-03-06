<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MatchResource extends JsonResource
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
            'id' => $this->id,
            'team_length' => $this->team_length,
            'users' => UserMatchResource::collection($this->users()->get()),
            'links' => [
                [
                    'rel' => 'self',
                    'href' => "/games/" . $this->game->slug . "/matches/$this->id",
                ],
                [
                    'rel' => 'game',
                    'href' => '/games/' . $this->game->slug,
                ],
            ],

        ];
    }
}
