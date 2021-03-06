<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GameResource extends JsonResource
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
            'name' => $this->name,
            'created_at' => $this->created_at->diffForHumans(),
            'links' => [
                [
                    'rel' => 'self',
                    'href' => '/games/' . $this->slug,
                ],
                [
                    'rel' => 'matches',
                    'href' => '/games/' . $this->slug . '/matches',
                ],
                [
                    'rel' => 'users',
                    'href' => '/games/' . $this->slug . '/users',
                ],
            ]
        ];
    }
}
