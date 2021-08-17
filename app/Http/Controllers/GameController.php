<?php

namespace App\Http\Controllers;

use App\Http\Resources\GameResource;
use App\Http\Resources\GameUserResource;
use App\Models\Game;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
class GameController extends Controller
{
    public function index()
    {
        return (GameResource::collection(Game::where("client_id",$this->client->id)->paginate(10)))
            ->response()
            ->setStatusCode(200);
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'string|required',
        ]);


        if ($validator->fails()) {
            return response($validator->errors(),400);
        }
        $data=array_merge($validator->validate(), ["client_id" => $this->client->id]);
        $game = Game::create($data);
        return response(['A game has been created', new GameResource($game->fresh())], 201);
    }

    public function show($game)
    {
        try
        {
            $game = Game::where('client_id', $this->client->id)->findOrFail($game);
        }
        catch(ModelNotFoundException $e)
        {
            return \response([], 404);
        }
        return response(
            new GameResource($game),200
        );
    }
    public function update(Request $request, Game $game)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'string|required',
        ]);


        if ($validator->fails()) {
            return response($validator->errors(),400);
        }
        $game->update(array_merge($validator->validate(), ["client_id" => $this->client->id]));
        return response(['message'=>'Updated successfully',"data" =>new GameResource($game)],200);
    }

    public function delete($game)
    {
        try
        {
            $game = Game::where('client_id', $this->client->id)->findOrFail($game);
        }
        catch(ModelNotFoundException $e)
        {
            return \response([], 404);
        }
        $game->delete();
        return response([],200);
    }
    public function indexUsers(Game $game)
    {
        $users = $game->users()->paginate(50);
        return (GameUserResource::collection($users)
            ->response()
            ->setStatusCode(200));
    }
    public function showUser(Game $game, User $user)
    {
        return \response(new GameUserResource($game->users()->find($user)), 200);
    }



}


