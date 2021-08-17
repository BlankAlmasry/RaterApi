<?php

namespace Tests\Feature;

use App\Http\Resources\MatchResource;
use App\Models\Game;
use App\Models\MatchUp;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Laravel\Passport\Client;
use Laravel\Passport\Http\Middleware\CheckClientCredentials;
use Laravel\Passport\Passport;
use Tests\TestCase;

class MatchTest extends TestCase
{
    use RefreshDatabase;

    public function setUp():void
    {
        parent::setUp();
        $this->client = Client::factory()->create();

        $response = $this->post('/api/login', [
            'grant_type' => 'client_credentials',
            'client_id' => $this->client->getKey(),
            'client_secret' => $this->client->secret,
        ]);
        $this->header =  ['Authorization' => 'Bearer '.$response->json()["access_token"]];
        $this->game = Game::factory()->create(["client_id" => $this->client->id]);

    }
    public function test_an_api_can_create_new_match_for_non_existing_users()
    {
        $response = $this->json('post',
            "/api/games/{$this->game->slug}/matches",
            $this->validFields()
            , $this->header
        );

        $response->assertStatus(201);
        $response->assertJsonStructure([
                "id","team_length","game","users", "links"
        ]);

        $this->assertDatabaseCount('matches',1);
        $this->assertDatabaseCount('users',4);
    }

    public function test_team_length_and_teams_are_required_to_create_a_match()
    {
        $response = $this->json('post',
            "/api/games/{$this->game->slug}/matches",
            [],
            $this->header
        );
        $response->assertStatus(400);
    }

    public function test_create_invalid_team_length_match_would_result_in_400()
    {
        $response = $this->json('post',
            "/api/games/{$this->game->slug}/matches",
            $this->validFields(['team_length' => '1']),
            $this->header
        );

        $response->assertStatus(400);

    }

    public function test_get_all_matches_for_a_game()
    {
        $match = MatchUp::factory()->create();
        $response = $this->json(
            'get',
            "api/games/{$match->game->slug}/matches",
            [],
            $this->header
        );
        $match->users()->attach(User::factory()->create()->id,["team" => "1"]);
        $match->users()->attach(User::factory()->create()->id,["team" => "2"]);
        $response->assertJsonStructure([
                "data","meta","links"
        ]);
        $response->assertStatus(200);
    }

    public function test_can_update_a_match()
    {
        $match = MatchUp::factory()->create();
        $response = $this->json(
            'put',
            "api/games/{$match->game->slug}/matches/{$match->id}",
            $this->validFields([
                "team_length" => 1,
                "teams" => [
                ["name" => "test1", "team" => "1"],
                ["name" => "test2", "team" => "2"]
            ]
                ]
        ),$this->header

        );
        $response->assertStatus(200);
        $this->assertDatabaseHas('matches',[
            "id" => $match->id,
            "team_length" => $match->fresh()->team_length,
        ]);
        $this->assertEquals($match->users->count(),2);
    }

    public function test_can_show_a_single_match()
    {
        $match = MatchUp::factory()->create();
        $response = $this->json(
            'get',
            "api/games/{$match->game->slug}/matches/{$match->id}",
            [],
            $this->header
        );
        $response->assertJsonStructure([
                "id" ,
                "team_length" ,
                "game",
                "users",
                "links"
        ]);
        $response->assertStatus(200);
    }

    public function test_uneven_teams_would_result_in_an_error()
    {
        $response = $this->json('post',
            "/api/games/{$this->game->slug}/matches",
            $this->validFields([
                'teams' => [
                    ["name" => Str::random(8), "team" => "1"],
                    ["name" => Str::random(8), "team" => "2"],
                    ["name" => Str::random(8), "team" => "2"],
                    ["name" => Str::random(8), "team" => "2"],
                ]
            ])
            ,$this->header

        );

        $response->assertStatus(400);

    }

    public function test_more_than_2_teams_are_not_supported()
    {
        $response = $this->json('post',
            "/api/games/{$this->game->slug}/matches",
            $this->validFields([
                'teams' => [
                    ["name" => Str::random(8), "team" => "1"],
                    ["name" => Str::random(8), "team" => "2"],
                ]
            ]),
            $this->header);
        $response->assertStatus(400);
    }

    protected function validFields($attrs =[]){
        return array_merge([
            'team_length' => '2',
            'teams' => [
                ["name" => Str::random(8), "team" => "1"],
                ["name" => Str::random(8), "team" => "1"],
                ["name" => Str::random(8), "team" => "2"],
                ["name" => Str::random(8), "team" => "2"],
            ]
        ],$attrs);
    }



    public function test_can_patch_match_results()
    {

        $this->withoutExceptionHandling();
        $match = MatchUp::factory()->create(["team_length" =>1]);
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $this->game->users()->attach($user1);
        $this->game->users()->attach($user2);
        $match->users()->attach($user1,["team" => "1"]);
        $match->users()->attach($user2,["team" => "2"]);
        $match->game = $this->game->id;
        $response = $this->json('patch',
            "/api/games/{$this->game->slug}/matches/{$match->id}",
            [
                "results" => [
                    ["team" =>"1", "result" => "1"],
                    ["team" =>"2", "result" => "0"]
                ]
            ],
            $this->header
        );
        $response->assertStatus(200);
        $this->assertEquals($match->users()->find($user1)->pivot->result,$user1->matches()->find($match)->pivot->result);

    }

    public function test_once_results_are_known_users_ratings_will_get_updated()
    {
        $this->withoutExceptionHandling();
        $match = MatchUp::factory()->create(["team_length" =>2]);
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $user3 = User::factory()->create();
        $user4 = User::factory()->create();
        $this->game->users()->attach($user1);
        $this->game->users()->attach($user2);
        $this->game->users()->attach($user3);
        $this->game->users()->attach($user4);
        $match->users()->attach($user1,["team" => "1"]);
        $match->users()->attach($user2,["team" => "1"]);
        $match->users()->attach($user3,["team" => "2"]);
        $match->users()->attach($user4,["team" => "2"]);
        $match->game = $this->game->id;

        $response = $this->json('patch',
            "/api/games/{$this->game->slug}/matches/{$match->id}",
            [
                "results" => [
                    ["team" =>"1", "result" => "1"],
                    ["team" =>"2", "result" => "0"]
                ]
            ],
            $this->header
        );
        $response->assertStatus(200);
        $this->assertEquals($match->users()->find($user1)->pivot->result,$user1->matches()->find($match)->pivot->result);
        $this->assertEquals($user2->games()->find($this->game)->pivot->rating, round(1662.2108928365,2));
        $this->assertEquals($user2->games()->find($this->game)->pivot->rating_deviation, round(290.46,2));
        $this->assertEquals($user2->games()->find($this->game)->pivot->rating_volatility, round(0.059999350751962,8));
    }




    public function test_delete_a_match()
    {
        $match = MatchUp::factory()->create();

        $this->json('delete', "/api/games/{$match->game->slug}/matches/{$match->id}",[],$this->header);
        $response = $this->json('get', "/api/games/{$match->game->slug}/matches/{$match->id}",[],$this->header);

        $response->assertStatus(404);

    }

}
