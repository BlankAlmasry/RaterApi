<?php

namespace Tests\Feature;

use App\Glicko\Glicko;
use App\Models\Game;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Client;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function setUp():void
    {
        parent::setUp(); //
        $this->client = Client::factory()->create();

        $response = $this->post('/api/login', [
            'grant_type' => 'client_credentials',
            'client_id' => $this->client->getKey(),
            'client_secret' => $this->client->secret,
        ]);
        $this->header =  ['Authorization' => 'Bearer '.$response->json()["access_token"]];

        $this->game = Game::factory()->create(["client_id"=>$this->client->id]);
        $this->user = User::factory()->create(["client_id"=>$this->client->id]);
        $this->game->users()->attach($this->user);
    }

    public function test_get_all_users()
    {
        $response = $this->json(
            'get',
            "/api/games/{$this->game->slug}/users",
            [],
            $this->header
        );
        $response->assertJsonStructure([
           "data","links","meta"
        ]);

        $response->assertStatus(200);
    }

    public function test_get_a_single_user()
    {
        $response = $this->json(
            'get',
            "/api/games/{$this->game->slug}/users/{$this->user->slug}",
            [],
            $this->header
        );
        $response->assertJsonFragment([
                "name" => $this->user->name,
                "rating" => $this->user->games()->first()->pivot->rating,

           ]);

        $response->assertStatus(200);
    }

    public function test_a_api_can_view_all_users_on_all_games()
    {

        $response = $this->json(
            'get',
            "/api/users",
            [],
            $this->header
        );
        $response->assertStatus(200);
        $response->assertJsonStructure([
            [
            "name", "links"
             ]
        ]);
    }

    public function test_an_api_can_show_a_single_user()
    {
        $response = $this->json(
            'get',
            "/api/users/{$this->user->slug}",
            [],
            $this->header
        );
        $response->assertStatus(200);
        $response->assertJsonStructure([
                "name", "links"
        ]);
    }

    public function test_return_numeric_to_tier()
    {
        $rating = Glicko::ratingToRank(2759);
        $this->assertEquals($rating,["rank" => "Master", "points" => 159]);
    }

    public function test_an_Api_can_get_user_games()
    {
        $response = $this->json(
            'get',
            "/api/users/{$this->user->slug}/games",
            [],
            $this->header
        );
        $response->assertStatus(200);
        $response->assertJsonStructure([
            "data", "links", "meta"
        ]);
    }

    public function test_an_api_can_get_user_matches()
    {
        $response = $this->json(
            'get',
            "/api/users/{$this->user->slug}/matches",
            [],
            $this->header
        );
        $response->assertStatus(200);
        $response->assertJsonStructure([
            "data", "links", "meta"
        ]);
    }

    /** @test */
    public function User_must_have_a_client()
    {
        $user = User::factory()->create();
        self::assertInstanceOf(Client::class ,$user->client);
    }
}
