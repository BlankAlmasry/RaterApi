<?php

namespace App\GameManager;

use App\Glicko\Glicko;
use Blankalmasry\Glicko2\Rating\Rating;

class GlickoAdapter
{

    public static function updateResults($game, $match, $results)
    {
        $teamsAverage = [];
        $i = 0;
        $users = [];
        $teamResults = [];
        foreach ($results as $team) {
            $users[$i] = $match->users()->wherePivot("team", $i)->get();
            $teamsAverage[$i] = $users[$i]->map(function ($user) {
                return new Rating(
                    $user->pivot->rating,
                    $user->pivot->rating_deviation,
                    $user->pivot->rating_volatility
                );
            });
            $i++;
            $teamResults[] = $team["result"];
        }
        $ratings = Glicko::match(
            $teamsAverage[0]->toArray(),
            $teamsAverage[1]->toArray(),
            $teamResults[0],
            $teamResults[1]
        );

        for ($i = 0; $i < 2; $i++) {
            for ($v = 0; $v < $users[$i]->count(); $v++) {
                $users[$i][$v]->games()->updateExistingPivot($game, [
                    "rating" => round($ratings[$i][$v][0], 2),
                    "rating_deviation" => round($ratings[$i][$v][1], 2),
                    "rating_volatility" => round($ratings[$i][$v][2], 8)
                ]);
            }
        }

    }
}
