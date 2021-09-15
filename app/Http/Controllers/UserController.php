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
        $users = User::where('client_id', $this->client->id)->paginate(10);
        return response(UserResource::collection($users), 200);
    }

    public function show($user)
    {
        return response(new UserResource($this->getUser($user)), 200);
    }

    public function indexGames($user)
    {
        $games = $this->getUser($user)->games()->paginate();
        return (GameUserResource::collection($games))
            ->response()
            ->setStatusCode(200);
    }

    public function indexMatches($user)
    {
        $matches = $this->getUser($user)->matches()->paginate();
        return (MatchUserResource::collection($matches))
            ->response()
            ->setStatusCode(200);
    }

    public function getUser($user)
    {
        return User::where('client_id', $this->client->id)->where("slug", $user)->first();
    }

}
