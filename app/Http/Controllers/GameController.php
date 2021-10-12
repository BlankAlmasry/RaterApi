<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGameRequest;
use App\Http\Requests\UpdateGameRequest;
use App\Http\Resources\GameResource;
use App\Http\Resources\GameUserResource;
use App\Http\Resources\RankingResource;
use App\Models\Game;
use Illuminate\Http\Request;

class GameController extends Controller
{

    public function index()
    {
        return (GameResource::collection($this->client->games()->paginate(10)))
            ->response()
            ->setStatusCode(200);
    }

    public function store(StoreGameRequest $request)
    {
        $validateData = $request->validated() + array("client_id" => $this->client->id);
        $game = Game::create($validateData);
        return response(['A game has been created', new GameResource($game)], 201);
    }

    public function show($game)
    {
        $game = $this->getGame($game);
        return response(
            new GameResource($game), 200
        );
    }

    public function update(UpdateGameRequest $request, $game)
    {
        $game = $this->getGame($game);
        $validateData = $request->validated() + array("client_id" => $this->client->id);
        $game->update($validateData);
        return response([
            'message' => 'Updated successfully',
            "data" => new GameResource($game)],
            200);
    }

    public function delete($game)
    {
        $game = $this->getGame($game);
        $game->delete();
        return response([], 200);
    }

    public function ranking(Request $request, $game)
    {
        $game = $this->getGame($game);
        $users = $game->users()
            ->where("rating_deviation", "<", $request->maxRatingDeviation ?? "75")
            ->orderBy("rating", "desc")
            ->paginate(10);
        return RankingResource::collection($users);
    }

    public function userRank($game, $user)
    {
        $game = $this->getGame($game);
        $user = $game->users()->where('slug', $user)->first();
        $usersRank = $game->users()->orderBy("rating")->pluck("id")->toArray();
        $rank = array_search($user->id, $usersRank);
        return response([
            "rank" => [
                "rank" => $rank + 1,
                "all" => count($usersRank)
            ]
        ], 200);
    }

    public function indexUsers($game)
    {
        $game = $this->getGame($game);
        $users = $game->users()->paginate(50);
        return (GameUserResource::collection($users)
            ->response()
            ->setStatusCode(200));
    }

    public function showUser($game, $user)
    {
        $game = $this->getGame($game);
        $usersRanks = $game->users()->orderBy("rating")->pluck("id")->toArray();
        $user = $game->users()->where('slug', $user)->firstOrFail();
        return \response(new GameUserResource($user), 200);
    }


    private function getGame($game)
    {
        return Game::where('client_id', $this->client->id)->where("slug", $game)->firstOrFail();
    }
}
