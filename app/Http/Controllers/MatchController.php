<?php

namespace App\Http\Controllers;

use App\GameManager\PlayerManager;
use App\Http\Requests\StoreMatchRequest;
use App\Http\Resources\MatchResource;
use App\Models\MatchUp;

class MatchController extends Controller
{

    public function store(StoreMatchRequest $request, $game)
    {
        $game = $this->getGame($game);
        $match = MatchUp::create([
            'game_id' => $game->id,
            'team_length' => count($request['teams'][0]['users']) #team1 length is same as #team2,
        ]);
        PlayerManager::insertUserWithNewRating($game, $match, $request->teams, $this->client->id);
        return response(new MatchResource($match->loadMissing('game')), 201);
    }

    public function index($game)
    {
        $game = $this->getGame($game);
        $matches = $game->matches();
        return MatchResource::collection($matches->latest()->paginate(10))
            ->response()
            ->setStatusCode(200);
    }

    public function show($game, $match)
    {
        $game = $this->getGame($game);
        $match = $game->matches()->findOrFail($match);
        return \response(new MatchResource($match), 200);
    }

    public function delete($game, $match)
    {
        $game = $this->getGame($game);
        $match = $game->matches()->findOrFail($match);
        $match->delete();
        return response(["message", "resource deleted"], 200);
    }

    private function getGame($game)
    {
        return $this->client->games()->where('slug', $game)->firstOrFail();
    }

}
