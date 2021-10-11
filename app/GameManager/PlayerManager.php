<?php

namespace App\GameManager;

use App\Glicko\Glicko;
use App\Models\User;
use Blankalmasry\Glicko2\Rating\Rating;

class PlayerManager
{
    public static function insertUserWithNewRating($game, $match, $teams, $clientId)
    {
        $teamsAdapter = [];
        $users = [];
        foreach ($teams as $index => $team) {

            foreach ($team['users'] as $user) {
                $user = User::firstOrCreate([
                    "name" => $user,
                    "client_id" => $clientId
                ]);
                $users[$index][] = $user;
                $game->users()->syncWithoutDetaching([$user->id]);

            }
            $users[$index] = collect($users[$index]);
            $teamsAdapter[$index]["rating"] = $users[$index]->map(function ($user) use ($game) {
                $user = $game->users()->find($user);
                return new Rating(
                    $user->pivot->rating,
                    $user->pivot->rating_deviation,
                    $user->pivot->rating_volatility
                );
            });

            $teamsAdapter[$index]["result"] = $team["result"];
        }

        $ratings = Glicko::match(
            $teamsAdapter[0]["rating"]->toArray(),
            $teamsAdapter[1]["rating"]->toArray(),
            $teamsAdapter[0]["result"],
            $teamsAdapter[1]["result"]
        );

        for ($i = 0; $i < 2; $i++) {
            for ($v = 0; $v < $users[$i]->count(); $v++) {
                $match->users()->attach($users[$i][$v], [
                    'team' => $index,
                    'result' => $team['result'],
                    "rating" => round($ratings[$i][$v][0], 2),
                    "rating_deviation" => round($ratings[$i][$v][1], 2),
                    "rating_volatility" => round($ratings[$i][$v][2], 8)
                ]);
                $users[$i][$v]->games()->updateExistingPivot($game, [
                    "rating" => round($ratings[$i][$v][0], 2),
                    "rating_deviation" => round($ratings[$i][$v][1], 2),
                    "rating_volatility" => round($ratings[$i][$v][2], 8)
                ]);
            }
        }
    }
}
