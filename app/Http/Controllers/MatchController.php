<?php

namespace App\Http\Controllers;

use App\Glicko\Glicko;
use Blankalmasry\Glicko2\Rating\Rating;
use App\Http\Resources\MatchResource;
use App\Models\Game;
use App\Models\MatchUp;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MatchController extends Controller
{

    public function create(Request $request, Game $game)
    {
        // if validation error exist
        if ($this->validateTeams($request)){
            return $this->validateTeams($request);
        };

        //else create the match

        $match = MatchUp::create([
            'game_id' => $game->id,
            'team_length' => $request->team_length,
        ]);
        $this->addPlayersToMatch($game, $match, $request->teams);


        return response(new MatchResource($match->loadMissing('game')),201);
    }

    public function index(Game $game)
    {

        $matches = $game->matches();
        if($matches->get()->isEmpty()){
            return MatchResource::collection([])
                ->response()
                ->setStatusCode(200);
        };
        return MatchResource::collection($matches->paginate(10))
            ->response()
            ->setStatusCode(200);
    }

    public function update(Game $game, MatchUp $match, Request $request)
    {

        if ($this->validateTeams($request)){
            return $this->validateTeams($request);
        };
        $match->update([
            'team_length' => $request->team_length,
        ]);

        $match->users()->detach();
        $this->addPlayersToMatch($game, $match, $request->teams);
        return response(new MatchResource($match),200);
    }

    public function show($game, $match)
    {
        try
        {
            $match = MatchUp::findOrFail($match);
        }
        catch(ModelNotFoundException $e)
        {
            return \response([], 404);
        }

        return \response(new MatchResource($match), 200);
    }


    public function patch(Game $game, MatchUp $match, Request $request)
    {

        if ($request->teams || $request->team_length){
            return \response(["message" => "Patch method are only to update results, Use PUT instead if you wish to change teams or team_length"],400);
        }


        if ($request->results){
            $validator = Validator::make($request->all(), [
                'teams.*.team' => 'required',
                'teams.*.result' => '|required',
            ]);
            if ($validator->fails()) {
                return response($validator->errors(),400);
            }
            $this->updateResults($game, $match, $request->results);
        }
        return \response(new MatchResource($match->fresh()),200);
    }

    public function delete($game, $match)
    {
        try
        {
            $match = MatchUp::findOrFail($match);
        }
        catch(ModelNotFoundException $e)
        {
            return \response([], 404);
        }
        $match->delete();
        return \response(["message", "resource deleted"], 200);
    }


    public function addPlayersToMatch($game, $match, $teams)
    {
        foreach ($teams as $team => $player) {
            $user = User::firstOrCreate([
                "name" => $player['name'],
                "client_id" => $this->client->id
            ]);
            $game->users()->syncWithoutDetaching([$user->id]);

            $match->users()->attach($user, [
                'team' => $player["team"],
                'rating' => $user->games()->find($game)->pivot->rating,
                'rating_deviation' => $user->games()->find($game)->pivot->rating_deviation,
                'rating_volatility' => $user->games()->find($game)->pivot->rating_volatility
            ]);
        }
    }


    public function updateResults($game, $match, $results ){
 /*       $teamsAverageRating = [];
        $teamsAverageRatingDelta = [];
        $matchResult = [];
        foreach ($results as $result ){
            $matchResult[] = $result["result"];
            $users = $match->users()->wherePivot("team", $result["team"])->get();
            $averageRating = [];
            $rating = $users->map(function ($user) use ($game) {
                    return $user->games()->find($game)->pivot->rating;
                })->sum()/$match->team_length;
            $rating_deviation = $users->map(function ($user) use ($game) {
                    return $user->games()->find($game)->pivot->rating_deviation;
                })->sum()/$match->team_length;
            $rating_volatility = $users->map(function ($user) use ($game) {
                    return $user->games()->find($game)->pivot->rating_volatility;
                })->sum()/$match->team_length;
            array_push($averageRating ,$rating, $rating_deviation, $rating_volatility);

            $teamsAverageRating[] = $averageRating;
        }
        $ratings = Glicko::Match
        (new Rating(
            ...$teamsAverageRating[0]
        ),
            new Rating(
                ...$teamsAverageRating[1]
            ),
            $matchResult[0],
            $matchResult[1],
            );
        for ($i=0; $i<2; $i++){
            $teamsAverageRatingDelta[] = array_map(function ($x, $y) { return $x - $y; } , $ratings[$i], $teamsAverageRating[$i]);

        }
        $n=0;
        foreach ($results as $result ){
            $users = $match->users()->wherePivot("team", $result["team"])->get();
            $match->users()->updateExistingPivot($users,["result" => $result['result']]);
            $users->each(function ($user) use ($game, $teamsAverageRatingDelta, $n, $result){
                $user->pivot->result = $result["result"];
                $user->games()->updateExistingPivot($game, [
                    "rating" => $user->games()->find($game)->pivot->rating + $teamsAverageRatingDelta[$n][0],
                    "rating_deviation" => $user->games()->find($game)->pivot->rating_deviation + $teamsAverageRatingDelta[$n][1],
                    "rating_volatility" => $user->games()->find($game)->pivot->rating_volatility + $teamsAverageRatingDelta[$n][2],
                ]);
            });
            $n++;
        }*/
        $teamsAverage = [];
        $i = 0;
        $users =[];
        foreach ($results as ["team" => $team, "result" => $result] ) {
            $users[$i] = $match->users()->wherePivot("team", $team)->get();
            $teamsAverage[$i] = $users[$i]->map(function ($user){
                return new Rating(
                    $user->pivot->rating,
                    $user->pivot->rating_deviation,
                    $user->pivot->rating_volatility
                );
            });
            $i++;
        }
        $ratings = Glicko::match(
            $teamsAverage[0]->toArray(),
            $teamsAverage[1]->toArray(),
            $results[0]["result"],
            $results[1]["result"]
        );
        for ($i=0; $i < 2; $i++){
            for ($v=0; $v < $users[$i]->count(); $v++){
                $users[$i][$v]->games()->updateExistingPivot($game,[
                    "rating" => round($ratings[$i][$v][0],2),
                    "rating_deviation" =>  round($ratings[$i][$v][1],2),
                    "rating_volatility" =>  round($ratings[$i][$v][2],8)
                ]);
            }
        }

    }

    public function validateTeams($request)
    {

        $validator = Validator::make($request->all(), [
            'team_length' => 'required|integer',
            'teams' => 'required|array',
            'teams.*.name' => 'required',
            'teams.*.team' => 'required',
        ]);
        if ($validator->fails()) {
            return response($validator->errors(),400);
        }
        $uniqueTeams=[];
        foreach ($request->teams as $team => $player)
        {
            if (!array_key_exists($player["team"],$uniqueTeams)){
                $uniqueTeams[$player["team"]]=1;
            }
            else{

                $uniqueTeams[$player["team"]]+=1;
            }
        }

        if (sizeof($uniqueTeams)!==2){
            return response(["message" => "Only 2 teams are supported"],400);

        }

        foreach ($uniqueTeams as $team => $noOfPlayers){
            if ($noOfPlayers !== (int)$request->team_length){
                return response(["message" => "each team should have exactly {$request->team_length} players"],400);
            }
        }
    }

}
