<?php

namespace App\Http\Controllers;


use App\Http\Resources\GameUserResource;
use App\Http\Resources\MatchUserResource;
use App\Http\Resources\UserResource;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        $users =User::where('client_id', $this->client->id)->paginate(10);
        return response(UserResource::collection($users), 200);
    }

    public function show($user)
    {

        $user = User::where('client_id', $this->client->id)->where("slug", $user)->first();
        return response(new UserResource($user), 200);
    }

    public function indexGames($user)
    {
        $games = User::where("client_id", $this->client->id)->where('slug', $user)->first()->games()->paginate();
        return (GameUserResource::collection($games))
            ->response()
            ->setStatusCode(200);
    }

    public function indexMatches($user)
    {
        $matches = User::where("client_id", $this->client->id)->where('slug', $user)->first()->matches()->paginate();
        return (MatchUserResource::collection($matches))
            ->response()
            ->setStatusCode(200);

    }

}
