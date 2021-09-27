<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGameRequest;
use App\Http\Requests\UpdateGameRequest;
use App\Http\Resources\GameResource;
use App\Http\Resources\GameUserResource;
use App\Models\Game;

class GameController extends Controller
{

    public function index()
    {
        return (GameResource::collection($this->client->games()->paginate(10)))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * @OA\Post(
     * path="/api/games",
     * summary="Create a game",
     * description="Create a game by name",
     * operationId="authLogin",
     * tags={"games"},
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass game name",
     *    @OA\JsonContent(
     *       required={"name"},
     *       @OA\Property(property="name", type="string", format="name", example="League of legends"),
     *    ),
     * ),
     * @OA\Response(
     *    response=201,
     *    description="Wrong credentials response",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="A game has been created"),
     *       @OA\Property(property="data", type="object"),
     *  ),
     *),
     * @OA\Response(
     *    response=400,
     *    description="Wrong credentials response",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Name is required")
     *        )
     *     ),
     *
     * @OA\Response(
     *    response=401,
     *    description="Unathourized request, you need api token",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Invalid or no api token, authorize with a valid api please")
     *        )
     *     )
     *
     * )
     */

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
            new GameResource($game),200
        );
    }

    public function update(UpdateGameRequest $request, $game)
    {
        $game = $this->getGame($game);
        $validateData = $request->validated() + array("client_id" => $this->client->id);
        $game->update($validateData);
        return response([
            'message'=>'Updated successfully',
            "data" => new GameResource($game)],
            200);
    }

    public function delete($game)
    {
        $game = $this->getGame($game);
        $game->delete();
        return response([],200);
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
        $user = $game->users()->where('slug', $user)->firstOrFail();
        return \response(new GameUserResource($user), 200);
    }

    private function getGame($game)
    {
        return $this->client->games()->where('slug', $game)->firstOrFail();
    }

}
