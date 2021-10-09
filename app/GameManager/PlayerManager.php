<?php

namespace App\GameManager;

use App\Http\Controllers\MatchController;
use App\Models\User;

class PlayerManager
{
   public static function addPlayersToMatch($game, $match, $teams, $clientId)
    {
        foreach ($teams as $index => $team) {
            foreach ($team['users'] as $user) {
                $user = User::firstOrCreate([
                    "name" => $user,
                    "client_id" => $clientId
                ]);
                $game->users()->syncWithoutDetaching([$user->id]);
                $match->users()->attach($user, [
                    'team' => $index,
                    'result' => $team['result'],
                    'rating' => $user->games()->find($game)->pivot->rating,
                    'rating_deviation' => $user->games()->find($game)->pivot->rating_deviation,
                    'rating_volatility' => $user->games()->find($game)->pivot->rating_volatility
                ]);
            }
        }
    }
}
