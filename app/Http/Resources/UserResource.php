<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'name' => $this->name,
            "links" => [
                [
                    "rel" => "self",
                    "href" => "/api/users/{$this->slug}"
                ],
                [
                    "rel" => "games",
                    "href" => "/api/users/{$this->slug}/games"
                ],
                [
                    "rel" => "matches",
                    "href" => "/api/users/{$this->slug}/matches"
                ]
            ],
        ];
    }
}
