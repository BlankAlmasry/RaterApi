<?php

namespace Database\Seeders;

use App\Http\Controllers\MatchController;
use App\Models\Game;
use App\Models\MatchUp;
use Illuminate\Database\Seeder;
use Laravel\Passport\Client;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        for ($ii=0; $ii < 2; $ii++) {
            $client = Client::factory()->create();
            $players = ["sami".$ii, "ahmed".$ii, "mo3tz".$ii, "osama".$ii, "blank".$ii, "ragaab".$ii, "mohamed".$ii, "tariq".$ii, "hassan".$ii, "yassin".$ii];
            $game = Game::factory()->create(["client_id" => $client->id]);
            $apiController = new MatchController();
            for ($i = 0; $i < 1000; $i++) {
                $match = MatchUp::factory()->create(["game_id" => $game->id]);
                $apiController->addPlayersToMatch($game, $match, [
                    ["name" => $players[$i % 10], "team" => "1"],
                    ["name" => $players[($i + random_int(1, 15)) % 10], "team" => "2"],
                ],$client->id);
                $apiController->updateResults($game, $match, [
                    ["team" => "1", "result" => "1"],
                    ["team" => "2", "result" => "0"]
                ]);
            }

        }
    }
}
