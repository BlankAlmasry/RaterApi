<?php

namespace Tests\Feature;

use App\Models\Game;
use App\Models\MatchUp;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Client;
use Tests\TestCase;

class MatchTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->client = Client::factory()->create();

        $response = $this->post('/login', [
            'grant_type' => 'client_credentials',
            'client_id' => $this->client->getKey(),
            'client_secret' => $this->client->secret,
        ]);
        $this->header = ['Authorization' => 'Bearer ' . $response->json()["access_token"]];
        $this->game = Game::factory()->create(["client_id" => $this->client->id]);

    }

    public function test_an_api_can_create_new_match_for_non_existing_users()
    {
        $response = $this->json('post',
            "/games/{$this->game->slug}/matches",
            $this->validFields()
            , $this->header
        );
        $response->assertStatus(201);
        $response->assertJsonStructure([
            "id", "game", "users", "links"
        ]);

        $this->assertDatabaseCount('matches', 1);
        $this->assertDatabaseCount('users', 4);
    }

    public function test_get_all_matches_for_a_game()
    {
        $match = MatchUp::factory()->create();
        $response = $this->json(
            'get',
            "/games/{$match->game->slug}/matches",
            [],
            $this->header
        );
        $match->users()->attach(User::factory()->create()->id, ["team" => "1"]);
        $match->users()->attach(User::factory()->create()->id, ["team" => "2"]);
        $response->assertJsonStructure([
            "data", "meta", "links"
        ]);
        $response->assertStatus(200);
    }

    public function test_get_all_matches_once_they_are_empty_will_stay_the_same_structure()
    {
        $response = $this->json(
            'get',
            "/games/{$this->game->slug}/matches",
            [],
            $this->header
        );
        $response->assertJsonStructure([
            "data", "meta", "links"
        ]);
        $response->assertStatus(200);
    }


    public function test_can_show_a_single_match()
    {
        $match = MatchUp::factory()->create();
        $response = $this->json(
            'get',
            "/games/{$match->game->slug}/matches/{$match->id}",
            [],
            $this->header
        );
        $response->assertJsonStructure([
            "id",
            "game",
            "users",
            "links"
        ]);
        $response->assertStatus(200);
    }

    /** @test */
    public function fetching_non_existing_match_will_result_in_404()
    {
        $response = $this->json(
            'get',
            "/games/{$this->game->slug}/matches/1",
            [],
            $this->header
        );

        $response->assertStatus(404);
    }

    /** @test */
    public function deleting_non_existing_match_will_result_in_404()
    {
        $response = $this->json(
            'delete',
            "/games/{$this->game->slug}/matches/1",
            [],
            $this->header
        );

        $response->assertStatus(404);
    }

    public function test_uneven_teams_would_result_in_an_error()
    {
        $response = $this->json('post',
            "/games/{$this->game->slug}/matches",
            $this->validFields([
                "teams" => [
                    ["users" => ["test1"], "result" => 0],
                    ["users" => ["test3", "test4"], "result" => 1]
                ]
            ])
            , $this->header

        );

        $response->assertStatus(400);

    }

    public function test_more_than_2_teams_are_not_supported()
    {
        $response = $this->json('post',
            "/games/{$this->game->slug}/matches",
            $this->validFields([
                'teams' => [
                    ["users" => ["test1"], "result" => 0],
                    ["users" => ["test2", "test3"], "result" => 1],
                    ["users" => ["test5", "test4"], "result" => 1]
                ]
            ]),
            $this->header);
        $response->assertStatus(400);
    }

    /** @test */
    public function Duplicate_users_entry_would_result_in_400()
    {
        $response = $this->json('post',
            "/games/{$this->game->slug}/matches",
            $this->validFields([
                "teams" => [
                    ["users" => ["test1", "test3"], "result" => 0],
                    ["users" => ["test3", "test4"], "result" => 1]
                ]
            ])
            , $this->header

        );

        $response->assertStatus(400);
    }


    /** @test */
    public function Once_Match_created_result_will_change_players_rating_accordingly()
    {
        $response = $this->json('post',
            "/games/{$this->game->slug}/matches",
            $this->validFields(),
            $this->header
        );
        $winner = Game::find($this->game)->first()->users()->where('name', "test1")->first();
        $response->assertStatus(201);
        $this->assertEquals($winner->games()->find($this->game)->pivot->rating, round(1662.2108928365, 2));
        $this->assertEquals($winner->games()->find($this->game)->pivot->rating_deviation, round(290.46, 2));
        $this->assertEquals($winner->games()->find($this->game)->pivot->rating_volatility, round(0.059999350751962, 8));
    }

    /** @test */
    public function A_draw_to_2_new_players_wont_change_rating()
    {
        $response = $this->json('post',
            "/games/{$this->game->slug}/matches",
            [
                'teams' => [
                    ["users" => ["test1"], "result" => 0.5],
                    ["users" => ["test2"], "result" => 0.5],
                ]
            ],
            $this->header
        );
        $drawer = Game::find($this->game)->first()->users()->where('name', "test1")->first();
        $response->assertStatus(201);
        $this->assertEquals($drawer->games()->find($this->game)->pivot->rating, round(1500, 2));
    }

    public function test_delete_a_match()
    {
        $match = MatchUp::factory()->create();

        $this->json('delete', "/games/{$match->game->slug}/matches/{$match->id}", [], $this->header);
        $response = $this->json('get', "/games/{$match->game->slug}/matches/{$match->id}", [], $this->header);

        $response->assertStatus(404);

    }

    /** @test */
    public function Inserting_empty_users_to_a_match_will_result_in_400()
    {
        $response = $this->json('post',
            "/games/{$this->game->slug}/matches",
            [
                'teams' => [
                    ["users" => ["test1", ""], "result" => 1],
                    ["users" => ["", "test4"], "result" => 0],
                ]
            ],
            $this->header
        );
        $response->assertStatus(400);
        $this->assertDatabaseCount(MatchUp::class, 0);

    }

    protected function validFields($attrs = [])
    {
        return array_merge([
            'teams' => [
                ["users" => ["test1", "test2"], "result" => 1],
                ["users" => ["test3", "test4"], "result" => 0],
            ]
        ], $attrs);
    }

}
